<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    protected $casts = [
        'price' => 'integer',
        'is_available' => 'boolean',
        'total_sold' => 'integer',
        'sort_order' => 'integer',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
