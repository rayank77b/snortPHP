<?php

require_once("class/html.php");
require_once("config.php");
require_once("class/class.snortDB.php");
require_once("class/class.SnortObject.php");


    function build_row($o) {
        $erg="";
        $erg.='<td>'.$o->cid.'</td>';
        $erg.='<td>'.$o->ip_src.'</td>';
        $erg.='<td>'.$o->ip_dst.'</td>';
        $erg.='<td>'.$o->sport.'</td>';
        $erg.='<td>'.$o->dport.'</td>';
        $erg.='<td>'.set_link_description($o->sig_name, 'alertInfo.php', $o->signature).'</td>';
        $erg.='<td>'.$o->timestamp.'</td>';
        return $erg;
    }

html_head("Alerts");

        $db=$sdb = new snortDB();
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
        echo '<tr>'.th(SnortObject::get_headers()).'</tr>'; 

        foreach ($alerts as $a) {
            echo '<tr>'.build_row($a).'</tr>';
        }
        echo '</table>';

html_end();

?>
