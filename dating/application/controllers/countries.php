<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Countries extends App_Controller {

    const ISRAEL_ID = 82;
    
    public function __construct() {
        parent::__construct();
        $this->load->model('country');
    }
    
    public function getAll() {
        //$this->input->allow_ajax_post();
        
        $dbResult = & $this->country->getAll();
        $jsonString = & json_encode($dbResult->queryResult);
        
        return $this->output->set_content_type('application/json')->set_output($jsonString);
    }
    
    public function getAllGroupedByContinents() {
        $this->input->allow_ajax_post();
        
        $dbResult = & $this->country->getAllGroupedByContinents();
        $jsonString = & json_encode($dbResult->queryResult);
        
        return $this->output->set_content_type('application/json')->set_output($jsonString);
    }
}

