<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Languages extends App_Controller {

    public function __construct() {
        parent::__construct();
    }

    // <editor-fold defaultstate="collapsed" desc="AJAX Functions">
    public function changeAjax() {
        $this->input->allow_ajax_post();
        $languageId = & $this->input->post('language_id');
        
        $error = NULL;
        $isSuccess = $this->lang->set_user_language($languageId, $error);

        $jsonString = & json_encode(array("success" => $isSuccess, 'error' => $error));
        return $this->output->set_content_type('application/json')->set_output($jsonString);
    }

    // </editor-fold>
}
