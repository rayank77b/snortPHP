<?php

/**
 * Snort PHP Monitor
 * @author Andrej Frank <Andrej.Frank@hs-esslingen.de>
 * @copyright Copyright (c) 2008, Andrej Frank, The GNU General Public License (GPL)
*/

class SnortObject {
    public $sid;
    public $cid;
    public $signature;
    public $timestamp;

    public $sig_name;
    public $sig_priority;

    public $ip_src;
    public $ip_dst;
    public $ip_protokol;

    public $sport;
    public $dport;

    public $headers;
    public $data;
    
    function SnortObject($e) {
        if(is_object($e) ) {
            $this->sid=$e->sid;
            $this->cid=$e->cid;
            $this->signature=$e->signature;
            $this->timestamp=$e->timestamp;
        } else {
            $this->sid=0;
            $this->cid=0;
            $this->signature=1;
            $this->timestamp='1970-01-01 00:00:00';
        }
    }

    static function set_signature($o, $db) {
        $sql = "SELECT 
              *
              FROM signature 
              WHERE sig_id='".$o->signature."'";
        $result = $db->query($sql);
        $row = $db->fetch_object($result);
        $o->sig_name=$row->sig_name;
        $o->sig_priority=$row->sig_priority;
    }

    static function set_iphdr($o, $db) {
        $sql = "SELECT 
                inet_ntoa(ip_src) AS ip_src,
                inet_ntoa(ip_dst) AS ip_dst,
                ip_proto
                FROM iphdr 
                WHERE cid='".$o->cid."'";
        $result = $db->query($sql);
        $row = $db->fetch_object($result);
        $o->ip_src=$row->ip_src;
        $o->ip_dst=$row->ip_dst;
        $o->ip_protokol=$row->ip_proto;
    }

    static function set_layer4($o, $db) {
        switch($o->ip_protokol) {
            case 1:
                $sql = "SELECT 
                    icmp_type AS sport,
                    icmp_code AS dport
                    FROM icmphdr
                    WHERE cid='".$o->cid."'";
            break;
            case 6:
                $sql = "SELECT 
                    tcp_sport AS sport,
                    tcp_dport AS dport
                    FROM tcphdr
                    WHERE cid='".$o->cid."'";
            break;
            case 17:
                $sql = "SELECT 
                    udp_sport AS sport,
                    udp_dport AS dport
                    FROM udphdr
                    WHERE cid='".$o->cid."'";
            break;
            default:
                $o->sport=1;
                $o->dport=0;
                return;
        }
        #$fh=fopen("/tmp/sql.txt",'a');
        #fwrite($fh, $sql."\n\n");
        #fclose($fh);
        $result = $db->query($sql);
        $row = $db->fetch_object($result);
        $o->sport=$row->sport;
        $o->dport=$row->dport;
    }        

}







?>





