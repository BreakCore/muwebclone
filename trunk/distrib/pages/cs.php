<?php if (!defined('insite')) die("no access"); 
/*
* scrypt by Leyas(hastlegames.com) 
* redecoded by epmak for MuWebClone 
*/

$gettime = time();
if(time() - load_cache("_dat/cach/{$_SESSION["mwclang"]}_cs",true) >0)
{

 ob_start();
	
 $content->out("cs_h.html");
	
 $owners = $db->query("SELECT TOP 1
 md.MAP_SVR_GROUP,
 md.SIEGE_START_DATE,
 md.SIEGE_END_DATE,
 md.SIEGE_GUILDLIST_SETTED,
 md.SIEGE_ENDED,
 md.CASTLE_OCCUPY,
 md.OWNER_GUILD,
 md.MONEY,
 md.TAX_RATE_CHAOS,
 md.TAX_RATE_STORE,
 md.TAX_HUNT_ZONE,
 g.G_Mark,
 g.G_Master
FROM
 MuCastle_DATA md left join Guild g on g.G_Name = md.OWNER_GUILD")->FetchRow();


 $cs_info = know_csstate($db,$content);

	
 if (!empty($owners["OWNER_GUILD"]))
 {
  $logo = GuildLogo($owners["G_Mark"],$owners["OWNER_GUILD"],32,$config["logotime"]);
  $content->set('|gname|', $owners["OWNER_GUILD"]);
  $content->set('|gmast|', $owners["G_Master"]);
  $content->set('|logo|', $logo);
  $content->out("cs_guild.html");
 } 
 if ((float)$owners["MONEY"]>0)
 {	
  $content->set('|zenmoney|', print_price($owners["MONEY"]));
  $content->set('|ttax|', $owners["TAX_RATE_CHAOS"]);
  $content->set('|ttax_r|', $owners["TAX_RATE_STORE"]);
  $content->set('|priceenter|', print_price($owners["TAX_HUNT_ZONE"]));
  $content->out("cs_info.html");
 }
				
 $content->set('|begin|', $cs_info[2]);		
 $content->set('|cs_period|', $content->getVal("cs_statez"));
 $content->set('|period|', $cs_info[1]);		
 $content->set('|end|', $cs_info[3]);
 $content->out("cs_attack.html");

 $gildie = $db->query("SELECT top 50 MAP_SVR_GROUP, REG_SIEGE_GUILD, REG_MARKS, IS_GIVEUP, SEQ_NUM FROM MuCastle_REG_SIEGE ORDER BY REG_MARKS desc, REG_SIEGE_GUILD asc");

 $content->out("cs_attackers_h.html");
 while ($res = $gildie->FetchRow())
 {

  $content->set('|name|', $res["REG_SIEGE_GUILD"]);
  $content->set('|marks|', $res["REG_MARKS"]);
  $content->out("cs_attackers_c.html");
 }

 $content->out("cs_f.html");

 $temp_ = ob_get_contents();
 write_catch("_dat/cach/".$_SESSION["mwclang"]."_cs",$temp_);
 ob_clean();
 echo $temp_;
}
else
 echo load_cache("_dat/cach/".$_SESSION["mwclang"]."_cs");