"""
Training & Evaluation Pipeline — AI Demand Forecasting NesaCourt.

Melatih dan membandingkan 4 pendekatan model peramalan permintaan (harian & per jam)
menggunakan chronological train/test split pada ai_dataset/*.csv (90 hari kalender /
77 hari operasional) tanpa kebocoran data masa depan (no data leakage).

Output:
- ai_service/artifacts/daily_forecast_model.joblib
- ai_service/artifacts/hourly_forecast_model.joblib
- ai_service/artifacts/evaluation_report.json
- ai_service/artifacts/evaluation_summary.md
"""

from __future__ import annotations

import json
from datetime import datetime
from pathlib import Path
from typing import Any, Dict, List, Tuple

import joblib
import numpy as np
import pandas as pd
from sklearn.compose import ColumnTransformer
from sklearn.ensemble import HistGradientBoostingRegressor, RandomForestRegressor
from sklearn.linear_model import Ridge
from sklearn.metrics import mean_absolute_error, mean_squared_error, r2_score
from sklearn.pipeline import Pipeline
from sklearn.preprocessing import OneHotEncoder, StandardScaler


BASE_DIR = Path(__file__).resolve().parent
DATASET_DIR = BASE_DIR.parent / "ai_dataset"
ARTIFACTS_DIR = BASE_DIR / "artifacts"


def compute_metrics(y_true: np.ndarray, y_pred: np.ndarray) -> Dict[str, float]:
    """Menghitung metrik evaluasi jujur: MAE, RMSE, MAPE, WAPE, dan R2."""
    y_true = np.asarray(y_true, dtype=float)
    y_pred = np.clip(np.asarray(y_pred, dtype=float), 0.0, None)

    mae = float(mean_absolute_error(y_true, y_pred))
    rmse = float(np.sqrt(mean_squared_error(y_true, y_pred)))
    r2 = float(r2_score(y_true, y_pred)) if len(y_true) > 1 else 0.0

    # MAPE hanya pada titik actual > 0 agar tidak terjadi pembagian nol
    non_zero = y_true > 0
    mape = (
        float(np.mean(np.abs((y_true[non_zero] - y_pred[non_zero]) / y_true[non_zero])) * 100.0)
        if np.any(non_zero)
        else 0.0
    )
    # WAPE (Weighted Absolute Percentage Error) lebih stabil untuk volume kecil
    total_actual = float(np.sum(np.abs(y_true)))
    wape = float((np.sum(np.abs(y_true - y_pred)) / total_actual) * 100.0) if total_actual > 0 else 0.0

    return {
        "mae": round(mae, 3),
        "rmse": round(rmse, 3),
        "mape_pct": round(mape, 2),
        "wape_pct": round(wape, 2),
        "r2": round(r2, 4),
    }


def verify_raw_cancelled_rule() -> Dict[str, Any]:
    """Memverifikasi aturan Konsep_AI_NESACOURT.pdf: transaksi batal tidak dihitung sebagai penjualan selesai."""
    raw_path = DATASET_DIR / "transactions_raw.csv"
    daily_path = DATASET_DIR / "sales_daily_per_menu.csv"

    raw_df = pd.read_csv(raw_path, usecols=["order_status", "quantity"])
    daily_df = pd.read_csv(daily_path, usecols=["completed_qty_sold", "cancelled_qty"])

    raw_completed = int(raw_df.loc[raw_df["order_status"] == "selesai", "quantity"].sum())
    raw_cancelled = int(raw_df.loc[raw_df["order_status"] == "dibatalkan", "quantity"].sum())
    daily_completed = int(daily_df["completed_qty_sold"].sum())
    daily_cancelled = int(daily_df["cancelled_qty"].sum())

    return {
        "raw_completed_qty": raw_completed,
        "daily_completed_qty": daily_completed,
        "raw_cancelled_qty": raw_cancelled,
        "daily_cancelled_qty": daily_cancelled,
        "cancelled_excluded_correctly": (raw_completed == daily_completed) and (raw_cancelled == daily_cancelled),
    }


def build_daily_features(daily_df: pd.DataFrame, menus_df: pd.DataFrame) -> pd.DataFrame:
    """Menyusun fitur time-series (lag, rolling 7 hari, rata-rata hari-yang-sama) per menu."""
    df = daily_df.copy()
    df["date_dt"] = pd.to_datetime(df["date"])
    df = df.merge(
        menus_df[["menu_id", "journal_popularity_weight", "estimated_time_minutes"]],
        on="menu_id",
        how="left",
    )
    df = df.sort_values(["menu_id", "date_dt"]).reset_index(drop=True)

    df["is_rainy"] = (df["weather"] == "hujan").astype(int)
    df["item_type_num"] = (df["item_type"] == "minuman").astype(int)

    # Fitur lag & rolling strictly memakai shift(1) agar bebas kebocoran data masa depan
    grp = df.groupby("menu_id")["completed_qty_sold"]
    df["lag_1"] = grp.shift(1)
    df["rolling_mean_7"] = grp.transform(lambda s: s.shift(1).rolling(7, min_periods=1).mean())
    df["rolling_std_7"] = grp.transform(lambda s: s.shift(1).rolling(7, min_periods=2).std()).fillna(0.0)

    # Rata-rata penjualan pada hari-dalam-minggu yang sama (4 kejadian terakhir) — Kasus B!
    df["same_dow_mean_4"] = df.groupby(["menu_id", "day_of_week"])["completed_qty_sold"].transform(
        lambda s: s.shift(1).rolling(4, min_periods=1).mean()
    )

    # Isi missing awal (hari pertama tiap menu/hari) dengan rolling_mean_7 lalu fallback proporsional stok
    fallback = (df["daily_stock_quota"] * 0.78).round(1)
    df["lag_1"] = df["lag_1"].fillna(df["rolling_mean_7"]).fillna(fallback)
    df["rolling_mean_7"] = df["rolling_mean_7"].fillna(fallback)
    df["same_dow_mean_4"] = df["same_dow_mean_4"].fillna(df["rolling_mean_7"])

    return df


def train_and_evaluate_daily(daily_df: pd.DataFrame, menus_df: pd.DataFrame) -> Tuple[Dict[str, Any], Dict[str, Any]]:
    df = build_daily_features(daily_df, menus_df)

    # Chronological split: 80% tanggal awal = train, 20% tanggal akhir = test
    unique_dates = sorted(df["date_dt"].unique())
    split_idx = int(len(unique_dates) * 0.80)
    train_dates = set(unique_dates[:split_idx])
    test_dates = set(unique_dates[split_idx:])

    # Buang 7 hari operasional pertama dari train agar fitur lag/rolling matang
    warmup_dates = set(unique_dates[:7])
    train_df = df[df["date_dt"].isin(train_dates - warmup_dates)].copy()
    test_df = df[df["date_dt"].isin(test_dates)].copy()

    cat_features = ["seller_id", "menu_id", "day_of_week"]
    num_features = [
        "item_type_num",
        "price_idr",
        "daily_stock_quota",
        "journal_popularity_weight",
        "is_weekend",
        "is_end_of_month",
        "is_rainy",
        "has_campus_event",
        "lag_1",
        "rolling_mean_7",
        "rolling_std_7",
        "same_dow_mean_4",
    ]
    feature_cols = cat_features + num_features

    X_train, y_train = train_df[feature_cols], train_df["completed_qty_sold"].values
    X_test, y_test = test_df[feature_cols], test_df["completed_qty_sold"].values

    results: Dict[str, Dict[str, float]] = {}
    predictions: Dict[str, np.ndarray] = {}
    fitted_models: Dict[str, Any] = {}

    # 1. Baseline: Moving Average 7 Hari (MA-7)
    pred_ma7 = test_df["rolling_mean_7"].values
    results["baseline_ma7"] = compute_metrics(y_test, pred_ma7)
    predictions["baseline_ma7"] = pred_ma7

    # 2. Ridge Regression
    pre_linear = ColumnTransformer(
        [
            ("cat", OneHotEncoder(handle_unknown="ignore"), cat_features),
            ("num", StandardScaler(), num_features),
        ]
    )
    ridge = Pipeline([("pre", pre_linear), ("model", Ridge(alpha=5.0))])
    ridge.fit(X_train, y_train)
    pred_ridge = np.clip(ridge.predict(X_test), 0, test_df["daily_stock_quota"].values)
    results["ridge_regression"] = compute_metrics(y_test, pred_ridge)
    predictions["ridge_regression"] = pred_ridge
    fitted_models["ridge_regression"] = ridge

    # 3. Random Forest Regressor
    pre_tree = ColumnTransformer(
        [
            ("cat", OneHotEncoder(handle_unknown="ignore", sparse_output=False), cat_features),
            ("num", "passthrough", num_features),
        ]
    )
    rf = Pipeline(
        [
            ("pre", pre_tree),
            ("model", RandomForestRegressor(n_estimators=120, max_depth=12, min_samples_leaf=3, random_state=42, n_jobs=1)),
        ]
    )
    rf.fit(X_train, y_train)
    pred_rf = np.clip(rf.predict(X_test), 0, test_df["daily_stock_quota"].values)
    results["random_forest"] = compute_metrics(y_test, pred_rf)
    predictions["random_forest"] = pred_rf
    fitted_models["random_forest"] = rf

    # 4. HistGradientBoosting Regressor
    hgb = Pipeline(
        [
            ("pre", pre_tree),
            (
                "model",
                HistGradientBoostingRegressor(
                    max_iter=220,
                    max_depth=6,
                    learning_rate=0.05,
                    min_samples_leaf=10,
                    random_state=42,
                ),
            ),
        ]
    )
    hgb.fit(X_train, y_train)
    pred_hgb = np.clip(hgb.predict(X_test), 0, test_df["daily_stock_quota"].values)
    results["hist_gradient_boosting"] = compute_metrics(y_test, pred_hgb)
    predictions["hist_gradient_boosting"] = pred_hgb
    fitted_models["hist_gradient_boosting"] = hgb

    # Pilih model terbaik (hanya dari model ML terlatih) berdasarkan Test MAE terkecil
    best_name = min(fitted_models.keys(), key=lambda k: results[k]["mae"])
    best_model = fitted_models[best_name]
    best_pred = predictions[best_name]

    # Breakdown evaluasi per rezim hari & per tenant
    test_eval = test_df.copy()
    test_eval["y_pred"] = best_pred

    per_regime: Dict[str, Dict[str, Any]] = {}
    for regime, g in test_eval.groupby("demand_regime"):
        m = compute_metrics(g["completed_qty_sold"].values, g["y_pred"].values)
        m["samples"] = int(len(g))
        per_regime[str(regime)] = m

    per_tenant: Dict[str, Dict[str, Any]] = {}
    for stall_name, g in test_eval.groupby("stall_name"):
        m = compute_metrics(g["completed_qty_sold"].values, g["y_pred"].values)
        m["samples"] = int(len(g))
        per_tenant[str(stall_name)] = m

    # Latih ulang model terbaik pada SELURUH data (setelah warmup) untuk dipakai produksi/API
    full_train_df = df[~df["date_dt"].isin(warmup_dates)].copy()
    best_model.fit(full_train_df[feature_cols], full_train_df["completed_qty_sold"].values)

    # Siapkan snapshot riwayat terakhir per menu & benchmark kategori (untuk Kasus D: menu baru)
    menu_profiles: Dict[int, Dict[str, Any]] = {}
    for menu_id, g in df.groupby("menu_id"):
        g_sorted = g.sort_values("date_dt")
        last7 = g_sorted.tail(7)["completed_qty_sold"].values
        dow_means = g_sorted.groupby("day_of_week")["completed_qty_sold"].mean().round(2).to_dict()
        last_row = g_sorted.iloc[-1]
        menu_profiles[int(menu_id)] = {
            "menu_id": int(menu_id),
            "seller_id": int(last_row["seller_id"]),
            "stall_name": str(last_row["stall_name"]),
            "menu_name": str(last_row["menu_name"]),
            "item_type": str(last_row["item_type"]),
            "price_idr": int(last_row["price_idr"]),
            "daily_stock_quota": int(last_row["daily_stock_quota"]),
            "journal_popularity_weight": float(last_row["journal_popularity_weight"]),
            "history_days_count": int(len(g_sorted)),
            "last_sold": float(last_row["completed_qty_sold"]),
            "rolling_mean_7": float(np.mean(last7)),
            "rolling_std_7": float(np.std(last7, ddof=1)) if len(last7) > 1 else 1.5,
            "dow_means": {int(k): float(v) for k, v in dow_means.items()},
        }

    category_benchmarks = (
        df.groupby(["item_type", "day_of_week"])["completed_qty_sold"].mean().round(2).unstack().to_dict(orient="index")
    )

    artifact = {
        "model_name": best_name,
        "model_version": f"{best_name}-daily-v1",
        "trained_at": datetime.now().isoformat(timespec="seconds"),
        "feature_cols": feature_cols,
        "pipeline": best_model,
        "test_mae": results[best_name]["mae"],
        "test_mape_pct": results[best_name]["mape_pct"],
        "menu_profiles": menu_profiles,
        "category_benchmarks": {
            str(k): {int(d): float(v) for d, v in vals.items()} for k, vals in category_benchmarks.items()
        },
    }

    eval_meta = {
        "split": {
            "total_operating_days": int(len(unique_dates)),
            "train_operating_days": int(len(train_dates - warmup_dates)),
            "test_operating_days": int(len(test_dates)),
            "test_date_range": [
                str(min(test_dates).date()),
                str(max(test_dates).date()),
            ],
            "train_rows": int(len(train_df)),
            "test_rows": int(len(test_df)),
        },
        "selected_model": best_name,
        "model_comparison": results,
        "improvement_vs_ma7_mae_pct": round(
            ((results["baseline_ma7"]["mae"] - results[best_name]["mae"]) / max(0.001, results["baseline_ma7"]["mae"]))
            * 100.0,
            2,
        ),
        "per_regime_test": per_regime,
        "per_tenant_test": per_tenant,
    }

    return artifact, eval_meta


def train_and_evaluate_hourly(
    hourly_df: pd.DataFrame, daily_df: pd.DataFrame, menus_df: pd.DataFrame
) -> Tuple[Dict[str, Any], Dict[str, Any]]:
    """Melatih model peramalan per jam (08:00-16:00) dengan grid penuh agar jam sepi (0 porsi) ikut terpelajari."""
    # Bangun grid penuh (tanggal x jam 8..16 x menu) dari daily_df
    dates_meta = daily_df[
        ["date", "day_of_week", "is_weekend", "is_end_of_month", "weather", "has_campus_event", "demand_regime"]
    ].drop_duplicates()

    hours = pd.DataFrame({"hour": list(range(8, 17))})
    grid = dates_meta.merge(hours, how="cross").merge(
        menus_df[["menu_id", "seller_id", "stall_name", "menu_name", "item_type", "daily_stock_quota", "journal_popularity_weight"]],
        how="cross",
    )

    h_agg = hourly_df[["date", "hour", "menu_id", "completed_qty_sold"]].copy()
    grid = grid.merge(h_agg, on=["date", "hour", "menu_id"], how="left")
    grid["completed_qty_sold"] = grid["completed_qty_sold"].fillna(0).astype(int)
    grid["date_dt"] = pd.to_datetime(grid["date"])
    grid["is_rainy"] = (grid["weather"] == "hujan").astype(int)
    grid["item_type_num"] = (grid["item_type"] == "minuman").astype(int)
    grid["is_peak_lunch"] = grid["hour"].isin([11, 12, 13]).astype(int)

    # Tambahkan rolling harian 7 hari sebelumnya per menu
    daily_sorted = daily_df.sort_values(["menu_id", "date"]).copy()
    daily_sorted["rolling_daily_mean_7"] = daily_sorted.groupby("menu_id")["completed_qty_sold"].transform(
        lambda s: s.shift(1).rolling(7, min_periods=1).mean()
    )
    daily_sorted["rolling_daily_mean_7"] = daily_sorted["rolling_daily_mean_7"].fillna(
        daily_sorted["daily_stock_quota"] * 0.78
    )
    grid = grid.merge(daily_sorted[["date", "menu_id", "rolling_daily_mean_7"]], on=["date", "menu_id"], how="left")

    unique_dates = sorted(grid["date_dt"].unique())
    split_idx = int(len(unique_dates) * 0.80)
    train_dates = set(unique_dates[7:split_idx])
    test_dates = set(unique_dates[split_idx:])

    train_df = grid[grid["date_dt"].isin(train_dates)].copy()
    test_df = grid[grid["date_dt"].isin(test_dates)].copy()

    cat_features = ["seller_id", "menu_id", "hour", "day_of_week"]
    num_features = [
        "item_type_num",
        "daily_stock_quota",
        "journal_popularity_weight",
        "is_peak_lunch",
        "is_weekend",
        "is_end_of_month",
        "is_rainy",
        "has_campus_event",
        "rolling_daily_mean_7",
    ]
    feature_cols = cat_features + num_features

    pre_tree = ColumnTransformer(
        [
            ("cat", OneHotEncoder(handle_unknown="ignore", sparse_output=False), cat_features),
            ("num", "passthrough", num_features),
        ]
    )

    hgb_hourly = Pipeline(
        [
            ("pre", pre_tree),
            (
                "model",
                HistGradientBoostingRegressor(
                    max_iter=200,
                    max_depth=6,
                    learning_rate=0.06,
                    min_samples_leaf=20,
                    random_state=42,
                ),
            ),
        ]
    )
    hgb_hourly.fit(train_df[feature_cols], train_df["completed_qty_sold"].values)
    pred_test = np.clip(hgb_hourly.predict(test_df[feature_cols]), 0, None)
    hourly_metrics = compute_metrics(test_df["completed_qty_sold"].values, pred_test)

    # Proporsi historis per jam untuk fallback / sanity check
    hour_shares = (
        grid.groupby("hour")["completed_qty_sold"].sum() / max(1, grid["completed_qty_sold"].sum())
    ).round(4).to_dict()

    # Refit pada seluruh data setelah warmup
    full_train = grid[grid["date_dt"].isin(set(unique_dates[7:]))]
    hgb_hourly.fit(full_train[feature_cols], full_train["completed_qty_sold"].values)

    artifact = {
        "model_name": "hist_gradient_boosting",
        "model_version": "hgb-hourly-v1",
        "trained_at": datetime.now().isoformat(timespec="seconds"),
        "feature_cols": feature_cols,
        "pipeline": hgb_hourly,
        "test_mae": hourly_metrics["mae"],
        "hour_shares": {int(h): float(s) for h, s in hour_shares.items()},
    }

    eval_meta = {
        "selected_model": "hist_gradient_boosting",
        "train_rows": int(len(train_df)),
        "test_rows": int(len(test_df)),
        "metrics": hourly_metrics,
        "peak_hour_distribution": {f"{int(h):02d}:00": round(float(s) * 100, 1) for h, s in hour_shares.items()},
    }

    return artifact, eval_meta


def write_markdown_summary(report: Dict[str, Any], out_path: Path) -> None:
    daily = report["daily_forecast"]
    hourly = report["hourly_forecast"]
    verify = report["data_integrity"]

    lines: List[str] = [
        "# Laporan Evaluasi Model AI Demand Forecasting — NesaCourt",
        "",
        f"- **Tanggal Training**: `{report['generated_at']}`",
        f"- **Dataset**: `ai_dataset/` (90 hari kalender / {daily['split']['total_operating_days']} hari operasional, 5 tenant, 50 menu)",
        f"- **Validasi Aturan Data**: Transaksi batal tidak dihitung sebagai penjualan selesai = **{'LULUS' if verify['cancelled_excluded_correctly'] else 'GAGAL'}** "
        f"({verify['raw_completed_qty']:,} porsi selesai vs {verify['raw_cancelled_qty']:,} porsi batal dipisahkan).",
        "",
        "---",
        "",
        "## 1. Perbandingan Model Forecasting Harian (`period_type = daily`)",
        f"Chronological split: **{daily['split']['train_operating_days']} hari train** ({daily['split']['train_rows']:,} baris) → "
        f"**{daily['split']['test_operating_days']} hari test** ({daily['split']['test_date_range'][0]} s/d {daily['split']['test_date_range'][1]}, {daily['split']['test_rows']:,} baris).",
        "",
        "| Model | Test MAE (porsi) | Test RMSE | Test MAPE (%) | Test WAPE (%) | R² |",
        "| :--- | ---: | ---: | ---: | ---: | ---: |",
    ]

    for name, m in daily["model_comparison"].items():
        badge = " **(Terpilih)**" if name == daily["selected_model"] else ""
        lines.append(
            f"| `{name}`{badge} | {m['mae']} | {m['rmse']} | {m['mape_pct']}% | {m['wape_pct']}% | {m['r2']} |"
        )

    lines.extend(
        [
            "",
            f"> Model terpilih **`{daily['selected_model']}`** menurunkan MAE sebesar **{daily['improvement_vs_ma7_mae_pct']}%** dibandingkan baseline Moving Average 7 Hari (`baseline_ma7`).",
            "",
            "---",
            "",
            "## 2. Evaluasi Harian per Kondisi Hari (`demand_regime`)",
            "| Kondisi Hari | Jumlah Sampel Uji | MAE (porsi) | MAPE (%) | WAPE (%) |",
            "| :--- | ---: | ---: | ---: | ---: |",
        ]
    )
    for reg, m in daily["per_regime_test"].items():
        lines.append(f"| `{reg}` | {m['samples']} | {m['mae']} | {m['mape_pct']}% | {m['wape_pct']}% |")

    lines.extend(
        [
            "",
            "## 3. Evaluasi Harian per Tenant",
            "| Tenant | Sampel Uji | MAE (porsi) | MAPE (%) | R² |",
            "| :--- | ---: | ---: | ---: | ---: |",
        ]
    )
    for tenant, m in daily["per_tenant_test"].items():
        lines.append(f"| {tenant} | {m['samples']} | {m['mae']} | {m['mape_pct']}% | {m['r2']} |")

    hm = hourly["metrics"]
    lines.extend(
        [
            "",
            "---",
            "",
            "## 4. Evaluasi Model Forecasting Per Jam (`period_type = hourly`, 08:00–16:00)",
            f"- **Model**: `{hourly['selected_model']}` ({hourly['train_rows']:,} baris train, {hourly['test_rows']:,} baris test)",
            f"- **Test MAE**: **{hm['mae']} porsi/jam** | **RMSE**: {hm['rmse']} | **WAPE**: {hm['wape_pct']}% | **R²**: {hm['r2']}",
            "",
            "Distribusi jam operasional dari data 90 hari:",
            "",
            "| Jam | Proporsi Penjualan |",
            "| :---: | ---: |",
        ]
    )
    for hr, pct in hourly["peak_hour_distribution"].items():
        lines.append(f"| {hr} | {pct}% |")

    out_path.write_text("\n".join(lines) + "\n", encoding="utf-8")


def main() -> None:
    ARTIFACTS_DIR.mkdir(parents=True, exist_ok=True)

    menus_df = pd.read_csv(DATASET_DIR / "menus_master.csv")
    daily_df = pd.read_csv(DATASET_DIR / "sales_daily_per_menu.csv")
    hourly_df = pd.read_csv(DATASET_DIR / "sales_hourly_per_menu.csv")

    integrity = verify_raw_cancelled_rule()
    daily_artifact, daily_eval = train_and_evaluate_daily(daily_df, menus_df)
    hourly_artifact, hourly_eval = train_and_evaluate_hourly(hourly_df, daily_df, menus_df)

    joblib.dump(daily_artifact, ARTIFACTS_DIR / "daily_forecast_model.joblib")
    joblib.dump(hourly_artifact, ARTIFACTS_DIR / "hourly_forecast_model.joblib")

    report = {
        "generated_at": datetime.now().isoformat(timespec="seconds"),
        "data_integrity": integrity,
        "daily_forecast": daily_eval,
        "hourly_forecast": hourly_eval,
    }
    (ARTIFACTS_DIR / "evaluation_report.json").write_text(
        json.dumps(report, indent=2, ensure_ascii=False), encoding="utf-8"
    )
    write_markdown_summary(report, ARTIFACTS_DIR / "evaluation_summary.md")

    print("=== TRAINING & EVALUATION COMPLETE ===")
    print(f"Best Daily Model : {daily_eval['selected_model']}")
    for k, m in daily_eval["model_comparison"].items():
        print(f"  - {k:24s} | MAE={m['mae']:.3f} | RMSE={m['rmse']:.3f} | MAPE={m['mape_pct']:.2f}% | WAPE={m['wape_pct']:.2f}% | R2={m['r2']:.4f}")
    hm = hourly_eval["metrics"]
    print(f"Hourly Model     : {hourly_eval['selected_model']} | MAE={hm['mae']:.3f} | RMSE={hm['rmse']:.3f} | WAPE={hm['wape_pct']:.2f}% | R2={hm['r2']:.4f}")
    print(f"Cancelled Check  : {'PASS' if integrity['cancelled_excluded_correctly'] else 'FAIL'}")


if __name__ == "__main__":
    main()
