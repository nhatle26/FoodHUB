<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    ];
}
