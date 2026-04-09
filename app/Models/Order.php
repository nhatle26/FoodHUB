<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'shop_id',
        'order_code',
        'customer_name',
        'customer_phone',
        'delivery_address',
        'note',
        'subtotal',
        'delivery_fee',
        'discount_amount',
        'total_amount',
        'payment_method',
        'payment_status',
        'status',
        'ordered_at',
        'confirmed_at',
        'delivered_at',
        'cancelled_at',
        'cancel_reason',
    ];
}
