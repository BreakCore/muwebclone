<?php if (!defined('insite')) die("no access"); 
require "configs/topguild_cfg.php";


 error_reporting(E_ALL);
$gettime = time();
function rond($text,$num)
{
 $numz[32]="class='bms'";
 $numz[64]="class='agms'";
 $numz[128]="class='gms'";
 $numz[0] = "";
 return "<span ".$numz[$num].">".$text."</span>";
}

if($gettime-load_cache("_dat/cach/".$_SESSION["mwclang"]."_topguild",true) > $topguild["topgcache"])
{
 ob_start();
 $content->add_dict("guildtop");
 $content->out("topguild_h.html");


 $guildtop = $db->query("SELECT TOP 100
 gg.G_Name,
 gg.G_Score,
 CONVERT(varchar(66),gg.G_Mark,2) as G_Mark,
 gg.G_Master,
 gg.G_Union,
 gg.number,
 CONVERT(varchar(max),(SELECT G_Name + ',,' FROM guild  WHERE (G_Union = gg.G_Union or  G_Union = gg.number or number=gg.G_Union) and G_Union >0 and G_Name <> gg.G_Name FOR XML PATH('')),2) as alianses,
 gm.Name,
 (SELECT count(*)  FROM GuildMember WHERE G_Name = gg.G_Name) as kolvo
FROM
 guild gg,
 GuildMember gm
WHERE
 gm.G_Name = gg.G_Name
 AND gm.G_Status = 128
order by gg.G_score desc");

 $rank = 1;
 while($res = $guildtop->FetchRow())
 {
  if ($rank % 2 ==0)
   $sh_pl = "class='lighter1'";
  else
   $sh_pl="";

  $content->add_dict($res);
  $content->set("|logo|",GuildLogo($res["G_Mark"],$res["G_Name"],20,$topguild["topgcache"]));
  $content->set("|rank|",$rank);
  $content->set("|style|",$sh_pl);
  $content->out("topguild_c.html");
  $rank ++;
 }
 $content->out("topguild_f.html");
/* for($i=0;$i < $num_gx ;$i++)
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


 }
 $content->out_content("theme/".$config["theme"]."/them/topguild_f.html");
 timing($topguild["topgcache"]);*/
 $temp = ob_get_contents();
 write_catch("_dat/cach/".$_SESSION["mwclang"]."_topguild",$temp);
 ob_clean();
}
else
 $temp = load_cache( "_dat/cach/".$_SESSION["mwclang"]."_topguild");
