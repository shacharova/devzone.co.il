<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Extend the native CI_Email class
 *
 * @author Shachar
 */
class App_Email extends CI_Email {
    /**
     * CodeIgniter instance
     * @var App_Controller
     */
    private $CI = null;
    
    // <editor-fold defaultstate="collapsed" desc="Private Functions">
    // </editor-fold>
    // ~~
    // ~~
    // <editor-fold defaultstate="collapsed" desc="Public Functions">
    public function __construct($config = array()) {
        parent::__construct($config);
        $this->CI = & get_instance();
        $this->CI->lang->load('email', 'english');
    }
    
    public function send_verification_code($code, $toEmail) {
        $this->initialize(array('mailtype' => 'html'));
        $this->subject($this->CI->lang->line('verify_your_email_address'));
        $this->from($this->CI->config->item('app_info_email'), $this->CI->config->item('app_name'));
        $this->to($toEmail);
        
        $viewData = array(
            'code' => $code,
            'url' => base_url("users/email_verification/{$toEmail}/{$code}"),
            'direction' => $this->CI->_language->direction
        );
        $this->message($this->load->view('emails/email_verify', $viewData, TRUE));

        return $this->send();
    }
    // </editor-fold>
}

