<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    // Mendaftarkan kolom yang boleh diisi secara massal
    protected $fillable = [
        'subscription_id',
        'invoice_number',
        'amount',
        'payment_status',
        'paid_at'
    ];

    // Mengonversi kolom paid_at menjadi tipe data DateTime otomatis
    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
        ];
    }

    /**
     * Relasi: Setiap invoice pasti terikat pada satu transaksi Subscription
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}