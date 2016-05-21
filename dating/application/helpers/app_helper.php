<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

if (!function_exists('image_url')) {
    function image_url($uri = '') {
        $CI = & get_instance();
        return "{$CI->config->item('image_url')}{$uri}";
    }
}

if (!function_exists('flash_uniqid')) {
    /**
     * Generate a unique string or return the last one (when pass true)
     * @param boolean $isExists If true then return the last unique string. Default is FALSE.
     * @return string
     */
    function flash_uniqid($isExists = FALSE) {
        $CI = & get_instance();
        if($isExists !== TRUE || !isset($CI->_sess_uniqid_)) {
            $CI->_sess_uniqid_ = & str_replace(".", "", uniqid("tid_", TRUE));   
        }
        return $CI->_sess_uniqid_;
    }
}

if (!function_exists('valid_url')) {
    /**
     * Validate url format.
     * @param string $url
     * @return boolean True if valid url. Else false.
     */
    function valid_url($url) {
        return !(filter_var($url, FILTER_VALIDATE_URL) === FALSE);
    }
}

