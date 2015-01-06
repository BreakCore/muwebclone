<?php if (!defined('insite')) die("no access"); 

require "configs/statistic_cfg.php";
$gettime = time();

if(time() - load_cache("_dat/cach/".$_SESSION["mwclang"]."_statistic",true) > $stat["cach"])
{
 ob_start();

 $characters = $db->query("
 SELECT (SELECT count(*) FROM Character) as all_,
 (SELECT count(*) FROM Character WHERE Class = 0) as dw,
 (SELECT count(*) FROM  Character WHERE Class = 1) as sm,
 (SELECT count(*) FROM  Character WHERE Class = 2 or Class = 3) as gm,
 (SELECT count(*) FROM  Character WHERE Class = 32) as elf,
 (SELECT count(*) FROM  Character WHERE Class = 33) as me,
 (SELECT count(*) FROM  Character WHERE Class = 33 or Class= 34) as he,
 (SELECT count(*) FROM  Character WHERE Class = 16) as dk,
 (SELECT count(*) FROM  Character WHERE Class = 17) as bk,
 (SELECT count(*) FROM  Character WHERE Class = 18 or Class = 19) as bm,
 (SELECT count(*) FROM  Character WHERE Class = 48) as mg,
 (SELECT count(*) FROM  Character WHERE Class = 49 or Class=50) as dm,
 (SELECT count(*) FROM  Character WHERE Class = 64) as dl,
 (SELECT count(*) FROM  Character WHERE Class = 65 or Class = 66) as le,
 (SELECT count(*) FROM  Character WHERE Class = 80) as s,
 (SELECT count(*) FROM  Character WHERE Class = 81) as bs,
 (SELECT count(*) FROM  Character WHERE Class = 82 or Class = 83) as dms,
 (SELECT count(*) FROM  Character WHERE Class = 96) as rf,
 (SELECT count(*) FROM  Character WHERE Class = 97 or Class=98) as fm,
 (SELECT count(*) FROM guild) as allg
 ")->FetchRow();


 $characters_["all_"]=$content->getVal("online_charnum");
 $characters_["dw"]="Dark Wizard";
 $characters_["sm"]="Soul Master";
 $characters_["gm"]="Grand Master";
 $characters_["elf"]="Elf";
 $characters_["me"]="Muse Elf";
 $characters_["he"]="High Elf";
 $characters_["dk"]="Dark Knight";
 $characters_["bk"]="Blade Knight";
 $characters_["bm"]="Blade Master";
 $characters_["mg"]="Magic Gladiator";
 $characters_["dm"]="Duel Master";
 $characters_["dl"]="Dark Lord";
 $characters_["le"]="Lord Emperor";
 $characters_["s"]="Summoner";
 $characters_["bs"]="Bloody Summoner";
 $characters_["dms"]="Dimension Master";
 $characters_["rf"]="Rage Fighter";
 $characters_["fm"]="Fists Master";


$content->set('|version|', $statistic["version"]);
$content->set('|exprate|', $statistic["exprate"]);
$content->set('|drop|', $statistic["drop"]);
$content->set('|all|',$characters["all"][0]);
$content->set('|all_g|', $characters["allg"]);
$content->out("statistic_h.html");

$i=0;

foreach($characters_ as $id=>$value)
{
  if ($i % 2 ==0) $sh_pl = ""; else $sh_pl="class='lighter1'";
  $content->set('|sh_pl|', $sh_pl);
  $content->set('|value|', $value);
  $content->set('|value1|', $characters[$id]);
  $content->out("statistic_c.html");
  $i++;
}

$content->set('|about_text|', bbcode(file_get_contents("faq/statistic")));
$content->out_content("theme/".$config["theme"]."/them/statistic_f.html");


timing($statistic["cach"],$content);
$temp = ob_get_contents();
write_catch("_dat/cach/".$_SESSION["mwclang"]."_statistic",$temp);
ob_clean();
}
else
 $temp = load_cache( "_dat/cach/".$_SESSION["mwclang"]."_statistic");
				