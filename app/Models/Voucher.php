<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voucher extends Model
{
    protected $fillable = [
        'shop_id',
        'code',
        'description',
        'type',
        'value',
        'min_order_amount',
        'max_discount',
        'max_uses',
        'used_count',
        'is_active',
        'starts_at',
        'expires_at',
        'conditions',
    ];

    protected $casts = [
        'value' => 'integer',
        'min_order_amount' => 'integer',
        'max_discount' => 'integer',
        'max_uses' => 'integer',
        'used_count' => 'integer',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'conditions' => 'array',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }
}
