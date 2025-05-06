<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
class Reservation extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'user_id',
        'reservation_time',
        'pin_valid_start',
        'pin_code',
        'status',
        'reservation_extension'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Determines if the user is still in queue — e.g., waiting for prior reservations to be processed.
     */
    public function isInQueue(): bool
    {
        return static::where('reservation_time', '<', $this->reservation_time)
            ->where('is_confirmed', false)
            ->exists();
    }

    /**
     * Check if the PIN is valid:
     * - Becomes valid 15 mins before reservation
     * - Expires between 10–30 mins after PIN becomes valid
     */
    public function isPinValid(): bool
    {
        $now = now();

        // Start of PIN validity
        $validFrom = $this->reservation_time->copy()->subMinutes(15);

        // Dynamically compute expiration duration (example: default 15 mins)
        $pinValidityMinutes = 15;

        // Enforce constraints: minimum 10, maximum 30 minutes
        $pinValidityMinutes = max(10, min($pinValidityMinutes, 30));   

        // End of PIN validity window
        $validUntil = $validFrom->copy()->addMinutes($pinValidityMinutes);

        // Extend the valid window if still in queue
        if ($this->isInQueue()) {
            $validUntil = $now->copy()->addMinutes(5); // allow a 5-minute extension
        }

        return $now->between($validFrom, $validUntil) && !$this->is_confirmed;
    }
}
