<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingLog extends Model
{

    protected $fillable = [
        'booking_reference',
        'hotel_code',
        'arrival_date',
        'departure_date',
        'rooms_count',
        'status',
        'successful_count',
        'failed_count',
        'request_data',
        'response_data',
    ];

    protected $casts = [
        'arrival_date' => 'date',
        'departure_date' => 'date',
        'request_data' => 'array',
        'response_data' => 'array',
    ];

    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_PARTIAL_FAILURE = 'partial_failure';
    public const STATUS_FAILED = 'failed';

    public const STATUSES = [
        self::STATUS_PROCESSING => 'Processing',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_PARTIAL_FAILURE,
        self::STATUS_FAILED,
    ];

}
