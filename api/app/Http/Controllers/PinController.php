<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Services\PinService;

class PinController extends Controller
{
    public function __construct(private readonly PinService $pinService)
    {
    }

    public function checkPin(Request $request, int $id)
    {
        $reservation = Reservation::findOrFail($id);
        if ($this->pinService->isPinValid($reservation)) {
            return response()->json([
                'status' => 'valid',
                'minutes_remaining' => $this->pinService->getMinutesUntilExpiration($reservation)
            ]);
        }

        return response()->json(['status' => 'expired'], 403);
    }
}
