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
        $this->init_language_html_attributes();
        $this->init_meta_data();
    }

    private function init_client_language() {
        // TODO: Implement init_client_language function
        // $this->config->set_item('language', $client_language);
    }
    private function init_language_html_attributes() {
        // Note: When there will be more then few languages then saparete this code
        //       to two switch-case blocks (the first for 'lang' and the second for 'dir')
        switch($this->config->item('language')) {
            case 'hebrew':
                $this->html_lang = 'he';
                $this->html_dir = 'rtl';
                break;
            default:
            case 'english':
                $this->html_lang = 'en';
                $this->html_dir = 'ltr';
                break;
        }
    }
    private function init_meta_data() {
        //$this->set_meta_data('charset', 'UTF-8');
        //$this->set_meta_data('http-equiv', 'Content-Type', 'text/html; charset=UTF-8');
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