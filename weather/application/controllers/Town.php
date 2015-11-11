<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Town extends CI_Controller {

	public function index($page = 'London'){
	
	/*Default View, this shows the weather for a default location*/
	
				$town = ucfirst(urldecode($page));
				$country = "UK";
				$this->getHeader();
				$this->town_forecast($town, $country);
				$this->getFooter();
				
	}

	/* Display forecast from Database */
	public function town_forecast($town = FALSE, $country){
	
				$data['title'] = $town;
				/*Get Town from URL e.g weather/controller/method/argument{town}
				example - weather/Faversham = Town->yahoo($town), this is set in routes
				*/
				$data['town'] = $town;
				$data['forecasts'] = $this->weather_model->get_town_forecasts($town);
				
				/*Check the Database for results, if so display them*/
				if (count($data['forecasts']) > 0){
				$data['success_message'] = "Results from Database";
				
				/* To do, loop to get each source id */
				$source_id = $data['forecasts'][0]['source_id'];
				$data['source'] = $this->weather_model->source($source_id);
				$this->load->view('forecasts', $data);
				
				}else{
				//Handler for if no database tasks were found
				$data['success_message'] = "Error, No results found";
				$this->load->view('errors/databaseerror', $data, $town);}
	}
	

	/*Yahoo API Call, these methods will get the information 
	required to get the weather forecast for a given town, 
	using the API - the call is requested and handled in a 
	Library written by CO600 project group
	*/
	
	public function yahoo($page = 'London')
	{
			$this->getHeader();
			
			//Title of Page
			$town = ucfirst(urldecode($page));
			$country = "UK";
			
			//Get Town from URL
			$data['town'] = $town;
			
			$this->yahoo_town_call($town, $country, $page);
			$this->getFooter($data);
		
	}


	public function yahoo_town_call($town, $country) {
		
		$data['title'] = $town;
		$data['town'] = $town;
		
		$forecasts = $this->weather_model->get_current_yahoo_feeds($town);
		
		/*Checks if the DB Array returned is empty
		If so, get from the API itself - loads a custom library 
		/libraries/Yahoo_API.php	
		*/
		if (count($forecasts) <= 0)
		{
				/* Load the Libraries we need for API/Database Call
			   Run the function (within Weather_model.php) to get the forecasts from db
			   Load Header
				*/
			
				$this->load->library('yahoo_api');
				$call = $this->yahoo_api->yahoo_call_town($town, $country);

				$data['success_message'] = "Results from Database";
				
				if($call == null)
				
				{
					//Check the API Result, IF NULL - send error to view
					$this->load->view('errors/apierror', $data);
				
				}else{
						/*Change the town name to the one the API References 
						(as queried towns can change e.g. St Albans becomes St.Albans and
						Check the DB for the new Town name, to see if this gets a result. This 
						will stop duplicated data being entered.
						*/
						$town = $call->query->results->channel->location->city;
						$forecasts = $this->weather_model->get_current_yahoo_feeds($town);
						
						if (count($forecasts) <= 0)
						{
						
						
						//Format Function for JSON result from Yahoo (Returns Array)
						$findtown = $this->yahoo_api->formatLocationJSON($call);
						$findtown = $this->weather_model->get_town_id($findtown);
						$forecast = $this->yahoo_api->formatWeatherJSON($call, $findtown);
						
						
						//Send JSON Object to Database Model
						$this->weather_model->insert_current_yahoo_feeds($forecast); 
						
						$data['success_message'] = $town . " API forecast";
						
						}
						
						//Retrieve the newly inserted record from the Database
						$data['forecasts'] = $this->weather_model->get_current_yahoo_feeds($town, $country);
						
						if (count($data['forecasts']) > 0){
						//Send Data array to the view with source information
						$source_id = $data['forecasts'][0]['source_id'];
						$data['source'] = $this->weather_model->source($source_id);
						$data['town'] = $town;
						$this->load->view('forecasts', $data);}
						else{$this->load->view('errors/apierror', $data);}
																
		}
		}else {
				
				$data['title'] = $town;
				$data['forecasts'] = $this->weather_model->get_current_yahoo_feeds($town, $country);
				$source_id = $data['forecasts'][0]['source_id'];
				$data['source'] = $this->weather_model->source($source_id);
			
				$data['success_message'] = "Results from Database";
				$this->load->view('forecasts', $data);

		}
	
	
	}
	
	
	public function getHeader($data = FALSE) {
	
	/*Function to return the usual preloaded libraries and views*/
	
		$this->load->view('header', $data);
		$this->load->helper('url'); 
		$this->load->model('weather_model');
		
	}
	
	public function getFooter($data = FALSE) {
	
	/*Function to return the usual preloaded libraries and views*/
	
		$this->load->view('footer', $data);
	
	}

}
