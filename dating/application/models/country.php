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
class Country extends App_Model {
    const ASIA_CONTINENT = "asia";
    const AMERICA_CONTINENT = "america";
    const AFRICA_CONTINENT = "africa";
    const EUROPE_CONTINENT = "europe";
    const OCEANIA_CONTINENT = "oceania";
    
    public function __construct() {
        parent::__construct();
    }

    /**
     * Return all countries records
     * @return \App_DBSPResult
     */
    public function getAll() {
        return $this->app_db->callSP('common', 'getCountries', true);
    }
    
    /**
     * Return all countries records grouped inside continents arrays
     * @return \App_DBSPResult
     */
    public function getAllGroupedByContinents() {
        $results = array(
            static::ASIA_CONTINENT => array(),
            static::AMERICA_CONTINENT => array(),
            static::AFRICA_CONTINENT => array(),
            static::EUROPE_CONTINENT => array(),
            static::OCEANIA_CONTINENT => array()
        );
        
        $dbResult = & $this->getAll();
        foreach ($dbResult->queryResult as &$stdClass){
            $continent = $stdClass->continent;
            unset($stdClass->continent);
            array_push($results[$continent], $stdClass);
        }
        
        $dbResult->queryResult = & $results;
        return $dbResult;
    }

    public function getByContinent($continentName) {
        return $this->app_db->callSP('common', 'getCountriesByContinent', true, array($continentName));
    }
    
    /**
     * Return country by country code.
     * @param int $countryCode
     * @return \App_DBSPResult
     */
    public function getByCountryCode($countryCode) {
        return $this->app_db->callSP('common', 'getCountryByCode', true, array($countryCode));
    }
}
