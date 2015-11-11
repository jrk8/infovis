<?php
class Weather_model extends CI_Model {



        public function __construct()
        {
                $this->load->database();
        }
		
		
		public function get_town_id($call){
		
		/*
		
		This function is used to return the town_id from the API Query
		It works by using the API Call, asking the database whether this town exists
		If not, it will then check if the region the town is in and the country exist
		It then inserts the new town, region and country into the database
		Returns array of town information (inc id)
		*/
		
		$town = $call['town_name'];
		$region_name = 'region_name';
		$country_name = 'country';
		
		$query = $this->db->get_where('town', array('town_name' => $town));	
		$result = $query->result_array();
		$region = $call['region_name'];
		$country = $call['country'];
		
		$country_string = 'United Kingdom';
		
		$region_exists = $this->does_exist($region_name, $region, $table = "region");
		$country_exists = $this->does_exist($country_name, $country, $table = "country");
		
				if (count($result) <= 0) {
				
						if ($region_exists != true){
						$this->db->insert('region', array('region_name' => $region));}
						if ($country_exists != true)
						{$this->db->insert('country', array('country' => $country, 'country_string' => $country_string));}
						
					$this->db->insert('town',$call);
					$query = $this->db->get_where('town', array('town_name' => $town));	
					return $query->result_array();
				} else {
				return $query->result_array();}
		
		}
		
		public function source ($id = FALSE) {
		
		/*
		This function returns an array of the provider
		This is used for getting copyright etc
		Return Array (information from provider)
		*/
	
			$query = $this->db->get_where('feed_provider', array('provder_id' => $id));	
			return $query->result_array();
			
			}
			
		public function does_exist($column, $value, $table)
		{
		
		/*
		This function checks whether the given
		value exists within the database and gives 
		a true or false result. The Params are 
		
		@column = table column you are searching e.g town_name
		@value = value you are trying to get 
		@table = Table name from the DB
		*/
		
			$this->db->where($column , $value);
			$query = $this->db->get($table);
			if ($query->num_rows() > 0){
				return true;
			}
			else{
				return false;
			}
		}	
		
		
		public function get_town_forecasts($town = FALSE){
		
		/*
		This function returns an array of forecasts 
		for the given town name (joins the weather_data and town)
		*/
		
				date_default_timezone_set('GMT');
				$date = date('Y-m-d');
				$this->db->select('*');
				$this->db->from('weather_data');
				$this->db->join('town', 'weather_data.town_id = town.town_id');
				$this->db->where('town.town_name', $town);
				$this->db->where('weather_data.weather_date', $date);
				$q = $this->db->get();
				return $q->result_array();
		
		}
		
		
		
			/*YAHOO API DATABASE CALLS*/
		
			public function get_current_yahoo_feeds($town = FALSE) {
			
				date_default_timezone_set('GMT');
				$date = date('Y-m-d');
				
				$this->db->select('*');
				$this->db->from('weather_data');
				$this->db->join('town', 'weather_data.town_id = town.town_id');
				$this->db->where('town.town_name', $town);
				$this->db->where('weather_data.weather_date', $date);
				$q = $this->db->get();
				
				return $q->result_array();
				
			
			}	
			
			/*YAHOO API DATABASE CALLS*/
		
			public function get_like_yahoo_feeds($town = FALSE) {
			
			/*This Function needs thinking about - if the requesting doesn't exist using the
			'when' constraint, maybe we should match the town using a 'LIKE' cause
			*/
				date_default_timezone_set('GMT');
				$date = date('Y-m-d');
				
				$query = $this->db->like('town', $town);
				$query = $this->db->get_where('weather_data', array('weather_date' => $date, 'source_id' => 1));
				return $query->result_array();
				
			
			}	
			

		

			public function insert_current_yahoo_feeds($forecast = FALSE) {
							
							return $this->db->insert('weather_data', $forecast);

				}
		
		
		
		
	
	
		
		
}