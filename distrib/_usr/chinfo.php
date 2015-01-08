<?php if (!defined('insite')) die("no access"); 

if (isset($_SESSION["character"]))
{
  require "configs/top100_cfg.php";
  $char = substr($_SESSION["character"],0,10);
  own_char($char,$_SESSION["user"],$db,$config);

  $infochar = $db->query("SELECT cLevel, LevelUpPoint, Class, Strength,Dexterity,Vitality,Energy,Leadership,{$top100["t100res_colum"]},CtlCode FROM Character WHERE Name='$char'")->FetchRow();

  if ($infochar["CtlCode"]==1) $bbf="<span class=\"bannedfont\">Banned!</span>"; else $bbf="";

  $content->set('|classpicture|', classpicture($infochar["Class"]));
  $content->set('|character|', $bbf." ".$char);
  $content->set('|Level|', $infochar["cLevel"]);
  $content->set('|Reset|', $infochar[$top100["t100res_colum"]]);
  $content->set('|Class|', classname($infochar["Class"]));
  $content->set('|str|', stats65($infochar["Strength"]));
  $content->set('|agi|', stats65($infochar["Dexterity"]));
  $content->set('|vit|', stats65($infochar["Vitality"]));
  $content->set('|ene|', stats65($infochar["Energy"]));
  $content->set('|cmd|', stats65($infochar["Leadership"]));
  $content->set('|getcharmenu|', getcharmenu($config,1));
  $content->out("chinfo.html");

}
else
{
 header("Location: ".$config["siteaddress"]."/?p=not&error=17");
 die();
}
