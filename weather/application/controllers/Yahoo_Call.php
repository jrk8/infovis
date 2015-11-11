<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pages extends CI_Controller {

	
	public function view($page = 'home', $town)
{
		
		//Title of Page
		$data['title'] = ucfirst($page); 
		$this->load->view('header', $data);
		$town = "Gillingham";
		$country = "UK";
		
		/* Load the Libraries we need for API/Database Calls
		   Initialise the URL Helper for native URL Functions
		   Run the function (within Weather_model.php) to get the forecasts from db
		   Load Header
		*/
		
		$this->load->model('weather_model');
		$this->load->helper('url_helper'); 
		$forecasts = $this->weather_model->get_current_yahoo_feeds($town);
		
		
		
		/*Checks if the DB Array returned is empty
		If so, get from the API itself	
		*/
		if (count($forecasts) <= 0)
		{
		
		echo "No Results <br>";

				$this->load->library('yahoo');
				$call = $this->yahoo->yahoo_call_town($town, $country);
				
				if($call == null)
				
				{
				//Check the API Result
				echo "Error with API Call!";
				
				}else{
				
				//Format Request and Forecast Date (function within Yahoo)
				$request_date = $call->query->created;
				$request_date = $this->yahoo->format_datetime($request_date);
				
				$forecast_date = $call->query->results->channel->item->forecast[0]->date;
				$forecast_date = $this->yahoo->format_datetime($forecast_date);
				
	
				//Send JSON Object to Database Model
				$this->weather_model->insert_current_yahoo_feeds($call, $forecast_date, $request_date);
				
				//Retrieve the newly inserted record from the Database
				$forecasts = $this->weather_model->get_current_yahoo_feeds($town, $country);
				
				foreach ($forecasts as $forecast):
				
				print_r($forecast);
				
				endforeach;

				
		}
		}else {
		
				echo "results found <br>";
				
				foreach ($forecasts as $forecast):
				
				print_r($forecast);
				
				endforeach;
				
		}
		
			
	
		
		$this->load->view('footer', $data);
		
		
		
}

}
