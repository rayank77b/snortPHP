<?php

/**
 * Snort PHP Monitor
 * @author Andrej Frank <Andrej.Frank@hs-esslingen.de>
 * @copyright Copyright (c) 2008, Andrej Frank, The GNU General Public License (GPL)
*/

/**
 * echo the html header with title, seted links and logo
 * @param string $title value of the title
 * @return none 
 */
function html_head($title) {
    echo '<html><head><title>'.$title.'</title>';
    echo '<link rel="stylesheet" type="text/css" href="css/style.css">';
    echo '</head><body><img src="images/logo.png" alt="Logo"><br>';
}

/**
 * echo the html header with title, seted links and without logo
 * @param string $title value of the title
 * @return none 
 */
function html_head_links($title) {
    echo '<html><head><title>'.$title.'</title>';
    echo '<link rel="stylesheet" type="text/css" href="css/style.css">';
    echo '</head><body>';
}

/**
 * echo the html header with title with refresh
 * @param string $title value of the title
 * @return none 
 */
function html_head_status($title) {
    echo '<html><head><title>'.$title.'</title>';
    echo '<meta http-equiv="refresh" content="30">';
    echo '<link rel="stylesheet" type="text/css" href="css/style.css">';
    echo '</head><body>';
}

/**
 * echo the html end
 * @return none 
 */
function html_end() {
    echo '</body></html>';
}

/**
 * echo table header
 * @param mixed $stuff values/value of headers/header (array/string)
 * @return none 
 */
function th($stuff) {
        if(is_array($stuff)) {
            foreach($stuff as $v) {
                $erg.="<th>$v</th>";
            }
        } else { 
            $erg.= "<th>$stuff</th>"; 
        }
        return $erg;
    }

/**
 * echo the html header with title, seted links and logos
 * @param string $title value of the title
 * @return none 
 */
function set_link_description($data, $path, $id) {
        return '<a class="description" href="'.$path.'?sigid='.$id.'" title="'.$data.'">'.substr($data,0,40).' </a>'."\n";
    }

/**
 * echo font with color
 * @param string $data value of content
 * @param string $color value of color
 * @return none 
 */
function font($data, $color) {
         return "<font color=\"$color\">".$data."</font>";
    }

/**
 * build a list from array and echo it
 * @param array $stuff content of list
 * @param string $color value of color
 * @return none 
 */
function listing($stuff) {
        $erg="<ul>";
        foreach($stuff as $v) {
            $erg.="<li>$v</li>";
        }
        $erg.="</ul>";
        return $erg;
    }

/**
 * build a table with conten of a hash
 * @param array $hash content of rows 
 * @param int $border value of border
 * @return none 
 */
function table_hash($hash, $border=0) {
        echo "<table border=\"$border\">";
        foreach(array_keys($hash) as $key) {
            echo "<tr><td>".$key."</td><td>".$hash[$key]."</td></tr>";
        }
        echo "</table>\n";
    }

/**
 * build a alerts row
 * @param object $o value of SnortObject 
 * @param string $color value of color
 * @return string  
 */
function build_row_alerts($o, $color) {
        $erg="";
        $erg.='<td>'.$o->cid.'</td>';
        $erg.='<td><b>'.$o->ip_src.'</b>:'.$o->sport.'</td>';
        $erg.='<td><b>'.$o->ip_dst.'</b>:'.$o->dport.'</td>';
        $erg.='<td>'.$o->timestamp.'</td>';
        $erg.='<td><img src="images/flag'.$color.'.png" alt="'.$color.'"></td>';
        return $erg;
    }

/**
 * get an integer from $_GET, validate it
 * @param string $name name of variable 
 * @return integer  
 */
function get_integer($name) {
    $val=(int)$_GET[$name];
    if( is_int($val) ) {
        return $val;
    } else {
        return 0;
    }
}

























?>
