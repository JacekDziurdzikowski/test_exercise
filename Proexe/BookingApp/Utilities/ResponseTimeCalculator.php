<?php
/**
 * Date: 09/08/2018
 * Time: 00:16
 * @author Artur Bartczak <artur.bartczak@code4.pl>
 */

namespace Proexe\BookingApp\Utilities;


class ResponseTimeCalculator {

	//Write your methods here

    public function calculate ( array $booking ) {

        $generalStart = new \DateTime( $booking['createdAt'] );
        $generalStop = new \DateTime( $booking['updatedAt'] );

        $generalInterval = $generalStop->diff( $generalStart );
        $generalDaysNumber = $generalInterval->days;

        $totalTimeDifference = 0;

        for ($i = 0; $i < $generalDaysNumber; $i++) {

            $workingHoursForSpecificDay = $this->getWorkingHoursForSpecificDay( $booking['office'], $generalStart->modify("+$i day") );

            if ( $workingHoursForSpecificDay['isClosed'] == false ){
                $timeDifferenceForSpecificDay = $this->calculateTimeDifferenceForSpecificDay( $generalStart, $generalStop, $workingHoursForSpecificDay, $i);
            }
            $totalTimeDifference += $timeDifferenceForSpecificDay;
        }


        return $totalTimeDifference;
    }




    private function getWorkingHoursForSpecificDay ( array $office, string $date ) {

        $timestamp = strtotime($date);
        $weekDay = date('w', $timestamp);

        $workingHours = $office['office_hours'][$weekDay];
        return $workingHours;
    }

    private function calculateTimeDifferenceForSpecificDay ($start, $stop, $workingHours, $day) {


    }

}