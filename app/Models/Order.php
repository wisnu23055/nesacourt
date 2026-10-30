<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    // ponytail: pembatalan atomik dan rollback stok porsi menu (Blueprint B-11, B-15)
    public function cancelAndRestoreStock(?int $actorId = null, string $actorType = 'system', ?string $reason = null): bool
    {
        return DB::transaction(function () use ($actorId, $actorType, $reason) {
            $locked = self::where('id', $this->id)->lockForUpdate()->first();
            if (! $locked || $locked->order_status === 'dibatalkan') {
                return false;
            }

            $oldStatus = $locked->order_status;
            $oldPayment = $locked->payment_status;

            $locked->update([
                'order_status' => 'dibatalkan',
                'payment_status' => 'dibatalkan',
            ]);

            // Kembalikan kuantitas stok menu secara atomik (cegah pengembalian ganda)
            foreach ($locked->items as $item) {
                if ($item->menu_id) {
                    Menu::where('id', $item->menu_id)->increment('stock', $item->quantity);
                }
            }

            OrderStatusLog::create([
                'order_id' => $locked->id,
                'actor_user_id' => $actorId,
                'actor_type' => $actorType,
                'old_order_status' => $oldStatus,
                'new_order_status' => 'dibatalkan',
                'old_payment_status' => $oldPayment,
                'new_payment_status' => 'dibatalkan',
                'note' => $reason ?? 'Pesanan dibatalkan dan stok dikembalikan.',
            ]);

            return true;
        });
    }

    protected $fillable = [
        'order_number',
        'buyer_id',
        'access_token',
        'seller_id',
        'customer_name',
        'order_status',
        'payment_status',
        'subtotal',
        'total',
        'payment_deadline',
        'note',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'payment_deadline' => 'datetime',
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(SellerProfile::class, 'seller_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(OrderStatusLog::class);
    }

    public function paymentConfirmation(): HasOne
    {
        return $this->hasOne(PaymentConfirmation::class);
    }
}
