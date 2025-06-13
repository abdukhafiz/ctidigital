<?php

namespace App\Http\Controllers\Api;

use App\Dto\Booking\BookingLogDto;
use App\Dto\Booking\BookingRequestDto;
use App\Events\Booking\BookingCompletedEvent;
use App\Events\Booking\BookingQueuedEvent;
use App\Exceptions\BookingException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Hotel\BookingHotelRequest;
use App\Jobs\Booking\ProcessBookingJob;
use App\Models\BookingLog;
use App\Services\Booking\BookingLogService;
use App\Services\Booking\BookingService;

class HotelBookingController extends Controller
{

    public function __construct(
        private BookingService    $bookingService,
        private BookingLogService $logService
    )
    {
    }

    public function bookingHotel(BookingHotelRequest $request)
    {
        $dto = BookingRequestDto::fromRequest($request);

        $logDto = BookingLogDto::fromBookingRequest($dto);
        $log = $this->logService->createLog($logDto->toArray());

        try {
            $results = $this->bookingService->book($dto);

            $log->update([
                'status' => BookingLog::STATUS_COMPLETED,
                'successful_count' => count($results),
                'response_data' => $results,
            ]);

            event(new BookingCompletedEvent($log));

            return response()->json([
                'data' => $results,
                'reference' => $log->booking_reference,
            ], 201);
        } catch (BookingException $e) {
            ProcessBookingJob::dispatch($log->id)
                ->delay(now()->addSeconds(30));

            event(new BookingQueuedEvent($log));

            return response()->json([
                'message' => 'Your booking is queued for processing.',
                'reference' => $log->booking_reference,
            ], 202);
        }
    }

}
