<?php 


Class Yahoo {



function yahoo_call_town($town, $country) {

	/*$api = "https://query.yahooapis.com/v1/public/yql?q=";  
	$weather_query =rawurlencode('select ') . '*' .  rawurlencode(' from weather.forecast where woeid in');
	$location_query = ' (' . rawurlencode('select woeid from geo.places') . '(1)' . rawurlencode(' where text="' . $town . ' , ' . $country . '"') . ')';
	$api_call = $api . $weather_query . $location_query . "&format=json";*/

	$query = "https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20weather.forecast%20where%20woeid%20in%20(select%20woeid%20from%20geo.places(1)%20where%20text%3D%22".$town."%2C%20".$country."%22)%20and%20u%20%3D%27c%27&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys";


	$weatherfeed = file_get_contents($query);

	$jsonObject = json_decode($weatherfeed);

				if ($jsonObject->query->results == null)
				{
					$jsonObject = null;
					return $jsonObject;

				}else{return json_decode($weatherfeed);}

}


function format_datetime($date) {

	$date = new Datetime($date);
	$date = $date->format('Y-m-d H:i:s');
	return $date;

}


function format_date($date) {

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




}

?>