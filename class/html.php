<?php

function html_head($title) {
    echo '<html><head><title>'.$title.'</title>';
    echo '<link rel="stylesheet" type="text/css" href="css/style.css">';
    echo '</head><body><img src="images/logo.png" alt="Logo"><br>';
}

function html_head_links($title) {
    echo '<html><head><title>'.$title.'</title>';
    echo '<link rel="stylesheet" type="text/css" href="css/style.css">';
    echo '</head><body>';
}

function html_end() {
    echo '</body></html>';
}

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

function set_link_description($data, $path, $id) {
        return '<a class="description" href="'.$path.'?sigid='.$id.'" title="'.$data.'">'.substr($data,0,40).' </a>'."\n";
    }

function font($data, $color) {
         return "<font color=\"$color\">".$data."</font>";
    }

function listing($stuff) {
        $erg="<ul>";
        foreach($stuff as $v) {
            $erg.="<li>$v</li>";
        }
        $erg.="</ul>";
        return $erg;
    }

function table_hash($hash, $border=0) {
        echo "<table border=\"$border\">";
        foreach(array_keys($hash) as $key) {
            echo "<tr><td>".$key."</td><td>".$hash[$key]."</td></tr>";
        }
        echo "</table>\n";
    }

?>
