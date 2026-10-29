<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecommendationLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'session_id',
        'budget',
        'taste_preference',
        'max_wait_minutes',
        'recommended_menu_ids',
        'created_at',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'recommended_menu_ids' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
