<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Role extends App_Model {

    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Trying to add capability to role.
     * @param int $capabilityId Exists capability ID.
     * @param int $roleId Exists role ID.
     * @return \App_DBSPResult
     */
    public function addCapability($capabilityId, $roleId) {
        return $this->app_db->callSP('dating', 'addCapabilityToRole', true,
                             array($capabilityId, $roleId), array('rowCount'));
    }

    /**
     * Delete role record (if exists).
     * @param string|int $roleNameOrId Role name(string) or ID(int).
     * @return \App_DBSPResult
     */
    public function deleteOne($roleNameOrId) {
        if(is_numeric($roleNameOrId)) {
            return $this->app_db->callSP('dating', "deleteRole", true, array($roleNameOrId),
                             array('rowCount'));
        } else if(is_string($roleNameOrId)) {
            return $this->app_db->callSP('dating', "deleteRoleByName", true, array($roleNameOrId),
                             array('rowCount'));
        }
        
    }
    
    /**
     * Set (Insert|Update) role.
     * @param string $roleName
     * @param string $roleDescription
     * @return \App_DBSPResult
     */
    public function setOne($roleName, $roleDescription) {
        return $this->app_db->callSP('dating', 'setRole', true,
                                    array($roleName, $roleDescription),
                                    array('id'));
    }
    
    /**
     * Return an Role (if exists)
     * @param string|int $nameOrId Role name(string) or ID(int).
     * @return \App_DBSPResult
     */
    public function getOne($nameOrId) {
        if(is_numeric($nameOrId)) {
            return $this->app_db->callSP('dating', 'getRole', true, array($nameOrId));
        } else if(is_string($nameOrId)) {
            return $this->app_db->callSP('dating', 'getRoleByName', true, array($nameOrId));
        }
    }
    
    /**
     * Return roles page according to parameters and order by name(ASC).
     * @param int $page
     * @param int $pageSize
     * @return \App_DBSPResult
     */
    public function getMany($page, $pageSize) {
        return $this->app_db->callSP('dating', 'getRoles', true,
                array($page, $pageSize),
                array('totalRows'));
    }
}
