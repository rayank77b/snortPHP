<?php

/**
 * Snort PHP Monitor
 * @author Andrej Frank <Andrej.Frank@hs-esslingen.de>
 * @copyright Copyright (c) 2008, Andrej Frank, The GNU General Public License (GPL)
*/


/**
 * class for snort database connections
 * @package class
 */
class snortDB {

    /**
     * safe connection, not yet in use
     * @var int $con value of connection
     */
    var $con;

    /** 
     * constructor, connect to sql-server and select a db
     * @param string $db value of database name
     */
    function snortDB($db="snort") {
        $c=new config();
        $host=$c->host;
        $user=$c->user;
        $pass=$c->pass;
        mysql_connect($host,$user,$pass) or die("cannot connect");
        mysql_select_db($db) or die("cannot select db");
    }

    /**
     * wrapper for xx_qurey()
     * @param string $sql value of sql request
     * @return object 
     */
    function query($sql) {
        return mysql_query($sql);
    }

    /**
     * wrapper for xx_fetch_object()
     * @param string $sql value of sql request
     * @return object 
     */
    function fetch_object($result) {
        return mysql_fetch_object($result);
    }

    /**
     * wrapper for xx_fetch_array()
     * @param object $result result of a query
     * @return array
     */
    function fetch_array($result) {
        return mysql_fetch_array($result);
    }

    /**
     * do query and get a result via object
     * @param string $sql value of sql request
     * @return object 
     */
    function do_sql($sql) {
        $result = mysql_query($sql);
        return mysql_fetch_object($result);
    }

    /**
     * get count of "select count(xxx) ..."
     * in sql query must be AS cnt
     * @param string $sql value of sql request
     * @return integer
     */
    function get_count($sql) {
        $result = mysql_query($sql);
        $row = mysql_fetch_object($result);
        return $row->cnt;
    }

}


?>
