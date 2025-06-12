<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomRoomRestriction extends Model
{
    protected $fillable = [
        'hotel_id',
        'room_type_id',
        'max_adults',
        'max_children',
        'children_allowed',
        'max_guests',
        'closure_start_date',
        'closure_end_date',
    ];
}
