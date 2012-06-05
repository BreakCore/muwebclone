<?php if (!defined('insite')) die("no access"); 
ob_start();
if (isset($_SESSION["character"]))
{

  global $db;  
  global $content; 
  global $config;
  require "configs/top100_cfg.php";
  $char = validate(substr($_SESSION["character"],0,10)); 
  own_char($char,validate($_SESSION["user"]));
  $infochar = $db->fetchrow($db->query("SELECT cLevel, LevelUpPoint, Class, Strength,Dexterity,Vitality,Energy,Leadership,".$top100["t100res_colum"].",CtlCode FROM Character WHERE Name='".$char."'"));
  if ($infochar[9]==1) $bbf="<span class=\"bannedfont\">Banned!</span>"; else $bbf="";

  $content->set('|classpicture|', classpicture($infochar[2]));
  $content->set('|character|', $bbf." ".$char);
  $content->set('|Level|', $infochar[0]);
  $content->set('|Reset|', $infochar[8]);
  $content->set('|Class|', classname($infochar[2]));
  $content->set('|str|', stats65($infochar[3]));
  $content->set('|agi|', stats65($infochar[4]));
  $content->set('|vit|', stats65($infochar[5]));
  $content->set('|ene|', stats65($infochar[6]));
  $content->set('|cmd|', stats65($infochar[7]));
  $content->set('|getcharmenu|', getcharmenu(1));
  $content->out_content("theme/".$config["theme"]."/them/chinfo.html");

}
else
{
 header("Location: ".$config["siteaddress"]."/?p=not&error=17");
 die();
}
$temp = ob_get_contents();
ob_end_clean();