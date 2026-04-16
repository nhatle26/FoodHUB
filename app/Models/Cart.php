<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'shop_id',
        'quantity',
    ];

    // Một dòng trong giỏ hàng thuộc về một sản phẩm
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Một dòng trong giỏ hàng thuộc về một shop
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
