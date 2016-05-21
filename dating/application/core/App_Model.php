<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * @abstract
 * // CodeIgniter Classes:
 * @property App_Input $input CI_Input class.
 * @property CI_Lang $lang CI_Lang class.
 * @property App_Session $session APP_Session class.
 * // Libraries:
 * @property App_logs $app_logs Application mechanism of logs which write logs of events.
 * @property App_db $app_db Application mechanism of data-access to DB.
 * @property App_auth $app_auth Application authentication mechanism.
 * @property App_layout $app_layout Application Layout mechanism class instance.
 * @property App_privileges $app_privileges Application mechanism of action privileges.
 */
class App_Model extends CI_Model {
    
    protected $db_cache_data = array();
    /**
     * Codeigniter instance
     * @var \App_Controller
     */
    protected $CI = NULL;
    
    public function __construct() {
        parent::__construct();
        $this->CI = & get_instance();
    }
    
    public function generate_db_cache_key($dbName, $spName, array $inParams = array()) {
        return sprintf('%s_%s_%s', $dbName, $spName, implode('_', $inParams));
    }
}
