<?php

namespace App\Services\Booking;

use App\Models\BookingLog;

class BookingLogService
{

    /**
     * Get booking log by id
     *
     * @param int $id
     * @return BookingLog|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function getById(int $id)
    {
        return BookingLog::find($id);
    }

    /**
     * Create a booking log
     *
     * @param array $data
     * @return mixed
     */
    public function createLog(array $data)
    {
        return BookingLog::create($data);
    }

}
