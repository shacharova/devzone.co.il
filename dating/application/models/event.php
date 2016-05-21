<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Event extends App_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Delete album (if exists) by his ID.
     * @param int $albumId Album ID.
     * @return \App_DBSPResult
     */
    public function deleteOne($albumId) {
        return $this->app_db->callSP('dating', 'deleteEvent', true, array($albumId),
                                    array('rowCount'));
    }

    /**
     * Return page(block) of events.
     * @param int $page Page number.
     * @param int $pageSize Page size.
     * @return \App_DBSPResult
     */
    public function getMany($page, $pageSize) {
        return $this->app_db->callSP('dating', 'getEvents', true, array($page, $pageSize),
                                    array('totalRows'));
    }

    /**
     * Set (Insert|Update) event record on events table.
     * @param string $eventName
     * @param string $eventDescription
     * @return \App_DBSPResult
     */
    public function setOne($eventName, $eventDescription) {
        return $this->app_db->callSP('dating', 'setEvent', true,
                                    array($eventName, $eventDescription),
                                    array('id'));
    }

}
