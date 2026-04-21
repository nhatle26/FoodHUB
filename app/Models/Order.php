<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'shop_id',
        'order_code',
        'subtotal',
        'shipping_fee',
        'discount',
        'total',
        'payment_method',
        'status',
        'confirmed_at',
        'cancel_reason',
        'voucher_code',
    ];

    public function delivery()
    {
        return $this->hasOne(OrderDelivery::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
