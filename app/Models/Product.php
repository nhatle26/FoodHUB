<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'shop_id',
        'name',
        'description',
        'price',
        'product_group',
        'image',
        'is_available',
        'total_sold',
        'sort_order',
    ];
}
