<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopMetric extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'shop_id';
    public $incrementing = false;
    protected $fillable = [
        'shop_id',
        'rating_avg',
        'total_orders',
        'total_reviews',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
