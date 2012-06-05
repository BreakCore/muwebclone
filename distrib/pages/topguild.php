<?php if (!defined('insite')) die("no access"); 
require "configs/topguild_cfg.php";
global $db;
global $content;
global $config;
 $content->add_dict($_SESSION["mwclang"],"guildtop");
 
$gettime = time();
$filetime = @filemtime("_dat/cach/".$_SESSION["mwclang"]."_topguild");
if(!$filetime || ($gettime-$filetime >$topguild["topgcache"]))
{
 ob_start();
 $content->out_content("theme/".$config["theme"]."/them/topguild_h.html");

 $result12 = $db->query("SELECT TOP 100 G_Name,G_Score,G_Mark,G_Master,G_Union, number FROM guild order by G_score desc");
 $num_gx=$db->numrows($result12);

 function rond($text,$num)
 {
  $numz[32]="class='bms'";
  $numz[64]="class='agms'";
  $numz[128]="class='gms'";
  $numz[0] = "";
  return "<span ".$numz[$num].">".$text."</span>";
 }
 
 for($i=0;$i < $num_gx ;$i++)
 {
  $row4 = $db->fetchrow($result12);
  $rank = $i+1;

  $row4[2] = GuildLogo($row4[2],$row4[0],24,$topguild["topgcache"]);
  $resmemb = $db->query("SELECT Name,G_Status FROM GuildMember WHERE G_Name= '".$row4[0]."' ORDER BY G_Status DESC, Name DESC");

  $sostav = "<table  border='0'>";
  $kolvo = $db->numrows($resmemb);
  for ($j=0;$j<$kolvo;$j++)
  {
   $gmember = $db->fetchrow($resmemb);
   $sostav.="<tr><td width='50%' align='left'>".rond($topguild[$gmember[1]],$gmember[1])."</td><td width='50%' align='right'>".$gmember[0]."</td></tr>";
  }
  $sostav .= "</table>";
  $aliance="&nbsp;";
  if ($row4[4]!=0)
  {
	$guilds = $db->query("SELECT G_Name FROM Guild WHERE G_Union='".$row4[4]."' or number='".$row4[4]."' or G_Union='".$row4[5]."'");
	while ($g_data = $db->fetcharray($guilds))
	 if($g_data["G_Name"]!=$row4[0])$aliance .= "<div><a href='#".$g_data["G_Name"]."'>".$g_data["G_Name"]."</a></div>";
  }

  if ($rank % 2 ==0) $sh_pl = "class='lighter1'"; else $sh_pl="";
  ($row4[1]=="")?$row4[1]=0:"";
  $content->set('|style|', $sh_pl);
  $content->set('|rank|', $rank);
  $content->set('|g_name|', $row4[0]);
  $content->set('|sostav|', $sostav);
  $content->set('|gm|', $row4[3]);
  $content->set('|logo|', $row4[2]);
  $content->set('|kolvo|', $kolvo);
  $content->set('|score|', $row4[1]);
  $content->set('|aliance|', $aliance);
  $content->out_content("theme/".$config["theme"]."/them/topguild_c.html");			
 }
 $content->out_content("theme/".$config["theme"]."/them/topguild_f.html");
 timing($topguild["topgcache"]);
 $temp = ob_get_contents();
 write_catch("_dat/cach/".$_SESSION["mwclang"]."_topguild",$temp);
 ob_end_clean(); 
}
else $temp = file_get_contents( "_dat/cach/".$_SESSION["mwclang"]."_topguild");
