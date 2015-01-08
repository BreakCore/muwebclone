<?php  if (!defined('insite')) die("no access"); 

if (!isset($_SESSION["character"]))
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

}

own_char($_SESSION["character"],$_SESSION["user"],$db,$config);
$perem = $_SESSION["user"];
$char = $_SESSION["character"];


if (isset($_REQUEST["dostat"]) && chck_online($db,$perem)==0)
{

 require "configs/stats_cfg.php";
 
 $row = $db->query("SELECT Vitality,Strength,Energy,Dexterity,LevelUpPoint, Leadership, Class from Character WHERE AccountID='$perem' and Name='$char'")->FetchRow();
 $strength = (int)$_POST["str"];
 $vitality = (int)$_POST["vit"];
 $energy = (int)$_POST["ene"];
 $dexterity = (int)$_POST["agi"];
 $command = (int)$_POST["com"];
		
 $gvit = stats65($row["Vitality"]);
 $gstr = stats65($row["Strength"]);
 $geng = stats65($row["Energy"]);
 $gagi = stats65($row["Dexterity"]);
 $gcom = stats65($row["LevelUpPoint"]);
		
 if ($strength+$gstr > $stats["max_stats"]) $strength=0;
 if ($vitality+$gvit > $stats["max_stats"]) $vitality=0;
 if ($energy+$geng > $stats["max_stats"]) $energy=0;
 if ($dexterity+$gagi > $stats["max_stats"]) $dexterity=0;
 if ($row["Leadership"]==0 or empty($row["Leadership"])){$command=0;}else{if ($command+$gcom > $stats["max_stats"])$command=0; }
			  
 if (($vitality >= 0)&&($strength >= 0)&&($energy >=0 )&&($dexterity >= 0)&&($command >=0 ))
 {
  $per = $row["LevelUpPoint"] - $vitality - $strength - $energy - $dexterity - $command;
  if ($per < 0){echo "<span class='warnms'>($per)</span><br>";}
  else
  {
	$new_vit = restats65($gvit + $vitality);
	$new_str = restats65($gstr  + $strength);
	$new_eng = restats65($geng + $energy);
	$new_agi = restats65($gagi + $dexterity);
	$new_com = restats65($gcom  + $command);
	$msresults = $db->query("UPDATE dbo.Character SET Vitality = '$new_vit', Strength = '$new_str', Energy = '$new_eng', Dexterity = '$new_agi', LevelUpPoint = '$per', Leadership = '$new_com' WHERE Name = '$char'");echo "<span class='succes'>Ok</span>"; 
	logs::WriteLogs ("stats_","јккаунт ".$_SESSION["user"]." персонаж ".$char." было ".$row["LevelUpPoint"]." свободных поинтов, стало ".$per." ".$vitality." - ".$strength." - ".$energy." - ".$dexterity." - ".$command."");

  }
  if ($per >1)
   header( 'location:'.$config["siteaddress"].'/?up=stats' );
  else
   header( 'location:'.$config["siteaddress"].'/?up=usercp');
  die();
 }
 else
  echo "<span class='warnms'>error</span>";
}
else
{
 $character_stats = $db->query("SELECT Strength, Dexterity, Vitality, Energy, Leadership, LevelUpPoint, Class  FROM Character where AccountID='$perem' and Name='$char'")->FetchRow();
 if ($character_stats["LevelUpPoint"]>0)
 {
  
  $content->set('|all_stats|', $character_stats["LevelUpPoint"]);
  $content->set('|character_stats|', $character_stats["LevelUpPoint"]);
  $content->set('|str_stats|', stats65($character_stats["Strength"]));
  $content->set('|agi_stats|', stats65($character_stats["Dexterity"]));
  $content->set('|vit_stats|', stats65($character_stats["Vitality"]));
  $content->set('|ene_stats|', stats65($character_stats["Energy"]));
  $content->set('|cmd_stats|', stats65($character_stats["Leadership"]));
			
  if (($character_stats["Class"]==64)|| ($character_stats["Class"]==65) || ($character_stats["Class"]==66))
	$content->set('|_disabled|', "");
  else
	$content->set('|_disabled|', "disabled");
			
  $content->out("stats.html");
 }
 else 
 {
  header("location:".$config["siteaddress"]."/?p=not&error=8");
  die("epic fail!");
 }	
}
