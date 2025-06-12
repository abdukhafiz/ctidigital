<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OracleRestriction extends Model
{
    protected $fillable = [
        'restriction_code',
        'name',
        'restriction_type',
    ];

    public const RESTRICTION_TYPE_ROOM_CLOSURE = 'room_closure';
    public const RESTRICTION_TYPE_RATE_CLOSURE = 'rate_closure';
    public const RESTRICTION_TYPE = [
        self::RESTRICTION_TYPE_ROOM_CLOSURE => 'Room closure',
        self::RESTRICTION_TYPE_RATE_CLOSURE => 'Rate closure',
    ];
}
