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

$extcid=0;
$extcid=get_integer('extcid');

function build_row($o, $color) {
        $erg="";
        $erg.='<td>'.$o->cid.'</td>';
        $erg.='<td><b>'.$o->ip_src.'</b>:'.$o->sport.'</td>';
        $erg.='<td><b>'.$o->ip_dst.'</b>:'.$o->dport.'</td>';
        $erg.='<td>'.set_link_description($o->sig_name, 'alertInfo.php', $o->signature ).'</td>';
        $erg.='<td>'.$o->timestamp.'</td>';
        $erg.='<td><img src="images/flag'.$color.'.png" alt="'.$color.'"></td>';
        return $erg;
    }

function print_payload($cid,$pl) {
    echo "Infos about CID: $cid<br>";
    $plar16=str_split($pl, 32);

    $cntlen=0;
    echo "<div class=\"hexdump\">";
    foreach($plar16 as $p16) {
        printf("%04.d :", $cntlen);
        $p8b=str_split($p16, 16);
        $p1ba=str_split($p8b[0]);
        $p1bb=str_split($p8b[1]);
        foreach($p1ba as $p) {
            printf(" %x", $p);      
            printf("%x", next($p1ba));
        }
        echo "&nbsp;";
        foreach($p1bb as $p) {
            printf(" %x", $p);      
            printf("%x", next($p1bb));
        }
        echo "&nbsp;"; echo "&nbsp;";
        foreach($p1ba as $p) {
            $z=$p*10+next($p1ba);
            if($z>25) { printf("%c", $z); } else { echo "."; }
        }
        foreach($p1bb as $p) {
            $z=$p*10+next($p1bb);
            if($z>25) { printf("%c", $z); } else { echo "."; }
        }
        echo "<br>";
        $cntlen=$cntlen+16;
    }
    echo "</div>";
}

html_head("Alerts");

        $db= new snortDB();
        $sql = "SELECT 
            *
            FROM event 
            ORDER BY cid DESC limit 10
        ";
        $ar=array();
        $result = $db->query($sql);
        while($row = $db->fetch_object($result)) {
             $alerts[]=new SnortObject($row);
        }

        foreach($alerts as $a) {
            SnortObject::set_signature($a, $db);
            SnortObject::set_iphdr($a, $db);
            SnortObject::set_layer4($a, $db);
        }
      
        echo '<table>';
        echo '<tr>'.th(array("CID", "SRC", "DST", "ALERTS", "TIME", "PRIO")).'</tr>'; 

        $ij=0;
        foreach ($alerts as $a) {
            if($ij==1) {
                $ij=0;
                $evenodd="even";
            } else {
                $ij=1;
                $evenodd="odd";
            }
            switch ($a->sig_priority) {
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
            echo '<tr class="'.$evenodd.'">'.build_row($a, $color).'</tr>'."\n";
        }
        echo '</table>'; 

echo "<hr>";
$cid=$alerts[0]->cid;
#$cid=214620;
$sql="SELECT data_payload AS payload FROM data WHERE cid='".$cid."'";
$row=$db->do_sql($sql);
$payload=$row->payload;

$h=fopen("/tmp/test.a.bin", "w");
fwrite($h, $payload);

print_payload($cid,$payload);

html_end();

?>
