<?php

namespace App\Dto\Booking;

use App\Http\Requests\Api\Hotel\BookingHotelRequest;

class BookingRequestDto
{

    /** @var BookingRoomDto[] */
    public array $rooms;

    public function __construct(
        public string  $arrivalDate,
        public string  $departureDate,
        public string  $hotelCode,
        public ?string $promoCode,
    )
    {
    }

    public static function fromRequest(BookingHotelRequest $r): self
    {
        $dto = new self(
            arrivalDate: $r->input('arrivalDate'),
            departureDate: $r->input('departureDate'),
            hotelCode: $r->input('hotelCode'),
            promoCode: $r->input('promoCode'),
        );

        $dto->rooms = array_map(
            fn(array $item) => new BookingRoomDto(
                adults: $item['adults'],
                children: $item['children'],
                roomCode: $item['roomCode'],
                rateCode: $item['rateCode'],
                totalPrice: $item['totalPrice'],
            ),
            $r->input('rooms')
        );

        return $dto;
    }

    public static function fromArray(array $data): self
    {
        $dto = new self(
            arrivalDate: $data['arrivalDate'],
            departureDate: $data['departureDate'],
            hotelCode: $data['hotelCode'],
            promoCode: $data['promoCode'] ?? null,
        );

        $dto->rooms = array_map(
            fn(array $item) => new BookingRoomDto(
                adults: $item['adults'],
                children: $item['children'],
                roomCode: $item['roomCode'],
                rateCode: $item['rateCode'],
                totalPrice: $item['totalPrice'],
            ),
            $data['rooms']
        );

        return $dto;
    }

}
