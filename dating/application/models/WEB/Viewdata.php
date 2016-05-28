<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Metadata short summary.
 *
 * Metadata description.
 *
 * @version 1.0
 * @author Shachar
 */
class Viewdata extends CI_Model {
    /**
     * The view path & name relative to views folder.
     * @var string
     */
    public $file_name;
    /**
     * Contains all the data for this view.
     * @var mixed (Optional)
     */
    public $data;


    public function __construct($file_name = NULL, $data = NULL) {
        parent::__construct();
        $this->file_name = $file_name;
        $this->data = $data;
    }
}