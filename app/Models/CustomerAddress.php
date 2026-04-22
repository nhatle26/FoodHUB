<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    protected $fillable = [
        'user_id',
        'phone_number',
        'address_line',
        'is_default',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
