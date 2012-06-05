<?php if (!defined('insite')) die("no access"); 
/*
* scrypt by Leyas(hastlegames.com) 
* redecoded by epmak for MuWebClone 
*/
$gettime = time();
$ntime = @filemtime("_dat/cach/".$_SESSION["mwclang"]."_cs"); 
if(!$ntime or time() - $ntime >3600)
{
 ob_start(); 

 global $content;
 global $config;
 global $db;
	
 $content->out_content("theme/".$config["theme"]."/them/cs_h.html");	
	
 $rowm = $db->fetchrow($db->query("SELECT top 1 MAP_SVR_GROUP, SIEGE_START_DATE, SIEGE_END_DATE, SIEGE_GUILDLIST_SETTED, SIEGE_ENDED, CASTLE_OCCUPY, OWNER_GUILD, MONEY, TAX_RATE_CHAOS, TAX_RATE_STORE, TAX_HUNT_ZONE FROM MuCastle_DATA"));
 $rowp = $db->fetchrow($db->query("SELECT G_Master,G_Mark FROM Guild WHERE G_Name = '$rowm[6]'"));
 $cs_info = know_csstate();	
	
 if (strlen($rowm[6])>2)
 {
  $logo = GuildLogo($rowp[1],$rowm[6],32,$config["logotime"]);
  $content->set('|gname|', $rowm[6]);
  $content->set('|gmast|', $rowp[0]);
  $content->set('|logo|', $logo);
  $content->out_content("theme/".$config["theme"]."/them/cs_guild.html");	
 } 
 if ($rowm[7]>0)
 {	
  $content->set('|zenmoney|', print_price($rowm[7]));	
  $content->set('|ttax|', $rowm[8]);	
  $content->set('|ttax_r|', $rowm[9]);		
  $content->set('|priceenter|', print_price($rowm[10]));	
  $content->out_content("theme/".$config["theme"]."/them/cs_info.html");
 }
				
 $content->set('|begin|', $cs_info[2]);		
 $content->set('|cs_period|', $content->lng["cs_statez"]);		
 $content->set('|period|', $cs_info[1]);		
 $content->set('|end|', $cs_info[3]);
 $content->out_content("theme/".$config["theme"]."/them/cs_attack.html");		

 $gildie = $db->query("SELECT top 50 MAP_SVR_GROUP, REG_SIEGE_GUILD, REG_MARKS, IS_GIVEUP, SEQ_NUM FROM MuCastle_REG_SIEGE ORDER BY REG_MARKS desc, REG_SIEGE_GUILD asc");
 $atguild_c = $db->numrows($gildie);
 if($atguild_c>0)
 {
  $content->out_content("theme/".$config["theme"]."/them/cs_attackers_h.html");
  for($i=0;$i<$atguild_c;$i++)
  {
   $rowg = $db->fetchrow($gildie);
   $content->set('|name|', $rowg[1]);
   $content->set('|marks|', $rowg[2]);
   $content->out_content("theme/".$config["theme"]."/them/cs_attackers_c.html");
  }
 }
 $content->out_content("theme/".$config["theme"]."/them/cs_f.html");
 $temp = ob_get_contents();
 write_catch("_dat/cach/".$_SESSION["mwclang"]."_cs",$temp);
 ob_end_clean();
}
else $temp = file_get_contents("_dat/cach/".$_SESSION["mwclang"]."_cs");