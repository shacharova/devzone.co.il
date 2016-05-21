<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Capability extends App_Model {

    /**
     * Capability's record ID (in capabilities table).
     * @var int
     */
    public $id;

    /**
     * The name of the capability.
     * @var string
     */
    public $name;

    /**
     * Describes the capability.
     * @var string
     */
    public $description;

    public function __construct() {
        parent::__construct();
    }

    /**
     * Add new capability.
     * @param string $name Capability name.
     * @param string $description (Optional) Capability description.
     * @return \App_DBSPResult
     */
    public function addOne($name, $description = null) {
        return $this->app_db->callSP('dating', "addCapability", true,
                                    array($name, $description),
                                    array('id'));
    }
    
    /**
     * Delete capability (if exists) by capability ID.
     * @param int $capabilityId Capability ID.
     * @return \App_DBSPResult
     */
    public function deleteOne($capabilityId) {
        return $this->app_db->callSP('dating', 'deleteCapability', true,
                                    array($capabilityId,), array('rowCount'));
    }

    /**
     * Set (Insert|Update) capability record on capabilities table.
     * @param string $capabilityName Capability name.
     * @param string $capabilityDescription Capability description.
     * @return \App_DBSPResult
     */
    public function setOne($capabilityName, $capabilityDescription) {
        // clear getMany keys
        return $this->app_db->callSP('dating', 'setCapability', true,
                                    array($capabilityName, $capabilityDescription),
                                    array('rowCount'));
    }

    /**
     * Return capabilities page according to parameters and order by name(ASC).
     * @param int $page
     * @param int $pageSize
     * @return \App_DBSPResult
     */
    public function getMany($page, $pageSize) {
        $cacheKey = & $this->generate_db_cache_key('dating', 'getCapabilities', func_get_args());
        $dbResult = & $this->CI->cache->get($cacheKey);
        
        if(empty($dbResult)) {
            $dbResult = & $this->app_db->callSP('dating', 'getCapabilities', true,
                                    array($page, $pageSize), array('totalRows'));
            if(!empty($dbResult) && empty($dbResult->error)) {
                $this->CI->cache->save($cacheKey, $dbResult, 60);
            }
        }
        
        return $dbResult;
    }

}
