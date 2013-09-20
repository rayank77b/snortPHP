<?php

/**
 * Snort PHP Monitor
 * @author Andrej Frank <Andrej.Frank@hs-esslingen.de>
 * @copyright Copyright (c) 2008, Andrej Frank, The GNU General Public License (GPL)
*/

require_once("class/html.php");
require_once("config.php");
require_once("class/class.snortDB.php");

$hours=0;
$hours=get_integer('hours');

html_head("Alerts");

$erg="";
$datum=strftime("%Y-%m-%d ", time());
#$datum=strftime("%Y-%m-%d ", 1208502073);

$xticksarray=array();
$ar=array();
$db = new snortDB();


if($hours==0) {  # hours
    for($i=0; $i<24; $i++) {
        $sql = "SELECT count(cid) as cntcid FROM event WHERE timestamp>'".$datum.$i.":00:00'";
        $sql.= "AND timestamp<='".$datum.($i+1).":00:00'";
        $result = $db->query($sql);
        $row = $db->fetch_object($result);
        $ar[]='['.$i.','.$row->cntcid.']';
        $xticksarray[]='{label:"'.($i).'", v:'.$i.'}';
    }
} else if($hours==1) {   #  halfhours
    for($i=0; $i<24; $i++) {
        $sql = "SELECT count(cid) as cntcid FROM event WHERE timestamp>'".$datum.$i.":00:00'";
        $sql.= "AND timestamp<='".$datum.$i.":30:00'";
        $result = $db->query($sql);
        $row = $db->fetch_object($result);
        $ar[]='['.($i*2).','.$row->cntcid.']';
        $sql = "SELECT count(cid) as cntcid FROM event WHERE timestamp>'".$datum.$i.":30:00'";
        $sql.= "AND timestamp<='".$datum.($i+1).":00:00'";
        $result = $db->query($sql);
        $row = $db->fetch_object($result);
        $ar[]='['.($i*2+1).','.$row->cntcid.']';   
        $xticksarray[]='{label:"'.($i).'", v:'.($i*2).'}';         
    }
} else if($hours==2) {   # Ten Minutes
    for($i=0; $i<24; $i++) {
        for($j=0; $j<6; $j++) {
            $sql = "SELECT count(cid) as cntcid FROM event WHERE ";
            $sql.= "timestamp>'".$datum.$i.":".$j."0:00'";
            $sql.= "AND timestamp<='".$datum.$i.":".$j."9:59.999'";
            $result = $db->query($sql);
            $row = $db->fetch_object($result);
            $ar[]='['.($i*6+$j).','.$row->cntcid.']';
        }            
        $xticksarray[]='{label:"'.($i).'", v:'.($i*6).'}';
    }
} else if($hours==3) {   # days
    $sec=time();
    for($i=0; $i<32; $i++) {    
        $secbefore=$sec-(3600*24)*(31-$i);
        $datum=strftime("%Y-%m-%d", $secbefore);
        $sql = "SELECT count(cid) as cntcid FROM event WHERE timestamp>'".$datum." 00:00:00'";
        $sql.= "AND timestamp<='".$datum."23:59:59.99'";
        $result = $db->query($sql);
        $row = $db->fetch_object($result);
        $ar[]='['.($i).','.$row->cntcid.']';
        $xticksarray[]='{label:"'.($i+1).'", v:'.$i.'}';
    }  
}



$data=implode(',', $ar);
$xticks=implode(',', $xticksarray);
?>

<script type="text/javascript" src="MochiKit/MochiKit.js"></script>
<script type="text/javascript" src="PlotKit/Base.js"></script>
<script type="text/javascript" src="PlotKit/Layout.js"></script>
<script type="text/javascript" src="PlotKit/Canvas.js"></script>
<script type="text/javascript" src="PlotKit/SweetCanvas.js"></script>
<br><br>
<div><canvas id="graphAlerts" height="450" width="800"></canvas></div>
<script type="text/javascript">
var options = {
   "IECanvasHTC": "/plotkit/iecanvas.htc",
   "colorScheme": PlotKit.Base.palette(PlotKit.Base.baseColors()[1]),
   "padding": {left: 75, right: 0, top: 10, bottom: 30},
   "xTicks": [<?php echo $xticks ?>]
};
function drawGraph() {
    var layout = new PlotKit.Layout("bar", options);
    layout.addDataset("sqrt", [ <?php echo $data ?> ]);
    layout.evaluate();
    var canvas = MochiKit.DOM.getElement("graphAlerts");
    var plotter = new PlotKit.SweetCanvasRenderer(canvas, layout, options);
    plotter.render();
}
MochiKit.DOM.addLoadEvent(drawGraph);
</script>
<br>
<a href="hours.php?hours=0">hours</a><br>
<a href="hours.php?hours=1">half hours</a><br>
<a href="hours.php?hours=2">ten minutes</a><br>

<?php

html_end();

?>
