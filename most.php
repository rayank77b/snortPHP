<?php

/**
 * Snort PHP Monitor
 * @author Andrej Frank <Andrej.Frank@hs-esslingen.de>
 * @copyright Copyright (c) 2008, Andrej Frank, The GNU General Public License (GPL)
*/

require_once("class/html.php");
require_once("config.php");
require_once("class/class.snortDB.php");

$mostpriority=0;
$mostpriority=get_integer('priority');

function get_cnt($db1, $sql1) {
    $result = $db1->query($sql1);
    $row = $db1->fetch_object($result);
    return $row->cnt;
}

html_head("Most alerts last Hour");

    $erg="";
    $sec=time();
    $sec= $sec - 3600; # 1 hour before 
    $datum=strftime("%Y-%m-%d %H:%M:%S", $sec);
 #  $datum=strftime("%Y-%m-%d ", 1208502073);

    $ar=array();
    $db = new snortDB();

    $sql="SELECT count(event.cid) AS cnt FROM event WHERE timestamp>'".$datum."'";
    $gesamt=get_cnt($db, $sql);

    $sql="SELECT count(event.cid) AS cnt FROM event LEFT JOIN signature ON event.signature=signature.sig_id WHERE timestamp>'".$datum."' AND signature.sig_priority='1' ";
    $prio1=get_cnt($db, $sql);
    $sql="SELECT count(event.cid) AS cnt FROM event LEFT JOIN signature ON event.signature=signature.sig_id WHERE timestamp>'".$datum."' AND signature.sig_priority='2' ";
    $prio2=get_cnt($db, $sql);
    $sql="SELECT count(event.cid) AS cnt FROM event LEFT JOIN signature ON event.signature=signature.sig_id WHERE timestamp>'".$datum."' AND signature.sig_priority='3' ";
    $prio3=get_cnt($db, $sql);

    $data="[0,$prio1],[1,$prio2],[2,$prio3]";

?>

<script type="text/javascript" src="MochiKit/MochiKit.js"></script>
<script type="text/javascript" src="PlotKit/Base.js"></script>
<script type="text/javascript" src="PlotKit/Layout.js"></script>
<script type="text/javascript" src="PlotKit/Canvas.js"></script>
<script type="text/javascript" src="PlotKit/SweetCanvas.js"></script>
<br><br>
<table border="0">
<tr><td></td><td><b>Priority in percent last hour</b></td></tr>
<tr><td>
    <div><canvas id="graphPriority" height="200" width="200"></canvas></div>
</td><td>
    <?php
        echo "<table>";
        echo "<tr><td>Gesamt Alerts:</td><td>$gesamt</td><td>100 %</td></tr>";
        echo "<tr><td>High Priority:</td><td> $prio1</td><td>".((int)(100*$prio1/$gesamt))." %</td></tr>";
        echo "<tr><td>Middle Priority:</td><td> $prio2</td><td>".((int)(100*$prio2/$gesamt))." %</td></tr>";
        echo "<tr><td>Low Priority:</td><td> $prio3</td><td>".((int)(100*$prio3/$gesamt))." %</td></tr>";
        echo "</table>";

    ?>
</td></tr>
<tr><td></td><td><b>Mosts alerts in percent last hour</b></td></tr>
<tr><td>
    <div><canvas id="graphMosts15" height="300" width="300"></canvas></div>
</td><td>
<?php
    $sql="SELECT sig_id AS cnt FROM signature ORDER BY sig_id DESC LIMIT 1";
    $last_sig_id=get_cnt($db, $sql);
    $ids=array();
    $sig_id=array();
    $prio=array();
    for($i=1;$i<=$last_sig_id;$i++) {
        $sql="SELECT count(event.cid) AS cnt
                FROM event
                WHERE timestamp>'".$datum."' AND signature='$i'";
        $lcnt=get_cnt($db,$sql);
        if($lcnt>0) {
            $sql="SELECT sig_name, sig_priority FROM signature WHERE sig_id='$i'";
            $result = $db->query($sql);
            $row = $db->fetch_object($result);
            $ids[$row->sig_name]=$lcnt;
            $sig_id[$row->sig_name]=$i;
            $prio[$row->sig_name]=$row->sig_priority;
        }
    }

    arsort($ids);
    $c=0;
    $summe=0;
    echo "<table>";
    $articks=array();
    $ardata2=array();
    $evenodd="odd";
    foreach($ids as $k=>$v) {
        $summe=$summe+$v;
        if($prio[$k]==$mostpriority or $mostpriority==0) {
            $color="colornone";
            if($ij==1) {
                $ij=0;
                $evenodd="odd";
            } else {
                $ij=1;
                $evenodd="even";
            }
            switch ($prio[$k]) {
                case 1:
                    $color="red";
                    break;
                case 2:
                    $color="yellow";
                    break;
                case 3:
                    $color="blue";
                    break;
                default:
                    $color="none";
            }
            $articks[]='{label:"'.$v.'", v:'.$c.'}';
            $ardata2[]='['.$c.', '.$v.']';
            echo '<tr class="'.$evenodd.'"><td>'.$v.'</td><td>';
            echo set_link_description($k, 'alertInfo.php', $sig_id[$k])."</td>";
            echo '<td><img src="images/flag'.$color.'.png" alt="'.$color.'"></td></tr>';
            $c++;
            if($c>10)
                break;
        }
    }
    $rest=$gesamt-$summe;
    echo '<tr class="'.$evenodd.'"><td>'.$rest."</td><td>Rest</td>";
    echo '<td><img src="images/flagnone.png" alt="none"></td></tr>';
    echo "</table>";

    $articks[]='{label:"Rest", v:'.$c.'}';
    $ardata2[]='['.$c.', '.$rest.']';

    $ticks=implode(',', $articks);
    $data2=implode(',', $ardata2);
?>


</tr>
</table>
<script type="text/javascript">

var options = {
   "IECanvasHTC": "/plotkit/iecanvas.htc",
   "colorScheme": PlotKit.Base.palette(PlotKit.Base.baseColors()[1]),
   "padding": {left: 0, right: 0, top: 10, bottom: 30},
   "xTicks": [{label:"High", v:0},{label:"Middle", v:1},{label:"Low", v:2}]
};
function drawGraph() {
    var layout = new PlotKit.Layout("pie", options);
    layout.addDataset("Priorities", [ <?php echo $data ?> ]);
    layout.evaluate();
    var canvas = MochiKit.DOM.getElement("graphPriority");
    var plotter = new PlotKit.SweetCanvasRenderer(canvas, layout, options);
    plotter.render();
}
MochiKit.DOM.addLoadEvent(drawGraph);


var options2 = {
   "IECanvasHTC": "/plotkit/iecanvas.htc",
   "colorScheme": PlotKit.Base.palette(PlotKit.Base.baseColors()[1]),
   "padding": {left: 0, right: 0, top: 10, bottom: 30},
   "xTicks": [<?php echo $ticks ?>]
};
function drawGraph2() {
    var layout = new PlotKit.Layout("pie", options2);
    layout.addDataset("Mosts15", [ <?php echo $data2 ?> ]);
    layout.evaluate();
    var canvas = MochiKit.DOM.getElement("graphMosts15");
    var plotter = new PlotKit.SweetCanvasRenderer(canvas, layout, options2);
    plotter.render();
}
MochiKit.DOM.addLoadEvent(drawGraph2);

</script>
<br>
<?php
#print_r($prio);
html_end();

?>
