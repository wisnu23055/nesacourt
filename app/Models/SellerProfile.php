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

    protected $appends = [
        'logo_url',
        'initials',
    ];

    public function getLogoUrlAttribute(): ?string
    {
        if (empty($this->logo_path)) {
            return null;
        }
        if (str_starts_with($this->logo_path, 'http://') || str_starts_with($this->logo_path, 'https://')) {
            return $this->logo_path;
        }
        return '/' . ltrim($this->logo_path, '/');
    }

    public function getInitialsAttribute(): string
    {
        $name = preg_replace('/^Stan\s+\d+\s*-\s*/i', '', $this->stall_name ?? 'Stan');
        $words = preg_split('/\s+/', trim($name));
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($name, 0, 2));
    }

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
