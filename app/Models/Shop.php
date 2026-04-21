<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'slug',
        'status',
        'reject_reason',
    ];

    public function details()
    {
        return $this->hasOne(ShopDetail::class);
    }

    public function metrics()
    {
        return $this->hasOne(ShopMetric::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
