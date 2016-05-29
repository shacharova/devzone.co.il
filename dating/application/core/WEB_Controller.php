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
     * Array of Viewdata models for all of the views to be display.
     * @var array
     */
    public $views = array();
    /**
     * Contains all scripts(js) to be locate in the end of BODY tag.
     * @var array
     */
    public $body_scripts = array();


    public function __construct() {
        parent::__construct();
        $this->init_client_language();
        $this->html_lang = $this->get_html_lang();
        $this->html_dir = $this->get_html_dir();
        $this->init_meta_data();

        // TODO: Checks what the following 3 lines doing exactly
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
    }   

    private function init_client_language() {
        // TODO: Implement init_client_language function
        // $this->config->set_item('language', $client_language);
    }
    private function get_html_lang() {
        switch($this->config->item('language')) {
            case 'hebrew':
                return 'iw';
            default:
            case 'english':
                return 'en';
        }
    }
    private function get_html_dir() {
        switch($this->config->item('language')) {
            case 'hebrew':
                return 'rtl';
            default:
            case 'english':
                 return 'ltr';
        }
    }
    private function init_meta_data() {
        $this->set_meta_data('charset', $this->config->item('charset'));
        $this->set_meta_data('name', 'viewport', 'width=device-width, initial-scale=1.0, maximum-scale=1.0001, user-scalable=yes');
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
    protected function set_meta_data($name, $value, $content = null) {
        if(is_string($name)) {
            $this->meta_data_items[] = new Metadata($name, $value, $content);
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
        if(is_string($file_name)) {
            $this->views[] = new Viewdata($file_name, $data);
        }
    }
    protected function set_body_script($src) {
        if(is_string($src)) {
            $this->body_scripts[] = $src;
        }
    }
    protected function render($layout_path = NULL) {
        if(is_string($layout_path)) {
            $this->load->view($layout_path);
        } else {
            foreach($this->views as &$view) {
                $this->load->view($view->file_name, $view->data);
            }
        }
    }
}