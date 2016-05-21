<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}


class Manage extends App_Controller {

    public function __construct() {
        parent::__construct();
        $this->app_layout->set_style('kendo/rtl.min');
        $this->app_layout->set_style('kendo/common.min');
        $this->app_layout->set_style('kendo/default.min');

        $this->app_layout->set_script('kendo/all.min', App_layout::LIB_FILE_TYPE);
        $this->app_layout->set_script('kendo/web.min.intellisense', App_layout::LIB_FILE_TYPE);
        if($this->_language->code !== "en") {
            $this->app_layout->set_script("kendo/cultures/{$this->_language->code}.min", App_layout::LIB_FILE_TYPE);
        }
        $this->app_layout->set_script('controllers/manage', App_layout::APP_FILE_TYPE);
    }

    // <editor-fold defaultstate="collapsed" desc="Actions Functions">
    public function index() {
        $this->app_layout->set_view('manage/index');
        $this->app_layout->render();
    }

    public function users() {
        $this->app_layout->set_view('manage/users');
        $this->app_layout->render();
    }

    public function roles() {
        $this->app_layout->set_view('manage/menu');
        $this->app_layout->set_view('manage/roles');
        $this->app_layout->render();
    }

    public function capabilities() {
        $this->app_layout->set_view('manage/menu');
        $this->app_layout->set_view('manage/capabilities');
        $this->app_layout->render();
    }
    // </editor-fold>
    // ~~
    // ~~
    // <editor-fold defaultstate="collapsed" desc="Partial Views - FOR DEBUG & DEVELOPMENT">
    public function menu() {
        $this->app_layout->set_view('manage/menu');
        $this->app_layout->render();
    }
    // </editor-fold>
    // ~~
    // ~~
    // <editor-fold defaultstate="collapsed" desc="AJAX Functions">
    
    // </editor-fold>
}
