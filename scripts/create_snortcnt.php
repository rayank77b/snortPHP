<?php

/**
 * Snort PHP Monitor
 * @author Andrej Frank <Andrej.Frank@hs-esslingen.de>
 * @copyright Copyright (c) 2008, Andrej Frank, The GNU General Public License (GPL)
*/

require_once("../config.php");
require_once("../class/class.snortDB.php");

function s3exec($db, $sql) {
    echo "[+] create \"$sql\"\n";
    $ret = sqlite3_exec ($db, $sql);
    if (!$ret) die (sqlite3_error($db));
    echo "[+] created\n";
}

echo "[+] getting last time\n";
$dbmysql= new snortDB();
$result = $dbmysql->query("SELECT timestamp FROM event limit 1");
$row = $dbmysql->fetch_array($result);
$t=$row['timestamp'];
$ta1=split(" ", $t);
$ta2=split(":", $ta1[1]);
$t2=$ta1[0]." ".$ta2[0].":00:00";



echo "[+] unlink file\n";
$file='../databases/statistikcount.sq3';
unlink($file);

echo "[+] create/open database\n";
$db = sqlite3_open($file);

echo "[+] created\n";

s3exec($db, "CREATE TABLE youngsttime (id INTEGER, time TIMESTAMP);");

s3exec($db, "CREATE TABLE week (id INTEGER PRIMARY KEY AUTOINCREMENT, 
        time TIMESTAMP, cnt INTEGER, cnticmp INTEGER, cnttcp INTEGER, cntudp INTEGER);");

s3exec($db, "CREATE TABLE day (id INTEGER PRIMARY KEY AUTOINCREMENT, 
        time TIMESTAMP, cnt INTEGER, cnticmp INTEGER, cnttcp INTEGER, cntudp INTEGER);");

s3exec($db, "CREATE TABLE hour (id INTEGER PRIMARY KEY AUTOINCREMENT, 
        time TIMESTAMP, cnt INTEGER, cnticmp INTEGER, cnttcp INTEGER, cntudp INTEGER);");

s3exec($db, "INSERT INTO youngsttime (id,time) VALUES (1,'".$t2."')");

sqlite3_close($db);
echo "[+] ende\n\n";

?>
