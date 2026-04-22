<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shop extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'slug',
        'phone',
        'address',
        'description',
        'cover_image',
        'logo',
        'open_time',
        'close_time',
        'status',
    ];

    protected $casts = [
        'rating_avg' => 'decimal:1',
        'total_orders' => 'integer',
        'total_reviews' => 'integer',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
