<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Authentication library.
 *
 * @author Shachar
 */
class App_auth {

    const MAX_AUTH_ATTEMPTS = 4;
    const SESSION_KEY_LOGGED_IN_COUNTER = '_logged_in_counter_';
    const SESSION_KEY_FREED_U_ARRAY = 'freed_u_array';
    const SESSION_KEY_AUTH_ATTEMPTS = 'auth_attempts';
    const COOKIE_NAME_LOGGED_IN_USERS = 'logged_in_users';
    
    
    /**
     * CodeIgniter instance
     * @var App_Controller
     */
    private $CI = null;

    // <editor-fold defaultstate="collapsed" desc="Private Functions">
    private function get_attempts_session_key(&$email) {
        return sprintf("%s_%s", $email, static::SESSION_KEY_AUTH_ATTEMPTS);
    }

    /**
     * Validate the user using his login details(email & password).
     * @param string $email User email.
     * @param string $password User password.
     * @param array $messages (By Ref) Contains messages of failure.
     * @return boolean True if validation success. Else false.
     */
    private function validate(&$email, &$password, &$messages) {
        $decodedPassword = & $this->CI->user->decoded_password($email);
        if (empty($decodedPassword)) {
            $messages['error_password_decoding_failed'] = & $this->lang->line('error_password_decoding_failed');
        } else if ($decodedPassword !== $this->CI->encrypt->sha1($password)) {
            $messages['error_email_password_not_match'] = & $this->CI->lang->line('error_email_password_not_match');
        } else {
            return true;
        }
        return false;
    }

    private function unset_logged_in_user($email) {
        $u = FALSE;
        $this->get_logged_in_user_by_email($email, $u); // $u initialized by ref
        if (is_int($u)) {
            $this->CI->session->unset_sub_userdata($u, 'user'); // Remove user data by u subkey
            $this->CI->session->unset_sub_userdata($email, 'u'); // Remove user's u by email subkey
            return $u;
        }
        return FALSE;
    }

    private function get_freed_u() {
        $freedUArray = & $this->CI->session->userdata(static::SESSION_KEY_FREED_U_ARRAY);

        if (empty($freedUArray) || !is_array($freedUArray)) {
            $loggedInCounter = & $this->CI->session->userdata(static::SESSION_KEY_LOGGED_IN_COUNTER);
            if (!is_int($loggedInCounter)) {
                $loggedInCounter = 0;
            }
            $this->CI->session->set_userdata(static::SESSION_KEY_LOGGED_IN_COUNTER, $loggedInCounter + 1);
            return $loggedInCounter;
        }
        reset($freedUArray); //  Set the internal pointer of an array to its first element
        $u = & key($freedUArray); // Fetch a key from an array
        unset($freedUArray[$u]);
        $this->CI->session->set_userdata(static::SESSION_KEY_FREED_U_ARRAY, $freedUArray);

        return $u;
    }

    private function add_freed_u($u, $associatedValue = '') {
        $freedUArray = $this->CI->session->userdata(static::SESSION_KEY_FREED_U_ARRAY);
        if (!is_array($freedUArray)) {
            $freedUArray = array();
        }
        $freedUArray[$u] = $associatedValue;
        ksort($freedUArray, SORT_NUMERIC);
        $this->CI->session->set_userdata(static::SESSION_KEY_FREED_U_ARRAY, $freedUArray);
    }
    
    private function set_stay_logged_in_array(array &$loggedInUsers) {
        $cookie = array(
            'name' => static::COOKIE_NAME_LOGGED_IN_USERS,
            'value' => $loggedInUsers,
            'expire' => config_item('cookie_expire'),
            'prefix' => config_item('cookie_prefix')
        );
        
        $this->CI->input->set_json_encode_cookie($cookie);
    }

    private function set_stay_logged_in(&$email) {
        $loggedInUsers = & $this->CI->input->json_decode_cookie(config_item('cookie_prefix') . static::COOKIE_NAME_LOGGED_IN_USERS, TRUE);
        if (!is_array($loggedInUsers)) {
            $loggedInUsers = array();
        }

        if (!in_array($email, $loggedInUsers)) {
            array_push($loggedInUsers, $email);
            $this->set_stay_logged_in_array($loggedInUsers);
        }
    }

    private function unset_stay_logged_in(&$email) {
        $loggedInUsers = & $this->CI->input->json_decode_cookie(config_item('cookie_prefix') . static::COOKIE_NAME_LOGGED_IN_USERS, TRUE);
        if (is_array($loggedInUsers)) {
            $key = array_search($email, $loggedInUsers);
            if ($key !== FALSE) {
                unset($loggedInUsers[$key]);
                $this->set_stay_logged_in_array($loggedInUsers);
            }
        }
    }
    // </editor-fold>
    // ~~
    // ~~
    // <editor-fold defaultstate="collapsed" desc="Public Functions">
    public function __construct() {
        $this->CI = & get_instance();
    }

    public function login_from_cookies() {
        // TODO
        $prefix = config_item('cookie_prefix');
        $loggedInUsers = & $this->CI->input->json_decode_cookie($prefix . static::COOKIE_NAME_LOGGED_IN_USERS);

        if (is_array($loggedInUsers)) {
            $messages = array();
            foreach ($loggedInUsers as &$email) {
                $this->login($email, FALSE, $messages);
            }
        }
    }

    public function reset_auth_attempts_counter($email) {
        $this->CI->session->set_userdata($this->get_attempts_session_key($email), 0);
    }

    /**
     * Try authenticate user by his email and password.
     * @param string $email User email to auth.
     * @param string $password User password to auth.
     * @param array $messages Array with messages when failure occurs
     * @return boolean True if success. Else false.
     */
    public function auth($email, $password, array &$messages) {
        $isSuccess = false;
        $attempts_session_key = $this->get_attempts_session_key($email);
        $attempts = & $this->CI->session->userdata($attempts_session_key);
        $this->CI->session->set_userdata($attempts_session_key, $attempts + 1);

        if ($attempts <= static::MAX_AUTH_ATTEMPTS) {
            if (empty($email)) {
                $messages['error_email_missing'] = & $this->CI->lang->line('error_email_missing');
            } else if (empty($password)) {
                $messages['error_password_missing'] = & $this->CI->lang->line('error_password_missing');
            } else if ($this->validate($email, $password, $messages)) {
                $this->CI->session->unset_userdata($attempts_session_key);
                $isSuccess = true;
            }
            if ($attempts === static::MAX_AUTH_ATTEMPTS) {
                $messages['alert_last_attempt'] = & $this->CI->lang->line('alert_last_attempt');
            }
        } else if ($attempts > static::MAX_AUTH_ATTEMPTS) {
            $messages['error_too_many_attempts'] = & $this->CI->lang->line('error_too_many_attempts');
        }

        return $isSuccess;
    }

    /**
     * Try return user if he logged in (and u by ref).
     * @param string $email User email.
     * @param int &$u (Out parameter) u number if user is logged. Else false.
     * @return stdClass|false
     */
    public function get_logged_in_user_by_email($email, &$u) {
        $u = & $this->CI->session->sub_userdata($email, 'u');
        return (is_int($u) ? $this->CI->session->sub_userdata($u, 'user') : FALSE);
    }
    
    /**
     * Try to return current logged in user.
     * @return stdClass|FALSE User data
     */
    public function get_current_logged_in_user() {
        $u = & $this->CI->input->get('u');
        if(!is_int($u)) {
            $u = 0;
        }
        return $this->CI->session->sub_userdata($u, 'user');
    }

    /**
     * Return true if there is logged in user. Else false.
     * @return boolean True if there is logged in user. Else false.
     */
    public function has_logged_in_user() {
        return $this->CI->session->userdata(static::SESSION_KEY_LOGGED_IN_COUNTER) >= 1;
    }

    /**
     * Return array of all logged in users public data.
     * @return array Array of stdClass object (or empty array)
     */
    public function get_logged_in_users() {
        $users = array();
        $loggedInCounter = & $this->CI->session->userdata(static::SESSION_KEY_LOGGED_IN_COUNTER);

        for ($u = 0; $u < $loggedInCounter; ++$u) {
            $user = & $this->CI->session->sub_userdata($u, 'user');
            if (!empty($user)) {
                array_push($users, $user);
            }
        }

        return $users;
    }

    /**
     * Try login user if exists by his email address.
     * @param string $email User's email.
     * @param boolean $stay_logged_in Indicates if the user will stay logged in after browser closing.
     * @param array &$messages Error messages (if failed).
     * @return boolean True on success. Else false.
     */
    public function login($email, $stay_logged_in, array &$messages) {
        $u = FALSE;
        $loggedInCounter = & $this->CI->session->userdata(static::SESSION_KEY_LOGGED_IN_COUNTER);

        $user = ($loggedInCounter >= 1 ? $this->get_logged_in_user_by_email($email, $u) : FALSE);

        // If logged in but without u number (because some unknown error)
        if (!empty($user) && empty($u)) {
            unset($user);
            $this->logout($email);
        }
        if (empty($user)) { // User NOT logged in then try do login if user exists.
            $user = & $this->CI->user->getDataByEmail($email);
            
            // If failed to get user data:
            if (empty($user)) {
                $messages['error_login_failed'] = $this->CI->lang->line("error_login_failed") . " - " . $this->lang->line('user_not_exists');
                return FALSE;
            }

            $u = & $this->get_freed_u();
            $this->CI->session->set_sub_userdata($email, 'u', $u);
            $this->CI->session->set_sub_userdata($u, 'user', $user);
            if ($stay_logged_in === TRUE) {
                $this->set_stay_logged_in($email);
            }
        }

        return $u;
    }

    /**
     * Trying to logout user by his email (If logged in)
     * @param string $email User's email.
     * @return boolean True on success. Else false.
     */
    public function logout($email) {
        $u = & $this->unset_logged_in_user($email);
        if (is_int($u)) { // If unset succeed
            $loggedInCounter = & $this->CI->session->userdata(static::SESSION_KEY_LOGGED_IN_COUNTER);
            $lastU = $loggedInCounter - 1;
            if ($lastU === $u) {
                $this->CI->session->set_userdata(static::SESSION_KEY_LOGGED_IN_COUNTER, $lastU);
            } else {
                $this->add_freed_u($u);
            }
            $this->unset_stay_logged_in($email);
            return TRUE;
        }
        return FALSE;
    }
    // </editor-fold>
}