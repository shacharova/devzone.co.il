<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Ipv4 extends App_Model {

    public function __construct() {
        parent::__construct();
    }
    
    public function setForIPV4($networkPart1, $networkPart2, $hostPart1, $hostPart2, $locationId) {
        $inParams = array($networkPart1, $networkPart2, $hostPart1, $hostPart2, $locationId);
        return $this->app_db->callSP('common', 'setIPV4Location', true, $inParams, array('rowCount'));
    }
}
