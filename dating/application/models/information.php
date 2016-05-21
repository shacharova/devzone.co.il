<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Information extends App_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Delete information record (if exists).
     * @param string|int $infoNameOrId Information key name(string) or ID(int).
     * @return \App_DBSPResult
     */
    public function deleteOne($infoNameOrId) {
        if(is_int($infoNameOrId)) {
            return $this->app_db->callSP('dating', 'deleteInformation', true,
                                    array($infoNameOrId), array('rowCount'));
        } else if(is_string($infoNameOrId)) {
            return $this->app_db->callSP('dating', 'deleteInformationByName', true,
                                    array($infoNameOrId), array('rowCount'));
        }
        
    }

    /**
     * Get information value (if exists).
     * @param string|int $infoNameOrId Information key name(string) or ID(int).
     * @return \App_DBSPResult
     */
    public function getOne($infoNameOrId) {
        if(is_numeric($infoNameOrId)) {
            return $this->app_db->callSP('dating', 'getInformation', true, array($infoNameOrId));
        } else if(is_string($infoNameOrId)) {
            return $this->app_db->callSP('dating', 'getInformationByName', true, array($infoNameOrId));
        }
    }

    /**
     * Set (Insert|Update) information record on information table.
     * @param string $infoName
     * @param string $infoValue
     * @return \App_DBSPResult
     */
    public function setOne($infoName, $infoValue) {
        return $this->app_db->callSP('dating', 'setInformation', true,
                                    array($infoName, $infoValue),
                                    array('rowCount'));
    }

}
