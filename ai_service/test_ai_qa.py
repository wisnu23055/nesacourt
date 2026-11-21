"""
Automated QA Test Suite — NesaCourt AI Service.

Menguji seluruh skenario wajib dari Panduan Konsep AI NesaCourt:
- Bagian 3 (Smart Menu Recommendation): Kasus A, B, C, D, E
- Bagian 4 (AI Demand Forecasting): Kasus A, B, C, D, E + aturan transaksi batal
- Endpoint FastAPI (/health, /api/ai/recommend, /api/ai/forecast/*, /api/ai/evaluation)
"""

from fastapi.testclient import TestClient

from app import app
from forecaster import DemandForecaster
from recommender import recommend_menus


client = TestClient(app)


# =========================================================
# BAGIAN 3: SMART MENU RECOMMENDATION (KASUS A - E)
# =========================================================

def test_rec_case_a_matches_all_criteria():
    """Kasus A: budget cukup, suka pedas, waktu cukup -> Ayam Geprek di urutan teratas."""
    res = recommend_menus(budget=15000, taste_preference="pedas", max_wait_minutes=8, item_type="makanan")
    assert res["status"] == "ok"
    assert len(res["recommendations"]) > 0
    top = res["recommendations"][0]
    assert top["price_idr"] <= 15000
    assert top["effective_wait_minutes"] <= 8
    assert top["taste_matched"] is True
    assert "geprek" in top["menu_name"].lower()


def test_rec_case_b_excludes_over_budget():
    """Kasus B: menu harga > budget tidak boleh muncul di rekomendasi."""
    res = recommend_menus(budget=12000, taste_preference="gurih", max_wait_minutes=10, item_type="makanan")
    assert res["status"] == "ok"
    assert all(r["price_idr"] <= 12000 for r in res["recommendations"])


def test_rec_case_c_never_recommends_out_of_stock_or_inactive():
    """Kasus C: menu stok habis / nonaktif wajib dikeluarkan dan memberi pesan alternatif."""
    sample_menus = [
        {
            "menu_id": 101,
            "seller_id": 1,
            "stall_name": "Stan 01",
            "menu_name": "Mie Pedas Habis",
            "item_type": "makanan",
            "price_idr": 12000,
            "estimated_time_minutes": 4,
            "stock": 0,
            "is_available": False,
            "is_active": True,
            "taste_tags": ["Pedas", "Gurih"],
        },
        {
            "menu_id": 102,
            "seller_id": 1,
            "stall_name": "Stan 01",
            "menu_name": "Menu Nonaktif",
            "item_type": "makanan",
            "price_idr": 11000,
            "estimated_time_minutes": 4,
            "stock": 20,
            "is_available": True,
            "is_active": False,
            "taste_tags": ["Pedas"],
        },
        {
            "menu_id": 103,
            "seller_id": 1,
            "stall_name": "Stan 01",
            "menu_name": "Ayam Geprek Tersedia",
            "item_type": "makanan",
            "price_idr": 13000,
            "estimated_time_minutes": 5,
            "stock": 15,
            "is_available": True,
            "is_active": True,
            "taste_tags": ["Pedas", "Gurih"],
        },
    ]
    res = recommend_menus(
        budget=15000,
        taste_preference="pedas",
        max_wait_minutes=10,
        menus=sample_menus,
        desired_keyword="Mie Pedas",
    )
    ids = [r["menu_id"] for r in res["recommendations"]]
    assert 101 not in ids
    assert 102 not in ids
    assert 103 in ids
    assert res["unavailable_notice"] is not None


def test_rec_case_d_wait_time_and_queue_penalty():
    """Kasus D: waktu tunggu terlalu lama (termasuk penalti antrean) disaring atau diturunkan."""
    # Antrean stan 1 = 8 pesanan -> penalti +12 menit -> total >= 16 menit > batas 8 menit
    res = recommend_menus(
        budget=15000,
        taste_preference="pedas",
        max_wait_minutes=8,
        item_type="makanan",
        strict_wait_time=True,
        seller_queues={1: 8, 2: 0, 3: 0, 4: 0, 5: 0},
    )
    assert res["status"] == "ok"
    assert all(r["seller_id"] != 1 for r in res["recommendations"])
    assert res["excluded_by_wait_time"] > 0


def test_rec_case_e_no_match_does_not_fabricate():
    """Kasus E: budget terlalu rendah -> sistem tidak mengarang hasil dan memberi saran budget minimum."""
    res = recommend_menus(budget=5000, taste_preference="pedas", max_wait_minutes=10, item_type="makanan")
    assert res["status"] == "no_match"
    assert res["recommendations"] == []
    assert res["suggested_min_budget"] == 10000


# =========================================================
# BAGIAN 4: AI DEMAND FORECASTING (KASUS A - E)
# =========================================================

def test_forecast_case_a_sufficient_data():
    """Kasus A: menu dengan riwayat cukup (77 hari) menghasilkan prediksi ML + rentang kehati-hatian."""
    fc = DemandForecaster()
    res = fc.forecast_daily(menu_id=1, target_date="2026-11-24", weather="cerah")  # Selasa
    assert res["status"] == "ok"
    assert res["data_status"] == "sufficient"
    assert res["is_temporary_estimate"] is False
    assert res["range_min"] <= res["predicted_quantity"] <= res["range_max"]
    assert res["predicted_quantity"] <= res["daily_stock_quota"]


def test_forecast_case_b_weekday_busy_vs_saturday_quiet():
    """Kasus B: model membedakan pola hari ramai (Selasa) vs hari sepi (Sabtu), bukan rata-rata datar."""
    fc = DemandForecaster()
    tuesday = fc.forecast_seller_daily(seller_id=1, target_date="2026-11-24", weather="cerah")   # Selasa (ramai)
    saturday = fc.forecast_seller_daily(seller_id=1, target_date="2026-11-28", weather="cerah")  # Sabtu (sepi)
    assert tuesday["summary"]["total_predicted_food_portions"] > saturday["summary"]["total_predicted_food_portions"]
    assert tuesday["summary"]["total_predicted_drink_cups"] > saturday["summary"]["total_predicted_drink_cups"]


def test_forecast_case_c_insufficient_data_labeled_temporary():
    """Kasus C: stan baru hanya punya 2 hari transaksi -> status insufficient_data & label sementara."""
    fc = DemandForecaster()
    res = fc.forecast_daily(
        menu_id=999,
        seller_id=99,
        target_date="2026-11-24",
        daily_stock_quota=30,
        custom_history=[
            {"date": "2026-11-20", "quantity": 18, "order_status": "selesai"},
            {"date": "2026-11-21", "quantity": 22, "order_status": "selesai"},
            # Transaksi batal tidak boleh menaikkan jumlah hari/porsi!
            {"date": "2026-11-21", "quantity": 50, "order_status": "dibatalkan"},
        ],
    )
    assert res["data_status"] == "insufficient_data"
    assert res["is_temporary_estimate"] is True
    assert res["confidence_level"] == "rendah"
    assert res["input_data_points"] == 2
    assert res["predicted_quantity"] == 20


def test_forecast_case_d_new_menu_fallback():
    """Kasus D: menu baru (0 riwayat) memakai pembanding kategori sejenis dengan disclaimer."""
    fc = DemandForecaster()
    res = fc.forecast_daily(
        menu_id=888,
        seller_id=1,
        target_date="2026-11-24",
        item_type="makanan",
        daily_stock_quota=25,
        custom_history=[],
    )
    assert res["data_status"] == "new_menu_fallback"
    assert res["is_temporary_estimate"] is True
    assert res["input_data_points"] == 0
    assert res["predicted_quantity"] > 0


def test_forecast_case_e_campus_event_boost():
    """Kasus E: lonjakan tidak biasa (acara kampus / penyesuaian manual) menaikkan estimasi."""
    fc = DemandForecaster()
    normal = fc.forecast_daily(menu_id=2, target_date="2026-11-23", weather="cerah", has_campus_event=False)
    event = fc.forecast_daily(menu_id=2, target_date="2026-11-23", weather="cerah", has_campus_event=True)
    assert event["predicted_quantity"] >= normal["predicted_quantity"]
    assert "acara kampus" in event["explanation"].lower()


# =========================================================
# FASTAPI ENDPOINTS INTEGRATION CHECK
# =========================================================

def test_fastapi_endpoints():
    r_health = client.get("/health")
    assert r_health.status_code == 200
    assert r_health.json()["forecast_models_loaded"] is True

    r_rec = client.post("/api/ai/recommend", json={"budget": 15000, "taste_preference": "pedas", "max_wait_minutes": 8})
    assert r_rec.status_code == 200
    assert r_rec.json()["status"] == "ok"

    r_seller = client.post("/api/ai/forecast/seller", json={"seller_id": 1, "target_date": "2026-11-24"})
    assert r_seller.status_code == 200
    body = r_seller.json()
    assert body["summary"]["total_menus"] == 10
    assert 70 <= body["summary"]["total_predicted_food_portions"] <= 100
    assert 90 <= body["summary"]["total_predicted_drink_cups"] <= 120

    r_hourly = client.post("/api/ai/forecast/hourly", json={"menu_id": 1, "target_date": "2026-11-24"})
    assert r_hourly.status_code == 200
    assert len(r_hourly.json()["hourly_predictions"]) == 9

    r_eval = client.get("/api/ai/evaluation")
    assert r_eval.status_code == 200
    assert r_eval.json()["data_integrity"]["cancelled_excluded_correctly"] is True
