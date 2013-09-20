<?php

/**
 * Snort PHP Monitor
 * @author Andrej Frank <Andrej.Frank@hs-esslingen.de>
 * @copyright Copyright (c) 2008, Andrej Frank, The GNU General Public License (GPL)
*/

/**
 * class for sqlite3 database connections
 * @package class
 */
class sqlite3 {

    /**
     * safe connection
     * @var int $con value of connection
     */
    var $con;

    /** 
     * constructor, connect to sqlite-file
     * @param string $file value of database filename
     */
    function sqlite3($file) {
        $this->con = sqlite3_open($file);
    }

    /**
     * wrapper for xx_qurey()
     * @param string $sql value of sql request
     * @return object 
     */
    function query($sql) {
        $query = sqlite3_query($this->con, $sql);
        if (!$query) die (sqlite3_error($this->con));
        return $query;
    }

    /**
     * wrapper for xx_fetch_object()
     * @param string $sql value of sql request
     * @return object 
     */
    function fetch_object($result) {
    }

    /**
     * wrapper for xx_fetch_array() with a sql parameter
     * @param string $sql value of sql 
     * @return array
     */
    function fetch_array($sql) {
        $query = sqlite3_query($this->con, $sql);
        if (!$query) die (sqlite3_error($this->con));
        return sqlite3_fetch_array($query);
    }

    /**
     * wrapper for xx_fetch_array() with a query parrameter
     * @param object $query result of a sql request
     * @return array
     */
    function fetch_array_query($query) {
        return sqlite3_fetch_array($query);
    }

    /**
     * do an exec
     * @param string $sql value of sql request
     * @return none
     */
    function exec($sql) {
    $ret = sqlite3_exec ($this->con, $sql);
        if (!$ret) die (sqlite3_error($db));
    }

    /**
     * get count of "select count(xxx) ..."
     * in sql query must be AS cnt
     * @param string $sql value of sql request
     * @return integer
     */
    function get_count($sql) {
    }

}


?>
