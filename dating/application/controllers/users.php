<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Users extends App_Controller {

    const SESSION_KEY_VERIFIED_EMAIL = '_verified_email_';

    
    // <editor-fold defaultstate="collapsed" desc="Private Functions">
    /**
     * Trying to send an verification email.
     * @param string $email Target user's email
     * @param string $code
     * @return boolean True is email sent successfully. Else false.
     */
    private function sendVerificationEmail($email, $code) {
        $this->load->library('email');
        $this->email->initialize(array('mailtype' => 'html'));

        $this->email->subject($this->lang->line('verify_your_email_address'));
        $this->email->from($this->config->item('app_info_email'), $this->config->item('app_name'));
        $this->email->to($email);

        $viewData = array('code' => $code, 'url' => base_url("users/email_verification/{$email}/{$code}"),
            'direction' => $this->_language->direction);
        $this->email->message($this->load->view('emails/email_verify', $viewData, TRUE));

        return $this->email->send();
    }

    private function sendVerifySuccessEmail($email) {
        $this->load->library('email');
        $this->email->initialize(array('mailtype' => 'html'));

        $this->email->subject($this->lang->line('email_verify_success'));
        $this->email->from($this->config->item('app_info_email'), $this->config->item('app_name'));
        $this->email->to($email);

        $viewData = array('direction' => $this->_language->direction);
        $this->email->message($this->load->view('emails/email_verify_success', $viewData, TRUE));

        return $this->email->send();
    }
    // </editor-fold>


    public function __construct() {
        parent::__construct();
        $this->load->model('user');
        
        $this->set_jquery_validate();
        
        $this->app_layout->set_script('controllers/users', App_layout::APP_FILE_TYPE);
    }

    // <editor-fold defaultstate="collapsed" desc="Actions (GET) Functions">
    public function terms_of_use() {
        $this->app_layout->set_view("users/terms_of_use");
        $this->app_layout->render();
    }

    public function signup() {
        $this->set_jquery_ui();
        $this->app_layout->set_view('users/signup');
        $this->app_layout->render('default');
    }

    public function email_verification($email, $code) {
        $this->input->allow_get();

        $user = & $this->user->getDataByEmail($email);
        die(var_dump($user));
        if (!$user || !is_numeric($user->id)) {
            exit(0);
        }

        $recode = & $this->user->getEmailVerificationCode($user->id);
        if ($recode->code === $code) {
            $this->user->deleteVerificationEmail($user->id);
            $this->sendVerifySuccessEmail($email);
        }

        redirect('/');
    }

    public function login($email = '') {
        // TODO: $this->input->deny_ajax();
        $this->session->unset_userdata(static::SESSION_KEY_VERIFIED_EMAIL);

        $this->load->helper('email');
        if (!empty($email) && !valid_email($email)) {
            $email = '';
        }
        
        $hasLoggedInUsers = $this->app_auth->has_logged_in_user();
        $this->app_layout->set_view('users/login', array('hasLoggedInUsers' => $hasLoggedInUsers,
            'email' => $email));
        $this->app_layout->render();
    }

    public function select_logged_in() {
        $redirectURL = "/";
        $users = & $this->app_auth->get_logged_in_users();
        $this->app_layout->set_view('users/select_logged_in', array('redirectURL' => $redirectURL,
            'users' => $users));
        $this->app_layout->render();
    }

    public function login_help() {
        // TODO: Make abstract generic help funcionallity
        // TODO: Initial help process for login
        // TODO: Get result as data to display in view
        $this->app_layout->set_view('users/login_help');
        $this->app_layout->render();
    }

    // TODO: implement view
    public function preferences() {
        $this->app_layout->set_view('users/preferences');
        $this->app_layout->render();
    }

    public function personal_details() {
        $this->app_layout->set_style('font-awesome.min', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/');
        
        $this->set_jquery_ui();
        
        $this->load->model('country');
        $dbResult = & $this->country->getByCountryCode($this->_country->code);
        $countryId = '';
        if(!empty($dbResult->queryResult)) {
            $countryId = $dbResult->queryResult[0]->id;
        }
        
        $this->app_layout->set_view('users/personal_details', array('countryId' => $countryId));
        $this->app_layout->render();
    }

    public function debug() {
        $this->app_layout->set_view('debug');
        $this->app_layout->render();
    }

    // </editor-fold>
    // ~~
    // ~~
    // <editor-fold defaultstate="collapsed" desc="Post Functions">
    public function redirectLoggedInPost() {
        $this->input->allow_post();

        $email = & $this->input->post('email');
        $redirectURL = & $this->input->post('redirectURL');

        $u = FALSE;
        if ($this->app_auth->get_logged_in_user_by_email($email, $u) !== FALSE && $u !== FALSE) {
            redirect("{$redirectURL}/?u={$u}");
        } else {
            redirect("/");
        }
    }

    // </editor-fold>
    // ~~
    // ~~
    // <editor-fold defaultstate="collapsed" desc="AJAX Functions">
    public function signupAjax() {
        $this->input->allow_ajax_post();

        $isSuccess = FALSE;
        $redirectURL = FALSE;
        $messages = array();
        $email = & $this->input->post('email');

        $userId = $this->user->signup($email, $this->input->post('password'), $messages);

        if (is_numeric($userId)) {
            $code = & random_string('alnum', 8);
            $dbResult = & $this->user->setVerificationEmail($userId, $code);
            if (is_numeric($dbResult->getOutParameter('rowCount'))) {
                $this->sendVerificationEmail($email, $code);
            }

            if ($this->app_auth->login($email, FALSE, $messages) !== FALSE) {
                $redirectURL = '/'; // TODO: If success then get redirect URL from function
                $isSuccess = TRUE;
            }
        }

        $jsonString = & json_encode(array('isSuccess' => $isSuccess, 'messages' => $messages, 'redirectURL' => $redirectURL));
        return $this->output->set_content_type('application/json')->set_output($jsonString);
    }

    public function emailVerifyAjax() {
        $this->input->allow_ajax_post();

        $isSuccess = FALSE;
        $redirectURL = FALSE;
        $u = FALSE;
        $messages = array();
        $email = & $this->input->post('email');
        $user = & $this->app_auth->get_logged_in_user_by_email($email, $u);

        if (!empty($user) && is_int($u)) { // If user already logged in
            $redirectURL = & site_url("/?u={$u}");
        } else if ($this->user->emailVerify($email, $user, $messages)) {
            $this->app_auth->reset_auth_attempts_counter($email);
            $isSuccess = TRUE;
            $this->session->set_userdata(static::SESSION_KEY_VERIFIED_EMAIL, $email);
        }

        $jsonString = & json_encode(array('isSuccess' => $isSuccess, 'user' => $user,
                    'messages' => $messages, 'redirectURL' => $redirectURL));
        return $this->output->set_content_type('application/json')->set_output($jsonString);
    }

    public function passwordVerifyAjax() {
        $this->input->allow_ajax_post();

        $isSuccess = FALSE;
        $redirectURL = FALSE;
        $messages = array();
        $email = & $this->session->userdata(static::SESSION_KEY_VERIFIED_EMAIL); // Note: set in emailVerifyAjax function

        if ($this->app_auth->auth($email, $this->input->post('password'), $messages)) {
            $stayLoggedIn = $this->input->post('stay_logged_in') === "on";
            $u = & $this->app_auth->login($email, $stayLoggedIn, $messages);
            if (is_int($u)) {
                $this->session->unset_userdata(static::SESSION_KEY_VERIFIED_EMAIL);
                $isSuccess = TRUE;
                $redirectURL = & site_url("/?u={$u}");
            }
        }

        $jsonString = & json_encode(array('isSuccess' => $isSuccess, 'redirectURL' => $redirectURL,
                    'messages' => $messages));
        return $this->output->set_content_type('application/json')->set_output($jsonString);
    }

    public function logoutAjax() {
        $this->input->allow_ajax_post();

        $email = & $this->input->post('email');
        $isSuccess = & $this->app_auth->logout($email);

        $jsonString = & json_encode(array('isSuccess' => $isSuccess));
        return $this->output->set_content_type('application/json')->set_output($jsonString);
    }
    
    // </editor-fold>
}
