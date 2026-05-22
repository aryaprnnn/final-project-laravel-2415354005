<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    // Properti mass assignment 
    protected $fillable = ['name', 'price', 'description', 'status'];

    // Konversi tipe data otomatis 
    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'price' => 'integer',
        ];
    }

    /**
     * Relasi ke model Subscription
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}