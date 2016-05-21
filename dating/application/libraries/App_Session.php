<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Extend the native CI_Session class
 *
 * @author Shachar
 */
class App_Session extends CI_Session {
    const SESSION_KEY_IS_KNOWN = '_is_known_';
    const SESSION_KEY_GUEST = 'guest';
    private $isKnown;
    
    function __construct() {
        parent::__construct();
        $this->isKnown = $this->userdata(static::SESSION_KEY_IS_KNOWN) === TRUE;
        if($this->isKnown !== TRUE) {
            $this->set_userdata(static::SESSION_KEY_IS_KNOWN, TRUE);
        }
    }
    
    /**
     * Returns true if it is NOT first usage in session. Else false.
     * @return bool
     */
    public function is_known() {
        return $this->isKnown;
    }
    
    public function set_sub_userdata($subkey, $newdata = array(), $newval = '') {
        if (is_array($newdata)) {
            foreach ($newdata as $key => &$value) {
                $this->set_userdata("_{$key}_{$subkey}_", $value);
            }
        } else {
            $this->set_userdata("_{$newdata}_{$subkey}_", $newval);
        }
    }

    public function sub_userdata($subkey, $item) {
        return $this->userdata("_{$item}_{$subkey}_");
    }

    public function unset_sub_userdata($subkey, $newdata = array()) {
        if (is_array($newdata)) {
            foreach ($newdata as $key => &$value) {
                $this->unset_userdata("_{$key}_{$subkey}_");
            }
        } else {
            $this->unset_userdata("_{$newdata}_{$subkey}_");
        }
    }

    public function set_sub_flashdata($subkey, $newdata = array(), $newval = '') {
        if (is_array($newdata)) {
            foreach ($newdata as $key => &$value) {
                $this->set_flashdata("_{$key}_{$subkey}_", $value);
            }
        } else {
            $this->set_flashdata("_{$newdata}_{$subkey}_", $newval);
        }
    }

    public function sub_flashdata($subkey, $key) {
        return $this->flashdata("_{$key}_{$subkey}_");
    }

    public function keep_sub_flashdata($subkey, $key) {
        $this->keep_flashdata("_{$key}_{$subkey}_");
    }
    
    /**
     * remove (unset) guest data from session
     */
    public function unset_guest() {
        $this->unset_userdata(static::SESSION_KEY_GUEST);
    }
    
    /**
     * Set guest instance into session
     * @param stdClass $guest
     */
    public function set_guest(stdClass $guest) {
        $this->set_userdata(static::SESSION_KEY_GUEST, $guest);
    }
    
    /**
     * Return guest class instance (if exists)
     * @return stdClass guest instance
     */
    public function guest() {
        return $this->userdata(static::SESSION_KEY_GUEST);
    }
}