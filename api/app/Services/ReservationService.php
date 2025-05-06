<?php
namespace App\Services;

use App\Models\Reservation;
use Carbon\Carbon;

class ReservationService
{
    public function __construct(private readonly PinService $pinService)
    {
    }

    public function confirmReservation(int $id): Reservation
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->status = Reservation::STATUS_CONFIRMED;
        $reservation->save();
        return $reservation;
    }

    public function createReservation(array $params): Reservation
    {
        $reservationTime = Carbon::parse($params['reservation_time']);
        // Convert the time to UTC and store it
        $utcTime = $reservationTime->setTimezone('UTC');
        
        $params['reservation_time'] =  $utcTime->toDateTimeString();

        $reservation = new Reservation();
        $reservation->fill($params);
        $this->pinService->prepareReservationPin($reservation);
        $reservation->save();

        return $reservation;
    }
}

