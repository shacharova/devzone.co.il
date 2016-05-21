<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Welcome extends App_Controller {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        // $this->output->cache(1); // 1 Minute cache
        $this->app_layout->set_view('welcome/index');
        $this->app_layout->render();
    }
}

