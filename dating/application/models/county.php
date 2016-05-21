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
class County extends App_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Return all counties records
     * @return \App_DBSPResult
     */
    public function getCounties() {
        return $this->app_db->callSP('common', 'getCounties', true);
    }
    
    /**
     * Returns country according to its code (if exists).
     * @return \App_DBSPResult
     */
    public function getByCode($code) {
        return $this->app_db->callSP('common', 'getCountryByCode', true, array($code));
    }
}
