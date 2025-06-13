<?php

namespace App\Jobs\Booking;

use App\Dto\Booking\BookingRequestDto;
use App\Events\Booking\BookingCompletedEvent;
use App\Exceptions\BookingException;
use App\Models\BookingLog;
use App\Services\Booking\BookingLogService;
use App\Services\Booking\BookingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessBookingJob implements ShouldQueue
{
    use Queueable;

    protected int $bookingLogId;

    public $backoff = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(int $bookingLogId)
    {
        $this->bookingLogId = $bookingLogId;
        $this->onQueue('bookings');
    }

    /**
     * Execute the job.
     */
    public function handle(BookingService $bookingService, BookingLogService $bLogService): void
    {
        $log = $bLogService->getById($this->bookingLogId);
        if (!$log) {
            return;
        }

        if ($log->status === BookingLog::STATUS_COMPLETED) {
            return;
        }

        try {
            $dto = BookingRequestDto::fromArray($log->request_data);
            $results = $bookingService->book($dto);

            $log->update([
                'status'           => BookingLog::STATUS_COMPLETED,
                'successful_count' => count($results),
                'failed_count'     => 0,
                'response_data'    => $results,
            ]);

            event(new BookingCompletedEvent($log));
        } catch (BookingException $e) {
            $log->increment('failed_count');
            $this->release(30);
        }
    }
}
