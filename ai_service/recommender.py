"""
Smart Menu Recommendation Engine — NesaCourt AI Service.

Mengimplementasikan sistem pencocokan multivariabel berbobot sesuai
Panduan Konsep AI NesaCourt (Bagian 3) serta bobot preferensi & gizi
dari jurnal Ramadhan & Ruhana (2026) — Foodcourt Baseball UNESA Surabaya.
"""

from __future__ import annotations

from pathlib import Path
from typing import Any, Dict, List, Optional
import csv


BASE_DIR = Path(__file__).resolve().parent
DATASET_DIR = BASE_DIR.parent / "ai_dataset"
MENUS_CSV = DATASET_DIR / "menus_master.csv"

VALID_TASTES = {"pedas", "gurih", "manis", "berkuah", "segar"}


def load_master_menus(csv_path: Path = MENUS_CSV) -> List[Dict[str, Any]]:
    """Memuat katalog 50 menu riil dari ai_dataset/menus_master.csv."""
    menus: List[Dict[str, Any]] = []
    if not csv_path.exists():
        return menus

    with csv_path.open("r", encoding="utf-8", newline="") as fp:
        reader = csv.DictReader(fp)
        for row in reader:
            tags = [t.strip() for t in row.get("taste_tags", "").split("|") if t.strip()]
            menus.append(
                {
                    "menu_id": int(row["menu_id"]),
                    "seller_id": int(row["seller_id"]),
                    "stall_name": row["stall_name"],
                    "category_slug": row["category_slug"],
                    "item_type": row["item_type"],
                    "menu_name": row["menu_name"],
                    "price_idr": int(float(row["price_idr"])),
                    "estimated_time_minutes": int(float(row["estimated_time_minutes"])),
                    "stock": int(float(row["daily_stock_quota"])),
                    "is_available": True,
                    "is_active": True,
                    "taste_tags": tags,
                    "journal_popularity_weight": float(row.get("journal_popularity_weight") or 12.0),
                    "energy_kcal": float(row["energy_kcal"]) if row.get("energy_kcal") else None,
                    "protein_g": float(row["protein_g"]) if row.get("protein_g") else None,
                    "fat_g": float(row["fat_g"]) if row.get("fat_g") else None,
                    "carbs_g": float(row["carbs_g"]) if row.get("carbs_g") else None,
                }
            )
    return menus


def _normalize_tags(raw_tags: Any) -> List[str]:
    if isinstance(raw_tags, list):
        return [str(t).strip().lower() for t in raw_tags if str(t).strip()]
    if isinstance(raw_tags, str):
        sep = "|" if "|" in raw_tags else ","
        return [t.strip().lower() for t in raw_tags.split(sep) if t.strip()]
    return []


def recommend_menus(
    budget: int,
    taste_preference: str = "semua",
    max_wait_minutes: int = 10,
    item_type: Optional[str] = None,
    strict_wait_time: bool = True,
    seller_queues: Optional[Dict[int, int]] = None,
    menus: Optional[List[Dict[str, Any]]] = None,
    desired_keyword: Optional[str] = None,
    limit: int = 5,
) -> Dict[str, Any]:
    """
    Memberikan rekomendasi menu teratas dengan skor kecocokan berbobot (0-100).

    Aturan wajib (sesuai Konsep_AI_NESACOURT.pdf Bagian 3):
    - Menu tidak aktif atau stok habis (stock <= 0 / is_available = False) TIDAK PERNAH direkomendasikan.
    - Menu dengan harga > budget tidak direkomendasikan sebagai hasil utama.
    - Jika tidak ada menu yang cocok, sistem tidak mengarang hasil dan memberi pesan saran yang jelas.
    """
    catalog = menus if menus is not None else load_master_menus()
    queues = seller_queues or {}
    pref = (taste_preference or "semua").strip().lower()
    keyword = (desired_keyword or "").strip().lower()

    unavailable_notice: Optional[str] = None
    if keyword:
        keyword_matches = [m for m in catalog if keyword in str(m.get("menu_name", "")).lower()]
        if keyword_matches and all(
            (not m.get("is_active", True))
            or (not m.get("is_available", True))
            or int(m.get("stock", 0)) <= 0
            for m in keyword_matches
        ):
            unavailable_notice = (
                f"Menu yang dicari ('{desired_keyword}') saat ini sedang habis atau nonaktif. "
                "Berikut alternatif menu tersedia yang paling cocok dengan kriteria Anda."
            )

    # 1. Filter wajib: aktif & stok tersedia (Kasus C)
    active_menus = [
        m
        for m in catalog
        if m.get("is_active", True)
        and m.get("is_available", True)
        and int(m.get("stock", 0)) > 0
    ]

    if item_type and item_type.lower() in {"makanan", "minuman"}:
        active_menus = [m for m in active_menus if str(m.get("item_type", "")).lower() == item_type.lower()]

    if not active_menus:
        return {
            "status": "no_match",
            "message": "Saat ini belum ada menu yang tersedia/aktif di foodcourt.",
            "unavailable_notice": unavailable_notice,
            "recommendations": [],
            "total_candidates_evaluated": 0,
            "suggested_min_budget": None,
        }

    # 2. Filter wajib: harga <= budget (Kasus B & E)
    within_budget = [m for m in active_menus if int(m.get("price_idr", 0)) <= budget]
    if not within_budget:
        min_price = min(int(m.get("price_idr", 0)) for m in active_menus)
        return {
            "status": "no_match",
            "message": (
                f"Belum ada menu tersedia dengan budget Rp{budget:,}. "
                f"Menu termurah yang tersedia saat ini mulai dari Rp{min_price:,}. "
                "Silakan naikkan budget atau ubah kategori pencarian."
            ).replace(",", "."),
            "unavailable_notice": unavailable_notice,
            "recommendations": [],
            "total_candidates_evaluated": len(active_menus),
            "suggested_min_budget": min_price,
        }

    # 3. Filter / penalti waktu tunggu + antrean (Kasus D)
    scored: List[Dict[str, Any]] = []
    excluded_by_wait = 0

    max_pop = max((float(m.get("journal_popularity_weight", 12.0)) for m in within_budget), default=35.0)
    max_pop = max(max_pop, 1.0)

    for m in within_budget:
        seller_id = int(m.get("seller_id", 0))
        prep_time = int(m.get("estimated_time_minutes", 5))
        queue_count = int(queues.get(seller_id, queues.get(str(seller_id), 0)))  # type: ignore[arg-type]
        queue_penalty = int(round(queue_count * 1.5))
        effective_wait = prep_time + queue_penalty

        if strict_wait_time and effective_wait > max_wait_minutes:
            excluded_by_wait += 1
            continue

        tags = _normalize_tags(m.get("taste_tags", []))
        price = int(m.get("price_idr", 0))
        stock = int(m.get("stock", 0))
        pop_weight = float(m.get("journal_popularity_weight", 12.0))

        # A. Skor Rasa (35 poin)
        if pref in {"", "semua", "all", "bebas"}:
            taste_score = 30.0
            taste_matched = True
        elif pref in tags:
            taste_score = 35.0
            taste_matched = True
        elif any(pref in t for t in tags):
            taste_score = 26.0
            taste_matched = True
        else:
            taste_score = 8.0
            taste_matched = False

        # B. Skor Efisiensi Budget (25 poin) — menghargai harga pas & hemat di bawah budget
        if budget > 0:
            price_ratio = price / budget
            # Optimal di 0.65 - 1.0 (cukup mengenyangkan & pas kantong); lebih murah tetap tinggi
            budget_score = 25.0 * (1.0 - 0.35 * abs(0.85 - min(price_ratio, 1.0)))
            budget_score = max(12.0, min(25.0, budget_score))
        else:
            budget_score = 15.0

        # C. Skor Kecepatan Penyajian + Antrean (20 poin)
        if effective_wait <= max_wait_minutes:
            wait_ratio = effective_wait / max(1, max_wait_minutes)
            speed_score = 20.0 * (1.0 - 0.45 * wait_ratio)
        else:
            # Jika strict_wait_time=False, menu yang melewati batas waktu diberi penalti tajam
            over = effective_wait - max_wait_minutes
            speed_score = max(0.0, 6.0 - over * 1.2)

        # D. Skor Popularitas Jurnal UNESA (15 poin)
        popularity_score = 15.0 * min(1.0, pop_weight / max_pop)

        # E. Skor Keamanan Stok (5 poin)
        stock_score = 5.0 if stock >= 10 else (3.5 if stock >= 5 else 2.0)

        total_score = round(taste_score + budget_score + speed_score + popularity_score + stock_score, 1)

        # Susun alasan rekomendasi singkat & informatif
        reasons: List[str] = []
        price_fmt = f"Rp{price:,}".replace(",", ".")
        reasons.append(f"Sesuai budget ({price_fmt})")
        if pref not in {"", "semua", "all", "bebas"} and taste_matched:
            reasons.append(f"Cocok dengan selera {pref}")
        if queue_count > 0:
            reasons.append(f"Estimasi saji ±{effective_wait} menit ({prep_time} mnt masak + {queue_count} antrean)")
        else:
            reasons.append(f"Cepat disajikan (±{prep_time} menit, antrean rendah)")
        if pop_weight >= 24.0:
            reasons.append("Menu terfavorit mahasiswa UNESA (survei Foodcourt Baseball)")
        if m.get("energy_kcal") is not None and float(m["energy_kcal"]) > 0:
            reasons.append(f"±{m['energy_kcal']} kkal, protein {m.get('protein_g', 0)}g")

        scored.append(
            {
                "menu_id": int(m["menu_id"]),
                "seller_id": seller_id,
                "stall_name": m.get("stall_name", f"Stan {seller_id}"),
                "menu_name": m["menu_name"],
                "item_type": m.get("item_type", "makanan"),
                "price_idr": price,
                "estimated_time_minutes": prep_time,
                "active_queue_count": queue_count,
                "effective_wait_minutes": effective_wait,
                "stock": stock,
                "taste_tags": m.get("taste_tags", []),
                "taste_matched": taste_matched,
                "match_score": total_score,
                "score_breakdown": {
                    "taste": round(taste_score, 1),
                    "budget": round(budget_score, 1),
                    "speed_and_queue": round(speed_score, 1),
                    "popularity": round(popularity_score, 1),
                    "stock": round(stock_score, 1),
                },
                "reasons": reasons,
                "summary_reason": " • ".join(reasons[:3]),
            }
        )

    if not scored:
        min_wait = min(
            int(m.get("estimated_time_minutes", 5))
            + int(round(int(queues.get(int(m.get("seller_id", 0)), 0)) * 1.5))
            for m in within_budget
        )
        return {
            "status": "no_match",
            "message": (
                f"Menu sesuai budget tersedia, tetapi semua estimasi tunggu melebihi batas {max_wait_minutes} menit "
                f"(tercepat saat ini ±{min_wait} menit). Coba naikkan batas waktu tunggu."
            ),
            "unavailable_notice": unavailable_notice,
            "recommendations": [],
            "total_candidates_evaluated": len(within_budget),
            "suggested_min_wait_minutes": min_wait,
        }

    # Urutkan berdasarkan match_score tertinggi, lalu waktu tunggu tercepat, lalu harga termurah
    scored.sort(key=lambda x: (-x["match_score"], x["effective_wait_minutes"], x["price_idr"]))
    top_results = scored[: max(1, limit)]

    has_exact_taste = any(item["taste_matched"] for item in top_results)
    msg = (
        f"Ditemukan {len(scored)} menu cocok; menampilkan {len(top_results)} rekomendasi teratas."
        if has_exact_taste
        else (
            f"Tidak ada menu dengan rasa '{taste_preference}' yang pas semua syarat; "
            f"menampilkan {len(top_results)} alternatif terbaik sesuai budget dan waktu."
        )
    )

    return {
        "status": "ok",
        "message": msg,
        "unavailable_notice": unavailable_notice,
        "criteria": {
            "budget": budget,
            "taste_preference": pref,
            "max_wait_minutes": max_wait_minutes,
            "strict_wait_time": strict_wait_time,
            "item_type": item_type,
        },
        "total_candidates_evaluated": len(active_menus),
        "excluded_by_wait_time": excluded_by_wait,
        "recommendations": top_results,
    }
