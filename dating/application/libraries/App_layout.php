<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class APP_LayoutData
{
    /**
     * HTML tag attributes and theirs values.
     * @var array
     */
    public $html_attributes = array();
    /**
     * HTML page title
     * @var string
     */
    public $title = null;
    /**
     * Page meta data items (placed inside the HEAD tag)
     * @var array
     */
    public $meta_data_items = array();
    /**
     * CSS styles href and theirs version(optionally)
     * @var array
     */
    public $styles = array();
    /**
     * Javascript sources placed at the HEAD tag and theirs version(optionally).
     * @var array
     */
    public $head_scripts = array();
    /**
     * BODY tag attributes and theirs values and theirs version(optionally).
     * @var array
     */
    public $body_attributes = array();
    /**
     * Contains the views to display in the HEADER tag.
     * @var array
     */
    public $header_views = array();
    /**
     * Contains the views to display and (optionally) theirs data.
     * Note: Views will be display in the order are stored.
     * @var array
     */
    public $views = array();
    /**
     * Contains the views to display in the FOOTER tag.
     * @var array
     */
    public $footer_views = array();
    /**
     * Javascript sources placed at the end (inside) of BODY tag.
     * @var array
     */
    public $body_scripts = array();


    public function print_html_attributes() {
        foreach($this->html_attributes as $attr_name => &$attr_value) {
            printf('%s="%s" ', $attr_name, $attr_value);
        }
    }

    public function print_title() {
        printf("<title>%s</title>", $this->title);
    }

    public function print_meta_data() {
        foreach ($this->meta_data_items as $attr_name => &$meta_data_item) {
            foreach ($meta_data_item as $attr_value => &$content) {
                if (empty($content)) {
                    printf('<meta %s="%s">', $attr_name, $attr_value);
                } else {
                    printf('<meta %s="%s" content="%s">', $attr_name, $attr_value, (string)$content);
                }
            }
        }
    }

    public function print_styles() {
        foreach($this->head_scripts as $href => $version) {
            if(empty($version)) {
                echo link_tag($href);
            } else {
                echo link_tag("{$href}?v={$version}");
            }
        }
    }

    public function print_head_scripts() {
        foreach($this->head_scripts as $src => $version) {
            if(empty($version)) {
                printf('<script src="%s" type="text/javascript"></script>', $src);
            } else {
                printf('<script src="%s?v=%s" type="text/javascript"></script>', $src, $version);
            }
        }
    }

    public function print_body_attributes() {
        foreach($this->body_attributes as $attr_name => &$attr_value) {
            printf('%s="%s" ', $attr_name, $attr_value);
        }
    }

    public function print_views() {
        $ci =& get_instance();
        foreach($this->views as $file_path => &$data) {
            $ci->load->view($file_path, $data);
        }
    }

    public function print_body_scripts() {
        foreach($this->body_scripts as $src => $version) {
            if(empty($version)) {
                printf('<script src="%s" type="text/javascript"></script>', $src);
            } else {
                printf('<script src="%s?v=%s" type="text/javascript"></script>', $src, $version);
            }
        }
    }
}


/**
 * Implements HTML page layout which allows to define all the needed data,
 * allows to load views to be displayed and print the whole page(render).
 * #############################################################################
 * The following are what can be set:
 * HTML tab attributes, page title, meta data, CSS styles, scripts(JS) and views
 *
 * @version 1.0
 * @author Shachar
 */
class APP_Layout
{
    /**
     * Stores all the required data for render this layout.
     * @var APP_LayoutData
     */
    private $_data = null;

    public function __construct() {
        $this->_data = new APP_LayoutData();
    }


    #region Private Function
    #endregion



    #region Public Function
    /**
     * Set attribute and value to HTML tag
     * @param string $attr_name
     * @param mixed $attr_value
     */
    public function set_html_attribute($attr_name, $attr_value) {
        if(is_string($attr_name)) {
            $this->_data->html_attributes[$attr_name] = (string)$attr_value;
        }
    }

    /**
     * Set page title
     * @param string $title
     */
    public function set_title($title) {
        if(is_string($title)) {
            $this->_data->title = $title;
        }
    }

    /**
     * Set meta data item
     * @param string $attr_name
     * @param string $attr_value
     * @param mixed $content_value
     */
    public function set_meta_data($attr_name, $attr_value, $content_value = null) {
        if(is_string($attr_name) && is_string($attr_value)) {
            if(!array_key_exists($attr_name, $this->_data->meta_data_items)) {
                $this->_data->meta_data_items[$attr_name] = array();
            }
            $this->_data->meta_data_items[$attr_name][$attr_value] = $content_value;
        }
    }

    /**
     * Set css style.
     * @param string $file_path
     * @param string $version
     */
    public function set_style($file_path, $version = null) {
        if(is_string($file_path)) {
            $this->_data->styles[$file_path] = (string)$version;
        }
    }

    /**
     * Set script to be load at the HEAD tag.
     * @param string $file_path Script src
     * @param string $version (Optional) allows set file version to force cache clear (when version changed).
     */
    public function set_head_script($file_path, $version = null) {
        if(is_string($file_path)) {
            $this->_data->head_scripts[$file_path] = (string)$version;
        }
    }

    /**
     * Set attribute and value to BODY tag
     * @param string $attr_name
     * @param mixed $attr_value
     */
    public function set_body_attribute($attr_name, $attr_value) {
        if(is_string($attr_name)) {
            $this->_data->body_attributes[$attr_name] = (string)$attr_value;
        }
    }

    /**
     * Register view and optionally his data to be displayed inside the BODY tag.
     * @param string $file_path
     * @param array (optional) $data View data
     */
    public function set_view($file_path, array $data = null) {
        if(is_string($file_path)) {
            $this->_data->views[$file_path] = $data;
        }
    }

    /**
     * Set script to be load at the end of the BODY tag.
     * @param string $file_path Script src
     * @param string $version (Optional) allows set file version to force cache clear (when version changed).
     */
    public function set_body_script($file_path, $version = null) {
        if(is_string($file_path)) {
            $this->_data->body_scripts[$file_path] = (string)$version;
        }
    }

    /**
     * Render the whole layout
     * @param string $layout_file_name
     */
    public function render($layout_file_name = null) {
        if(is_string($layout_file_name)) {
            $ci =& get_instance();
            $ci->load->helper('html');
            $ci->load->view($layout_file_name, array('layout_data' => $this->_data));
        } else {
            $this->_data->print_views();
        }
    }
    #endregion
}