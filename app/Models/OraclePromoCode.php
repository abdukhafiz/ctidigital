<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OraclePromoCode extends Model
{
    protected $fillable = [
        'promo_code',
        'name',
        'discount_type',
        'discount_value',
        'valid_from',
        'valid_to',
    ];

    public const DISCOUNT_TYPE_PERCENTAGE = 'percentage';
    public const DISCOUNT_TYPE_FIXED = 'fixed';

    public const DISCOUNT_TYPES = [
        self::DISCOUNT_TYPE_PERCENTAGE => 'Percentage',
        self::DISCOUNT_TYPE_FIXED => 'Fixed',
    ];
}
