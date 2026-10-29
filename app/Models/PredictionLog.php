<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PredictionLog extends Model
{
    protected $fillable = [
        'seller_id',
        'menu_id',
        'prediction_date',
        'period_type',
        'predicted_quantity',
        'actual_quantity',
        'model_version',
        'input_data_points',
    ];

    protected $casts = [
        'prediction_date' => 'date',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(SellerProfile::class, 'seller_id');
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
}
