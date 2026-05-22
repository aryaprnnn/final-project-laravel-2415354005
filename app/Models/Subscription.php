<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    // Mendaftarkan kolom yang boleh diisi secara massal
    protected $fillable = [
        'user_id',
        'service_id',
        'start_date',
        'end_date',
        'status'
    ];

    // Mengonversi tipe data kolom secara otomatis
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /**
     * Relasi: Setiap langganan pasti dimiliki oleh satu User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Setiap langganan pasti terikat pada satu Service (Layanan)
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}