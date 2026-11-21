# Laporan Evaluasi Model AI Demand Forecasting — NesaCourt

- **Tanggal Training**: `2026-11-21T16:27:54`
- **Dataset**: `ai_dataset/` (90 hari kalender / 77 hari operasional, 5 tenant, 50 menu)
- **Validasi Aturan Data**: Transaksi batal tidak dihitung sebagai penjualan selesai = **LULUS** (66,772 porsi selesai vs 1,174 porsi batal dipisahkan).

---

## 1. Perbandingan Model Forecasting Harian (`period_type = daily`)
Chronological split: **54 hari train** (2,700 baris) → **16 hari test** (2026-11-03 s/d 2026-11-20, 800 baris).

| Model | Test MAE (porsi) | Test RMSE | Test MAPE (%) | Test WAPE (%) | R² |
| :--- | ---: | ---: | ---: | ---: | ---: |
| `baseline_ma7` | 3.366 | 4.143 | 39.12% | 18.86% | 0.8487 |
| `ridge_regression` | 1.304 | 2.045 | 14.18% | 7.31% | 0.9631 |
| `random_forest` **(Terpilih)** | 0.808 | 1.423 | 9.84% | 4.53% | 0.9821 |
| `hist_gradient_boosting` | 0.816 | 1.341 | 9.2% | 4.57% | 0.9842 |

> Model terpilih **`random_forest`** menurunkan MAE sebesar **76.0%** dibandingkan baseline Moving Average 7 Hari (`baseline_ma7`).

---

## 2. Evaluasi Harian per Kondisi Hari (`demand_regime`)
| Kondisi Hari | Jumlah Sampel Uji | MAE (porsi) | MAPE (%) | WAPE (%) |
| :--- | ---: | ---: | ---: | ---: |
| `normal` | 100 | 0.902 | 9.0% | 4.7% |
| `ramai` | 350 | 0.432 | 4.28% | 2.1% |
| `ramai_event` | 50 | 0.439 | 3.57% | 2.1% |
| `sedang_jumat` | 150 | 1.115 | 14.74% | 7.38% |
| `sepi` | 150 | 1.438 | 20.54% | 11.73% |

## 3. Evaluasi Harian per Tenant
| Tenant | Sampel Uji | MAE (porsi) | MAPE (%) | R² |
| :--- | ---: | ---: | ---: | ---: |
| Stan 01 - Gado-Gado Bu Ismah | 160 | 0.746 | 11.32% | 0.9857 |
| Stan 02 - Dapur Budhe' Gun | 160 | 0.812 | 9.73% | 0.984 |
| Stan 03 - Bakul Sego Bu Ani | 144 | 0.893 | 9.24% | 0.9797 |
| Stan 04 - Nasi Campur Bestie | 176 | 0.821 | 9.4% | 0.983 |
| Stan 05 - Nasi Goreng Markisa | 160 | 0.774 | 9.48% | 0.9771 |

---

## 4. Evaluasi Model Forecasting Per Jam (`period_type = hourly`, 08:00–16:00)
- **Model**: `hist_gradient_boosting` (24,300 baris train, 7,200 baris test)
- **Test MAE**: **0.992 porsi/jam** | **RMSE**: 1.344 | **WAPE**: 50.04% | **R²**: 0.6148

Distribusi jam operasional dari data 90 hari:

| Jam | Proporsi Penjualan |
| :---: | ---: |
| 08:00 | 6.0% |
| 09:00 | 8.9% |
| 10:00 | 10.2% |
| 11:00 | 18.1% |
| 12:00 | 24.3% |
| 13:00 | 12.7% |
| 14:00 | 8.8% |
| 15:00 | 6.9% |
| 16:00 | 4.1% |
