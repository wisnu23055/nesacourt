<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class CancelExpiredOrders extends Command
{
    // ponytail: scheduler otomatis pembatalan pesanan lewat batas waktu pembayaran 15 menit (Blueprint B-11, B-15)
    protected $signature = 'orders:cancel-expired';

    protected $description = 'Batalkan pesanan yang melewati batas waktu pembayaran 15 menit dan kembalikan stok menu';

    public function handle(): int
    {
        $expiredOrders = Order::where('order_status', 'dibuat')
            ->where('payment_status', 'menunggu')
            ->where('payment_deadline', '<', now())
            ->get();

        $count = 0;
        foreach ($expiredOrders as $order) {
            if ($order->cancelAndRestoreStock(null, 'system', 'Batas waktu pembayaran QRIS 15 menit telah habis.')) {
                $count++;
            }
        }

        $this->info("Berhasil membatalkan {$count} pesanan kedaluwarsa dan mengembalikan stok.");

        return Command::SUCCESS;
    }
}
