<?php if (!defined('insite')) die("no access");  require "opt.php"; 
header ('Location:'.$config["siteaddress"]);
$temp="<div align=\"center\" valign=\"center\"><a href=\"".$config["siteaddress"]."\">".redirect_msg_ifno."</a></div>";