<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OracleHotelRoomRestriction extends Model
{
    protected $fillable = [
        'hotel_id',
        'room_type_id',
        'restriction_id',
        'date',
    ];
}
