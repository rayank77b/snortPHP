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

function info_login() {
    $str="<b>Login: </b><br>";

    $cmd= "/usr/bin/last | /usr/bin/tr -s ' ' | /bin/fgrep -v begins";
    exec($cmd, $erg, $ret);

    $str.="\n\n<table border=0>\n";
    $str.="<thead><th>User</th><th>Terminal</th><th>from</th><th>time</th></thead>\n";
    $ij=1;
    foreach($erg as $a1) {
        if($ij==1) {
            $ij=0; $evenodd="even";
        } else {
            $ij=1; $evenodd="odd";
        }
        $a2=split(" ", $a1);
        $str.="<tr class=\"".$evenodd."\"><td>$a2[0]</td><td>$a2[1]</td><td>$a2[2]</td><td>";
        $str.=join(" ", array_slice($a2,3))."</td></tr>\n";
    }
    $str.="</table>\n\n";

    $erg="";
    $str.="<br><b>Lastlogin: </b><br>";
    $cmd= "/usr/bin/lastlog | /bin/fgrep -v 'Never logged in' | /bin/fgrep -v Username  | /usr/bin/tr -s ' '";
    exec($cmd, $erg, $ret);
    $str.="\n\n<table border=0>\n";
    $str.="<thead><th>Username</th><th>Port</th><th>From</th><th>Latest</th></thead>\n";
    $ij=1;
    foreach($erg as $a1) {
        if($ij==1) {
            $ij=0; $evenodd="even";
        } else {
            $ij=1; $evenodd="odd";
        }
        $a2=split(" ", $a1);
        if(strncmp($a2[1], "tty", 3)==0) {
            $str.="<tr class=\"".$evenodd."\"><td>$a2[0]</td><td>$a2[1]</td><td> </td><td>";
            $str.=join(" ", array_slice($a2, 2))."</td></tr>\n";        
        } else {
            $str.="<tr class=\"".$evenodd."\"><td>$a2[0]</td><td>$a2[1]</td><td>$a2[2]</td><td>";
            $str.=join(" ", array_slice($a2,3))."</td></tr>\n";
        }
    }
    $str.="</table>\n\n";

    return $str;
}

function info_df() {
    $str="<b>Harddrive: </b><br>";

    $cmd= "/bin/df -h | /bin/fgrep -v 'Filesystem' | /usr/bin/tr -s ' '";
    exec($cmd, $erg, $ret);
    $str.="\n\n<table border=0>\n";
    $str.="<thead><th>Filesystem</th><th>Size</th><th>Used</th><th>Avail</th><th>Percent</th><th>Mounted on</th></thead>\n";
    $ij=1;
    foreach($erg as $a1) {
        if($ij==1) {
            $ij=0; $evenodd="even";
        } else {
            $ij=1; $evenodd="odd";
        }
        $str.="<tr class=\"$evenodd\">";
        foreach(split(" ", $a1) as $v) {
            $str.="<td>$v</td>";
        }
        $str.="</tr>\n";
    }
    $str.="</table>\n\n";

    return $str;
}

function info_mem() {
    $str="<b>Memory (in MB): </b><br>";

    $cmd= "/usr/bin/free -tom | /bin/fgrep -v 'used' | /usr/bin/tr -s ' '";
    exec($cmd, $erg, $ret);
    $str.="\n\n<table border=0>\n";
    $str.="<thead><th> </th><th>total</th><th>used</th><th>free</th><th>free </th></thead>\n";
    $ij=1;
    foreach($erg as $a1) {
        if($ij==1) {
            $ij=0; $evenodd="even";
        } else {
            $ij=1; $evenodd="odd";
        }
        $a2=split(" ", $a1);
        $str.="<tr class=\"".$evenodd."\"><td>$a2[0]</td><td>$a2[1]</td><td>$a2[2]</td><td>$a2[3]</td><td>".sprintf("%2.2f",$a2[3]/$a2[1]*100)." %</td></tr>";
    }
    $str.="</table>\n\n";

    return $str;
}



html_head("SystemInfo");


echo info_login()."<hr>\n";
echo info_df()."<hr>\n";
echo info_mem()."<hr>\n";

html_end();

?>
