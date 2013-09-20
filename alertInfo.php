<?php

require_once("class/html.php");
require_once("config.php");
require_once("class/class.snortDB.php");
require_once("class/class.SnortObject.php");

html_head("Alerts");

$sid=$_GET['sigid'];

echo "Info about Signature: $sid <hr>";
        $erg=array();
        $db = new snortDB();
        $sql = "SELECT
            signature.sig_name AS description, 
            signature.sig_priority AS priority, 
            signature.sig_rev AS revesion, 
            signature.sig_sid AS nummer, 
            sig_class.sig_class_name AS class_name 
            FROM signature 
            LEFT JOIN sig_class ON sig_class.sig_class_id=signature.sig_class_id
            WHERE signature.sig_id='$sid'
        ";
        $result = $db->query($sql);
        $row = $db->fetch_object($result);
        $erg['Description']=$row->description;
        $erg['priority']=$row->priority;
        $erg['Classe']=$row->class_name;

        $sql = "SELECT 
            reference.ref_tag as tag,
            reference_system.ref_system_name as system
            FROM sig_reference
            INNER JOIN reference ON sig_reference.ref_id=reference.ref_id
            INNER JOIN reference_system ON reference.ref_system_id=reference_system.ref_system_id
            WHERE sig_reference.sig_id='$sid'
        ";
        $result = $db->query($sql);
        $a=array();
        while($row = $db->fetch_object($result)) {
            if(strstr($row->system, "url")) {
                $a[]="<A href=\"http://".$row->tag."\">".$row->tag."</A>";
            } else if(strstr($row->system, "cve")) {
                $a[]="<A href=\"http://cve.mitre.org/cgi-bin/cvename.cgi?name=CAN-".$row->tag."\">CVE (cve.mitre.org) - ".$row->tag."</A>";
                $a[]="<A href=\"http://nvd.nist.gov/nvd.cfm?cvename=CVE-".$row->tag."\">CVE (nvd.nist.gov) - ".$row->tag."</A>";
            } else if(strstr($row->system, "bugtraq")) {
                $a[]="<A href=\"http://www.securityfocus.com/bid/".$row->tag."\">Bugtraq - ".$row->tag."</A>";
            } else {
                $a[]=$row->tag." ".$row->system;
            }
        }
        $erg['Reference']=listing($a);
        
        table_hash($erg);


html_end();

?>
