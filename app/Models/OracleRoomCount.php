<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OracleRoomCount extends Model
{
    protected $fillable = [
        'hotel_id',
        'room_type_id',
        'availability_date',
        'rooms_available',
    ];
}
