<?php 

namespace gmap;
////// Method class for action /////////
class GoogleAPI {
	private $latitude;
	private $longitude;
	private $coordinatesArray;
	private $distLat;
	private $distLong;
	private $unit;

	private $directionLat1; 
	private $directionLong1; 
	private $directionLat2; 
	private $directionLong2;
	private $bearingAngle;

	public function __construct(){
	}
/// Set Lat Long for fetch address from GPS ///
	public function SetLatLong($lat,$long){
		$this->latitude = $lat;
		$this->longitude = $long;
	}
/// Set the dictance Lat Long array from DB ///
	/// Set unit as reuires "K" for Km "M" for Miles
	public function setCoordinate($coArray,$unit){
		$this->coordinatesArray = $coArray;
		$this->unit = $unit;
	}
/// Set the coordinates from direction 	    ///
	public function setDirectionCo($gpsArray){
		$this->directionLat1 = $gpsArray[0]['lat'];
		$this->directionLat2 = $gpsArray[1]['lat'];
		$this->directionLong1 = $gpsArray[0]['long'];
		$this->directionLong2 = $gpsArray[1]['long'];
	}

//// Fetch Direction from given lat long ///////////
	public function getDirection() {
		//difference in longitudinal coordinates
		$dLon = deg2rad($this->directionLong2) - deg2rad($this->directionLong1);
		//difference in the phi of latitudinal coordinates
		$dPhi = log(tan(deg2rad($this->directionLat2) / 2 + pi() / 4) / tan(deg2rad($this->directionLat1) / 2 + pi() / 4));
		//we need to recalculate $dLon if it is greater than pi
		if(abs($dLon) > pi()) {
			if($dLon > 0) {
				$dLon = (2 * pi() - $dLon) * -1;
				}
		else {
			$dLon = 2 * pi() + $dLon;
			}
		}
		//return the angle, normalized
		$this->bearingAngle  = (rad2deg(atan2($dLon, $dPhi)) + 360) % 360;
		return $this->getCompassDirection();
	}
	private function getCompassDirection() {
	   $tmp = round($this->bearingAngle / 45);
	   switch($tmp) {
			case 1:
				$direction = "NE";
				break;
			case 2:
				$direction = "E";
				break;
			case 3:
				$direction = "SE";
				break;
			case 4:
				$direction = "S";
				break;
			case 5:
				$direction = "SW";
				break;
			case 6:
				$direction = "W";
				break;
			case 7:
				$direction = "NW";
				break;
			case 8:
				$direction = "N";
				break;
			default:
				$direction = "N";
	   }
	   return $direction;
	}
//// Fetch Distance from 2 Coordinates /////////////
	// $coordinateArray = array('lat' => 0, 'long' => 0 );
	// return the (int) distance 
	public function calDistance(){
		$num = 0;
		foreach ($this->coordinatesArray as $key => $row) {
        	$this->distLat[] = $row['lat'];
	        $this->distLong[] =$row['long'];
    	}
    	//// Get the Leangth of passed array //////
    	$num = sizeof($this->coordinatesArray);
	    for($i=0;$i<$num;$i++){
	    	if($this->distLat[$i+1] != '' and $this->distLong[$i+1] != '') {
	            $distance[]=$this->distance($this->distLat[$i],$this->distLong[$i],$this->distLat[$i+1],$this->distLong[$i+1],$this->unit);
	        }
	    }
		return round(array_sum($distance));
	}
	////// Called by function calDistance() for total disctance calc //////
	private function distance($lat1, $lon1, $lat2, $lon2, $unit){
	        $theta = $lon1 - $lon2;
	        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
	        $dist = acos($dist);
	        $dist = rad2deg($dist);
	        $miles = $dist * 60 * 1.1515;
	        $unit = strtoupper($unit);
	        if ($unit == "M") {
	          return $miles;
	        } else if ($unit == "N") {
	          return ($miles * 0.8684);
	        } else {
	          return ($miles * 1.60934);
		    }
	}
//// Fetch Address from given coordinates //////////
	public function addFetch(){
		$url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=$this->latitude,$this->longitude&sensor=false";
	    $curlData=file_get_contents($url);
	    $address = json_decode($curlData);
	    $a=$address->results[0];
	    return explode(",",$a->formatted_address);
	}

}

//////////// Access Class for calling //////////
class MapAPI {
	public static function SetLatLong($lat,$long){
		$g = new GoogleAPI();
		$g->SetLatLong($lat,$long);
		return  $g;
	}
	public static function setDisctance($coArray,$unit){
		$g = new GoogleAPI();
		$g->setCoordinate($coArray,$unit);
		return  $g;
	}
	public static function setDirection($coArray){
		$g = new GoogleAPI();
		$g->setDirectionCo($coArray);
		return  $g;
	}
}
###################### END of GEO API ##############################################
?>