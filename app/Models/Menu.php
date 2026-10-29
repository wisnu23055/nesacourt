<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    protected $fillable = [
        'seller_id',
        'category_id',
        'name',
        'price',
        'description',
        'image_path',
        'estimated_time_minutes',
        'taste_tags',
        'stock',
        'is_available',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'taste_tags' => 'array',
        'is_available' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(SellerProfile::class, 'seller_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
