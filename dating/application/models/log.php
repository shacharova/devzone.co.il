<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Log extends App_Model {

    /**
     * Log's record ID (in logs table).
     * @var int
     */
    public $id;

    /**
     * The ID of the event related to the log.
     * @var int
     */
    public $event_id;

    /**
     * Optional text which specify about the log's event.
     * @var string
     */
    public $text;

    /**
     * Creation date and time.
     * @var DateTime
     */
    public $created;

    public function __construct() {
        parent::__construct();
    }

    /**
     * Trying to add event log with additional optional text.
     * @param int $eventId Exists event ID.
     * @param string $text Additional specific description.
     * @return \App_DBSPResult
     */
    public function add($eventId, $text = null) {
        return $this->app_db->callSP('dating', 'addLog', true, array($eventId, $text),
                             array('id'));
    }

    /**
     * Get logs records according to parameters.
     * @param int $eventId
     * @param string $text
     * @param DateTime|string|null $fromDate
     * @param DateTime|string|null $toDate
     * @param int $page
     * @param int $pageSize
     * @return \App_DBSPResult
     */
    public function getMany($eventId, $text, $fromDate, $toDate, $page, $pageSize) {
        return $this->app_db->callSP('dating', 'getLogs', false,
                                    array($eventId, $text, $fromDate, $toDate, $page, $pageSize),
                                    array('totalRows'));
    }

}
