<?php
namespace App\Services;

use App\Models\Reservation;
use Carbon\Carbon;

class PinService
{
    /**
     * Generate pin_code and pin_valid_start.
     */
    public function prepareReservationPin(Reservation &$reservation): void
    {
        if (!$reservation->reservation_time) {
            throw new \Exception('Reservation time must be set before generating PIN.');
        }

        $reservationTime = Carbon::parse($reservation->reservation_time);
        $reservation->pin_valid_start = $reservationTime->copy()->subMinutes(15);

        // Generate unique 6-digit PIN
        do {
            $pin = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (Reservation::where('pin_code', $pin)->exists());

        $reservation->pin_code = $pin;
    }

    /**
     * Check if current time is within the PIN validity window:
     * from pin_valid_start until reservation_time.
     */
    public function isPinValid(Reservation $reservation): bool
    {
        if ($reservation->status != Reservation::STATUS_PENDING || !$reservation->pin_valid_start || !$reservation->reservation_time) {
            return false;
        }

        $now = Carbon::now();
        if ($now->gt($reservation->pin_valid_start) && !$this->isUserInQueue($reservation)) {
            // If current time is after pin_valid_start and more than 10 minutes from now and not in queue, expire the PIN
            $minValidEnd = Carbon::parse($reservation->pin_valid_start)->copy()->addMinutes(env('APP_MIN_RESERVATION_EXTENSION'));
            if ($now->gt($minValidEnd->toDateTimeString())) {
                $reservation->status = Reservation::STATUS_EXPIRED;
                $reservation->save();
                return false;
            }
        } else if ($now->gt($reservation->reservation_time) && $reservation->reservation_extension >= env('APP_MAX_RESERVATION_EXTENSION')) {
            $reservation->status = Reservation::STATUS_EXPIRED;
            $reservation->save();
            return false;
        }

        $validEnd = $reservation->reservation_time;
        if ($reservation->reservation_extension > 0) {
            $validEnd = Carbon::parse($reservation->reservation_time)
                ->copy()->addMinutes($reservation->reservation_extension)
                ->toDateTimeString();
        }
        
        return $now->between($reservation->pin_valid_start, $validEnd);
    }

    private function isUserInQueue(Reservation $reservation): bool
    {
        $queueCount = Reservation::where('reservation_time', '<', $reservation->reservation_time)
            ->where('status', Reservation::STATUS_PENDING)
            ->count();

        return $queueCount > 0;
    }

    /**
     * Get minutes remaining until PIN expires (reservation_time).
     */
    public function getMinutesUntilExpiration(Reservation $reservation): int
    {
        $now = Carbon::now();
        return max(0, $now->diffInMinutes(Carbon::parse($reservation->reservation_time), false));
    }
}
