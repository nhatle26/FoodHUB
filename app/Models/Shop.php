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
        'phone',
        'address',
        'description',
        'cover_image',
        'logo',
        'open_time',
        'close_time',
        'status',
    ];
}
