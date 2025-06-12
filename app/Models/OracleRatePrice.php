<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OracleRatePrice extends Model
{
    protected $fillable = [
        'hotel_id',
        'room_type_id',
        'rate_id',
        'price_date',
        'price',
        'currency',
    ];
}
