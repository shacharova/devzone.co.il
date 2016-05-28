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
class Metadata extends CI_Model {
    public $name;
    public $value;
    public $content;


    public function __construct($name = NULL, $value = NULL, $content = NULL) {
        parent::__construct();
        $this->name = $name;
        $this->value = $value;
        $this->content = $content;
    }
}