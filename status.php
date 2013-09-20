<?php

require_once("class/html.php");
require_once("config.php");
require_once("class/class.snortDB.php");
require_once("class/class.SnortObject.php");

html_head_links("Links");

function status() {
        $cmd= "/bin/ps afx | fgrep '/usr/sbin/snort' | fgrep -v fgrep";

        exec($cmd, $erg, $ret);

        $data="";
        if($ret==0) {
            $data.=font("snort OK   on ".$interface, "green")."<br>";
        } else {
            $data.=font("snort not running!!!", "red")."<br>";
        }

        $cmd= "/bin/ps afx | fgrep '/usr/bin/mysqld' | fgrep -v fgrep";

        exec($cmd, $erg, $ret);

        if($ret==0) {
            $data.=font("mysqld OK", "green")."<br>";
        } else {
            $data.=font("mysqld not running!!!", "red")."<br>";
        }

        return $data;
}

echo status();


html_end();

?>
