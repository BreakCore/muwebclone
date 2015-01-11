<?php if (!defined('insite')) die("no access"); 

require "configs/top100_cfg.php";
require "configs/topguild_cfg.php";
if (isset($_GET["guild"]))
{

 $content->add_dict($_SESSION["mwclang"]."_guildtop");
 $nedd_g = substr($_GET["guild"],0,10);
 $g_result = $db->query("SELECT G_Name,G_Score,G_Mark,G_Master,G_Union, number FROM guild Where G_Name='$nedd_g'")->FetchRow();

 if (!empty($g_result["G_Name"]))
 {
  $g_result["G_Mark"] = GuildLogo($g_result["G_Mark"],$g_result["G_Name"],64,$topguild["topgcache"]);
 
  $content->set('|G_Name|', $g_result["G_Name"]);
  $content->set('|G_Mark|', $g_result["G_Mark"]);
  $content->set('|G_Score|', $g_result["G_Score"]);

  $temp = $content->out("search_h.html",1);
 
  $m_result = $db->query("SELECT Name,G_Status FROM GuildMember WHERE G_Name= '{$g_result["G_Name"]}' ORDER BY G_Status DESC, Name DESC");

  while ($gmember = $m_result->FetchRow())
  {
	$content->set('|guild_stat|', $topguild[$gmember["G_Status"]]);
	$content->set('|gmname|', $gmember["Name"]);
	$temp.=$content->out("search_c.html",1);
  }
 
  $aliance="";
  if ($g_result["G_Union"]!=0)
  {
    $guilds = $db->query("SELECT G_Name FROM Guild WHERE G_Union='{$g_result["G_Union"]}' or number='{$g_result["G_Union"]}' or G_Union='{$g_result["number"]}'");

	while ($g_data = $guilds->FetchRow())
		if($g_data["G_Name"] != $g_result["G_Union"])
			$aliance .= "<div><a href='#".$g_data["G_Name"]."'>".$g_data["G_Name"]."</a></div>";
  }  

  $content->set('|aliance|', $aliance);
  $temp.= $content->out("search_f.html",1);
 }		
}
elseif(isset($_GET["caracter"]))
{

	$show_ch = substr($_GET["caracter"],0,10);
	$array = $db->query("Select
ch.inventory,
ch.class,
ch.cLevel,
ch.{$top100["t100res_colum"]},
ch.gr_res,
ch.Strength,
ch.Dexterity,
ch.Vitality,
ch.Energy,
ch.AccountID,
ch.Leadership,
gm.G_name,
ms.Connectstat,
mi.opt_inv
FROM
 Character ch
 left JOIN GuildMember gm ON gm.Name COLLATE DATABASE_DEFAULT = ch.Name COLLATE DATABASE_DEFAULT
 inner join MEMB_STAT ms ON ms.memb___id COLLATE DATABASE_DEFAULT = ch.AccountID COLLATE DATABASE_DEFAULT,
 MEMB_INFO mi
WHERE
ch.Name='$show_ch'
AND mi.memb___id COLLATE DATABASE_DEFAULT = ch.AccountID COLLATE DATABASE_DEFAULT")->FetchRow();

	$guild = (empty($array["G_name"])) ? "-" : $array["G_name"];

	if (!empty($array["class"]))
	{
		//center
		$content->set('|name|', $show_ch);
		$content->set('|level|', $array["cLevel"]);
		$content->set('|res|', $array[$top100["t100res_colum"]]);
		$content->set('|grres|', $array["gr_res"]);
		if ($array["opt_inv"]==0)
		{
		 $content->set('|str|', stats65($array["Strength"]));
		 $content->set('|agi|', stats65($array["Dexterity"]));
		 $content->set('|vit|', stats65($array["Vitality"]));
		 $content->set('|ene|', stats65($array["Energy"]));
		 $content->set('|cmd|', stats65($array["Leadership"]));

         if($array["Connectstat"]==1)
			 $state="<span style='color:green;'>Online</span>";
		 else
			 $state="<span style='color:red;'>Offline</span>";
         $content->set('|onlin|', $state);
		}
		else
		{
		 $content->set('|str|', "<img src='imgs/lock.png' border='0' alt='hide'>");
		 $content->set('|agi|', "<img src='imgs/lock.png' border='0' alt='hide'>");
		 $content->set('|vit|', "<img src='imgs/lock.png' border='0' alt='hide'>");
		 $content->set('|ene|', "<img src='imgs/lock.png' border='0' alt='hide'>");
		 $content->set('|cmd|', "<img src='imgs/lock.png' border='0' alt='hide'>");
		 $content->set('|onlin|', "<img src='imgs/lock.png' border='0' alt='hide'>");
		}
		$content->set('|guild|',$guild);
		$content->set('|classname|', classname($array["class"]));
		$temp.=$content->out("search_ch_c.html",1);
		
		//footer
		$temp.=$content->out("search_ch_f.html",1);

	}
}
else 
{
 if (!isset($_REQUEST["ok"]))
   $temp.=$content->out_content("theme/".$config["theme"]."/them/search.html",1);
 else 
 {
  switch($_POST["sertype"])
  {
   case 1: header("location:".$config["siteaddress"]."/?p=search&caracter=".substr($_POST["choosed"],0,10));break;
   case 2: header("location:".$config["siteaddress"]."/?p=search&guild=".substr($_POST["choosed"],0,10));break;
   default:die("wrong text!");
  }
 }
}