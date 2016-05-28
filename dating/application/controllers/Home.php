<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends WEB_Controller {

	public function index()
	{
        // TODO: complate root/index action
		$this->set_view('root/index');
        $this->render('WEB/layout');
	}

    public function about_us() {
        // TODO: root/about_us action
    }
    public function terms_of_use() {
        // TODO: root/terms_of_use action
    }
    public function login() {
        // TODO: root/login action
    }
    public function signup() {
        // TODO: root/signup action
    }
    public function questions_and_answers() {
        // TODO: root/questions_and_answers action
    }
    public function stories() {
        // TODO: root/stories action
    }


}
