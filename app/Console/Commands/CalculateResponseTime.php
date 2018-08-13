<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Proexe\BookingApp\Bookings\Models\BookingModel;
use Proexe\BookingApp\Utilities\ResponseTimeCalculator;

class CalculateResponseTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookingApp:calculateResponseTime';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculates response time';

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
     * @return mixed
     */
    public function handle() {
        $responseTimeCalculator = new ResponseTimeCalculator();
        $bookings = BookingModel::with('office')->get()->toArray();

        foreach ( $bookings as $booking ) {

            $this->line( "Response time for booking with id: " . $booking['id'] . " in office " . $booking['office']['name'] . " was: " . $responseTimeCalculator->calculate( $booking ) );

        }

	    //Use ResponseTimeCalculator class for all calculations
	    //You can use $this->line() to write out any info to console
    }
}
