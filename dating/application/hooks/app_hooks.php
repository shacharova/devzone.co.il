<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Handle hooks.
 * @link http://www.codeigniter.com/user_guide/general/hooks.html More information about hooks
 */
class App_hooks {
    /**
     * CodeIgniter instance
     * @var App_Controller
     */
    private $CI = null;

    public function __construct() {
        $this->CI = & get_instance();
    }

    /**
     * Hooked to 'post_controller_constructor'.
     * @see /application/config/hooks.php file.
     */
    public function post_controller_constructor() {
        if($this->CI->app_privileges->authControllerAction()) {
            // TODO
        }
    }

}
