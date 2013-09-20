<?php

# Snort PHP Monitor
# Copyright (C) 2008 Andrej Frank
# The GNU General Public License (GPL)

require_once("class/html.php");
require_once("config.php");

html_head("DB Info");

$tables=array("data","detail","encoding","event","icmphdr","iphdr","opt",
        "reference","reference_system","schema","sensor","sig_class","sig_reference","signature","tcphdr","udphdr");




html_end();

?>
