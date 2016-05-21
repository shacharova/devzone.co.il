<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class LayoutView {

    /**
     * View name
     * @var string
     */
    public $name;

    /**
     * Parameters for the view
     * @var array with parameters (optionaly)
     */
    public $params;

    /**
     * Decide if parameters will be deleted right after render this view.
     * @var boolean True for delete. Else false.
     */
    public $is_clear_params;

    public function __construct($name, $params = array(), $is_clear_params = FALSE) {
        $this->name = $name;
        $this->params = $params;
        $this->is_clear_params = $is_clear_params;
    }

}

class App_layout {
    const LIB_FILE_TYPE = 1;
    const APP_FILE_TYPE = 2;
    
    
    /**
     * CodeIgniter instance
     * @var App_Controller
     */
    private $CI = NULL;
    private $title = NULL;
    private $base_url = NULL; // For <base> tag
    private $meta_data_items = array();
    private $js_urls = array();
    private $scripts = array();
    private $css_url = NULL;
    private $styles = array();
    private $header_views = array();
    private $content_views = array();
    private $footer_views = array();
    private $html_attributes = array();

    // <editor-fold defaultstate="collapsed" desc="Private Functions">   
    /**
     * Return default title which is "[CONTROLLER_NAME] [ACTION_NAME]".
     * @return string
     */
    private function _get_default_title() {
        return "{$this->CI->router->fetch_class()} {$this->CI->router->fetch_method()}";
    }
    
    /**
     * Return default javascript url accurding to file type.
     * @param int $type File type
     * @return string
     */
    private function _get_default_js_url($type = App_layout::APP_FILE_TYPE) {
        if(array_key_exists($type, $this->js_urls)) {
            return $this->js_urls[$type];
        }
        return $this->js_urls[App_layout::APP_FILE_TYPE];
    }

    /**
     * Clear cached view data by replace values with NULL value.
     * 
     * @param array &$data_array View data array
     */
    private function _clear_view_data(&$data_keys) {
        foreach ($data_keys as $key) {
            $this->CI->load->vars(array($key => NULL));
        }
    }
    
    /**
     * Load views
     * @param array $views Array of LayoutView
     */
    private function _e_views(array &$views) {
        foreach ($views as $view) {
            $this->CI->load->view($view->name, $view->params);
            if ($view->is_clear_params === true) {
                $this->_clear_view_data(array_keys($view->params));
            }
        }
    }
    // </editor-fold>
    // ~~
    // ~~
    // <editor-fold defaultstate="collapsed" desc="Public Functions">
    /**
     * Create the applicaion layout instance.
     * Note: Default base paths:
     *  scripts from: $this->CI->config->item('js_url')
     *  styles from: $this->CI->config->item('css_url')
     */
    public function __construct() {
        $this->CI = & get_instance();
        $this->js_urls[static::APP_FILE_TYPE] = $this->CI->config->item('js_url');
        $this->js_urls[static::LIB_FILE_TYPE] = $this->CI->config->item('common_js_url');
        $this->css_url = $this->CI->config->item('css_url');
    }

    /**
     * Set the title for the Html page.
     * 
     * @param string $title (Optional) Html page title. Default is NULL.
     * If NULL then set "[CONTROLLER_NAME] [ACTION_NAME]" as Html page title.
     */
    public function set_title($title = NULL) {
        $this->title = $title;
    }

    /**
     * Set absolute url value for <base> element (inside the head tag)
     * Note: There can be at maximum one <base> element in a document
     * Note: Only valid url will be set.
     * @param string|NULL $absolute_url
     */
    public function set_base($absolute_url = NULL) {
        $this->base_url = (is_string($absolute_url) ? $absolute_url : NULL);
    }

    /**
     * Add new meta data item according to his attributes.
     * Note: Existing attribute will be replaced.
     * @param string $attr_name Attribute name.
     * @param string $attr_value Attribute value.
     * @param string $content (Optional) content value.<br/>
     *  Note: If content's value is NULL then will not be content attribute
     */
    public function set_meta_data($attr_name, $attr_value, $content = NULL) {
        if (!is_string($attr_name) || empty($attr_name)) {
            return trigger_error('Invalid parameter: $attr_name must be NOT empty string', E_ERROR);
        } else if (!is_string($attr_value) || empty($attr_value)) {
            return trigger_error('Invalid parameter: $attr_value must be NOT empty string', E_ERROR);
        } else if (!array_key_exists($attr_name, $this->meta_data_items)) {
            $this->meta_data_items[$attr_name] = array();
        }
        $this->meta_data_items[$attr_name][$attr_value] = $content;
    }

    /**
     * Checks if metadata has been set for specific attribute (NO matter what is the content's value!)
     * @param string $attr_name Attribute name.
     * @param string $attr_value Attribute value.
     * @return boolean Tru if exists. Else false.
     */
    public function is_meta_data_exists($attr_name, $attr_value) {
        return array_key_exists($attr_name, $this->meta_data_items) && array_key_exists($attr_value, $this->meta_data_items[$attr_name]);
    }

    /**
     * Adds array of JavaScript file(s) to be loaded when the layout is rendered.
     * 
     * @param array $filenames Array of relative JavaScrript file name(s) to $base_path.
     * @param int $type (Optional) Specify ordering for scripts with dependencies. Default is 1.
     * @param string|NULL $base_path (Optional) Custom base path to the script file.<br />
     * Default is config['js_url'] value.
     */
    public function set_scripts(array $filenames, $type = App_layout::APP_FILE_TYPE, $base_path = NULL) {
        if (!is_string($base_path) || empty($base_path)) {
            $base_path = $this->_get_default_js_url($type);
        }

        if (!array_key_exists($type, $this->scripts)) {
            $this->scripts[$type] = array();
        }

        foreach ($filenames as $filename) {
            if (!is_string($filename) || empty($filename)) {
                trigger_error('Invalid parameter: $filenames must contains only strings which NOT empty', E_ERROR);
                continue;
            }
            $this->scripts[$type][] = "{$base_path}{$filename}.js";
        }
    }

    /**
     * Adds a JavaScript file to be loaded when the layout is rendered.
     * 
     * @param string $filename Relative JavaScrript file name to $base_path.
     * @param int $type (Optional) Specify ordering for scripts with dependencies. Default is 1.
     * @param string|NULL $base_path (Optional) Custom base path to the script file.<br />
     * Default is config['js_url'] value.
     */
    public function set_script($filename, $type = App_layout::APP_FILE_TYPE, $base_path = NULL) {
        if (!is_string($filename) || empty($filename)) {
            return trigger_error("Invalid parameter: " . __CLASS__ . "->" . __FUNCTION__ . '$filename must be string and NOT empty', E_ERROR);
        }

        if (!array_key_exists($type, $this->scripts)) {
            $this->scripts[$type] = array();
        }
        
        if (!is_string($base_path) || empty($base_path)) {
            $base_path = $this->_get_default_js_url($type);
        }
        $this->scripts[$type][] = "{$base_path}{$filename}.js";
    }

    /**
     * Adds style path to load when layout is rendering.
     * 
     * @param string $filename Source of the css style file related to $base_path.
     * @param string|NULL $base_path (Optional) Custom base path to the css file.<br />
     * Default is NULL. If NULL then using config['css_url'] value.
     */
    public function set_style($filename, $base_path = NULL) {
        if (!is_string($filename) || empty($filename)) {
            return trigger_error("Invalid parameter: " . __CLASS__ . "->" . __FUNCTION__ . '$filename must be string and NOT empty', E_ERROR);
        }

        if (!is_string($base_path) || empty($base_path)) {
            $this->styles[] = "{$this->css_url}{$filename}.css";
        } else {
            $this->styles[] = "{$base_path}{$filename}.css";
        }
    }

    /**
     * Adds array of style file(s) to be loaded when the layout is rendered.
     * 
     * @param array $filenames Array of relative style file name(s) to $base_path.
     * @param string|NULL $base_path (Optional) Custom base path to the style file.<br />
     * Default is NULL. If NULL then using config['css_url'] value.
     */
    public function set_styles(array $filenames, $base_path = NULL) {
        if (!is_string($base_path) || empty($base_path)) {
            $base_path = & $this->css_url;
        }

        foreach ($filenames as $filename) {
            if (!is_string($filename) || empty($filename)) {
                trigger_error("Invalid parameter: " . __CLASS__ . "->" . __FUNCTION__ . '$filenames must contains only strings which NOT empty', E_ERROR);
                continue;
            }
            $this->styles[] = "{$base_path}{$filename}.css";
        }
    }

    /**
     * Set(add or update exists) attribute for the HTML tag.
     * @param string $attr_name Attribute name
     * @param string $attr_value Attribute value
     * @return boolean
     */
    public function set_html_attribute($attr_name, $attr_value) {
        if (!is_string($attr_name) || empty($attr_name)) {
            return trigger_error('Invalid parameter: $attr_name must be NOT empty string.', E_ERROR);
        }
        $this->html_attributes[$attr_name] = $attr_value;
        return TRUE;
    }
    
    /**
     * Add view to be rendered in header section. When rendering, views will be rendered in the adding order.
     * 
     * @param string $view View name to render.
     * @param array $params (Optional) view's parameters. Default is empty array.
     * @param boolean $is_clear_params (Optional) True means clearing parameters values right after rendering the view.
     */
    public function set_header_view($view, array $params = array(), $is_clear_params = FALSE) {
        array_push($this->header_views, new LayoutView($view, $params, $is_clear_params));
    }
    
    /**
     * Add view to be rendered in main section(content). When rendering, views will be rendered in the adding order.
     * 
     * @param string $view View name to render.
     * @param array $params (Optional) view's parameters. Default is empty array.
     * @param boolean $is_clear_params (Optional) True means clearing parameters values right after rendering the view.
     */
    public function set_view($view, array $params = array(), $is_clear_params = FALSE) {
        array_push($this->content_views, new LayoutView($view, $params, $is_clear_params));
    }
    
    /**
     * Add view to be rendered in footer section. When rendering, views will be rendered in the adding order.
     * 
     * @param string $view View name to render.
     * @param array $params (Optional) view's parameters. Default is empty array.
     * @param boolean $is_clear_params (Optional) True means clearing parameters values right after rendering the view.
     */
    public function set_footer_view($view, array $params = array(), $is_clear_params = FALSE) {
        array_push($this->footer_views, new LayoutView($view, $params, $is_clear_params));
    }

    /**
     * Render view(s) inside layout mechanism.
     * 
     * @param string $layout (Optional) layout name to use. default is 'default'.
     * Note: layout files location is: /views/layouts/{$layout}.php (Make sure it's exists!)
     */
    public function render($layout = 'default') {
        if (empty($layout)) { // If NO layout then render only content (partial view(s))!
            $this->_e_content();
        } else {
            $data = array(
                'controller' => $this->CI->router->fetch_class(),
                'action' => $this->CI->router->fetch_method()
            );
            $this->CI->load->view("layouts/{$layout}", $data);
        }
    }

    /**
     * Echo the title. If title is not been set then echo default title.
     * Note: default title is "[CONTROLLER_NAME] [ACTION_NAME]"
     */
    public function _e_title() {
        echo ($this->title === NULL ? $this->_get_default_title() : $this->title);
    }

    /**
     * Print base tag element if set and is valid url.
     */
    public function _e_base() {
        if ($this->base_url !== NULL) {
            $this->base_url = & filter_var($this->base_url, FILTER_VALIDATE_URL);
            if (is_string($this->base_url)) {
                printf('<base href="%s" target="_blank"><!--[if lte IE 6]></base><![endif]-->', $this->base_url);
            }
        }
    }

    /**
     * Echo all meta data items which has been set properly.
     * Note: Each meta data item is array of key-value pairs.
     */
    public function _e_meta_data() {
        foreach ($this->meta_data_items as $attr_name => &$meta_data_item) {
            foreach ($meta_data_item as $attr_value => &$content) {
                if (is_string($content)) {
                    printf('<meta %s="%s" content="%s" >', $attr_name, $attr_value, $content);
                } else {
                    printf('<meta %s="%s" >', $attr_name, $attr_value);
                }
            }
        }
    }

    /**
     * Echo all scripts items which has been set properly.
     * Note: Order of appearance is determined by the order of adding.
     */
    public function _e_scripts() {
        ksort($this->scripts);
        foreach ($this->scripts as $srcs) {
            foreach ($srcs as $src) {
                printf('<script src="%s" type="text/javascript"></script>', $src);
                //printf('<script src="%s?v=%s" type="text/javascript"></script>', $src, date('YmdHis')); // When developing
            }
        }
    }

    /**
     * Echo all style items which has been set properly.
     * Note: Order of appearance is determined by the order of adding.
     */
    public function _e_styles() {
        foreach ($this->styles as $href) {
            printf('<link href="%s" rel="stylesheet">', $href);
            //printf('<link href="%s?v=%s" rel="stylesheet" media="screen,projection">', $href, date('YmdHis')); // When developing
        }
    }
    
    /**
     * Echo header views
     */
    public function _e_header() {
        $this->_e_views($this->header_views);
    }

    /**
     * Echo content views.
     */
    public function _e_content() {
        $this->_e_views($this->content_views);
    }
    
    /**
     * Echo footer views
     */
    public function _e_footer() {
        $this->_e_views($this->footer_views);
    }

    /**
     * Echo HTML tag attributes(name & values).
     */
    public function _e_html_attributes() {
        foreach ($this->html_attributes as $attr_name => $attr_value) {
            printf(' %s="%s"', $attr_name, $attr_value);
        }
    }

    // </editor-fold>
}
