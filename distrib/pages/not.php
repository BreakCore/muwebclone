<?php if (!defined('insite')) die("no access");  
if (isset ($_GET["error"])) $errnum = checknum(substr($_GET["error"],0,3));
else $errnum =404;

global $config;
global $content;


require "lang/".$_SESSION["mwclang"]."/".$_SESSION["mwclang"]."_errors.php";

switch($errnum)
{
 case 0:  $content->set("|text|",$lang["reset_msg1"]); break;
 case 1:  $content->set("|text|",$lang["reset_msg2"]); break;
 case 2:  $content->set("|text|",$lang["reset_msg3"]); break;
 case 3:  $content->set("|text|",$lang["reset_msg4"]); break;
 case 4:  $content->set("|text|",$lang["bank_err1"]); break;
 case 5:  $content->set("|text|",$lang["bank_err2"]); break;
 case 6:  $content->set("|text|",$lang["upan_err_msg1"]); break;
 case 7:  $content->set("|text|",$lang["upan_err_msg2"]); break;
 case 8:  $content->set("|text|",$lang["no_stats"]); break;
 case 9:  $content->set("|text|",$lang["error_noaccess"]); break;
 case 10: $content->set("|text|",$lang["reset_msg5"]); break;
 case 11: $content->set("|text|",$lang["cred2zen_done"]); break;
 case 12: $content->set("|text|",$lang["cspoints_done"]); break;
 case 13: $content->set("|text|","Done!.."); break;
 case 14: $content->set("|text|",$lang["ban_account"]); break;
 case 15: $content->set("|text|","Reset limit!"); break;
 case 16: $content->set("|text|",$lang["enpught_zen"]); break;
 case 17: $content->set("|text|",$lang["no_character"]); break;
 case 18: $content->set("|text|",$lang["register_warr"]); break;
 case 19:  $content->set("|text|",$lang["notdone"]); break;
 case 20:  $content->set("|text|",$lang["online"]); break;
 case 404: $content->set("|text|",$lang["error_no404"]); break;
 default: $content->set("|text|",$lang["error_no404"]);
}
if ($errnum!=19) $temp = $content->out_content("theme/".$config["theme"]."/them/shoerr.html",1);
else $temp = $content->out_content("theme/".$config["theme"]."/them/logininpage.html",1);