<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReservationService;
use App\Http\Resources\ReservationResource;
use App\Services\PinService;
use App\Models\Reservation;
use Carbon\Carbon;
class ReservationController extends Controller
{
    public function __construct(
        private readonly ReservationService $reservationService,
        private readonly PinService $pinService
    )
    {
    }

    public function all(Request $request)
    {
        $reservations = Reservation::orderBy('reservation_time', 'desc')->get();
        return ReservationResource::collection($reservations);
    }

    public function confirm(Request $request, int $pin)
    {
        $reservation = Reservation::where('pin_code', $pin)->firstOrFail();
        if ($this->pinService->isPinValid($reservation)) {
            $reservation = $this->reservationService->confirmReservation($reservation->id);
            return new ReservationResource($reservation);
        }

        return response()->json(['message' => 'Invalid pin. Please try again later.'], 403);
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'reservation_time' => [
                'required',
                'date',
                'after:now',
                function ($attribute, $value, $fail) {
                    // Convert input to UTC and format it to 'Y-m-d H:i'
                    $formattedInputUtc = Carbon::parse($value)->setTimezone('UTC')->format('Y-m-d H:i');
        
                    $exists = Reservation::whereRaw(
                        "DATE_FORMAT(reservation_time, '%Y-%m-%d %H:%i') = ?", 
                        [$formattedInputUtc]
                    )->exists();
        
                    if ($exists) {
                        $fail('The selected reservation time is already taken.');
                    }
                },
            ],
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
        ]);
        $reservation = $this->reservationService->createReservation($validated);
        return new ReservationResource($reservation);
    }
}
