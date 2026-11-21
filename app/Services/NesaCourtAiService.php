<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * HTTP Client untuk komunikasi Laravel <-> NesaCourt Python AI Service (FastAPI).
 *
 * Seluruh method bersifat fail-safe: jika layanan Python AI sedang offline/timeout,
 * method mengembalikan null (bukan exception) sehingga website tetap berjalan mulus
 * dengan fallback lokal dan pesan status yang jelas (sesuai Konsep_AI_NESACOURT.pdf).
 */
class NesaCourtAiService
{
    private string $baseUrl;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.ai.url', 'http://127.0.0.1:8001'), '/');
        $this->timeout = max(1, (int) config('services.ai.timeout', 4));
    }

    public function isAvailable(): bool
    {
        try {
            $resp = Http::timeout(2)->get($this->baseUrl . '/health');

            return $resp->successful() && ($resp->json('status') === 'ok');
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * Panggil Smart Menu Recommendation (POST /api/ai/recommend).
     */
    public function recommend(array $payload): ?array
    {
        try {
            $resp = Http::timeout($this->timeout)
                ->acceptJson()
                ->post($this->baseUrl . '/api/ai/recommend', $payload);

            if ($resp->successful()) {
                return $resp->json();
            }
        } catch (Throwable $e) {
            Log::warning('NesaCourt AI recommend unavailable: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Panggil AI Demand Forecasting seluruh menu 1 tenant (POST /api/ai/forecast/seller).
     */
    public function forecastSeller(
        int $sellerId,
        string $targetDate,
        string $weather = 'cerah',
        bool $hasCampusEvent = false,
        float $manualAdjustmentPct = 0.0
    ): ?array {
        try {
            $resp = Http::timeout($this->timeout)
                ->acceptJson()
                ->post($this->baseUrl . '/api/ai/forecast/seller', [
                    'seller_id' => $sellerId,
                    'target_date' => $targetDate,
                    'weather' => $weather,
                    'has_campus_event' => $hasCampusEvent,
                    'manual_adjustment_pct' => $manualAdjustmentPct,
                ]);

            if ($resp->successful()) {
                return $resp->json();
            }
        } catch (Throwable $e) {
            Log::warning('NesaCourt AI forecastSeller unavailable: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Panggil AI Demand Forecasting harian per menu (POST /api/ai/forecast/daily).
     */
    public function forecastDaily(array $payload): ?array
    {
        try {
            $resp = Http::timeout($this->timeout)
                ->acceptJson()
                ->post($this->baseUrl . '/api/ai/forecast/daily', $payload);

            if ($resp->successful()) {
                return $resp->json();
            }
        } catch (Throwable $e) {
            Log::warning('NesaCourt AI forecastDaily unavailable: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Panggil AI Demand Forecasting per jam 08:00-16:00 (POST /api/ai/forecast/hourly).
     */
    public function forecastHourly(
        int $menuId,
        string $targetDate,
        string $weather = 'cerah',
        bool $hasCampusEvent = false
    ): ?array {
        try {
            $resp = Http::timeout($this->timeout)
                ->acceptJson()
                ->post($this->baseUrl . '/api/ai/forecast/hourly', [
                    'menu_id' => $menuId,
                    'target_date' => $targetDate,
                    'weather' => $weather,
                    'has_campus_event' => $hasCampusEvent,
                ]);

            if ($resp->successful()) {
                return $resp->json();
            }
        } catch (Throwable $e) {
            Log::warning('NesaCourt AI forecastHourly unavailable: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Ambil laporan evaluasi MAE/RMSE/MAPE/WAPE/R2 dari model AI (GET /api/ai/evaluation).
     */
    public function evaluation(): ?array
    {
        try {
            $resp = Http::timeout(5)
                ->acceptJson()
                ->get($this->baseUrl . '/api/ai/evaluation');

            if ($resp->successful()) {
                return $resp->json();
            }
        } catch (Throwable) {
            // Abaikan error saat AI offline
        }

        return null;
    }
}
