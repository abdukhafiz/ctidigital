<?php

namespace App\Dto\Booking;

class BookingRoomDto
{

    public function __construct(
        public int    $adults,
        public int    $children,
        public string $roomCode,
        public string $rateCode,
        public float  $totalPrice,
    )
    {
    }

}
