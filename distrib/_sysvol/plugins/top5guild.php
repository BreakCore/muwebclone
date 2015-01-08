<?php
/**
 * top 5 strongest guilds
 */

require "configs/topguild_cfg.php";
if(is_array($topguild))
{
 if(time() - load_cache("_dat/cach/top5guild",true) > $topguild["topgcache"])
 {
  require "configs/top100_cfg.php";
  ob_start();
  $gtop_r = $db->query("SELECT TOP 5 G_Name,G_Mark, G_Master FROM Guild  order by G_Score desc,G_Count desc,G_Name desc ");
  $content->out("top5guild_h.html");
  while ($show_g = $gtop_r->FetchRow())
  {
   $show_g["G_Mark"] = GuildLogo($show_g["G_Mark"],$show_g["G_Name"],12,$top100["t100logotime"]);
   $content->set('|G_Mark|', $show_g["G_Mark"]);
   $content->set('|G_Name|', $show_g["G_Name"]);
   $content->out("top5guild_c.html");
  }
  $content->out("top5guild_f.html");
  $temp = ob_get_contents();
  ob_clean();
  write_catch("_dat/cach/top5guild",$temp);
 }
 else
  $temp = load_cache("_dat/cach/top5guild");
}
else
{
 echo "wrong top 5 guild config";
}
