<?php

namespace App\Dto\Booking;

use App\Models\BookingLog;
use Illuminate\Support\Str;

class BookingLogDto
{
    public string $bookingReference;
    public string $hotelCode;
    public string $arrivalDate;
    public string $departureDate;
    public int $roomsCount;
    public string $status;
    public array $requestData;

    private function __construct(
        string $bookingReference,
        string $hotelCode,
        string $arrivalDate,
        string $departureDate,
        int    $roomsCount,
        string $status,
        array  $requestData
    )
    {
        $this->bookingReference = $bookingReference;
        $this->hotelCode = $hotelCode;
        $this->arrivalDate = $arrivalDate;
        $this->departureDate = $departureDate;
        $this->roomsCount = $roomsCount;
        $this->status = $status;
        $this->requestData = $requestData;
    }

    public static function fromBookingRequest(BookingRequestDto $dto): self
    {
        $roomsPayload = array_map(fn($room) => [
            'adults' => $room->adults,
            'children' => $room->children,
            'roomCode' => $room->roomCode,
            'rateCode' => $room->rateCode,
            'totalPrice' => $room->totalPrice,
        ], $dto->rooms);

        return new self(
            Str::uuid()->toString(),
            $dto->hotelCode,
            $dto->arrivalDate,
            $dto->departureDate,
            count($dto->rooms),
            BookingLog::STATUS_PROCESSING,
            [
                'arrivalDate' => $dto->arrivalDate,
                'departureDate' => $dto->departureDate,
                'hotelCode' => $dto->hotelCode,
                'promoCode' => $dto->promoCode,
                'rooms' => $roomsPayload,
            ]
        );
    }

    public function toArray(): array
    {
        return [
            'booking_reference' => $this->bookingReference,
            'hotel_code' => $this->hotelCode,
            'arrival_date' => $this->arrivalDate,
            'departure_date' => $this->departureDate,
            'rooms_count' => $this->roomsCount,
            'status' => $this->status,
            'request_data' => $this->requestData,
        ];
    }
}
