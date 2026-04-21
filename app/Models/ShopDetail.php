<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopDetail extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'shop_id';
    public $incrementing = false;
    protected $fillable = [
        'shop_id',
        'phone',
        'address',
        'description',
        'cover_image',
        'logo',
        'open_time',
        'close_time',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
