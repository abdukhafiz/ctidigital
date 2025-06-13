<?php

namespace App\Services\Booking;

use App\Dto\Booking\BookingRequestDto;
use App\Exceptions\BookingException;
use App\Services\Oracle\OracleReservationService;

class BookingService
{

    private OracleReservationService $oracleReservationService;

    public function __construct(OracleReservationService $oracleApiService)
    {
        $this->oracleReservationService = $oracleApiService;
    }

    /**
     * Book rooms
     *
     * @param BookingRequestDto $dto
     * @return array
     * @throws BookingException
     */
    public function book(BookingRequestDto $dto): array
    {
        $results = [];

        foreach ($dto->rooms as $room) {
            try {
                $payload = $this->oracleReservationService->preparePayload($dto->hotelCode, $room, $dto);

                $results[] = $this->oracleReservationService->reserveRoom($dto->hotelCode, $payload);
            } catch (\Throwable $e) {
                throw new BookingException('Could not finished book', 0, $e);
            }
        }

        return $results;
    }

}
