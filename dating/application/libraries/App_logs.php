<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class App_logs {
    
    /**
     * CodeIgniter instance
     * @var App_Controller
     */
    private $CI = null;
    
    public function __construct() {
        $this->CI = & get_instance();
    }

    public function writeLog($eventId, $text = "") {
        // TODO
        // return $this->CI->app_db->addLog($eventId, $text);
    }

}