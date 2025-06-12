<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceModifier extends Model
{
    protected $fillable = [
        'hotel_id',
        'rate_id',
        'date',
        'modifier_type',
        'operation',
        'price',
    ];

    public const MODIFIER_TYPE_PER_GUEST = 'per_guest';
    public const MODIFIER_TYPE_PER_ROOM = 'per_room';
    public const MODIFIER_TYPES = [
        self::MODIFIER_TYPE_PER_GUEST => 'Per guest',
        self::MODIFIER_TYPE_PER_ROOM => 'Per room',
    ];

    public const OPERATIONS_ADD = 'add';
    public const OPERATIONS_SUBTRACT = 'subtract';
    public const OPERATIONS = [
        self::OPERATIONS_ADD => 'Add',
        self::OPERATIONS_SUBTRACT => 'Subtract',
    ];
}
