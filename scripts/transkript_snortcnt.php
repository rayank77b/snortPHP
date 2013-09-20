<?php

/**
 * Snort PHP Monitor
 * @author Andrej Frank <Andrej.Frank@hs-esslingen.de>
 * @copyright Copyright (c) 2008, Andrej Frank, The GNU General Public License (GPL)
*/

require_once("../config.php");
require_once("../class/class.snortDB.php");

function s3exec($db, $sql) {
    echo "[+] SQL \"$sql\"\n";
    $ret = sqlite3_exec ($db, $sql);
    if (!$ret) die (sqlite3_error($db));
    echo "[+] exec\n";
}

function get_count($db, $yt, $nt) {
    $sql="SELECT count(cid) as cnt 
        FROM event 
        WHERE timestamp>='".strftime("%Y-%m-%d %H:%M:%S",$yt)."' 
            AND timestamp<'".strftime("%Y-%m-%d %H:%M:%S",$nt)."'";
    #echo "[+] SQL: ".$sql."\n";
    $row=$db->do_sql($sql);
    $cnt=$row->cnt;
    #echo "[+] CNT: $cnt\n";
    return $cnt;
}

function get_cid($db, $t) {
    $sql="SELECT cid FROM event 
        WHERE timestamp>='".strftime("%Y-%m-%d %H:%M:%S",$t)."' LIMIT 1";
    $row=$db->do_sql($sql);
    #echo "[+] CID: ".$row->cid."\n"; 
    return $row->cid;
}

function get_count2($layer, $db,  $yc, $oc) {
    $sql="SELECT count(cid) as cnt 
        FROM ".$layer."hdr
        WHERE cid>='$yc'AND cid<'$oc'";
    #echo "[+] SQL: ".$sql."\n";
    $row=$db->do_sql($sql);
    $cnt=$row->cnt;
    #echo "[+] CNT $layer: $cnt\n";
    return $cnt;
}

function get_newtime($db) {
    $sql="SELECT timestamp FROM event ORDER BY cid DESC LIMIT 1";
    $row=$db->do_sql($sql);
    return strtotime($row->timestamp);
}

$file='../databases/statistikcount.sq3';

echo "[+] connect to DB\n";
$dbmysql= new snortDB();
$dbsqlite = sqlite3_open($file);

echo "[+] get latest time\n";
$query = sqlite3_query($dbsqlite, "SELECT time FROM youngsttime limit 1");
if (!$query) 
    die (sqlite3_error($dbsqlite));

$row = sqlite3_fetch_array($query);

echo "[+] LASTTIME: ".strtotime($row['time'])." ".$row['time']."\n";
$newtime=get_newtime($dbmysql);
echo "[+] NEWTIME: $newtime ".strftime("%Y-%m-%d %H:%M:%S",$newtime)."\n";

for($youngsttime=strtotime($row['time']), $nexttime=$youngsttime+3600, $i=0; 
        $i<6;
        $youngsttime+=3600, $nexttime+=3600, $i++) {

echo "[-] youngsttime: $youngsttime  \n[-] nexttime:    $nexttime  \n[-] newtime:     $newtime\n";
    if($nexttime>=$newtime) 
        break;

    $all=get_count($dbmysql, $youngsttime, $nexttime);
    $ycid=get_cid($dbmysql, $youngsttime);
    $ocid=get_cid($dbmysql, $nexttime);
    $tcp=get_count2("tcp", $dbmysql, $ycid, $ocid);
    $udp=get_count2("udp", $dbmysql, $ycid, $ocid);
    $icmp=get_count2("icmp", $dbmysql, $ycid, $ocid);

    echo "[+] ALL: $all  TCP: $tcp(".round(100*$tcp/$all,2)."%) ";
    echo "UDP: $udp(".round(100*$udp/$all,2)."%) ";
    echo "ICMP: $icmp(".round(100*$icmp/$all,2)."%)\n";

    $sql="INSERT INTO hour (time, cnt, cnticmp, cnttcp, cntudp) 
            VALUES ('".strftime("%Y-%m-%d %H:%M:%S",$youngsttime)."', 
                $all, $icmp, $tcp, $udp)";
    s3exec($dbsqlite, $sql);
    $sql= "UPDATE youngsttime 
            SET time='".strftime("%Y-%m-%d %H:%M:%S",$nexttime)."'
            WHERE id==1";
    s3exec($dbsqlite, $sql);
}



echo "[+] end\n";
?>
