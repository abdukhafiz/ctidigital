<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OracleRoomType extends Model
{
    protected $table = 'oracle_roomtypes';

    protected $fillable = [
        'room_code',
        'name',
    ];
}
