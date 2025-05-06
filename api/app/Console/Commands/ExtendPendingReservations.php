<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExtendPendingReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:extend-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extend PIN validity for pending reservations if they are in queue';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $self = $this;
        $max = env('APP_MAX_RESERVATION_EXTENSION');
        $interval = env('APP_RESERVATION_EXTENSION_INTERVAL');

        Reservation::where('status', Reservation::STATUS_PENDING)
            ->whereRaw('TIMESTAMPADD(MINUTE, reservation_extension, reservation_time) < ?', [Carbon::now()])
            ->chunk(100, function ($reservations) use ($self, $max, $interval) {
                foreach ($reservations as $reservation) {
                    if ($reservation->reservation_extension >= $max) {
                        $reservation->status = Reservation::STATUS_EXPIRED;
                        $reservation->save();
                        $self->info("Reservation ID {$reservation->id} has reached the maximum extension limit.");
                        continue;
                    }

                    $reservation->reservation_extension += $interval;
                    $reservation->save();

                    $self->info("Extended reservation ID {$reservation->id} by {$interval} minutes.");
                }
            });

        return 0;
    }
}
