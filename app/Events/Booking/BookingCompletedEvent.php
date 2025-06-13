<?php

namespace App\Events\Booking;

use App\Models\BookingLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingCompletedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public BookingLog $bookingLog;

    /**
     * Create a new event instance.
     */
    public function __construct(BookingLog $bookingLog)
    {
        $this->bookingLog = $bookingLog;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn(): Channel
    {
        return new Channel('booking.' . $this->bookingLog->booking_reference);
    }

    public function broadcastWith(): array
    {
        return [
            'status' => $this->bookingLog->status,
            'reference' => $this->bookingLog->booking_reference,
            'response' => $this->bookingLog->response_data,
        ];
    }
}
