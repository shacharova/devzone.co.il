<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Localities extends App_Controller  {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('locality');
    }
    
    public function getByCountryAjax() {
        $this->input->allow_ajax_post();
        
        $countryId = & $this->input->post("countryId");
        $jsonString = NULL;
        
        if(is_numeric($countryId)) {
            $dbResult = & $this->locality->getByCountry($countryId);
            $jsonString = & json_encode($dbResult->queryResult);
        }
        
        return $this->output->set_content_type('application/json')->set_output($jsonString);
    }
}

