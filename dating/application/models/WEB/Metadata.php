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
    public $attribute_name;
    public $attribute_value;
    public $content_value;


    public function __construct($attribute_name, $attribute_value, $content_value = NULL) {
        parent::__construct();
        $this->attribute_name = $attribute_name;
        $this->attribute_value = $attribute_value;
        $this->content_value = $content_value;
    }
}