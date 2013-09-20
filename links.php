<?php

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
echo strftime("%Y-%m-%d %H:%M", time())."<br>";
?>

<br><br><br>
<ul  class="sidebar">
    <li><a href="alerts.php" target="mainFrame" >alerts</a></li>
    <li><a href="hours.php" target="mainFrame" >hours</a></li>
    <ul  class="sidebar">
        <li><a href="hours.php?hours=1" target="mainFrame">half hours</a><br>
        <li><a href="hours.php?hours=2" target="mainFrame">ten minutes</a><br>
    </ul>


    <li><a href="most.php" target="mainFrame" >most</a></li>

</ul>

<?php

html_end();

?>
