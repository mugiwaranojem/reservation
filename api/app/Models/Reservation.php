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
        'reservation_time',
        'pin_valid_start',
        'pin_code',
        'status',
        'reservation_extension',
        'first_name',
        'last_name',
        'phone_number'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
