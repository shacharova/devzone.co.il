<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Capabilities extends App_Controller {

    public function __construct() {
        parent::__construct();
    }
    // ~~
    // ~~
    // <editor-fold defaultstate="collapsed" desc="AJAX Functions">
    public function deleteOneAjax() {
        if ($this->_isAjax === true) {
            $id = $this->input->post('id');
            if (is_numeric($id)) {
                $this->load->model('capability');
                $isSuccess = $this->capability->deleteOne($id)->getOutParameter('rowCount') == 1;
            } else {
                $isSuccess = false;
            }

            echo json_encode(array('isSuccess' => $isSuccess));
        }
        die();
    }
    
    public function updateOneAjax() {
        if ($this->_isAjax === true) {
            $id = (int)$this->input->post('id');
            $name = $this->input->post('name');
            $description = $this->input->post('description');
            if (is_int($id) && !empty($name)) {
                $this->load->model('capability');
                $dbResult = $this->capability->updateOne($id, $name, $description);
                $isSuccess = $dbResult->getOutParameter('rowCount') === "1";
                $errors = $dbResult->error;
            }

            echo json_encode(array('isSuccess' => $isSuccess, 'errors' => $errors));
        }
        die();
    }
    
    public function createOneAjax() {
        if ($this->_isAjax === true) {
            $name = $this->input->post('name');
            $description = $this->input->post('description');
            if (!empty($name)) {
                $this->load->model('capability');
                $id = $this->capability->addOne($name, $description)->getOutParameter('id');
            }
            echo json_encode(array('id' => $id));
        }
        die();
    }
    
    public function getManyAjax() {
        if ($this->_isAjax === true) {
            $page = $this->input->post('page');
            $pageSize = $this->input->post('pageSize');

            $this->load->model('capability');
            $dbResult = & $this->capability->getMany($page, $pageSize);

            echo json_encode(array(
                "data" => $dbResult->queryResult,
                "total" => $dbResult->getOutParameter('totalRows')
            ));
        }
        die();
    }
    // </editor-fold>
}
