<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class App_Language extends stdClass {

    public $id;
    public $name;
    public $direction;
    public $code;
    public $priority;

}

class App_Lang extends CI_Lang {

    const SESSION_KEY_USER_LANGUAGE = 'user_language';

    public function __construct() {
        parent::__construct();
    }

    // ~~
    // ~~
    // <editor-fold defaultstate="collapsed" desc="Private & Protected Function">
    /**
     * Return language direction by language code.
     * @param string $languageCode
     * @return string (rtl|ltr)
     */
    private function get_direction_by_code($languageCode) {
        switch (strtolower($languageCode)) {
            case 'he': case 'ar': case 'arc':
            case 'bcc': case 'bqi': case 'ckb':
            case 'dv': case 'fa': case 'glk':
            case 'lrc': case 'mzn': case 'pnb':
            case 'ps': case 'sd': case 'ug':
            case 'ur': case 'yi':
                return 'rtl';
            default:
                return 'ltr';
        }
    }

    /**
     * Return language code by language name
     * @param string $languageName Language name.
     * @return string
     */
    private function get_code_by_name($languageName) {
        switch (strtolower($languageName)) {
            case "hebrew": return "he";
            case "english": return "en";
            default: return "en";
        }
    }

    // </editor-fold>
    // ~~
    // ~~
    // <editor-fold defaultstate="collapsed" desc="Public Function">
    /**
     * Return language object by user country if possible.
     * @return stdClass|NULL
     */
    public function get_language_by_country($country = NULL) {
        $ci = & get_instance();

        if (!is_string($country)) {
            $country = & $ci->session->userdata('country');
        }

        if (!empty($country) && !empty($country->code)) {
            $ci->load->model('language');
            $dbResult = & $ci->language->getByCountryCode($country->code);
            if (!empty($dbResult) && !empty($dbResult->queryResult[0])) {
                return $dbResult->queryResult[0];
            }
        }

        return NULL;
    }

    /**
     * Update user's display language to another language - In session & DB.
     * Note: If there is not dictionary for the new language,
     *       then the default language will be displayed.
     * @param stdClass $languageId
     * @param string|null &$error Contains error if there was.
     */
    public function set_user_language($languageId, &$error) {
        // TODO: Implement function (See & complete chart flow:
        if (!is_numeric($languageId)) {
            $error = "Invalid language number";
        } else {
            $ci = & get_instance();
            $this->load->model('language');
            $dbResult = & $ci->language->getLanguageById($languageId);

            if (!empty($dbResult) && !empty($dbResult->queryResult)) {
                
            }
        }
        
        
        
        
        
        
        $ci = & get_instance();
        $u = & $ci->input->get('u');
        
        if (!empty($u)) {
            $ci->session->set_sub_userdata($u, static::SESSION_KEY_USER_LANGUAGE, $languageId);
        } else {
            $ci->session->set_userdata(static::SESSION_KEY_USER_LANGUAGE, $languageId);
        }
        
        // TODO: Update user language in db
    }

    /**
     * Return user's language.<br/>
     * First, try get from sub session.
     * Second, try get from session
     * Third, try get by IP (Country)
     * Four, get the config defaults.
     * @return string
     */
    public function get_user_language() {
        $ci = & get_instance();
        $u = & $ci->input->get('u');
        $language = NULL;

        for ($attempt = 0; empty($language) && $attempt <= 4; ++$attempt) {
            if ($attempt === 1 && !empty($u)) { // From sub user SESSION
                $language = & $ci->session->sub_userdata($u, static::SESSION_KEY_USER_LANGUAGE);
                $ci->session->set_sub_userdata($u, static::SESSION_KEY_USER_LANGUAGE, $language);
            } else if ($attempt === 2) { // From user SESSION (NOT sub)
                $language = & $ci->session->userdata(static::SESSION_KEY_USER_LANGUAGE);
            } else if ($attempt === 3) { // By country (IP)
                $language = & $this->get_language_by_country();
            } else if ($attempt === 4) { // By default configs
                $language = new stdClass();
                $language->name = & $ci->config->item('language');
                $language->code = & $this->get_code_by_name($language->name);
                $language->direction = & $this->get_direction_by_code($language->code);
            }
        }

        // Save language to SESSION:
        $ci->session->set_userdata(static::SESSION_KEY_USER_LANGUAGE, $language); // Save language to specific user SESSION:

        return $language;
    }

    public function line($line = '') {
        return parent::line(strtolower($line));
    }

    // </editor-fold>
    // ~~
    // ~~
}
