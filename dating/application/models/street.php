<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Street extends App_Model {

    public function __construct() {
        parent::__construct();
    }
    
    public function getMany($countryId, $localityId) {
        $inParams = array($countryId, $localityId);
        return $this->app_db->callSP('common', 'getStreets', true, $inParams);
    }
}
