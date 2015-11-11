<?php 


Class Yahoo_API {



function yahoo_call_town($town, $country) {

	$BASE_URL = "http://query.yahooapis.com/v1/public/yql";
    $yql_query = 'select * from weather.forecast where woeid in (select woeid from geo.places(1) where text="'.$town.','.$country.'") and u = "c"';
    $yql_query_url = $BASE_URL . "?q=" . urlencode($yql_query) . "&format=json";

	$connected = @fsockopen($yql_query_url, 80); 
    echo $connected;
	
	 // Make call with cURL
    $session = curl_init($yql_query_url);
	
    curl_setopt($session, CURLOPT_RETURNTRANSFER,true);
	$httpCode = get_headers($yql_query_url, 1);
	//Redo!
	if ($httpCode == "HTTP/1.0 200 OK") { 
	
	$jsonObject = null;
	return $jsonObject;
	
	} else {
	
	
    $weatherfeed = curl_exec($session);

	$jsonObject = json_decode($weatherfeed);
	
				if ($jsonObject->query->results == null || $jsonObject->query->results->channel->location->city == "United Kingdom")
				{
					$jsonObject = null;
					return $jsonObject;

				}else{return json_decode($weatherfeed);}
			}

}


function format_datetime($call) {
	date_default_timezone_set('GMT');
	$date = $call->query->created;
	$date = new Datetime($date);
	$date = $date->format('Y-m-d H:i:s');
	return $date;

}


function format_date($call) {
	date_default_timezone_set('GMT');
	$date = $call->query->results->channel->item->forecast[0]->date;
	$date = new Datetime($date);
	$date = $date->format('Y-m-d');
	return $date;

}


function api_call($call)
{

	echo "No Results";
				
				$this->load->library('yahoo');
				$call = $this->yahoo->yahoo_call_town($town);
				
				//Format Request and Forecast Date (function within Yahoo)
				$request_date = $call->query->created;
				$request_date = $this->yahoo->format_datetime($request_date);
				
				$forecast_date = $call->query->results->channel->item->forecast[0]->date;
				$forecast_date = $this->yahoo->format_datetime($forecast_date);
				
				//Send JSON Object to Database Model
				$this->weather_model->insert_current_yahoo_feeds($call, $forecast_date, $request_date);

}


function formatWeatherJSON ($call = FALSE, $towninfo) {
	
	/*
	Format Engine: Array to format the JSON object for Yahoo
	Weather Information
	*/
	$request_date = $this->format_datetime($call);
	$forecast_date = $this->format_date($call);

	
	$data = array(
						'town_id' => $towninfo[0]['town_id'],
						'source_id' => 1,
						'weather_date' => $forecast_date,
						'min_temp' => $call->query->results->channel->item->forecast[0]->low,
						'max_temp' => $call->query->results->channel->item->forecast[0]->high,
						'avg_temp' => $call->query->results->channel->item->condition->temp,
						'wind_dir' => $call->query->results->channel->wind->direction,
						'conditions' => $call->query->results->channel->item->forecast[0]->text,
						'other' => 'other'
					);
					
	return $data; 					


}

function formatLocationJSON ($call = FALSE) {
	
	/*
	Format Engine: Array to format the JSON object for Yahoo
	Location Information
	*/
	
	$town 	= $call->query->results->channel->location->city;
	$lon 	= $call->query->results->channel->item->long;
	$lat 	= $call->query->results->channel->item->lat;
	
	//Check if the region is empty (sometimes it is empty, if so fall back to town
	if (empty($call->query->results->channel->location->region))
	{$region = $call->query->results->channel->location->city;}
	else {$region = $call->query->results->channel->location->region;}
	
	$coun   = $call->query->results->channel->location->country;
	
	$data = array(
						'town_name' => $town,
						'lon' => $lon,
						'lat' => $lat,
						'country' => $coun,
						'region_name' => $region
					);
					
	return $data; 					


}



 






}

?>