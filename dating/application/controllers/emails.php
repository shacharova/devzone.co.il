<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class emails extends App_Controller {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function email_verify() {
        $data = array(
            'code' => 'qazwsxedc',
            'url' => '',
            'direction' => $this->_language->direction
        );
        
        $this->app_layout->set_view('emails/email_verify', $data);
        $this->app_layout->render('email');
    }
    
    public function email_verify_success() {
        $data = array('direction' => $this->_language->direction);
        
        $this->app_layout->set_view('emails/email_verify_success', $data);
        $this->app_layout->render('email');
    }
}

