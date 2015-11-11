<?php 

	$town = "Gillingham";
		$country = "UK";

	$api = "https://query.yahooapis.com/v1/public/yql?q=";  
	$weather_query =rawurlencode('select ') . '*' .  rawurlencode(' from weather.forecast where woeid in');
	$location_query = ' (' . rawurlencode('select woeid from geo.places') . '(1)' . rawurlencode(' where text="' . $town . ' , ' . $country . '"') . ')';
	
$api_call = $api . $weather_query . $location_query . "&format=json";	

echo $api_call;

?>