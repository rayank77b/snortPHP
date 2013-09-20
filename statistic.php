<?php

/**
 * Snort PHP Monitor
 * @author Andrej Frank <Andrej.Frank@hs-esslingen.de>
 * @copyright Copyright (c) 2008, Andrej Frank, The GNU General Public License (GPL)
*/

require_once("class/html.php");
require_once("config.php");
require_once("class/class.snortDB.php");
require_once("class/class.sqlite3.php");

$hours=0;
#$statistic=$_GET['stat'];
$sec=time();
$file='databases/statistikcount.sq3';

html_head("Statistic");

# Plotkit scripte einbinden
?>
<script type="text/javascript" src="MochiKit/MochiKit.js"></script>
<script type="text/javascript" src="PlotKit/Base.js"></script>
<script type="text/javascript" src="PlotKit/Layout.js"></script>
<script type="text/javascript" src="PlotKit/Canvas.js"></script>
<script type="text/javascript" src="PlotKit/SweetCanvas.js"></script>
<?php

echo '<b>Statistic</b> about TCP, UDP, ICMP etc<br>';

$dbmysql= new snortDB();
$dbsqlite = new sqlite3($file);

$sql="SELECT timestamp, cid FROM event ORDER BY cid DESC LIMIT 1";
$row=$dbmysql->do_sql($sql);
$lasttime=$row->timestamp;
$lastcid=$row->cid;

$r=$dbsqlite->fetch_array("SELECT time FROM youngsttime limit 1");

$sql="SELECT cid FROM event WHERE timestamp>='".$r['time']."' LIMIT 1";
$row=$dbmysql->do_sql($sql);
$cid1=$row->cid;
$lastsqlite3time=$r['time'];
echo "lastsqlite $lastsqlite3time<br>lasttime: $lasttime<br>cids: $cid1 - $lastcid<br><br>";

$hourmax=12;
$cntall=array();

$r=$dbsqlite->fetch_array("SELECT id FROM hour limit 1");
$first_sqlite_id=$r['id'];
$r=$dbsqlite->fetch_array("SELECT id FROM hour ORDER BY id DESC limit 1");
$id=$r['id']-$hourmax+1;

if($id<$first_sqlite_id)
    $id=$first_sqlite_id;

echo "<b>last 12 hours</b><br>";
$i=0; 
$cnt=""; 
$cnttcp=""; 
$cntudp=""; 
$cnticmp="";
$query=$dbsqlite->query("SELECT * FROM hour WHERE id>'$id'");
while($r=$dbsqlite->fetch_array_query($query)) {
    $cntall[]=array($r['time'], $r['cnt'], $r['cnttcp'], $r['cntudp'], $r['cnticmp']);
    $cnt.="[$i, ".$r['cnt']."], ";
    $cnttcp.="[$i, ".$r['cnttcp']."], ";
    $cntudp.="[$i, ".$r['cntudp']."], ";
    $cnticmp.="[$i, ".$r['cnticmp']."], ";
    $i++;
}

$lastcnt=$dbmysql->get_count("SELECT COUNT(cid) AS cnt FROM event WHERE cid>='$cid1'");
$lasttcp=$dbmysql->get_count("SELECT COUNT(cid) AS cnt FROM tcphdr WHERE cid>='$cid1'");
$lastudp=$dbmysql->get_count("SELECT COUNT(cid) AS cnt FROM udphdr WHERE cid>='$cid1'");
$lasticmp=$dbmysql->get_count("SELECT COUNT(cid) AS cnt FROM icmphdr WHERE cid>='$cid1'");

$cntall[]=array($lastsqlite3time,$lastcnt, $lasttcp, $lastudp, $lasticmp);

$cnt.="[$i, ".$lastcnt."] ";
$cnttcp.="[$i, ".$lasttcp."] ";
$cntudp.="[$i, ".$lastudp."] ";
$cnticmp.="[$i, ".$lasticmp."] ";

echo "<table></tr><td><table border=\"0\"><thead><th>Zeit</th><th>All</th><th>Tcp</th><th>udp</th><th>icmp</th></thead>\n";
$ij=1;
foreach($cntall as $c) {
        if($ij==1) {
            $ij=0; $evenodd="even";
        } else {
            $ij=1; $evenodd="odd";
        }
    echo "<tr class=\"$evenodd\"><td>".$c[0]."</td><td> ".$c[1]." </td>";
    echo "<td> ".$c[2]." (".(round(100*$c[2]/$c[1],0))."%) </td>";
    echo "<td> ".$c[3]." (".(round(100*$c[3]/$c[1],0))."%) </td>";
    echo "<td> ".$c[4]." (".(round(100*$c[4]/$c[1],0))."%)</td></tr>\n";
}
echo "</table>\n</td><td>";

?>
<center><b>Counts last 12 hour</b> (all, tcp, udp, icmp)</center><br>
<div><canvas id="graphAlertsHours" height="300" width="400"></canvas></div>
<script type="text/javascript">
var options = {
   "IECanvasHTC": "/plotkit/iecanvas.htc",
   "colorScheme": PlotKit.Base.palette(PlotKit.Base.baseColors()[1]),
   "padding": {left: 75, right: 0, top: 10, bottom: 30},
   
};
function drawGraph() {
    var layout = new PlotKit.Layout("line", options);
    var cnt=[<?php echo $cnt ?>];
    var cnttcp=[<?php echo $cnttcp ?>];
    var cntudp=[<?php echo $cntudp ?>];
    var cnticmp=[<?php echo $cnticmp ?>];
    layout.addDataset("cnt", cnt);  
    layout.addDataset("cnttcp", cnttcp);
    layout.addDataset("cntudp", cntudp);
    layout.addDataset("cnticmp", cnticmp);
    layout.evaluate();
    var canvas = MochiKit.DOM.getElement("graphAlertsHours");
    var plotter = new PlotKit.SweetCanvasRenderer(canvas, layout, options);
    plotter.render();
}
MochiKit.DOM.addLoadEvent(drawGraph);
</script>
<br>

<?php

echo "</td></tr></table><hr>\n";

echo "<b>Last 7 days</b><br>";

$i=0; 
$cnt=""; 
$cnttcp=""; 
$cntudp=""; 
$cnticmp="";


$idlow=$id-(24*7);  # for 7 days
if($idlow<$first_sqlite_id)
    $idlow=$first_sqlite_id;

$query=$dbsqlite->query("SELECT * FROM hour WHERE id>'$idlow' AND id<='$id'");

while($r=$dbsqlite->fetch_array_query($query)) {
    $cnt.="[$i, ".$r['cnt']."], ";
    $cnttcp.="[$i, ".$r['cnttcp']."], ";
    $cntudp.="[$i, ".$r['cntudp']."], ";
    $cnticmp.="[$i, ".$r['cnticmp']."], ";
    $i++;
}
?>
<div><canvas id="graphAlertsWeek" height="400" width="800"></canvas></div>
<script type="text/javascript">
var options = {
   "IECanvasHTC": "/plotkit/iecanvas.htc",
   "colorScheme": PlotKit.Base.palette(PlotKit.Base.baseColors()[1]),
   "padding": {left: 50, right: 0, top: 10, bottom: 30},
   
};
function drawGraphWeek() {
    var layout = new PlotKit.Layout("line", options);
    var cnt=[<?php echo $cnt ?>];
    var cnttcp=[<?php echo $cnttcp ?>];
    var cntudp=[<?php echo $cntudp ?>];
    var cnticmp=[<?php echo $cnticmp ?>];
    layout.addDataset("cnt", cnt);  
    layout.addDataset("cnttcp", cnttcp);
    layout.addDataset("cntudp", cntudp);
    layout.addDataset("cnticmp", cnticmp);
    layout.evaluate();
    var canvas = MochiKit.DOM.getElement("graphAlertsWeek");
    var plotter = new PlotKit.SweetCanvasRenderer(canvas, layout, options);
    plotter.render();
}
MochiKit.DOM.addLoadEvent(drawGraphWeek);
</script>


<?php

echo "</td></tr></table><hr>\n";

echo "<b>Last 30 days</b><br>";

$i=0; 
$cnt=""; 
$cnttcp=""; 
$cntudp=""; 
$cnticmp="";


$idlow=$id-(24*30);  # for 7 days
if($idlow<$first_sqlite_id)
    $idlow=$first_sqlite_id;

$query=$dbsqlite->query("SELECT * FROM hour WHERE id>'$idlow' AND id<='$id'");

while($r=$dbsqlite->fetch_array_query($query)) {
    $cnt.="[$i, ".$r['cnt']."], ";
    $cnttcp.="[$i, ".$r['cnttcp']."], ";
    $cntudp.="[$i, ".$r['cntudp']."], ";
    $cnticmp.="[$i, ".$r['cnticmp']."], ";
    $i++;
}
?>
<div><canvas id="graphAlertsMonth" height="400" width="800"></canvas></div>
<script type="text/javascript">
var options = {
   "IECanvasHTC": "/plotkit/iecanvas.htc",
   "colorScheme": PlotKit.Base.palette(PlotKit.Base.baseColors()[1]),
   "padding": {left: 50, right: 0, top: 10, bottom: 30},
   
};
function drawGraphMonth() {
    var layout = new PlotKit.Layout("line", options);
    var cnt=[<?php echo $cnt ?>];
    var cnttcp=[<?php echo $cnttcp ?>];
    var cntudp=[<?php echo $cntudp ?>];
    var cnticmp=[<?php echo $cnticmp ?>];
    layout.addDataset("cnt", cnt);  
    layout.addDataset("cnttcp", cnttcp);
    layout.addDataset("cntudp", cntudp);
    layout.addDataset("cnticmp", cnticmp);
    layout.evaluate();
    var canvas = MochiKit.DOM.getElement("graphAlertsMonth");
    var plotter = new PlotKit.SweetCanvasRenderer(canvas, layout, options);
    plotter.render();
}
MochiKit.DOM.addLoadEvent(drawGraphMonth);
</script>


<?php
 
html_end();

?>




















