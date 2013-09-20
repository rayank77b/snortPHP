<?php

require_once("class/html.php");
require_once("config.php");
require_once("class/class.snortDB.php");

$hours=0;
$hours=$_GET['hours'];

html_head("Alerts");

        $erg="";
        $datum=strftime("%Y-%m-%d ", time());
 #       $datum=strftime("%Y-%m-%d ", 1208502073);

        $ar=array();
        $db = new snortDB();

    if($hours==0) {
        for($i=0; $i<24; $i++) {
            $sql = "SELECT count(cid) as cntcid FROM event WHERE timestamp>'".$datum.$i.":00:00'";
            $sql.= "AND timestamp<='".$datum.($i+1).":00:00'";
            $result = $db->query($sql);
            $row = $db->fetch_object($result);
            $ar[]='['.$i.','.$row->cntcid.']';
        }
    } else if($hours==1) {
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
        }
    } else {
        for($i=0; $i<24; $i++) {
            for($j=0; $j<6; $j++) {
                $sql = "SELECT count(cid) as cntcid FROM event WHERE ";
                $sql.= "timestamp>'".$datum.$i.":".$j."0:00'";
                $sql.= "AND timestamp<='".$datum.$i.":".$j."9:59.999'";
                $result = $db->query($sql);
                $row = $db->fetch_object($result);
                $ar[]='['.($i*6+$j).','.$row->cntcid.']';
            }
        }
    }



        $data=implode(',', $ar);
?>

<script type="text/javascript" src="MochiKit/MochiKit.js"></script>
<script type="text/javascript" src="PlotKit/Base.js"></script>
<script type="text/javascript" src="PlotKit/Layout.js"></script>
<script type="text/javascript" src="PlotKit/Canvas.js"></script>
<script type="text/javascript" src="PlotKit/SweetCanvas.js"></script>
<br><br>
<div><canvas id="graphAlerts" height="450" width="800"></canvas></div>
<script type="text/javascript">
function drawGraph() {
    var layout = new PlotKit.Layout("bar", {});
    layout.addDataset("sqrt", [ <?php echo $data ?> ]);
    layout.evaluate();
    var canvas = MochiKit.DOM.getElement("graphAlerts");
    var plotter = new PlotKit.SweetCanvasRenderer(canvas, layout, {});
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
