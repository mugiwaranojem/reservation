<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReservationService;
use App\Http\Resources\ReservationResource;
use App\Services\PinService;
use App\Models\Reservation;

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
        $reservations = Reservation::all();
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
            'reservation_time' => 'required|date|after:now',
            'user_id' => 'required|exists:users,id',
        ]);
        $reservation = $this->reservationService->createReservation($validated);
        return new ReservationResource($reservation);
    }
}
