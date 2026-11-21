# NesaCourt AI Service (Python + FastAPI)

Modul khusus **AI / Data / QA** untuk proyek **NesaCourt** (*Foodcourt Baseball UNESA 1 Ketintang*), dipisahkan sepenuhnya dari kode Backend Laravel dan Frontend Blade.

Mencakup dua fitur utama sesuai `Konsep_AI_NESACOURT.pdf`:
1. **Smart Menu Recommendation** (`recommender.py`) — Rekomendasi menu berbobot untuk Pembeli (budget, rasa, waktu tunggu + antrean, popularitas survei UNESA 2026, info gizi).
2. **AI Demand Forecasting** (`train_forecasting.py` & `forecaster.py`) — Prediksi permintaan harian (`daily`) & per jam (`hourly`) dengan perbandingan 4 model (Baseline MA-7, Ridge Regression, Random Forest, HistGradientBoosting) serta penanganan kasus khusus (data sedikit, menu baru, hari ramai vs sepi, lonjakan acara kampus, dan penyaringan transaksi batal).

---

## 1. Struktur Folder `ai_service/`

| File | Fungsi |
| :--- | :--- |
| `requirements.txt` | Daftar library Python (`numpy`, `pandas`, `scikit-learn`, `joblib`, `fastapi`, `uvicorn`, `pytest`). |
| `recommender.py` | Engine *Smart Menu Recommendation* berbobot (Bagian 3 `Konsep_AI_NESACOURT.pdf`). |
| `train_forecasting.py` | Script training, perbandingan 4 model, evaluasi MAE/RMSE/MAPE/WAPE/R², dan export model `.joblib` + laporan evaluasi. |
| `forecaster.py` | Engine inferensi *AI Demand Forecasting* harian & per jam (Bagian 4 `Konsep_AI_NESACOURT.pdf`, Kasus A–E). |
| `app.py` | REST API FastAPI siap diintegrasikan oleh tim Backend Laravel. |
| `test_ai_qa.py` | Pengujian QA otomatis (`pytest`) untuk seluruh Kasus A–E Rekomendasi & Forecasting serta endpoint API. |
| `artifacts/` | Hasil training: `daily_forecast_model.joblib`, `hourly_forecast_model.joblib`, `evaluation_report.json`, `evaluation_summary.md`. |

---

## 2. Cara Menjalankan di Terminal (PowerShell)

Pastikan Anda berada di root proyek (`C:\laragon\www\nesacourt`). Virtual environment sudah disiapkan di `ai_service\.venv`.

### A. Jalankan Training & Evaluasi Model (MAE / RMSE / MAPE / R²)
```powershell
& "ai_service\.venv\Scripts\python.exe" ai_service\train_forecasting.py
```
Setelah selesai, hasil lengkap tersimpan di:
- `ai_service\artifacts\evaluation_summary.md` *(siap dipakai untuk laporan/presentasi)*
- `ai_service\artifacts\evaluation_report.json`
- `ai_service\artifacts\daily_forecast_model.joblib`
- `ai_service\artifacts\hourly_forecast_model.joblib`

### B. Jalankan Pengujian QA Otomatis (Kasus A–E + Endpoint API)
```powershell
& "ai_service\.venv\Scripts\python.exe" -m pytest ai_service\test_ai_qa.py -v
```

### C. Jalankan Server FastAPI
```powershell
cd ai_service
.\.venv\Scripts\python.exe -m uvicorn app:app --host 127.0.0.1 --port 8001 --reload
```
Buka dokumentasi interaktif Swagger UI di browser:
- **Swagger UI**: `http://127.0.0.1:8001/docs`
- **Health Check**: `http://127.0.0.1:8001/health`

---

## 3. Daftar Endpoint API (Untuk Integrasi Backend Laravel)

### 1) `POST /api/ai/recommend` — Smart Menu Recommendation
**Contoh Request JSON:**
```json
{
  "budget": 15000,
  "taste_preference": "pedas",
  "max_wait_minutes": 8,
  "item_type": "makanan",
  "strict_wait_time": true,
  "seller_queues": { "1": 2, "2": 0, "3": 1, "4": 0, "5": 0 },
  "limit": 5
}
```
*(Opsional: Laravel juga dapat mengirimkan array `menus` langsung dari MySQL agar skor membaca stok real-time.)*

### 2) `POST /api/ai/forecast/daily` — Prediksi Permintaan Harian per Menu
**Contoh Request JSON:**
```json
{
  "menu_id": 1,
  "seller_id": 1,
  "target_date": "2026-11-24",
  "weather": "cerah",
  "has_campus_event": false,
  "manual_adjustment_pct": 0
}
```

### 3) `POST /api/ai/forecast/seller` — Prediksi Seluruh Menu 1 Tenant Sekaligus
**Contoh Request JSON:**
```json
{
  "seller_id": 1,
  "target_date": "2026-11-24",
  "weather": "cerah",
  "has_campus_event": false
}
```

### 4) `POST /api/ai/forecast/hourly` — Prediksi Per Jam (08:00–16:00)
**Contoh Request JSON:**
```json
{
  "menu_id": 1,
  "target_date": "2026-11-24",
  "weather": "cerah"
}
```

### 5) `GET /api/ai/evaluation` — Laporan Metrik MAE, RMSE, MAPE, R²
Mengembalikan isi `evaluation_report.json` hasil training terakhir.
