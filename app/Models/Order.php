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
        'shipping_fee',
        'discount',
        'total',
        'payment_method',
        'payment_status',
        'status',
        'confirmed_at',
        'delivered_at',
        'cancelled_at',
        'cancel_reason',
    ];

    public function items() {
    return $this->hasMany(OrderItem::class);
}

public function user() {
    return $this->belongsTo(User::class);
}

}
