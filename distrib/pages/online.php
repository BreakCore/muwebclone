<?php if (!defined('insite')) die("no access"); 

require "configs/top100_cfg.php";
require "configs/online_cfg.php";
$gettime = time();
$ntime = @filemtime("_dat/cach/".$_SESSION["mwclang"]."_toponline"); 	
if(!$ntime || ($gettime-$ntime>300))
{
global $db;
global $config;
global $content;
ob_start();
$i=0;
$acc_online = $db->query ("SELECT memb___id FROM MEMB_STAT WHERE Connectstat=1 order by  ConnectTM desc");
if($db->numrows($acc_online)>0)
{
 $content->out_content("theme/".$config["theme"]."/them/online_h.html");

 while ($racc_on = $db->fetchrow($acc_online))
 {
  if ($i % 2 ==0) $sh_pl = "class='lighter1'"; else $sh_pl="";	
		
  $charact = $db->fetchrow($db->query("SELECT GameIDC FROM AccountCharacter WHERE Id='".$racc_on[0]."'"));
  $shoc = $db->fetchrow($db->query("SELECT Class, cLevel, ".$top100["t100res_colum"]." FROM Character WHERE Name='".$charact[0]."'"));
  $shoc[0] = classname($shoc[0]);
  $content->set('|style|', $sh_pl);
  $content->set('|name|', $charact[0]);
  $content->set('|class|', $shoc[0]);
  $content->set('|level|', $shoc[1]);
  $content->set('|reset|', $shoc[2]);
  $content->out_content("theme/".$config["theme"]."/them/online_c.html");
  $i++;
 }
 $content->out_content("theme/".$config["theme"]."/them/online_f.html");
 timing(300);
} else echo "<div class='succes' style='text-align=center'>List is empty</div>"; 
$temp = ob_get_contents();
write_catch("_dat/cach/".$_SESSION["mwclang"]."_toponline",$temp);
ob_end_clean(); 
}
else $temp = file_get_contents ( "_dat/cach/".$_SESSION["mwclang"]."_toponline");
