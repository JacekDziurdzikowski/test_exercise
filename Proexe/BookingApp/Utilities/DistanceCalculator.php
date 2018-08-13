<?php
/**
 * Date: 08/08/2018
 * Time: 16:20
 * @author Artur Bartczak <artur.bartczak@code4.pl>
 */

namespace Proexe\BookingApp\Utilities;


use Proexe\BookingApp\Offices\Models\OfficeModel;

class DistanceCalculator {

    const EARTH_RADIUS = 6371000; // in meters

	/**
	 * @param array  $from
	 * @param array  $to
	 * @param string $unit - m, km
	 *
	 * @return mixed
	 */
	public function calculate(array $from, array $to, string $unit = 'm' ) {
		//var_dump($from, $to, $unit);

        // convert from degrees to radians
        $latFrom = deg2rad($from['lat']);
        $lngFrom = deg2rad($from['lng']);
        $latTo = deg2rad($to['lat']);
        $lngTo = deg2rad($to['lng']);

        // Computations using Vincenty formula
        $lngDelta = $lngTo - $lngFrom;
        $a = pow(cos($latTo) * sin($lngDelta), 2) +
            pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lngDelta), 2);
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lngDelta);

        $angle = atan2(sqrt($a), $b);

        $distance = $angle * DistanceCalculator::EARTH_RADIUS;
        return ($unit === 'km') ? $distance/1000 : $distance;
	}

	/**
	 * @param array $from
	 * @param array $offices
	 *
	 * @return array
	 */
	public function findClosestOffice( $from, $offices ) {

	    $leastDistance = 40000;
	    $closestOffice = null;

	    foreach ( $offices as $office ) {

	        $distance = $this->calculate($from, [ 'lat' => $office['lat'], 'lng' =>$office['lng'] ], 'km');
	        if ( $leastDistance > $distance ) {
	            $leastDistance = $distance;
	            $closestOffice = $office;
            }

        }

		return $closestOffice;
	}

    /*
     * Proposal for finding database(MySQL) rows inside requested distance:
     * $qry = "SELECT *,(((acos(sin((".$latitude."*pi()/180)) * sin((`Latitude`*pi()/180))+cos((".$latitude."*pi()/180)) * cos((`Latitude`*pi()/180)) * cos(((".$longitude."- `Longitude`)*pi()/180))))*180/pi())*60*1.1515*1.609344) as distance
       FROM `offices`
       WHERE distance >= ".$distance."
     */

}