<?php if (!defined('insite')) die("no access"); 
global $config;
global $db;
global $content;

require "configs/statistic_cfg.php";
$gettime = time();
$ntime = @filemtime("_dat/cach/".$_SESSION["mwclang"]."_statistic"); 
if(!$ntime or time() - $ntime > $stat["cach"])
{
 ob_start();
	
 $characters["all"] = $db->fetchrow($db->query("SELECT count(*) FROM Character"));
 $characters["dw"] = $db->fetchrow($db->query("SELECT count(*) FROM Character WHERE Class = 0"));
 $characters["sm"] = $db->fetchrow($db->query("SELECT count(*) FROM  Character WHERE Class = 1"));
 $characters["gm"] = $db->fetchrow($db->query("SELECT count(*) FROM  Character WHERE Class = 2 or Class = 3"));
 $characters["elf"] = $db->fetchrow($db->query("SELECT count(*) FROM  Character WHERE Class = 32"));
 $characters["me"] = $db->fetchrow($db->query("SELECT count(*) FROM  Character WHERE Class = 33"));
 $characters["he"] = $db->fetchrow($db->query("SELECT count(*) FROM  Character WHERE Class = 33 or Class= 34"));
 $characters["dk"] = $db->fetchrow($db->query("SELECT count(*) FROM  Character WHERE Class = 16"));
 $characters["bk"] = $db->fetchrow($db->query("SELECT count(*) FROM  Character WHERE Class = 17"));
 $characters["bm"] = $db->fetchrow($db->query("SELECT count(*) FROM  Character WHERE Class = 18 or Class = 19"));
 $characters["mg"] = $db->fetchrow($db->query("SELECT count(*) FROM  Character WHERE Class = 48"));
 $characters["dm"] = $db->fetchrow($db->query("SELECT count(*) FROM  Character WHERE Class = 49 or Class=50"));
 $characters["dl"] = $db->fetchrow($db->query("SELECT count(*) FROM  Character WHERE Class = 64"));
 $characters["le"] = $db->fetchrow($db->query("SELECT count(*) FROM  Character WHERE Class = 65 or Class = 66"));
 $characters["s"] = $db->fetchrow($db->query("SELECT count(*) FROM  Character WHERE Class = 80"));
 $characters["bs"] = $db->fetchrow($db->query("SELECT count(*) FROM  Character WHERE Class = 81"));
 $characters["dms"] = $db->fetchrow($db->query("SELECT count(*) FROM  Character WHERE Class = 82 or Class = 83"));
 $characters["rf"] = $db->fetchrow($db->query("SELECT count(*) FROM  Character WHERE Class = 96"));
 $characters["fm"] = $db->fetchrow($db->query("SELECT count(*) FROM  Character WHERE Class = 97 or Class=98"));
 $all_g = $db->fetchrow($db->query("SELECT count(*) FROM guild"));
 $characters["all"][1]=$content->lng["online_charnum"];
 $characters["dw"][1]="Dark Wizard";
 $characters["sm"][1]="Soul Master";
 $characters["gm"][1]="Grand Master";
 $characters["elf"][1]="Elf";
 $characters["me"][1]="Muse Elf";
 $characters["he"][1]="High Elf";
 $characters["dk"][1]="Dark Knight";
 $characters["bk"][1]="Blade Knight";
 $characters["bm"][1]="Blade Master";
 $characters["mg"][1]="Magic Gladiator";
 $characters["dm"][1]="Duel Master";
 $characters["dl"][1]="Dark Lord";
 $characters["le"][1]="Lord Emperor";
 $characters["s"][1]="Summoner";
 $characters["bs"][1]="Bloody Summoner";
 $characters["dms"][1]="Dimension Master";
 $characters["rf"][1]="Rage Fighter";
 $characters["fm"][1]="Fists Master";
		
if($statistic["showgraph"]==1)
{	
 $i=0;
 foreach($characters as $id=>$value)
 {
  if($value[1]!= $content->lng["online_charnum"] && $value[0] > 0)
  {
   $VALUES[$i]=$value[0];
   $LEGEND[$i]=$value[1];
   $i++;			
  }
 }
 if ($i>0)
 {
  $im=ImageCreate($statistic["sizey"],$statistic["sizex"]);

  // Зададим цвет фона. 
  $bgcolor=ImageColorAllocate($im,$statistic["bgcolR"],$statistic["bgcolG"],$statistic["bgcolB"]);
  Diagramm($im,$VALUES,$LEGEND);
  // Генерация изображения
  Imagepng($im,"imgs/statistic.png");
  Imagedestroy($im);
  $graphic = "<img border=\"0\" src=\"imgs/statistic.png\">";}else $graphic="";
}
else $graphic="";

$content->set('|version|', $statistic["version"]);
$content->set('|exprate|', $statistic["exprate"]);
$content->set('|drop|', $statistic["drop"]);
$content->set('|all|',$characters["all"][0]);
$content->set('|all_g|', $all_g[0]);
$content->out_content("theme/".$config["theme"]."/them/statistic_h.html");

$i=0; 
foreach($characters as $id=>$value)
{
 if($value[0] > 0 && $id!="all")
 {
  if ($i % 2 ==0) $sh_pl = ""; else $sh_pl="class='lighter1'";
  $content->set('|sh_pl|', $sh_pl);
  $content->set('|value|', $value[1]);
  $content->set('|value1|', $value[0]);
  $content->out_content("theme/".$config["theme"]."/them/statistic_c.html");
  $i++;
 }
}
$content->set('|graphic|', $graphic);
$content->set('|about_text|', bbcode(file_get_contents("faq/statistic")));
$content->out_content("theme/".$config["theme"]."/them/statistic_f.html");


timing($statistic["cach"]);
$temp = ob_get_contents();
write_catch("_dat/cach/".$_SESSION["mwclang"]."_statistic",$temp);
ob_end_clean(); 
}
else $temp = file_get_contents( "_dat/cach/".$_SESSION["mwclang"]."_statistic");
				