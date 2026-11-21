"""
AI Demand Forecasting Inference Engine — NesaCourt AI Service.

Menangani seluruh skenario Panduan Konsep AI NesaCourt (Bagian 4):
- Kasus A: Data cukup (>= 7 hari) -> prediksi ML + rentang kehati-hatian
- Kasus B: Pola hari tertentu (misal Selasa-Kamis ramai vs Sabtu sepi)
- Kasus C: Data masih sedikit (1-6 hari) -> status insufficient_data & label sementara
- Kasus D: Menu baru (0 riwayat) -> estimasi pembanding kategori sejenis & disclaimer kasar
- Kasus E: Lonjakan tidak biasa (acara kampus / penyesuaian manual penjual)
- Transaksi status 'dibatalkan' otomatis disaring jika history dikirim dari Laravel.
"""

from __future__ import annotations

from datetime import date, datetime
from pathlib import Path
from typing import Any, Dict, List, Optional
import json
import math

import joblib
import numpy as np
import pandas as pd


BASE_DIR = Path(__file__).resolve().parent
ARTIFACTS_DIR = BASE_DIR / "artifacts"
DAILY_MODEL_PATH = ARTIFACTS_DIR / "daily_forecast_model.joblib"
HOURLY_MODEL_PATH = ARTIFACTS_DIR / "hourly_forecast_model.joblib"
EVAL_REPORT_PATH = ARTIFACTS_DIR / "evaluation_report.json"

DAY_NAMES_ID = {
    1: "Senin",
    2: "Selasa",
    3: "Rabu",
    4: "Kamis",
    5: "Jumat",
    6: "Sabtu",
    7: "Minggu",
}


class DemandForecaster:
    def __init__(self) -> None:
        self._daily_artifact: Optional[Dict[str, Any]] = None
        self._hourly_artifact: Optional[Dict[str, Any]] = None

    def load(self) -> None:
        if self._daily_artifact is None and DAILY_MODEL_PATH.exists():
            self._daily_artifact = joblib.load(DAILY_MODEL_PATH)
        if self._hourly_artifact is None and HOURLY_MODEL_PATH.exists():
            self._hourly_artifact = joblib.load(HOURLY_MODEL_PATH)

    @property
    def is_ready(self) -> bool:
        self.load()
        return self._daily_artifact is not None and self._hourly_artifact is not None

    def get_evaluation_report(self) -> Dict[str, Any]:
        if not EVAL_REPORT_PATH.exists():
            return {"status": "not_found", "message": "Laporan evaluasi belum tersedia. Jalankan train_forecasting.py."}
        return json.loads(EVAL_REPORT_PATH.read_text(encoding="utf-8"))

    def _parse_date(self, target_date: Optional[str]) -> date:
        if not target_date:
            return date.today()
        return datetime.strptime(target_date, "%Y-%m-%d").date()

    def _clean_custom_history(self, history: List[Dict[str, Any]]) -> List[Dict[str, Any]]:
        """Menyaring transaksi dibatalkan dari custom history (aturan wajib Konsep AI)."""
        cleaned: List[Dict[str, Any]] = []
        for item in history:
            status = str(item.get("order_status", "selesai")).lower()
            if status in {"dibatalkan", "cancelled", "batal"}:
                continue
            qty = int(item.get("quantity", item.get("completed_qty_sold", 0)))
            dt_str = str(item.get("date", ""))
            if dt_str:
                cleaned.append({"date": dt_str, "quantity": max(0, qty)})
        # Agregasi per tanggal jika ada beberapa record pada hari yang sama
        by_date: Dict[str, int] = {}
        for c in cleaned:
            by_date[c["date"]] = by_date.get(c["date"], 0) + c["quantity"]
        return [{"date": d, "quantity": by_date[d]} for d in sorted(by_date.keys())]

    def forecast_daily(
        self,
        menu_id: Optional[int] = None,
        seller_id: Optional[int] = None,
        target_date: Optional[str] = None,
        weather: str = "cerah",
        has_campus_event: bool = False,
        manual_adjustment_pct: float = 0.0,
        item_type: str = "makanan",
        price_idr: int = 13000,
        daily_stock_quota: int = 20,
        custom_history: Optional[List[Dict[str, Any]]] = None,
    ) -> Dict[str, Any]:
        self.load()
        if self._daily_artifact is None:
            raise RuntimeError("Model daily forecast belum dilatih. Jalankan train_forecasting.py terlebih dahulu.")

        dt = self._parse_date(target_date)
        dow = dt.isoweekday()
        day_name = DAY_NAMES_ID[dow]

        # Minggu: Foodcourt kampus libur
        if dow == 7:
            return {
                "status": "closed",
                "data_status": "non_operating_day",
                "target_date": dt.isoformat(),
                "day_name": day_name,
                "predicted_quantity": 0,
                "range_min": 0,
                "range_max": 0,
                "confidence_level": "tinggi",
                "is_temporary_estimate": False,
                "model_version": self._daily_artifact["model_version"],
                "input_data_points": 0,
                "explanation": "Hari Minggu operasional Foodcourt Baseball UNESA tutup/libur.",
            }

        profiles = self._daily_artifact["menu_profiles"]
        benchmarks = self._daily_artifact["category_benchmarks"]
        profile = profiles.get(int(menu_id)) if menu_id is not None else None

        # Jika caller mengirim custom_history eksplisit (misal uji Kasus C: stan baru 2 hari, atau Kasus D: menu baru 0 hari)
        if custom_history is not None:
            cleaned_hist = self._clean_custom_history(custom_history)
            n_points = len(cleaned_hist)

            # KASUS D: Menu Baru (0 riwayat)
            if n_points == 0:
                return self._new_menu_fallback(
                    dt=dt,
                    dow=dow,
                    day_name=day_name,
                    item_type=item_type,
                    daily_stock_quota=daily_stock_quota,
                    has_campus_event=has_campus_event,
                    manual_adjustment_pct=manual_adjustment_pct,
                    benchmarks=benchmarks,
                )

            # KASUS C: Data Masih Sedikit (1 - 6 hari)
            if n_points < 7:
                qtys = [h["quantity"] for h in cleaned_hist]
                base_est = float(np.mean(qtys))
                if has_campus_event:
                    base_est *= 1.15
                if manual_adjustment_pct:
                    base_est *= 1.0 + (manual_adjustment_pct / 100.0)
                pred_qty = max(1, min(daily_stock_quota, int(round(base_est))))
                margin = max(2, int(round(pred_qty * 0.25)))
                return {
                    "status": "ok",
                    "data_status": "insufficient_data",
                    "target_date": dt.isoformat(),
                    "day_name": day_name,
                    "menu_id": menu_id,
                    "seller_id": seller_id,
                    "predicted_quantity": pred_qty,
                    "range_min": max(0, pred_qty - margin),
                    "range_max": min(daily_stock_quota, pred_qty + margin),
                    "confidence_level": "rendah",
                    "is_temporary_estimate": True,
                    "model_version": "temporary-mean-fallback-v1",
                    "input_data_points": n_points,
                    "explanation": (
                        f"Data belum cukup (baru tersedia {n_points} hari transaksi selesai; minimal 7 hari). "
                        "Angka ini hanya perkiraan sementara rata-rata dasar dan belum dapat disebut prediksi AI akurat."
                    ),
                }

            # Jika custom_history >= 7 hari -> hitung fitur lag dari custom_history
            qtys = [h["quantity"] for h in cleaned_hist]
            lag_1 = float(qtys[-1])
            rolling_mean_7 = float(np.mean(qtys[-7:]))
            rolling_std_7 = float(np.std(qtys[-7:], ddof=1)) if len(qtys[-7:]) > 1 else 1.5
            same_dow_vals = [
                h["quantity"]
                for h in cleaned_hist
                if datetime.strptime(h["date"], "%Y-%m-%d").isoweekday() == dow
            ]
            same_dow_mean = float(np.mean(same_dow_vals[-4:])) if same_dow_vals else rolling_mean_7
            input_points = n_points
        else:
            # Tidak ada custom_history -> gunakan profil hasil training dari ai_dataset
            if profile is None:
                # KASUS D: Menu belum dikenal (menu baru)
                return self._new_menu_fallback(
                    dt=dt,
                    dow=dow,
                    day_name=day_name,
                    item_type=item_type,
                    daily_stock_quota=daily_stock_quota,
                    has_campus_event=has_campus_event,
                    manual_adjustment_pct=manual_adjustment_pct,
                    benchmarks=benchmarks,
                )
            lag_1 = profile["last_sold"]
            rolling_mean_7 = profile["rolling_mean_7"]
            rolling_std_7 = profile["rolling_std_7"]
            same_dow_mean = profile["dow_means"].get(dow, rolling_mean_7)
            input_points = profile["history_days_count"]
            seller_id = profile["seller_id"]
            item_type = profile["item_type"]
            price_idr = profile["price_idr"]
            daily_stock_quota = profile["daily_stock_quota"]

        # Susun fitur input untuk pipeline ML terlatih (Kasus A, B, E)
        pop_weight = profile["journal_popularity_weight"] if profile else 14.0
        is_weekend = 1 if dow == 6 else 0
        is_end_of_month = 1 if dt.day >= 24 else 0
        is_rainy = 1 if str(weather).lower() == "hujan" else 0
        event_flag = 1 if has_campus_event else 0

        row = pd.DataFrame(
            [
                {
                    "seller_id": int(seller_id or 1),
                    "menu_id": int(menu_id or 1),
                    "day_of_week": dow,
                    "item_type_num": 1 if str(item_type).lower() == "minuman" else 0,
                    "price_idr": int(price_idr),
                    "daily_stock_quota": int(daily_stock_quota),
                    "journal_popularity_weight": float(pop_weight),
                    "is_weekend": is_weekend,
                    "is_end_of_month": is_end_of_month,
                    "is_rainy": is_rainy,
                    "has_campus_event": event_flag,
                    "lag_1": lag_1,
                    "rolling_mean_7": rolling_mean_7,
                    "rolling_std_7": rolling_std_7,
                    "same_dow_mean_4": same_dow_mean,
                }
            ]
        )

        raw_pred = float(self._daily_artifact["pipeline"].predict(row)[0])

        # Kasus E: penyesuaian lonjakan acara kampus / input manual penjual
        event_note = ""
        if has_campus_event:
            raw_pred = max(raw_pred, min(daily_stock_quota, raw_pred * 1.08))
            event_note = " Termasuk penyesuaian lonjakan acara kampus."
        if manual_adjustment_pct:
            raw_pred *= 1.0 + (manual_adjustment_pct / 100.0)
            event_note += f" Penyesuaian manual penjual {manual_adjustment_pct:+.0f}%."

        pred_qty = max(1, min(int(daily_stock_quota), int(round(raw_pred))))
        mae_band = max(1, int(math.ceil(self._daily_artifact["test_mae"] * 1.25)))

        # Penjelasan singkat (tidak mengklaim pasti benar)
        day_context = (
            "pola hari Sabtu yang cenderung lebih sepi"
            if dow == 6
            else ("pola hari Jumat kuliah lebih singkat" if dow == 5 else f"pola hari kerja ({day_name})")
        )
        weather_context = "cuaca hujan" if is_rainy else "cuaca cerah"

        return {
            "status": "ok",
            "data_status": "sufficient",
            "target_date": dt.isoformat(),
            "day_name": day_name,
            "menu_id": menu_id,
            "seller_id": seller_id,
            "menu_name": profile["menu_name"] if profile else None,
            "stall_name": profile["stall_name"] if profile else None,
            "item_type": item_type,
            "daily_stock_quota": int(daily_stock_quota),
            "predicted_quantity": pred_qty,
            "range_min": max(0, pred_qty - mae_band),
            "range_max": min(int(daily_stock_quota), pred_qty + mae_band),
            "confidence_level": "tinggi" if input_points >= 21 and not has_campus_event else "sedang",
            "is_temporary_estimate": False,
            "model_version": self._daily_artifact["model_version"],
            "input_data_points": int(input_points),
            "explanation": (
                f"Perkiraan berdasarkan {input_points} hari riwayat penjualan selesai, mengikuti {day_context} "
                f"dan kondisi {weather_context} (rata-rata hari {day_name} sebelumnya ±{same_dow_mean:.1f} porsi)."
                f"{event_note} Gunakan sebagai bantuan perencanaan stok."
            ),
        }

    def _new_menu_fallback(
        self,
        dt: date,
        dow: int,
        day_name: str,
        item_type: str,
        daily_stock_quota: int,
        has_campus_event: bool,
        manual_adjustment_pct: float,
        benchmarks: Dict[str, Dict[int, float]],
    ) -> Dict[str, Any]:
        cat_key = "minuman" if str(item_type).lower() == "minuman" else "makanan"
        cat_avg = benchmarks.get(cat_key, {}).get(dow, daily_stock_quota * 0.65)
        est = min(float(daily_stock_quota), float(cat_avg))
        if has_campus_event:
            est *= 1.12
        if manual_adjustment_pct:
            est *= 1.0 + (manual_adjustment_pct / 100.0)
        pred_qty = max(1, min(daily_stock_quota, int(round(est))))
        margin = max(3, int(round(pred_qty * 0.30)))
        return {
            "status": "ok",
            "data_status": "new_menu_fallback",
            "target_date": dt.isoformat(),
            "day_name": day_name,
            "predicted_quantity": pred_qty,
            "range_min": max(0, pred_qty - margin),
            "range_max": min(daily_stock_quota, pred_qty + margin),
            "confidence_level": "rendah",
            "is_temporary_estimate": True,
            "model_version": "category-benchmark-fallback-v1",
            "input_data_points": 0,
            "explanation": (
                f"Menu baru belum memiliki riwayat penjualan. Perkiraan kasar ini memakai pembanding rata-rata "
                f"kategori {cat_key} pada hari {day_name}; penjual disarankan menyesuaikan secara manual."
            ),
        }

    def forecast_hourly(
        self,
        menu_id: int,
        target_date: Optional[str] = None,
        weather: str = "cerah",
        has_campus_event: bool = False,
    ) -> Dict[str, Any]:
        self.load()
        if self._daily_artifact is None or self._hourly_artifact is None:
            raise RuntimeError("Model hourly forecast belum dilatih.")

        dt = self._parse_date(target_date)
        dow = dt.isoweekday()
        day_name = DAY_NAMES_ID[dow]

        if dow == 7:
            return {
                "status": "closed",
                "target_date": dt.isoformat(),
                "day_name": day_name,
                "hourly_predictions": [],
                "explanation": "Foodcourt tutup pada hari Minggu.",
            }

        profile = self._daily_artifact["menu_profiles"].get(int(menu_id))
        if profile is None:
            return {
                "status": "not_found",
                "message": f"Menu ID {menu_id} belum memiliki riwayat per jam.",
                "hourly_predictions": [],
            }

        is_weekend = 1 if dow == 6 else 0
        is_end_of_month = 1 if dt.day >= 24 else 0
        is_rainy = 1 if str(weather).lower() == "hujan" else 0
        event_flag = 1 if has_campus_event else 0

        rows = []
        for h in range(8, 17):
            rows.append(
                {
                    "seller_id": profile["seller_id"],
                    "menu_id": int(menu_id),
                    "hour": h,
                    "day_of_week": dow,
                    "item_type_num": 1 if profile["item_type"] == "minuman" else 0,
                    "daily_stock_quota": profile["daily_stock_quota"],
                    "journal_popularity_weight": profile["journal_popularity_weight"],
                    "is_peak_lunch": 1 if h in (11, 12, 13) else 0,
                    "is_weekend": is_weekend,
                    "is_end_of_month": is_end_of_month,
                    "is_rainy": is_rainy,
                    "has_campus_event": event_flag,
                    "rolling_daily_mean_7": profile["rolling_mean_7"],
                }
            )

        df_in = pd.DataFrame(rows)
        preds = np.clip(self._hourly_artifact["pipeline"].predict(df_in), 0, None)

        hourly_list = []
        for h, p in zip(range(8, 17), preds):
            qty = int(round(float(p)))
            hourly_list.append(
                {
                    "hour": f"{h:02d}:00",
                    "predicted_quantity": qty,
                    "is_peak_hour": h in (11, 12, 13),
                }
            )

        peak_slot = max(hourly_list, key=lambda x: x["predicted_quantity"])
        return {
            "status": "ok",
            "target_date": dt.isoformat(),
            "day_name": day_name,
            "menu_id": int(menu_id),
            "menu_name": profile["menu_name"],
            "stall_name": profile["stall_name"],
            "model_version": self._hourly_artifact["model_version"],
            "total_predicted_daily": sum(x["predicted_quantity"] for x in hourly_list),
            "peak_hour": peak_slot["hour"],
            "hourly_predictions": hourly_list,
            "explanation": f"Puncak permintaan diperkirakan pada jam {peak_slot['hour']} (jam istirahat kuliah).",
        }

    def forecast_seller_daily(
        self,
        seller_id: int,
        target_date: Optional[str] = None,
        weather: str = "cerah",
        has_campus_event: bool = False,
        manual_adjustment_pct: float = 0.0,
    ) -> Dict[str, Any]:
        self.load()
        if self._daily_artifact is None:
            raise RuntimeError("Model daily forecast belum dilatih.")

        profiles = self._daily_artifact["menu_profiles"]
        seller_menus = sorted(
            (p for p in profiles.values() if int(p["seller_id"]) == int(seller_id)),
            key=lambda x: (x["item_type"] == "minuman", -x["daily_stock_quota"]),
        )
        if not seller_menus:
            return {
                "status": "not_found",
                "message": f"Seller ID {seller_id} belum memiliki riwayat menu.",
                "forecasts": [],
            }

        dt = self._parse_date(target_date)
        dow = dt.isoweekday()
        day_name = DAY_NAMES_ID[dow]

        # Hari Minggu tutup -> langsung kembalikan 0 untuk semua menu tanpa panggil model
        if dow == 7:
            forecasts = [
                {
                    "status": "closed",
                    "data_status": "non_operating_day",
                    "target_date": dt.isoformat(),
                    "day_name": day_name,
                    "menu_id": p["menu_id"],
                    "seller_id": int(seller_id),
                    "menu_name": p["menu_name"],
                    "stall_name": p["stall_name"],
                    "item_type": p["item_type"],
                    "daily_stock_quota": int(p["daily_stock_quota"]),
                    "predicted_quantity": 0,
                    "range_min": 0,
                    "range_max": 0,
                    "confidence_level": "tinggi",
                    "is_temporary_estimate": False,
                    "model_version": self._daily_artifact["model_version"],
                    "input_data_points": int(p["history_days_count"]),
                    "explanation": "Hari Minggu operasional Foodcourt Baseball UNESA tutup/libur.",
                }
                for p in seller_menus
            ]
            return {
                "status": "ok",
                "seller_id": int(seller_id),
                "stall_name": seller_menus[0]["stall_name"],
                "target_date": dt.isoformat(),
                "day_name": day_name,
                "summary": {
                    "total_predicted_food_portions": 0,
                    "total_predicted_drink_cups": 0,
                    "total_menus": len(forecasts),
                },
                "forecasts": forecasts,
            }

        # Batch prediction: 1 kali predict untuk seluruh menu tenant (jauh lebih cepat!)
        is_weekend = 1 if dow == 6 else 0
        is_end_of_month = 1 if dt.day >= 24 else 0
        is_rainy = 1 if str(weather).lower() == "hujan" else 0
        event_flag = 1 if has_campus_event else 0

        batch_rows = []
        same_dow_list = []
        for p in seller_menus:
            same_dow = p["dow_means"].get(dow, p["rolling_mean_7"])
            same_dow_list.append(same_dow)
            batch_rows.append(
                {
                    "seller_id": int(p["seller_id"]),
                    "menu_id": int(p["menu_id"]),
                    "day_of_week": dow,
                    "item_type_num": 1 if p["item_type"] == "minuman" else 0,
                    "price_idr": int(p["price_idr"]),
                    "daily_stock_quota": int(p["daily_stock_quota"]),
                    "journal_popularity_weight": float(p["journal_popularity_weight"]),
                    "is_weekend": is_weekend,
                    "is_end_of_month": is_end_of_month,
                    "is_rainy": is_rainy,
                    "has_campus_event": event_flag,
                    "lag_1": float(p["last_sold"]),
                    "rolling_mean_7": float(p["rolling_mean_7"]),
                    "rolling_std_7": float(p["rolling_std_7"]),
                    "same_dow_mean_4": float(same_dow),
                }
            )

        raw_preds = self._daily_artifact["pipeline"].predict(pd.DataFrame(batch_rows))
        mae_band = max(1, int(math.ceil(self._daily_artifact["test_mae"] * 1.25)))
        day_context = (
            "pola hari Sabtu yang cenderung lebih sepi"
            if dow == 6
            else ("pola hari Jumat kuliah lebih singkat" if dow == 5 else f"pola hari kerja ({day_name})")
        )
        weather_context = "cuaca hujan" if is_rainy else "cuaca cerah"

        forecasts = []
        total_food = 0
        total_drink = 0

        for p, raw_val, same_dow in zip(seller_menus, raw_preds, same_dow_list):
            quota = int(p["daily_stock_quota"])
            val = float(raw_val)
            event_note = ""
            if has_campus_event:
                val = max(val, min(quota, val * 1.08))
                event_note = " Termasuk penyesuaian lonjakan acara kampus."
            if manual_adjustment_pct:
                val *= 1.0 + (manual_adjustment_pct / 100.0)
                event_note += f" Penyesuaian manual penjual {manual_adjustment_pct:+.0f}%."

            pred_qty = max(1, min(quota, int(round(val))))
            input_points = int(p["history_days_count"])

            forecasts.append(
                {
                    "status": "ok",
                    "data_status": "sufficient",
                    "target_date": dt.isoformat(),
                    "day_name": day_name,
                    "menu_id": int(p["menu_id"]),
                    "seller_id": int(seller_id),
                    "menu_name": p["menu_name"],
                    "stall_name": p["stall_name"],
                    "item_type": p["item_type"],
                    "daily_stock_quota": quota,
                    "predicted_quantity": pred_qty,
                    "range_min": max(0, pred_qty - mae_band),
                    "range_max": min(quota, pred_qty + mae_band),
                    "confidence_level": "tinggi" if input_points >= 21 and not has_campus_event else "sedang",
                    "is_temporary_estimate": False,
                    "model_version": self._daily_artifact["model_version"],
                    "input_data_points": input_points,
                    "explanation": (
                        f"Perkiraan berdasarkan {input_points} hari riwayat penjualan selesai, mengikuti {day_context} "
                        f"dan kondisi {weather_context} (rata-rata hari {day_name} sebelumnya ±{same_dow:.1f} porsi)."
                        f"{event_note} Gunakan sebagai bantuan perencanaan stok."
                    ),
                }
            )
            if p["item_type"] == "minuman":
                total_drink += pred_qty
            else:
                total_food += pred_qty

        return {
            "status": "ok",
            "seller_id": int(seller_id),
            "stall_name": seller_menus[0]["stall_name"],
            "target_date": dt.isoformat(),
            "day_name": day_name,
            "summary": {
                "total_predicted_food_portions": total_food,
                "total_predicted_drink_cups": total_drink,
                "total_menus": len(forecasts),
            },
            "forecasts": forecasts,
        }
