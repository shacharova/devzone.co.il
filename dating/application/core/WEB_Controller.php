<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * WEB_Controller short summary.
 *
 * WEB_Controller description.
 *
 * @version 1.0
 * @author Shachar
 */
abstract class WEB_Controller extends CI_Controller
{
    /**
     * HTML tag 'lang' attribute value.
     * @var string
     */
    public $html_lang;
    /**
     * HTML tag 'dir' attribute value.
     * @var string
     */
    public $html_dir;
    /**
     * Meta data items.
     * @var mixed
     */
    public $meta_data_items = array();
    /**
     * HTML page title.
     * @var string
     */
    public $page_title;
    /**
     * Contains all CSS styles hrefs.
     * @var array Array of strings
     */
    public $css_hrefs = array();
    /**
     * Contains all scripts(js) to be locate in the HEAD tag.
     * @var array
     */
    public $head_scripts = array();
    /**
     * Contains all scripts(js) to be locate in the end of BODY tag.
     * @var array
     */
    public $body_scripts = array();


    public function __construct() {
        parent::__construct();
        // TODO: set lang
        // TODO: set dir
        // TODO: set meta data common to all pages
    }

    protected function set_html_lang($lang) {
        if(is_string($lang)) {
            $this->html_lang = $lang;
        }
    }
    protected function set_html_dir($dir) {
        if(is_string($dir)) {
            $this->html_dir = $dir;
        }
    }
    protected function set_meta_data($attribute_name, $attribute_value, $content_value = null) {
        if(is_string($attribute_name)) {
            $this->meta_data_items[] = new Metadata($attribute_name, $attribute_value, $content_value);
        }
    }
    protected function set_page_title($page_title) {
        if(is_string($page_title)) {
            $this->page_title = $page_title;
        }
    }
    protected function set_style($href) {
        if(is_string($href)) {
            $this->css_hrefs[] = $href;
        }
    }
    protected function set_head_script($src) {
        if(is_string($src)) {
            $this->head_scripts[] = $src;
        }
    }
    protected function set_view($file_name, $data = NULL) {
        
    }
    protected function set_body_script($src) {
        if(is_string($src)) {
            $this->body_scripts[] = $src;
        }
    }

}