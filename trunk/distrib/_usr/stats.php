<?php  if (!defined('insite')) die("no access"); 
global $config;
ob_start();
if (!$_SESSION["character"])
{
 if ($_GET["chname"])
 {
  $reschar = substr($_GET["chname"],0,10);
  $_SESSION["character"] = $reschar;
 }
 elseif ($_POST["selectedchar"])
 {
  $reschar = substr($_POST["selectedchar"],0,10);
  $_SESSION["character"] = $reschar; 
 }
 else 
 {
  header("Location: ".$config["siteaddress"]."/?p=not&error=17");
  die();
 }
 own_char($reschar,$_SESSION["user"]);
}
	
$perem = validate($_SESSION["user"]);
$char = validate($_SESSION["character"]);
global $db;

if ($_REQUEST["dostat"] && chck_online($perem)==0)
{
 own_char($char,$perem);
 require "configs/stats_cfg.php";
 
 $row = $db->fetchrow($db->query("SELECT Vitality,Strength,Energy,Dexterity,LevelUpPoint, Leadership, Class from Character WHERE AccountID='$perem' and Name='$char'"));
 $strength = checknum(substr($_POST["str"],0,5)); 
 $vitality = checknum(substr($_POST["vit"],0,5)); 
 $energy = checknum(substr($_POST["ene"],0,5)); 
 $dexterity = checknum(substr($_POST["agi"],0,5)); 
 $command = checknum(substr($_POST["com"],0,5));
		
 $gvit = stats65($row[0]);
 $gstr = stats65($row[1]);
 $geng = stats65($row[2]);
 $gagi = stats65($row[3]);
 $gcom = stats65($row[5]);
		
 if ($strength+$gstr > $stats["max_stats"]) $strength=0;
 if ($vitality+$gvit > $stats["max_stats"]) $vitality=0;
 if ($energy+$geng > $stats["max_stats"]) $energy=0;
 if ($dexterity+$gagi > $stats["max_stats"]) $dexterity=0;
 if ($row[5]==0 or empty($row[5])){$command=0;}else{if ($command+$gcom > $stats["max_stats"])$command=0; } 
			  
 if (($vitality >= 0)&&($strength >= 0)&&($energy >=0 )&&($dexterity >= 0)&&($command >=0 ))
 {
  $per = $row[4] - $vitality - $strength - $energy - $dexterity - $command;
  if ($per < 0){echo "<span class='warnms'>".warning1_stat_more."($per)</span><br>";}
  else
  {
	$new_vit = restats65($gvit + $vitality);
	$new_str = restats65($gstr  + $strength);
	$new_eng = restats65($geng + $energy);
	$new_agi = restats65($gagi + $dexterity);
	$new_com = restats65($gcom  + $command);
	$msresults = $db->query("UPDATE dbo.Character SET Vitality = '$new_vit', Strength = '$new_str', Energy = '$new_eng', Dexterity = '$new_agi', LevelUpPoint = '$per', Leadership = '$new_com' WHERE Name = '$char'");echo "<span class='succes'>Ok</span>"; 
	WriteLogs ("stats_","јккаунт ".$_SESSION["user"]." персонаж ".$char." было ".$row[4]." свободных поинтов, стало ".$per." ".$vitality." - ".$strength." - ".$energy." - ".$dexterity." - ".$command."");
	unset($new_vit,$new_str,$new_eng,$new_agi,$new_com,$msresults,$_POST["str"],$_POST["vit"],$_POST["agi"],$_POST["ene"],$_POST["com"]);
  }
  if ($per >1) header( 'location:'.$config["siteaddress"].'/?up=stats' ); 
  else header( 'location:'.$config["siteaddress"].'/?up=usercp');
 }
 else
  echo "<span class='warnms'>error</span>";
}
else
{
 $character_stats = $db->fetchrow($db->query("SELECT Strength, Dexterity, Vitality, Energy, Leadership, LevelUpPoint, Class  FROM Character where AccountID='".$perem."' and Name='".$char."'"));
 if ($character_stats[5]>0)
 {
  global $content;
  
  $content->set('|all_stats|', $character_stats[5]);
  $content->set('|character_stats|', $character_stats[5]);
  $content->set('|str_stats|', stats65($character_stats[0]));
  $content->set('|agi_stats|', stats65($character_stats[1]));
  $content->set('|vit_stats|', stats65($character_stats[2]));
  $content->set('|ene_stats|', stats65($character_stats[3]));
  $content->set('|cmd_stats|', stats65($character_stats[4]));
			
  if (($character_stats[6]==64)|| ($character_stats[6]==65) || ($character_stats[6]==66))
	$content->set('|_disabled|', "");
  else
	$content->set('|_disabled|', "disabled");
			
  $content->out_content("theme/".$config["theme"]."/them/stats.html");
 }
 else 
 {
  header("location:".$config["siteaddress"]."/?p=not&error=8");
  die("epic fail!");
 }	
}
$temp = ob_get_contents();
ob_end_clean();