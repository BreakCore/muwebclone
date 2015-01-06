<?php
/**
 * Банлист
 */

if (!defined('insite')) die("no access");

require("configs/banlist_cfg.php");

$gettime = time();
if( time() - load_cache("_dat/cach/{$_SESSION["mwclang"]}_ban",true) > $banlist["cach"])
{
 ob_start();
 $content->out("banlist_h.html");

 $query_s = $db->query("SELECT memb___id,bloc_code,mwcban_time,ban_des FROM memb_info where ban_des!='0'");
	
 while ($show_ar = $query_s->FetchRow())
 {
  if ($show_ar["bloc_code"]==0) 
  {
   $b_chr = $db->fetchrow($db->query("SELECT Name FROM Character WHERE CtlCode = 1 and AccountID='".$show_ar["memb___id"]."'"));
   $show_ar["memb___id"] = $b_chr[0];
  }
  else $show_ar["memb___id"]=hide_acc($show_ar["memb___id"]);

  if ($show_ar["mwcban_time"]==0) $show_ar["mwcban_time"]="never";
		
  $content->set('|who|', $show_ar["memb___id"]);
  $content->set('|des|', $show_ar["ban_des"]);	
  $content->set('|date|', @date('G:i j-m-Y',$show_ar["mwcban_time"]));
  $content->out("banlist_c.html");
 }

 $content->out("banlist_f.html");

 $temp = ob_get_contents();
 write_catch("_dat/cach/".$_SESSION["mwclang"]."_ban",$temp);
 ob_end_clean();
}
else
 $temp = load_cache("_dat/cach/".$_SESSION["mwclang"]."_ban");