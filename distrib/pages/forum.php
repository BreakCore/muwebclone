<?php if (!defined('insite')) die("no access");  
global $config;
header ('Location:'.$config["forum"]);
$temp =" <div align=\"center\" valign=\"center\"><a href=\"".$config["forum"]."\">To redirect press here</a></div>";