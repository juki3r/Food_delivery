<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    protected $fillable = [
        'restaurant_name',
        'owner_name',
        'address',
        'phone',
        'api_token',
        'password',
    ];
}
