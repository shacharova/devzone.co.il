<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Message extends App_Model {

    /**
     * Message's record ID (in messages table).
     * @var int
     */
    public $id;

    /**
     * The content of this message.
     * @var string
     */
    public $content;

    /**
     * The ID of the user who sends the message.
     * @var int
     */
    public $source_user_id;

    /**
     * The ID of the user who receives the message.
     * @var int
     */
    public $target_user_id;

    /**
     * Creation date and time.
     * @var DateTime
     */
    public $created;

    /**
     * The date and time this message has been watched.
     * @var DateTime
     */
    public $watched;

    public function __construct() {
        parent::__construct();
    }

}
