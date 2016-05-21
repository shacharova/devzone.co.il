<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * @abstract
 * // CodeIgniter Classes:
 * @property App_Input $input CI_Input class.
 * @property CI_Lang $lang CI_Lang class.
 * @property App_Session $session APP_Session class.
 * // Libraries:
 * @property App_logs $app_logs Application mechanism of logs which write logs of events.
 * @property App_db $app_db Application mechanism of data-access to DB.
 * @property App_auth $app_auth Application authentication mechanism.
 * @property App_layout $app_layout Application Layout mechanism class instance.
 * @property App_privileges $app_privileges Application mechanism of action privileges.
 */
class User extends App_Model {

    const MAX_AUTH_ATTEMPTS = 4;
    const SESSION_KEY_LOGGED_IN_COUNTER = 'logged_in_counter';
    const SESSION_KEY_FREED_U_ARRAY = 'freed_u_array';

    // <editor-fold defaultstate="collapsed" desc="Public Functions">
    public function __construct() {
        parent::__construct();
    }

    /**
     * Check if $email is in valid email format.
     * @param string $email
     * @param array $messages Contains an error message if there was.
     * @return boolean True if valid. Else false.
     */
    public function emailValid($email, array &$messages) {
        if (empty($email)) {
            $messages['error_field_missing'] = & $this->lang->line('error_field_missing');
        } else {
            $this->load->helper('email');
            if (!valid_email($email)) {
                $messages['error_email_invalid'] = & $this->lang->line('error_email_invalid');
            } else {
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * Set user status to active if is NOT active.
     * The status will be changed only if user's email exists.
     * @param string $userEmail Email of user
     * @return \App_DBSPResult
     */
    public function activate($userEmail) {
        return $this->app_db->callSP('dating', 'activateUser', false, array($userEmail), array('rowCount'));
    }

    /**
     * Set (Insert or Update) user by email.
     * @param string $userEmail User email address.
     * @param string $userEncryptedPassword User password.
     * @return \App_DBSPResult
     */
    public function set($userEmail, $userEncryptedPassword) {
        $inParams = array($userEmail, $userEncryptedPassword);
        return $this->app_db->callSP('dating', 'setUser', true, $inParams, array('id'));
    }

    /**
     * Set(insert or update) email verification details which has been sent to user's email after signup.
     * @param int $userId Exists (logged in) user ID
     * @param string $code Email verification code
     * @return \App_DBSPResult
     */
    public function setVerificationEmail($userId, $code) {
        return $this->app_db->callSP('dating', 'setUserVerificationEmail', true, array($userId, $code), array('rowCount'));
    }

    /**
     * Trying to change user status to 'blocked'.
     * @param string $userEmail User email address.
     * @param string $userPassword User password.
     * @return \App_DBSPResult
     */
    public function block($userEmail) {
        return $this->app_db->callSP('dating', 'blockUser', false, array($userEmail), array(
                    'rowCount'));
    }

    /**
     * Trying to change user status to 'inactive' password is matched.
     * @param string $userEmail User email.
     * @param string $userPassword Current user password.
     * @return \App_DBSPResult
     */
    public function deactivate($userEmail) {
        return $this->app_db->callSP('dating', "deactivateUser", TRUE, array($userEmail), array(
                    'rowCount'));
    }

    /**
     * Return decoded password of user by his email (if exists).
     * @param string $email User email.
     * @return string|false Decoded password  if success(NOT the original password). Else false.
     */
    public function decoded_password(&$email) {
        $dbResult = & $this->app_db->callSP('dating', 'getUserEncryptedPasswordByEmail', true, array($email));
        if (!empty($dbResult) && !empty($dbResult->queryResult)) {
            return $this->encrypt->decode($dbResult->queryResult[0]->password);
        }
        return false;
    }

    /**
     * Trying to delete user if password matched.
     * @param string $userEmail User email.
     * @param string $userPassword Current user password.
     * @return \App_DBSPResult
     */
    public function delete($userEmail, $userPassword) {
        return $this->app_db->callSP('dating', "deleteUser", TRUE, array($userEmail, $userPassword), array('rowCount'));
    }
    
    /**
     * Delete language of user (If there is).
     * @param int $userId User ID
     * @param int $languageId Language ID
     * @return \App_DBSPResult
     */
    public function deleteLanguage($userId, $languageId) {
        $inParams = array($userId, $languageId);
        return $this->app_db->callSP('dating', "deleteUserLanguage", TRUE, $inParams, array('rowCount'));
    }
    
    /**
     * Delete an email verification record which belongs to user by user id.
     * @param int|string $userId
     * @return App_DBSPResult
     */
    public function deleteVerificationEmail($userId) {
        return $this->app_db->callSP('dating', "deleteUserVerificationEmail", TRUE, array($userId), array('rowCount'));
    }
    
    /**
     * 
     * @param int $userId
     * @param string $albumName (Optional)
     * @param string $albumDescription (Optional)
     * @param int $page
     * @param int $pageSize
     * @return App_DBPagingData
     */
    public function getAlbums($userId, $albumName, $albumDescription, $page, $pageSize) {
        $inParams = array($userId, $albumName, $albumDescription, $page, $pageSize);
        $outParams = array('totalRows');
        return $this->app_db->callSP('dating', 'getUserAlbums', TRUE, $inParams, $outParams);
    }
    
    /**
     * Search images and return the matches results.
     * @param int $userId
     * @param string $title (Optional)
     * @param string $description (Optional)
     * @param int $page
     * @param int $pageSize
     * @return \App_DBSPResult
     */
    public function getImages($userId, $title, $description, $page, $pageSize) {
        $inParams = array($userId, $title, $description, $page, $pageSize);
        return $this->app_db->callSP('dating', 'getUserImages', true, $inParams, array('totalRows'));
    }
    
    /**
     * 
     * @param int $userId
     * @return \App_DBSPResult
     */
    public function getRoles($userId) {
        return $this->app_db->callSP('dating', "getRolesOfUserId", false, array($userId));
    }

    /**
     * If email exists, return public user data. Else return null.
     * @param string $email User's email.
     * @return stdClass|null
     */
    public function getDataByEmail($email) {
        $dbResult = & $this->app_db->callSP('dating', 'getUserDataByEmail', TRUE, array($email));

        if (empty($dbResult) || empty($dbResult->queryResult)) {
            return FALSE;
        }
        return $dbResult->queryResult[0];
    }

    /**
     * Return an email verification code information as StdClass instance by user ID.
     * Note: If email verification code data not exists for this user id then return false.
     * @param int $userId User ID
     * @return stdClass|false
     */
    public function getEmailVerificationCode($userId) {
        $dbResult = & $this->app_db->callSP('dating', "getEmailVerificationCode", TRUE, array(
                    $userId));

        if (empty($dbResult) || empty($dbResult->queryResult)) {
            return FALSE;
        }
        return $dbResult->queryResult[0];
    }

    /**
     * Trying to register new user by his email and password.
     * @param string $email User's email
     * @param string $password User's registration password
     * @param array $messages
     * @return integer|false If success then return user ID. Else return false.
     */
    public function signup($email, $password, array &$messages) {
        if ($this->exists($email, $messages)) {
            $messages['email_already_registered'] = & $this->lang->line('email_already_registered');
        } else if (empty($password)) {
            $messages['error_password_missing'] = $this->lang->line('error_password_missing');
        } else {
            $encryptedPassword = & $this->encrypt->encode($this->encrypt->sha1($password));
            $dbResult = & $this->set($email, $encryptedPassword);
            $id = & $dbResult->getOutParameter('id');
            if (!is_numeric($id)) {
                $messages['error_password_missing'] = $this->lang->line('error_password_missing');
            } else {
                return $id;
            }
        }
        return FALSE;
    }

    /**
     * Trying to verify an email - Checks if valid email and if belongs to one of the users.
     * @param string $email User email to verify.
     * @param stdClass $user Public data belongs to user.
     * @param array $messages Array with messages when failure occurs.
     * @return boolean True if success. Else false.
     */
    public function emailVerify($email, &$user, array &$messages) {
        if ($this->emailValid($email, $messages)) {
            $user = & $this->getDataByEmail($email);
            if (empty($user) || !isset($user->id)) {
                $messages['error_email_unrecognized'] = & $this->lang->line('error_email_unrecognized');
            } else {
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * Check if users exists by his email.
     * @param string $email User's email
     * @param array &$messages
     * @return boolean True if user exists. Else false.
     */
    public function exists($email, array &$messages) {
        if ($this->emailValid($email, $messages)) { // If valid email
            $user = & $this->getDataByEmail($email);
            return !empty($user); // True if user exists by email
        }
        return FALSE;
    }

    /**
     * Set user default language to exists user personal details.
     * @param int|string $userId
     * @param int|string $languageId
     * @param string $languageProficiency Can be one of the following values:<br/>
     *  'none','elementary','advanced','professional','Native or bilingual')
     * @return \stdClass
     */
    public function set_language($userId, $languageId, $languageProficiency) {
        $inParams = array($userId, $languageId, $languageProficiency);
        $outParams = array('rowCount');
        return $this->app_db->callSP('dating', 'setUserLanguage', true, $inParams, $outParams);
    }
    
    // </editor-fold>
}
