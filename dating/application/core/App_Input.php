<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Extend the native CI_Input class
 *
 * @author Shachar
 */
class App_Input extends CI_Input {
    const ERR_INVALID_REQ_STR = "error_invalid_request";

    public function __construct() {
        parent::__construct();
    }
    
    public function allow_get() {
        if($this->server('REQUEST_METHOD') !== "GET") {
            $ci = & get_instance();
            die($ci->lang->line(static::ERR_INVALID_REQ_STR));
        }
    }
    
    public function allow_post() {
        if($this->server('REQUEST_METHOD') !== "POST") {
            $ci = & get_instance();
            die($ci->lang->line(static::ERR_INVALID_REQ_STR));
        }
    }

    public function allow_ajax() {
        if(!$this->is_ajax_request()) {
            $ci = & get_instance();
            die($ci->lang->line(static::ERR_INVALID_REQ_STR));
        }
    }
    public function allow_ajax_post() {
        $this->allow_ajax();
        $this->allow_post();
    }
    
    public function delete_cookie($name) {
        $this->set_cookie(array(
            'name' => $name,
            'value' => '',
            'expire' => 0
        ));
    }
    
    public function set_json_encode_cookie($name = '', $value = '', $expire = '', $domain = '', $path = '/', $prefix = '', $secure = FALSE) {
        if(is_array($name) && isset($name['value'])) {
            $name['value'] = & json_encode($name['value']);
        } else {
            $value = & json_encode($value);
        }
        $this->set_cookie($name, $value, $expire, $domain, $path, $prefix, $secure);
    }
    
    public function json_decode_cookie($index = '', $xss_clean = FALSE) {
        return json_decode($this->cookie($index, $xss_clean));
    }
    
    public function ip_address_country() {
        
        $ipAddress = & $this->ip_address();
        $geoResult = & json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip={$ipAddress}"));
        if($geoResult instanceof stdClass && isset($geoResult->geoplugin_countryName)) {
            $country = new stdClass();
            $country->name = $geoResult->geoplugin_countryName;
            $country->code = $geoResult->geoplugin_countryCode;
            return $country;
        }
        return NULL;
    }
}
