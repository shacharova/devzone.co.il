<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Locations extends App_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('location');
    }
}

