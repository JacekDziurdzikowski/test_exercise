<?php
/**
 * Date: 09/08/2018
 * Time: 00:16
 * @author Artur Bartczak <artur.bartczak@code4.pl>
 */

namespace Proexe\BookingApp\Utilities;


use Proexe\BookingApp\Offices\Interfaces\ResponseTimeCalculatorInterface;


class ResponseTimeCalculator implements ResponseTimeCalculatorInterface {


    public function calculate ( $bookingDateTime, $responseDateTime, $officeHours ) {

        $generalStart = new \DateTime( $bookingDateTime );
        $generalStop = new \DateTime( $responseDateTime );

        $bookingDay = (int) $generalStart->format('z');
        $responseDay = (int)  $generalStop->format('z');

        //Situation which mostly takes place
        if ( $responseDay >= $bookingDay ) {
            $generalDaysNumber = $responseDay - $bookingDay + 1 ;
        }
        //Situation which takes place on a turn of a year
        else {
            $generalDaysNumber = $responseDay + ( $generalStart->format('L') ? (366 - $bookingDay) : (365 - $bookingDay) ) + 1 ;
        }


        $totalTimeDifference = 0;
        for ($i = 0; $i < $generalDaysNumber; $i++) {

            $timeDifferenceForSpecificDay = $this->calculateTimeDifferenceForSpecificDay( $generalStart, $generalStop, $generalDaysNumber, $i, $officeHours);
            $totalTimeDifference += $timeDifferenceForSpecificDay ?? 0;
        }

        return $totalTimeDifference / 60 ;
    }



    private function calculateTimeDifferenceForSpecificDay (\DateTime $generalStart, \DateTime $generalStop, int $generalDaysNumber, int $day, array $officeHours) {

        $workingHoursForSpecificDay = $this->getWorkingHoursForSpecificDay( $officeHours, $generalStart->modify("+$day day") );
        if ( $workingHoursForSpecificDay['isClosed']  === false ) {

            //Situation where the booking and the response occurred in the same day
            if ( $generalDaysNumber === 1 ) {
                $timeSpanFrom = max( strtotime($generalStart->format('H:i')), strtotime($workingHoursForSpecificDay['from']) );
                $timeSpanTo = min( strtotime($generalStop->format('H:i')), strtotime($workingHoursForSpecificDay['to']) );
            }
            //Situation where computation takes place for the day where the booking occurred
            else if ( $day === 0 ) {
                $timeSpanFrom = max( strtotime($generalStart->format('H:i')), strtotime($workingHoursForSpecificDay['from']) );
                $timeSpanTo = strtotime($workingHoursForSpecificDay['to']);
            }
            //Situation where computation takes place for the day where the response occurred
            else if ( $day + 1 === $generalDaysNumber ) {
                $timeSpanFrom = strtotime($workingHoursForSpecificDay['from']);
                $timeSpanTo = min( strtotime($generalStop->format('H:i')), strtotime($workingHoursForSpecificDay['to']) );
            }
            //Situation where computation takes place for a day between days where the booking or the response occurred
            else {
                $timeSpanFrom = strtotime($workingHoursForSpecificDay['from']);
                $timeSpanTo = strtotime($workingHoursForSpecificDay['to']);
            }

            //On days where a response occurred before an opening hour or a booking occurred after a closing hour it will result in a negative time span
            //so there is need to check if the value is negative or no
            $differenceInSeconds = (($timeSpanTo - $timeSpanFrom) > 0) ? ($timeSpanTo - $timeSpanFrom) : 0 ;
        }

        return $differenceInSeconds ?? 0 ;
    }


    private function getWorkingHoursForSpecificDay ( array $officeHours, \DateTime $date ) {
        $timestamp = $date->getTimestamp();
        $weekDay = date('w', $timestamp);

        $workingHours = $officeHours[$weekDay];
        return $workingHours;
    }


}