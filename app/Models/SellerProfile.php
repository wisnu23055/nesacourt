<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SellerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'stall_name',
        'slug',
        'description',
        'logo_path',
        'location',
        'is_active',
        'open_time',
        'close_time',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function qris(): HasOne
    {
        return $this->hasOne(SellerPaymentQris::class, 'seller_id');
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class, 'seller_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'seller_id');
    }
}
