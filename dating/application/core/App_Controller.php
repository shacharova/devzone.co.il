<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Contains functions and parameters common to all application's controls.
 * 
 * @abstract
 * // CodeIgniter Classes:
 * @property CI_Cache $cache Codeigniter cache.
 * @property App_Email $email Codeigniter email class library.
 * @property App_Lang $lang CI_Lang class.
 * @property App_Input $input CI_Input class. // Extended
 * @property Output $output 
 * // Libraries:
 * @property App_Session $session App_Session class. // Extended
 * @property App_logs $app_logs Application mechanism of logs which write logs of events.
 * @property App_db $app_db Application mechanism of data-access to DB.
 * @property App_Cache $cache App_Cache class. // Extended
 * @property App_auth $app_auth Application authentication mechanism.
 * @property App_layout $app_layout Application Layout mechanism class instance.
 * @property App_privileges $app_privileges Application mechanism of action privileges.
 * // Models:
 * @property Album $album Album model (Required load).
 * @property Capability $capability Capability model (Required load).
 * @property Event $event Event model (Required load).
 * @property Image $image Image model (Required load).
 * @property Information $information Information model (Required load).
 * @property Log $log Log model (Required load).
 * @property Message $message Message model (Required load).
 * @property Role $role Role model (Required load).
 * @property User $user User model (Required load).
 * @property Location $location Location model (Required load).
 * @property Locality $locality Locality model (Required load).
 * @property Country $country Country model (Requited load).
 * @property Language $language Language model (Requited load).
 */
abstract class App_Controller extends CI_Controller {

    const IS_PROFILER_ENABLED = FALSE;

    public function __construct() {
        parent::__construct();
        $this->app_name = $this->config->item('app_name');
        $this->_isAjax = $this->input->is_ajax_request();

        $this->init_cache_driver();
        $this->init_header_cache_control();
        $this->init_country();
        $this->init_language();
        
        if ($this->_isAjax !== true) {
            $this->init_not_ajax();
        }
    }

    // <editor-fold defaultstate="collapsed" desc="Private & Protected Variables">
    /**
     * Indicate if the request is AJAX.
     * Initialized on App_Controller constructor.
     * @var bool True if AJAX request. Else false.
     */
    protected $_isAjax = NULL;

    /**
     * Current user country. Contains name, code of the country.
     * @var \stdClass|null
     */
    protected $_country = NULL;

    /**
     * Contains the current language.
     * @var stdClass
     */
    protected $_language = '';
    // </editor-fold>
    // ~~
    // ~~
    // <editor-fold defaultstate="collapsed" desc="Public Variables">
    public $app_name = NULL;

    // </editor-fold>
    // ~~
    // ~~
    // <editor-fold defaultstate="collapsed" desc="Private & Protected Functions">
    private function init_header_cache_control() {
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
    }

    private function init_cache_driver() {
        $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));

        // How use cache
        // https://ellislab.com/codeigniter/user-guide/libraries/caching.html
//        $this->cache->save('app_cache1', "shachar1", 60);
//        $data = $this->cache->get('app_cache1');
//        $cacheMetadata = $this->cache->get_metadata('app_cache1'); // Return an array with expire an mmtime belongs to cache item 
//        $cacheInfo = $this->cache->cache_info(); // Return an array of cache information
//        $this->cache->delete('app_cache1');
//        $this->cache->clean(); // Clean entire cache
    }

    private function init_country() {
        // Try get country object from SESSION:
        $this->_country = & $this->session->userdata('country');

        // If NO country in SESSION then get by ip address:
        if (empty($this->_country)) {
            $this->_country = & $this->input->ip_address_country();
        }

        // Save country to SESSION:
        $this->session->set_userdata('country', $this->_country);
    }

    private function init_language() {
        $this->_language = & $this->lang->get_user_language();

//        $this->_language->code = 'en';
//        $this->_language->name = "English";
//        $this->_language->direction = "ltr";
//        $this->_language->code = 'he';
//        $this->_language->name = "Hebrew";
//        $this->_language->direction = "rtl";
        // Load language file (for translations):
        $this->lang->load('app', strtolower($this->_language->name));

        // Load english translation for profiler (if enabled):
        if (static::IS_PROFILER_ENABLED === TRUE) {
            $this->lang->load('profiler', 'english');
        }

        // Load english translations for DB messages:
        $this->lang->load('db', 'english');
    }

    private function init_html_attributes() {
        if (!empty($this->_language)) {
            $this->app_layout->set_html_attribute('lang', $this->_language->code);
        }
        if (!empty($this->_language->direction)) {
            $this->app_layout->set_html_attribute('dir', $this->_language->direction);
        }
        if ($this->_language->direction === 'rtl') {
            $this->app_layout->set_html_attribute('class', 'k-rtl');
        }
    }

    private function init_meta_data() {
        $this->app_layout->set_meta_data('charset', 'UTF-8');
        $this->app_layout->set_meta_data('http-equiv', 'X-UA-Compatible', 'IE=edge');

        $this->app_layout->set_meta_data('name', 'viewport', 'width=device-width, initial-scale=1.0, maximum-scale=1.0001, user-scalable=yes');

        $properites = array('og:locale' => '_locale_',
            'og:site_name' => '_default_metadata_og:site_name_',
            'og:title' => '_default_metadata_og:title_',
            'og:description' => '_default_metadata_og:description_',
            'og:image' => '_default_metadata_og:image_');
        foreach ($properites as $attr_value => &$content_key) {
            $this->app_layout->set_meta_data('property', $attr_value, $this->lang->line($content_key));
        }

        $names = array('description' => '_default_metadata_description_',
            'keywords' => '_default_metadata_keywords_');
        foreach ($names as $attr_value => &$content_key) {
            $this->app_layout->set_meta_data('name', $attr_value, $this->lang->line($content_key));
        }
    }

    private function init_image_url_meta_data() {
        if (!$this->app_layout->is_meta_data_exists('app_config', 'image_url')) {
            $this->app_layout->set_meta_data('app_config', 'image_url', $this->config->item('image_url'));
        }
    }

    private function init_styles() {
        $this->app_layout->set_style('app/styles');
        //'bootstrap/min'
        //'materialize/min'
    }

    private function init_scripts() {
        $this->app_layout->set_script('jquery/1.11.3.min', App_layout::LIB_FILE_TYPE);
        $this->app_layout->set_script('jquery/ui/all.min', App_layout::LIB_FILE_TYPE);
        $this->app_layout->set_script('app_scripts', App_layout::APP_FILE_TYPE);
    }

    private function init_header() {
        $this->load->model('language');

        $hasLoggedInUsers = $this->app_auth->has_logged_in_user();
        if ($hasLoggedInUsers) {
            
        } else {
            
        }

        $params = array(
            'languages' => $this->language->getAll()->queryResult,
            'userLanguage' => $this->_language,
            'hasLoggedInUsers' => $hasLoggedInUsers
        );
        $this->app_layout->set_header_view('shared/header', $params);
    }

    private function init_footer() {
        $this->app_layout->set_footer_view('shared/footer');
    }

    private function init_not_ajax() {
        $this->output->enable_profiler(static::IS_PROFILER_ENABLED); // for debuging

        $this->init_html_attributes();
        $this->init_meta_data();
        $this->init_image_url_meta_data();
        $this->init_styles();
        $this->init_scripts();

        if ($this->session->is_known() !== TRUE) {
            $this->app_auth->login_from_cookies();
        }

        $this->init_header();
        $this->init_footer();
    }

    protected function set_jquery_ui() {
        $this->app_layout->set_style('jquery/ui/all.min');
        $this->app_layout->set_script('jquery/ui/all.min', App_layout::LIB_FILE_TYPE);

        if ($this->_language->code !== "en") {
            $this->app_layout->set_script("jquery/ui/localization/{$this->_language->code}", App_layout::LIB_FILE_TYPE);
        }
    }

    protected function set_jquery_validate() {
        $this->app_layout->set_script('jquery/validate/min', App_layout::LIB_FILE_TYPE);
        if ($this->_language->code !== 'en') {
            $this->app_layout->set_script("jquery/validate/localization/messages_{$this->_language->code}.min", App_layout::LIB_FILE_TYPE);
        }
    }

    protected function set_jquery_fileupload() {
        // TODO:
        $this->app_layout->set_script('jquery/fileupload/core', App_layout::LIB_FILE_TYPE);
        $this->app_layout->set_script('jquery/fileupload/iframe-transport', App_layout::LIB_FILE_TYPE);
    }

    // </editor-fold>
    // ~~
    // ~~
}
