<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDelivery extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'order_id';
    public $incrementing = false;
    protected $fillable = [
        'order_id',
        'delivery_address',
        'customer_phone',
        'note',
        'delivered_at',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
