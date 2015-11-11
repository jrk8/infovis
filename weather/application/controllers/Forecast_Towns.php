<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class Forecast_Towns extends CI_Controller {


public function view($page = 'towns') {


		$data['title'] = $page; 
		$this->load->view('header', $data);
		$town = "Gillingham";
		$country = "UK";

}




}
?>