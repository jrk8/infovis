<?php

class Yahoo {



	public function getAllTowns()
{

	require_once('Logging.php');
	include 'db_connect.php';
	
	date_default_timezone_set('GMT');
	$query = "SELECT town_name, country from town ORDER BY town_name";
	$date = date('Y-m-d H:i:s');
	
	/* Create Log file */
	fopen("c:\xampp\htdocs\weather\automated\log\yahoo-api-download.txt", "w");
	
	
	// Logging class initialization
	$log = new Logging();
		 
	// set path and name of log file (optional)
	$log->lfile('c:\xampp\htdocs\weather\automated\log\yahoo-api-download.txt');
	$log->lwrite('Script Started at: ' . $date );
	
	if ($result = $dbcon->query($query)) {

    /* fetch associative array */
    while ($row = $result->fetch_assoc()) {
      
	  $town = $row['town_name'];
      $country = 'UK';
	  
	  echo $town . "\n";
	 
	 $query = "https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20weather.forecast%20where%20woeid%20in%20(select%20woeid%20from%20geo.places(1)%20where%20text%3D%22".rawurlencode($town)."%2C%20".$country."%22)%20and%20u%20%3D%27c%27&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys";


			$weatherfeed = file_get_contents($query);
			$weatherfeed = json_decode($weatherfeed);
		
			if ($weatherfeed->query->results == null || $weatherfeed->query->results->channel->location->city == "United Kingdom" )
				{
					$weatherfeed = null;
					
						// write message to the log file
						$log->lwrite('API Fail for Town: ' . $row['town_name']);


				}else{

							$request_date 	= new Datetime($weatherfeed->query->created);
							$request_date 	= $request_date->format('Y-m-d H:i:s');
							$fdate 			= new Datetime($weatherfeed->query->results->channel->item->forecast[0]->date);
							$forecast_date 	= $fdate->format('Y-m-d');
							$feed_url 		= $weatherfeed->query->results->channel->link;
							$town 			= $weatherfeed->query->results->channel->location->city;
							$postcode	    = null;
							$min_temp	 	= $weatherfeed->query->results->channel->item->forecast[0]->low;
							$max_temp 		= $weatherfeed->query->results->channel->item->forecast[0]->high;
							$avg_temp 		= $weatherfeed->query->results->channel->item->condition->temp;
							$wind_dir 		= $weatherfeed->query->results->channel->wind->direction;
							$conditions 	= $weatherfeed->query->results->channel->item->condition->text;
							$source_id	 	= 1;
							
							$second_date		= new Datetime($weatherfeed->query->results->channel->item->forecast[1]->date);
							$second_date		= $second_date->format('Y-m-d');
							$second_mintemp		= $weatherfeed->query->results->channel->item->forecast[1]->low;
							$second_maxtemp		= $weatherfeed->query->results->channel->item->forecast[1]->high;
							$second_conditions	= $weatherfeed->query->results->channel->item->forecast[1]->text;
							$second_avg			= ($second_maxtemp / 2) + ($second_mintemp / 2);

			}
			
			$presentforecast = "INSERT INTO weather_forecast (request_date, forecast_date, feed_url, town, postcode_prefix, min_temp, max_temp, avg_temp, wind_dir, conditions, source_id)
			VALUES ('$request_date', '$forecast_date', '$feed_url', '$town', '$postcode', '$min_temp', '$max_temp', '$avg_temp', '$wind_dir', '$conditions', '$source_id')";
			
			$secondforecast = "INSERT INTO weather_forecast (request_date, forecast_date, feed_url, town, postcode_prefix, min_temp, max_temp, avg_temp, wind_dir, conditions, source_id)
			VALUES ('$request_date', '$second_date', '$feed_url', '$town', '$postcode', '$second_mintemp', '$second_maxtemp', '$second_avg', null, 'second_conditions', '$source_id')";

			if ($dbcon->query($presentforecast) === TRUE) {
				echo "New first created successfully \n";
			if ($dbcon->query($secondforecast) === TRUE) {
				echo "New second forecast created successfully \n\n";
			 }
			 else {
				echo "Error: \n" . $dbcon->error;
			}
				
			} else {
				echo "Error: \n" . $dbcon->error;
			}
			
			
	
	  
	   
    }


$log->lwrite('Script Run on: '. $date);

// close log file
$log->lclose();
$dbcon->close();	
		
}

}


	
	




}


$yahoo = new Yahoo;
$location = $yahoo->getAllTowns();





