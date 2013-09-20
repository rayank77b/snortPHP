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

html_head_links("Links");

?>

Snort Monitor<br>
Version: <?php 
$c=new config(); 
echo $c->version."<br>"; 

?>

<br><br><br>
<ul  class="sidebar">
    <li><a href="alerts.php" target="mainFrame" >alerts</a></li>
    <li><a href="hours.php" target="mainFrame" >hours</a></li>
    <ul  class="sidebar">
        <li><a href="hours.php?hours=3" target="mainFrame">per days</a><br>
        <li><a href="hours.php?hours=1" target="mainFrame">half hours</a><br>
        <li><a href="hours.php?hours=2" target="mainFrame">ten minutes</a><br>
    </ul>


    <li><a href="most.php" target="mainFrame" >most</a></li>
    <ul  class="sidebar">
        <li><a href="most.php?priority=1" target="mainFrame">most high</a><br>
        <li><a href="most.php?priority=2" target="mainFrame">most middle</a><br>
        <li><a href="most.php?priority=3" target="mainFrame">most low</a><br>
    </ul>
    <li><a href="statistic.php" target="mainFrame" >statistic</a></li>
    <li><a href="systeminfo.php" target="mainFrame" >SystemInfo</a></li>
    <li><a href="todo.php" target="mainFrame" >TODO</a></li>

</ul>

<?php

html_end();

?>
