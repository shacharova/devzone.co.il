<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class App_privileges {
    
    /**
     * CodeIgniter instance
     * @var App_Controller
     */
    private $CI = null;
    
    public function __construct() {
        $this->CI = & get_instance();
    }
    
    /**
     * Recommended to call once only from 'post_controller_constructor' hook.
     * @see App_hooks
     */
    public function authControllerAction() {
        // TODO:
        // check if guest
        // Use GET u parameter to get current user id
        // User $this->user->isLoggedIn($userId) to check if user is logged in
        $path = sprintf("%s/%s", $this->CI->router->fetch_class(), $this->CI->router->fetch_method());
        
        switch ($path) {
            case 'users/emailVerifyAjax':
            case 'users/passwordVerifyAjax':
            case 'users/login':
            case 'welcome/index':
            default :
                return true;
        }
    }
}