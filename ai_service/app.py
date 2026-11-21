"""
NesaCourt AI Microservice (FastAPI).

Menyediakan REST API untuk dua fitur AI NesaCourt:
1. Smart Menu Recommendation (Pembeli)  -> POST /api/ai/recommend
2. AI Demand Forecasting (Penjual)      -> POST /api/ai/forecast/daily
                                        -> POST /api/ai/forecast/hourly
                                        -> POST /api/ai/forecast/seller
3. Evaluasi & Kesehatan Layanan         -> GET  /api/ai/evaluation
                                        -> GET  /health
"""

from __future__ import annotations

from typing import Any, Dict, List, Optional

from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel, Field

from forecaster import DemandForecaster
from recommender import recommend_menus


app = FastAPI(
    title="NesaCourt AI Service",
    description=(
        "Layanan AI untuk Smart Menu Recommendation dan AI Demand Forecasting "
        "Foodcourt Baseball UNESA 1 Ketintang (dikalibrasi dari wawancara 5 tenant "
        "dan jurnal Ramadhan & Ruhana, 2026)."
    ),
    version="1.0.0",
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_methods=["*"],
    allow_headers=["*"],
)

forecaster = DemandForecaster()
forecaster.load()  # Preload model saat server start agar request pertama langsung cepat


class MenuInput(BaseModel):
    menu_id: int
    seller_id: int
    stall_name: str = "Stan Kantin"
    menu_name: str
    item_type: str = "makanan"
    price_idr: int
    estimated_time_minutes: int = 5
    stock: int = 10
    is_available: bool = True
    is_active: bool = True
    taste_tags: List[str] = Field(default_factory=list)
    journal_popularity_weight: float = 12.0
    energy_kcal: Optional[float] = None
    protein_g: Optional[float] = None


class RecommendRequest(BaseModel):
    budget: int = Field(..., ge=0, examples=[15000])
    taste_preference: str = Field("semua", examples=["pedas"])
    max_wait_minutes: int = Field(10, ge=1, examples=[8])
    item_type: Optional[str] = Field(None, examples=["makanan"])
    strict_wait_time: bool = True
    desired_keyword: Optional[str] = None
    seller_queues: Optional[Dict[int, int]] = Field(None, examples=[{1: 2, 2: 0}])
    menus: Optional[List[MenuInput]] = None
    limit: int = Field(5, ge=1, le=20)


class HistoryPoint(BaseModel):
    date: str
    quantity: int
    order_status: str = "selesai"


class DailyForecastRequest(BaseModel):
    menu_id: Optional[int] = Field(1, examples=[1])
    seller_id: Optional[int] = Field(1, examples=[1])
    target_date: Optional[str] = Field(None, examples=["2026-11-24"])
    weather: str = Field("cerah", examples=["cerah"])
    has_campus_event: bool = False
    manual_adjustment_pct: float = Field(0.0, ge=-50.0, le=100.0)
    item_type: str = "makanan"
    price_idr: int = 13000
    daily_stock_quota: int = 20
    custom_history: Optional[List[HistoryPoint]] = None


class HourlyForecastRequest(BaseModel):
    menu_id: int = Field(..., examples=[1])
    target_date: Optional[str] = Field(None, examples=["2026-11-24"])
    weather: str = Field("cerah", examples=["cerah"])
    has_campus_event: bool = False


class SellerForecastRequest(BaseModel):
    seller_id: int = Field(..., examples=[1])
    target_date: Optional[str] = Field(None, examples=["2026-11-24"])
    weather: str = Field("cerah", examples=["cerah"])
    has_campus_event: bool = False
    manual_adjustment_pct: float = Field(0.0, ge=-50.0, le=100.0)


@app.get("/health")
def health_check() -> Dict[str, Any]:
    return {
        "status": "ok",
        "service": "nesacourt-ai",
        "forecast_models_loaded": forecaster.is_ready,
    }


@app.post("/api/ai/recommend")
def api_recommend(req: RecommendRequest) -> Dict[str, Any]:
    menus_payload = [m.model_dump() for m in req.menus] if req.menus is not None else None
    return recommend_menus(
        budget=req.budget,
        taste_preference=req.taste_preference,
        max_wait_minutes=req.max_wait_minutes,
        item_type=req.item_type,
        strict_wait_time=req.strict_wait_time,
        seller_queues=req.seller_queues,
        menus=menus_payload,
        desired_keyword=req.desired_keyword,
        limit=req.limit,
    )


@app.post("/api/ai/forecast/daily")
def api_forecast_daily(req: DailyForecastRequest) -> Dict[str, Any]:
    try:
        hist = [h.model_dump() for h in req.custom_history] if req.custom_history is not None else None
        return forecaster.forecast_daily(
            menu_id=req.menu_id,
            seller_id=req.seller_id,
            target_date=req.target_date,
            weather=req.weather,
            has_campus_event=req.has_campus_event,
            manual_adjustment_pct=req.manual_adjustment_pct,
            item_type=req.item_type,
            price_idr=req.price_idr,
            daily_stock_quota=req.daily_stock_quota,
            custom_history=hist,
        )
    except Exception as exc:
        raise HTTPException(status_code=400, detail=str(exc))


@app.post("/api/ai/forecast/hourly")
def api_forecast_hourly(req: HourlyForecastRequest) -> Dict[str, Any]:
    try:
        return forecaster.forecast_hourly(
            menu_id=req.menu_id,
            target_date=req.target_date,
            weather=req.weather,
            has_campus_event=req.has_campus_event,
        )
    except Exception as exc:
        raise HTTPException(status_code=400, detail=str(exc))


@app.post("/api/ai/forecast/seller")
def api_forecast_seller(req: SellerForecastRequest) -> Dict[str, Any]:
    try:
        return forecaster.forecast_seller_daily(
            seller_id=req.seller_id,
            target_date=req.target_date,
            weather=req.weather,
            has_campus_event=req.has_campus_event,
            manual_adjustment_pct=req.manual_adjustment_pct,
        )
    except Exception as exc:
        raise HTTPException(status_code=400, detail=str(exc))


@app.get("/api/ai/evaluation")
def api_evaluation() -> Dict[str, Any]:
    return forecaster.get_evaluation_report()
