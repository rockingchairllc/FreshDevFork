<?php
class GPlaceAPI {

	function getGeoCoords($address)
	{
		$coords = array();
		$address = utf8_encode($address);		
		// call geoencoding api with param json for output
		$geoCodeURL = "http://maps.google.com/maps/api/geocode/json?address=".urlencode(stripslashes($address))."&sensor=false";		
		$result = json_decode(@file_get_contents($geoCodeURL), true);    
		$coords['status'] = $result["status"];
		$coords['lat'] = $result["results"][0]["geometry"]["location"]["lat"];
		$coords['lng'] = $result["results"][0]["geometry"]["location"]["lng"];
		
		return $coords;
	}
	
	/*	
	function searchBusiness($category,$name,$location)
	{
		$coords = $this->getGeoCoords($location);
		if ($category == "All")
			$gPlaceURL = "https://maps.googleapis.com/maps/api/place/search/json?location=" . $coords['lat'] . "," . $coords['lng'] . "&radius=500&name=" . $name . "&sensor=false&key=AIzaSyAdndlByHtgVTERL45nF33Lr72qnw5V49I";					
		else
			$gPlaceURL = "https://maps.googleapis.com/maps/api/place/search/json?location=" . $coords['lat'] . "," . $coords['lng'] . "&radius=500&types=" . $category ."&name=" . $name . "&sensor=false&key=AIzaSyAdndlByHtgVTERL45nF33Lr72qnw5V49I";			
		$result = json_decode(file_get_contents($gPlaceURL), true);
		return $result;
	}
	*/
	
	function searchBusiness($name,$lat,$lng)
	{	
		$gPlaceURL = "https://maps.googleapis.com/maps/api/place/search/json?location=" . $lat . "," . $lng . "&radius=500&name=" . $name . "&sensor=false&key=AIzaSyAdndlByHtgVTERL45nF33Lr72qnw5V49I";		
		//echo $gPlaceURL;
		$result = json_decode(file_get_contents($gPlaceURL), true);
		return $result;
	}
	
	function getBusinessDetail($reference)
	{	
		//$gPlaceURL = "https://maps.googleapis.com/maps/api/place/search/json?location=" . $lat . "," . $lng . "&radius=500&name=" . $name . "&sensor=false&key=AIzaSyAdndlByHtgVTERL45nF33Lr72qnw5V49I";		
		$gPlaceURL = "https://maps.googleapis.com/maps/api/place/details/json?reference=" . $reference . "&sensor=false&key=AIzaSyAdndlByHtgVTERL45nF33Lr72qnw5V49I";
		//echo $gPlaceURL;
		$result = json_decode(file_get_contents($gPlaceURL), true);
		return $result;
	}
	
	function displayResult($result){
		
	}
	
	function getHttpResource($url){
		$curl_handle=curl_init();
		curl_setopt($curl_handle, CURLOPT_URL,$url);
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_handle, CURLOPT_USERAGENT, 'vumaster');
		$ret = curl_exec($curl_handle);
		curl_close($curl_handle);
		return $ret;
	}
}
?>