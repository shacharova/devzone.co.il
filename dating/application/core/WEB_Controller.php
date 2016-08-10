<?php
defined('BASEPATH') OR exit('No direct script access allowed');

abstract class TempName
{

}

/**
 * Web_Controller short summary.
 *
 * Web_Controller description.
 *
 * @version 1.0
 * @author Shachar
 */
abstract class Web_Controller extends CI_Controller
{
    #region Public Properties:
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
     * Array of Viewdata models for all of the views to be display.
     * @var array
     */
    public $views = array();
    /**
     * Contains all scripts(js) to be locate in the end of BODY tag.
     * @var array
     */
    public $scripts = array();
    #endregion


    public function __construct() {
        parent::__construct();

        // Set default charset:
        $this->set_meta_data('charset', $this->config->item('charset'));
        // Set default viewport:
        $this->set_meta_data('name', 'viewport', 'width=device-width, initial-scale=1.0, maximum-scale=1.0001, user-scalable=yes');

        // TODO: Checks what the following 3 lines doing exactly
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
    }


    #region Protected Functions:
    protected function set_language($language) {
        switch($language) {
            case 'hebrew':
                $this->html_dir = 'rtl';
                $this->html_lang = 'iw';
                break;
            case 'english':
            default:
                $this->html_dir = 'ltr';
                $this->html_lang = 'en';
                break;
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
    protected function set_script($src, $priority) {
        if(is_string($src) && is_numeric($priority)) {
            $this->scripts[$priority] = $src;
        }
    }

    protected function set_view($file_name, $data = NULL) {
        if(is_string($file_name)) {
            $this->views[] = new Viewdata($file_name, $data);
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
    #endregion
}