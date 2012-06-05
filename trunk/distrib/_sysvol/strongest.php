<?php if (!defined('insite')) die("no access"); 
unset($ntime);
$ntime =  @filemtime("_dat/cach/top_strongest");
require "configs/strongest_cfg.php";

if(!$ntime || (time()-$ntime>$strongest["cach"]))
{
  global $db;
  global $config;
  global $content;
  
  if ($strongest["show_adm"]==0) $show_gm="CtlCode !=32 and";
  else $show_gm="";
  $templ = explode(",",$strongest["stron_hiden"]);
  if (count($templ>0))
  {
    $hiden = "";
	foreach ($templ as $id=>$val)
	{
	 $hiden.=" And Name!='".$val."'";
	}
  }
  else $hiden="";
  $resulttop5 = $db->query("SELECT TOP 5 Name,".$strongest["res_colum"].",cLevel, AccountID, Class,gr_res from Character WHERE ".$show_gm." CtlCode != 1 ".$hiden." and CtlCode != 17  ".$strongest["str_sort"]);
  $count = $db->numrows($resulttop5);
  ob_start();	

  if ($strongest["top_type"]==1)
  {
   for($i=0;$i < $count;$i++)
   {
    $rowtop5 = $db->fetchrow($resulttop5);
    $oinfoc = $db->fetchrow($db->query("SELECT MEMB_STAT.ConnectStat FROM AccountCharacter, MEMB_STAT WHERE AccountCharacter.GameIDC='$rowtop5[0]' and MEMB_STAT.memb___id='$rowtop5[3]'"));
    $respg = $db->query("SELECT G_Name FROM GuildMember WHERE Name='$rowtop5[0]'");
    $pg1 = $db->numrows($respg);
    if ($pg1>0) $pg =$db->fetchrow($respg);
    else $pg[0]=0;	
    $rowtop5[4] = classname($rowtop5[4]);
    if (empty($pg[0])||$pg[0]==0)
    {
     $chkgm = $db->fetchrow($db->query("SELECT Name,G_Name FROM GuildMember WHERE Name='$rowtop5[0]'"));
     if ($chkgm) $pg[0]=$chkgm[1];
     else $pg[0]="none";
    }
    if ($oinfoc[0]==0)
    {
     $oinfo1 = $db->fetchrow($db->query("SELECT DisConnectTM FROM MEMB_STAT WHERE memb___id='$rowtop5[3]'"));
     $stats = "<span style=color:#FF0505;font-weight:bold;font-size:12px;>Offline</span> ";
    }
    else if ($oinfoc[0]==1)
    {
     $oinfo1 = $db->fetchrow($db->query("SELECT ConnectTM FROM MEMB_STAT WHERE memb___id='$rowtop5[3]'"));	
     $stats="<span style=color:#04C200;font-weight:bold;font-size:12px;>Online</span> ";
    }
    if ($strongest["greset"]==1 && $strongest["greset_st"]==1)
    {
     $gr_star="&nbsp;";
     while ($rowtop5[5]>0)
     {
      $gr_star.="<img src=\"imgs/gres.gif\"  border=\"0\" />";
      $rowtop5[5]--;
     }
    }
    else if ($str["greset"]==1 && $str["greset_st"]==0)
    {
     $gr_star=" <br>Grand Reset:".$rowtop5[5];
    }
	$oinfo = "
	".$content->lng["sreongest_guild"]." <i> ".$pg[0]."</i><br>
	".$content->lng["sreongest_class"]." <i>".$rowtop5[4]."</i><br>
	".$content->lng["sreongest_status"]." <i>".$stats."<br>"; if($oinfo1[0]!=NULL) $oinfo.="".$content->lng["sreongest_pr"]." ".$oinfo1[0]." </i>";
    $content->set("|oinfo|", $oinfo);
    $content->set("|level|", $rowtop5[2]);
    $content->set("|reset|", $rowtop5[1]);
    $content->set("|gstar|", $gr_star);
    $content->set("|cname|", $rowtop5[0]);
    $content->out_content("theme/".$config["theme"]."/them/strongest.html");
   }
  }
  elseif ($strongest["top_type"]==2)
  {
	$class_list = explode(",", $strongest["stron_sh"]);
	$st_show="";
	foreach ($class_list as $n=>$v)
	{
		$st_show.=q_chr_top($v);
	}
	echo $st_show;
  }
  else echo "error: in top_type!";
  $temp = ob_get_contents();
  write_catch ("_dat/cach/top_strongest",$temp);
  ob_end_clean();	 	
} else $temp = file_get_contents ('_dat/cach/top_strongest');