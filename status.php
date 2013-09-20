<?php

/**
 * Snort PHP Monitor
 * @author Andrej Frank <Andrej.Frank@hs-esslingen.de>
 * @copyright Copyright (c) 2008, Andrej Frank, The GNU General Public License (GPL)
*/

require_once("class/html.php");
require_once("config.php");
require_once("class/class.snortDB.php");
require_once("class/class.SnortObject.php");

// holt den zeitstempel aus event tabelle
function get_time_and_count() {

    $db= new snortDB();

    $time="Zeitstatus: ";

    $sql = "SELECT timestamp AS zeit FROM event limit 1";
    $result = $db->query($sql);
    $row = $db->fetch_object($result);
    $time .= $row->zeit." to ";

    $sql = "SELECT timestamp AS zeit FROM event ORDER BY cid DESC limit 1";
    $result = $db->query($sql);
    $row = $db->fetch_object($result);
    $time .= $row->zeit."<br>";

    $sql = "SELECT count(cid) AS cnt FROM event";
    $result = $db->query($sql);
    $row = $db->fetch_object($result);
    $time .= "Count: ".$row->cnt."<br>";

    return $time;
}

// liefert status ob snort und mysql laufen
function status() {
        $cmd= "/bin/ps afx | fgrep '/usr/sbin/snort' | fgrep -v fgrep";

        exec($cmd, $erg, $ret);

        $data="";
        if($ret==0) {
            $data.=font("snort OK   on ".$interface, "green")." || ";
        } else {
            $data.=font("snort not running!!!", "red")." || ";
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

html_head_status("Links");

echo status();
echo "Time now: ".strftime("%Y-%m-%d %H:%M", time())."<br>";


echo get_time_and_count();



html_end();

?>
