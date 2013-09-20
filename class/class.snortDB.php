<?php

require_once("config.php");

class snortDB {
    var $con;

    function snortDB() {
        $c=new config();
        $host=$c->host;
        $user=$c->user;
        $pass=$c->pass;
        $db=$c->db;
        mysql_connect($host,$user,$pass) or die("cannot connect");
        mysql_select_db($db) or die("cannot select db");

    }

    function query($sql) {
        $result = mysql_query($sql);
        return $result;
    }

    function fetch_object($result) {
        $row = mysql_fetch_object($result);
        return $row;
    }

}


?>
