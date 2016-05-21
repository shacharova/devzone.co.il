<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Help extends App_Controller {
    public function __construct() {
        parent::__construct();
        $this->app_layout->set_script('controllers/help', App_layout::APP_FILE_TYPE);
    }
    
    public function users_login() {
        
    }
}

