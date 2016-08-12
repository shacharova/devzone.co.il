<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Root extends Web_Controller {
    public function __construct() {
        parent::__construct();
        $this->set_script("")
    }


    public function about_us() {
        // TODO: root/about_us action
    }
    public function connect_us() {
        // TODO: root/connect_us action
    }
    public function index()
	{
        // TODO: complete root/index action
		$this->set_view('root/index');
        $this->render();
	}
    public function login() {
        // TODO: root/login action
    }
    public function questions_and_answers() {
        // TODO: root/questions_and_answers action

    }
    public function signup() {
        // TODO: root/signup action
    }
    public function stories() {
        // TODO: root/stories action
    }
    public function terms_of_use() {
        // TODO: root/terms_of_use action
    }

}
