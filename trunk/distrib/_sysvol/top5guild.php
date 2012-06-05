<?php if (!defined('insite')) die("no access"); 
unset($gettime);
require "configs/topguild_cfg.php";
$gettime = time();
$filetime = @filemtime("_dat/cach/top5guild");
if(!$topstr1 || ($gettime-$filetime >$topguild["topgcache"]))
{
 global $db;
 global $config;
 global $content;
 require "configs/top100_cfg.php";
 ob_start();
 $gtop_r = $db->query("SELECT TOP 5 G_Name,G_Mark, G_Master FROM Guild  order by G_Score desc,G_Count desc,G_Name desc ");
 $content->out_content("theme/".$config["theme"]."/them/top5guild_h.html");
 while ($show_g = $db->fetcharray($gtop_r))
 {
  $show_g["G_Mark"] = GuildLogo($show_g["G_Mark"],$show_g["G_Name"],12,$top100["t100logotime"]);
  $content->set('|G_Mark|', $show_g["G_Mark"]);
  $content->set('|G_Name|', $show_g["G_Name"]);
  $content->out_content("theme/".$config["theme"]."/them/top5guild_c.html");
 }
 $content->out_content("theme/".$config["theme"]."/them/top5guild_f.html");
 $temp = ob_get_contents();
 ob_end_clean(); 
 write_catch("_dat/cach/top5guild",$temp);
}
else $temp = file_get_contents("_dat/cach/top5guild");