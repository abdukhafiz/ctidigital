<?php

namespace App\Services\Oracle;

use App\Dto\Booking\BookingRequestDto;
use App\Dto\Booking\BookingRoomDto;
use App\Exceptions\OracleException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OracleReservationService extends OracleTokenService
{

    /**
     * Make room reservation
     *
     * @param string $hotelCode
     * @param array $payload
     * @return array
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws OracleException
     */
    public function reserveRoom(string $hotelCode, array $payload): array
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->retry(2, 1000)
            ->post($this->baseUrl . '/rsv/v1/hotels/'. $hotelCode .'/reservations', $payload);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Failed to make reservation', [
            'hotel'   => $hotelCode,
            'payload' => $payload,
            'status'  => $response->status(),
            'body'    => $response->body(),
        ]);

        throw new OracleException('Failed to make reservation');
    }

    /**
     * Prepare room reservation payload
     *
     * @param string $hotelCode
     * @param BookingRoomDto $room
     * @param BookingRequestDto $dto
     * @return array[]
     */
    public function preparePayload(string $hotelCode, BookingRoomDto $room, BookingRequestDto $dto): array
    {
        return [
            'reservations' => [
                'reservation' => [[
                    'sourceOfSale' => [
                        'sourceType' => 'WEB',
                        'sourceCode' => $hotelCode,
                    ],
                    'roomStay' => [
                        'roomRates' => [[
                            'total'        => ['price' => $room->totalPrice],
                            'roomType'     => $room->roomCode,
                            'sourceCode'   => 'WEB',
                            'ratePlanCode' => $room->rateCode,
                        ]],
                        'guestCounts' => [
                            'adult'   => $room->adults,
                            'child'   => $room->children,
                        ],
                        'arrivalDate'   => $dto->arrivalDate,
                        'departureDate' => $dto->departureDate,
                        'promotion'     => ['promotionCode' => $dto->promoCode],
                    ],
                    'hotelId' => $hotelCode,
                ]],
            ],
        ];
    }

}
