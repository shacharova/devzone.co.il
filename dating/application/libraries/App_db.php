<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class App_db {

    // <editor-fold defaultstate="collapsed" desc="Static Variables(Request Lifetime)">
    private static $databases = array();
    // </editor-fold>
    // ~~
    // ~~
    // <editor-fold defaultstate="collapsed" desc="Private & Protected Variables">
    /**
     * CodeIgniter instance
     * @var App_Controller
     */
    private $CI = null;

    // </editor-fold>
    // ~~
    // ~~
    // <editor-fold defaultstate="collapsed" desc="Private & Protected Functions">
    private function _escapeInParams(&$dbName, array &$inParams) {
        foreach ($inParams as $key => &$inParam) {
            if (is_string($inParam)) {
                $inParam = "'" . static::$databases[$dbName]->escape_str($inParam, !is_int($key)) . "'";
            } else if (is_bool($inParam)) {
                $inParam = ($inParam === FALSE) ? 0 : 1;
            } else if (is_null($inParam)) {
                $inParam = 'NULL';
            } else if (is_numeric($inParam)) {
                $inParam = (string) $inParam;
            } else if ($inParam instanceof DateTime) {
                $inParam = & $inParam->format("'Y-m-d H:i:s'");
            } else {
                unset($inParams[$key]);
            }
        }
    }

    private function _escapeOutParams(array &$outParams) {
        foreach ($outParams as $key => &$outParam) {
            if (!is_string($outParam) || empty($outParam)) {
                unset($outParams[$key]);
            } else {
                $outParam = & trim($outParam, " \t\n\r\0\x0B@");
            }
        }
    }

    private function _prepareQueries(&$spName, array &$inParams, array &$outParams) {
        $queries = new stdClass();

        if (empty($outParams)) {
            if (empty($inParams)) { // NO out NO in
                $queries->call = & sprintf("CALL `%s` ()", $spName);
            } else { // No out
                $queries->call = & sprintf("CALL `%s` (%s)", $spName, implode(",", $inParams));
            }
        } else if (empty($inParams)) { // YES out NO in
            $outParamsString = & implode(",@", $outParams);
            $queries->call = & sprintf("CALL `%s` (@%s)", $spName, $outParamsString);
            $queries->out = & sprintf("SELECT @%s", $outParamsString);
        } else { // YES out YES in
            $outParamsString = & implode(",@", $outParams);
            $queries->call = & sprintf("CALL `%s` (%s,@%s)", $spName, implode(",", $inParams), $outParamsString);
            $queries->out = & sprintf("SELECT @%s", $outParamsString);
        }

        return $queries;
    }

    /**
     * Executing the stored procedure query and OUT query (if exists).<br />
     *  Query result will initialized into $queryResult property.<br />
     *  Out parameters will initizlized into $outResult property (if exists).
     * @param string $query
     * @param string $outQuery (Optional)
     * @param boolean $isResultObject (Optional) Default false.<br />
     *  Set true for object result data type. Else array result data type.
     * @param bool $isTransStart Indicate if trans_start will called before queries
     * @param bool $isTransComplete Indicate if trans_complete() and close() will called after queries
     * @return \App_DBSPResult SP results as App_DBSPResult instance
     */
    private function _transSP(&$dbName, &$query, &$outQuery = NULL, &$isResultObject = FALSE, $isTransStart = TRUE, $isTransComplete = TRUE) {
        $db = & static::$databases[$dbName];
        $startTime = & microtime(true);
        if ($isTransStart === true) {
            $db->trans_start();
        }

        $dbResult = new App_DBSPResult($isResultObject, $db, $query, $outQuery); // $queryResultObject

        if (!empty($dbResult->error) || $isTransComplete === TRUE) {
            $db->trans_complete();
            $db->close();
        }

        $dbResult->endMicroTime = & microtime(TRUE);
        $dbResult->startMicroTime = & $startTime;

        return $dbResult;
    }

    /**
     * If required, load and connect to database or reconnect if exists but not connected.
     * @param string $dbName
     */
    private function _prepare_db(&$dbName) {
        if (!array_key_exists($dbName, static::$databases) || empty(static::$databases[$dbName]->conn_id)) {
            static::$databases[$dbName] = & $this->CI->load->database($dbName, TRUE);
        } else {
            static::$databases[$dbName]->reconnect();
        }
    }

    /**
     * Escape IN & OUT parameters, prepare(build) query(ies) string(s) and transact it.
     * @param string $dbName
     * @param string $spName
     * @param bool $isResultObject
     * @param array $inParams
     * @param array $outParams
     * @return \App_DBSPResult
     */
    private function _executeSP(&$dbName, &$spName, &$isResultObject, array &$inParams, array &$outParams, $isTransStart = true, $isTransComplete = true) {
        $this->_escapeInParams($dbName, $inParams); // Pass by ref
        $this->_escapeOutParams($outParams); // Pass by ref
        $queries = & $this->_prepareQueries($spName, $inParams, $outParams); // Pass by ref
        //die(var_dump($queries));
        $dbResult = & $this->_transSP($dbName, $queries->call, $queries->out, $isResultObject, $isTransStart, $isTransComplete); // Pass by ref

        if ($dbResult instanceof App_DBSPResult) {
            $dbResult->inParameters = & $inParams;
            if (!empty($dbResult->error)) {
                throw new Exception($dbResult->error->message);
            }
        }
        
        return $dbResult;
    }

    // </editor-fold>
    // ~~
    // ~~
    // <editor-fold defaultstate="collapsed" desc="Public Functions">
    public function __construct() {
        $this->CI = & get_instance();
    }

    /**
     * Call stored procedure optionally with IN and OUT parameters.
     * @param string $dbName Database name which contains the SP to execute.
     * @param string $spName The name of the stored procedure to call.
     * @param boolean $isResultObject (Optional) Default false.<br />
     *  Set true for object result data type. Else array result data type.
     * @param array $inParams (Optional)
     * @param array $outParams (Optional)
     * @return \App_DBSPResult Query result object
     */
    public function callSP($dbName, $spName, $isResultObject = false, array $inParams = array(), array $outParams = array()) {
        $this->_prepare_db($dbName);
        return $this->_executeSP($dbName, $spName, $isResultObject, $inParams, $outParams, true, true);
    }

    /**
     * Call more then one stored procedures (optionally with IN and OUT parameters for each one) in single transcation.
     * @param string $dbName
     * @param App_DBSPDefinition $spDef1 First Stored Procedure to call
     * @param App_DBSPDefinition $_ (Optional) more parameters of Stored Procedure to call (separated by comma)
     * @return array Array of App_DBSPResult instances
     * @throws InvalidArgumentException
     */
    public function callManySP($dbName, App_DBSPDefinition $spDef1, App_DBSPDefinition $_ = NULL) {
        $this->_prepare_db($dbName);

        $results = array();

        $args = & func_get_args();
        $lastIndex = count($args) - 1;
        for ($i = 1; $i <= $lastIndex; ++$i) {
            if (!$args[$i] instanceof App_DBSPDefinition) {
                throw new InvalidArgumentException("'callManySP' function only accepts 'App_DBSPDefinition' after the first parameter");
            }
            $spDef1 = & $args[$i];
            $dbResult = & $this->_executeSP($dbName, $spDef1->name, $spDef1->isResultObject, $spDef1->inParams, $spDef1->outParams, $i === 1, $i === $lastIndex);
            array_push($results, $dbResult);
        }

        return $results;
    }

    // </editor-fold>
}

class App_DBSPDefinition {

    /**
     * The name of the stored procedure
     * @var string
     */
    public $name;

    /**
     * Array with input parameters values belongs to this SP.
     * @var array
     */
    public $inParams;

    /**
     * Array with output parameters name(Not values) belongs to this SP.
     * @var array
     */
    public $outParams;

    /**
     * Boolean which indicate the result struct.
     * @var bool True for stdClass object. False for array. (Default is false)
     */
    public $isResultObject;

    /**
     * 
     * @param string $name
     * @param bool $isResultObject
     * @param array $inParams
     * @param array $outParams
     */
    public function __construct($name, $isResultObject = false, array $inParams = array(), array $outParams = array()) {
        $this->name = $name;
        $this->isResultObject = $isResultObject;
        $this->inParams = $inParams;
        $this->outParams = $outParams;
    }

}

class App_DBSPResult {

    // <editor-fold defaultstate="collapsed" desc="Private & Protected Variables">
    /**
     *
     * @var CI_DB_mysqli_result
     */
    private $queryResultObject = null;

    /**
     * Array of out parameters returns values (If there are). <br />
     *  Note: Keys are the names of the out parameters.
     * @var array
     */
    private $outResult = null;

    /**
     * Contain the calculated value of query run duration after call getRunDuration() function.
     * @var float
     */
    private $runDuration = null;
    // </editor-fold>
    // ~~
    // ~~
    // <editor-fold defaultstate="collapsed" desc="Public Variables">
    /**
     * Contains the query result as array format.
     *  Note: May be array of stdClass instances.
     * @var array|null array of arrays or of stdClass(record) instances for each record.<br/>
     *  Note: If multiple select SP then return array of arrays(Tables) which each one is array of stdClass(record)
     */
    public $queryResult = NULL;

    /**
     * Array of all IN parameters which sent to MySQL query for this result (if there are parameters).
     * @var array key = parameter name, value = parameter value.
     */
    public $inParameters = NULL;

    /**
     * Start query time (with accuracy of microseconds). <br />
     * @var float Time with seconds unit.
     */
    public $startMicroTime = NULL;

    /**
     * End query time (with accuracy of microseconds). <br />
     * @var float Time with seconds unit.
     */
    public $endMicroTime = NULL;

    /**
     * Error object with details about error (If occured)
     * @var \App_DBError
     */
    public $error = NULL;
    // </editor-fold>
    // ~~ 
    // ~~
    // <editor-fold defaultstate="collapsed" desc="Private Functions">
    private function _readNextResults(&$queryResultObject) {
        if (empty($queryResultObject)) {
            return FALSE;
        }
        $data = array();

        while ($queryResultObject->next_result()) {
            $storedResults = & mysqli_store_result($queryResultObject->conn_id);
            if (empty($storedResults)) {
                continue;
            }
            $rows = array();
            while ($row = $storedResults->fetch_object()) {
                array_push($rows, $row);
            }
            array_push($data, $rows);
        }
        $queryResultObject->free_result();

        return $data;
    }

    private function set_error(CI_DB_mysqli_driver $db) {
        $errorNumber = & $db->_error_number();
        if (is_int($errorNumber)) {
            $this->error = new App_DBError($errorNumber, $db->_error_message());
        }
    }

    // </editor-fold>
    // ~~ 
    // ~~
    // <editor-fold defaultstate="collapsed" desc="Public Functions">
    public function __construct($isResultObject, CI_DB_mysqli_driver &$db, $query, $outQuery = NULL) { // CI_DB_mysqli_result $queryResultObject = null
        $queryResultObject = $db->query($query);
        if ($queryResultObject instanceof CI_DB_mysqli_result) {
            $this->queryResultObject = $queryResultObject;
            $this->queryResult = ($isResultObject === true ? $queryResultObject->result_object() : $queryResultObject->result_array());

            // Handle multiple select queries (Note: This is NOT common use)
            $moreData = & $this->_readNextResults($queryResultObject);
            if (!empty($moreData)) {
                array_unshift($moreData, $this->queryResult);
                $this->queryResult = & $moreData;
            }

            if (is_string($outQuery)) {
                $this->setOutResult($db->query($outQuery));
            }
        } else {
            $this->set_error($db);
        }
    }

    public function setOutResult(CI_DB_mysqli_result $outResultObject) {
        if (!empty($outResultObject) && $outResultObject->num_rows() > 0) {
            $result = & $outResultObject->result_array();
            $this->outResult = (empty($result) ? array() : $result[0]);
        }
    }

    /**
     * Return the value of OUT parameter by his name.
     * @param string $paramName Parameter name.
     * @return mixed
     */
    public function getOutParameter($paramName) {
        if (!empty($this->outResult) && is_string($paramName) && !empty($paramName)) {
            $escapedParamName = & sprintf("@%s", trim($paramName, " \t\n\r\0\x0B@"));
            if (array_key_exists($escapedParamName, $this->outResult)) {
                return $this->outResult[$escapedParamName];
            }
        }
    }

    /**
     * Returns the query execution duration time in seconds unit.
     * @return double
     */
    public function getRunDuration() {
        if ($this->runDuration === null && !empty($this->endMicroTime)) {
            $this->runDuration = $this->endMicroTime - $this->startMicroTime;
        }

        return $this->runDuration;
    }

    /**
     * Return next query result (if exists).
     * @param bool $isResultObject (Optional) If true then each record return as stdClass. Else return array of arrays.
     * @return array|null array of arrays or of stdClass instances for each record.
     */
    public function next_results($isResultObject = false) {
        if (!$this->queryResultObject->next_result()) {
            return NULL;
        }

        $next_results = & mysqli_store_result($this->queryResultObject->conn_id);
        if (!is_object($next_results)) {
            return NULL;
        }

        $rows = array();
        if ($isResultObject === true) {
            while ($row = $next_results->fetch_object()) {
                array_push($rows, $row);
            }
        } else {
            while ($row = $next_results->fetch_assoc()) {
                array_push($rows, $row);
            }
        }

        mysqli_free_result($next_results);
        return $rows;
    }

    // </editor-fold>
}

class App_DBError {

    public $number;
    public $message;

    public function __construct($errorNumber, $errorMessage) {
        $this->number = $errorNumber;
        $this->message = $errorMessage;
    }

}

