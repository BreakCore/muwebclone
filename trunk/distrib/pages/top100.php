<?php
if (!defined('insite')) die("no access");
require "configs/top100_cfg.php";

$gettime = time();
if (isset($_GET["class"]) && !empty($_GET["class"]))
{
 switch($_GET["class"])
 {
  case "dw": $texq="and ch.Class in (0,1,2,3)";  $okon = "sm"; break;
  case "dk": $texq="and ch.Class in (16,17,18,19) "; $okon = "bm"; break;
  case "elf":$texq="and ch.Class in (32,33,34,35) "; $okon = "he"; break;
  case "mg": $texq="and ch.Class in (48,49,50) "; $okon = "mg"; break;
  case "dl": $texq="and ch.Class in (64,65,66) "; $okon = "dl"; break;
  case "sum":$texq="and ch.Class in (80,81,82,83) "; $okon = "sum";break;
  case "rf": $texq="and ch.Class in (96,97,98,99) "; $okon = "rf"; break;
  default: die("no such class!");
 }
}
else
{
 $okon="";
 $texq = "";
}

$hidead = explode(",",$top100["hidenicks"]);
$hide = "";
$j=0;
foreach ($hidead as $id=>$val)
{
 if($j>0)
  $hide.=",";
 $hide.="'$val'";

 $j++;
}
if(!empty($hide))
{
 $hidead = " AND ch.Name not in($hide) ";
}
$resultop = "SELECT TOP 100
 ch.Name,
 ch.Class,
 ch.cLevel,
 ch.{$top100["t100res_colum"]},
 ch.Strength,
 ch.Dexterity,
 ch.Vitality,
 ch.Energy,
 ch.Leadership,
 ch.AccountID,
 ch.gr_res,
 mi.opt_inv,
 gld.G_Name,
 CONVERT(varchar(max),gld.G_Mark,2) as g_mark,
 ms.ConnectStat,
 CONVERT(varchar(max),ms.ConnectTM,120) as ConnectTM,
 CONVERT(varchar(max),ms.DisConnectTM ,120) as DisConnectTM,ms.ServerName
FROM
 [Character] ch
 left join [GuildMember] gm ON gm.Name = ch.Name
 left join [Guild] gld on gld.G_Name = gm.G_Name
 left join AccountCharacter ac on ac.Id = ch.AccountID
 inner join MEMB_STAT ms on ms.memb___id COLLATE DATABASE_DEFAULT = ch.AccountID COLLATE DATABASE_DEFAULT,
 MEMB_INFO mi
WHERE
 mi.memb___id=ch.AccountID
 AND ch.CtlCode not in (1,17) ".$hidead.$texq.$top100["t100str_sort"];


if(time() - load_cache("_dat/cach/top_100".$okon,true) > $top100["t100cach"])
{
 ob_start();
 $content->set('|siteaddress|', $config["siteaddress"]);
 $content->out("top100_h.html");
			
 $show_chrs = explode(",",$top100["t100show_class"]);
 if (count($show_chrs)>0)
 {
  function s_cl($class)
  {
   if (strlen($class)>1)
   {
	switch($class)
	{
	 case "dw":return "DW/SM/GM"; 
	 case "dk":return "DK/BK/BM"; 
	 case "dl":return "DL/LE"; 
	 case "elf":return "Elf/ME/HE"; 
	 case "mg":return "MG/DM"; 
	 case "sum":return "S/BS/DMS"; 
	 case "rf":return "RF/FM"; 
	 case "all":return "All"; 
	 default: die("wrong paramenter");
	}
   }
  }						
	
  foreach ($show_chrs as $val) 
  {
   if ($val !="all") $lval= "&class=".$val; else $lval="";
   $content->set('|lval|', $lval);
   $content->set('|cl_n|', s_cl($val));
   $content->out("top100_m_chrs.html");
  }
 }
 
 $content->out("top100_h1.html");
			
 $columns = explode(",",$top100["t100_titles"]);
 if (in_array("imggres",$columns))
 $columns[array_search("imggres",$columns)] = "<img src=\"imgs/gres.gif\"  border=\"0\" />";
			
 foreach ($columns as $n=>$c)
 {
  if ($c) 
  {
	$content->set('|cap|', $c);
	$content->out("top100_h1_c.html");
  }
 }
	
 $content->out("top100_h1_f.html");
 $rank = 1;
 $i = 1;
 $resultop_ = $db->query($resultop);

 while($rowop = $resultop_->FetchRow())
 {

  if (isset($rowop["G_Name"]) && !empty($rowop["G_Name"]))
  {
   $mark = GuildLogo($rowop["g_mark"],$rowop["G_Name"],10,$top100["t100logotime"]);
   $rowop["G_Name"] = "<a href='".$config["siteaddress"]."/?p=topguild#".$rowop["G_Name"]."'>".$mark.$rowop["G_Name"]."</a>";
  }
  else
  {
   $rowop["G_Name"]=" ";
  }

  $rowop["Class"] = classname($rowop["Class"]);
  if(empty($rowop["Leadership"]) || !isset($rowop["Leadership"]))
   $rowop["Leadership"]=0;

  if($rowop["ConnectStat"] == 0)
  {
   $contime = "Disconnected  ".$rowop["DisConnectTM"];
   $bgcol="background:#fd7272;";
   $statz = "offline";
  }
  else
  {
   $contime = "Connected  ".$rowop["ConnectTM"];
   $bgcol="background:#b0fdab;";
   $statz = "online";
  }

  if ($rowop["opt_inv"] == 0)
  {
   /**
    * отслеживание 65к статов
    */
   $rowop["Strength"] = stats65($rowop["Strength"]);
   $rowop["Dexterity"] = stats65($rowop["Dexterity"]);
   $rowop["Vitality"] = stats65($rowop["Vitality"]);
   $rowop["Energy"] = stats65($rowop["Energy"]);
   $rowop["Leadership"] = stats65($rowop["Leadership"]);
  }
  else
  {
   $rowop["Strength"] = "<img src='imgs/lock.png' border='0' alt='hide'>";
   $rowop["Dexterity"] = "<img src='imgs/lock.png' border='0' alt='hide'>";
   $rowop["Vitality"] = "<img src='imgs/lock.png' border='0' alt='hide'>";
   $rowop["Energy"] = "<img src='imgs/lock.png' border='0' alt='hide'>";
   $rowop["Leadership"] = "<img src='imgs/lock.png' border='0' alt='hide'>";
  }

  if ($rank % 2 ==0)
   $sh_pl = "class='lighter1'";
  else
   $sh_pl="";


  //header center
  $content->set('|style|', $sh_pl);
  $content->out("top100_c_h.html");

  if ($rowop["gr_res"]>0)
   $assoc["imggres"] = $rowop["gr_res"];//$rowop[10]; //greset
  else
   $assoc["imggres"]="";

  $assoc["num"] = "<a href='#".$rank."' id='tooltiper' title='".$statz." ".$contime."'>".$rank."</a>";
  $assoc["Name"] = $rowop["Name"];
  $assoc["Class"] = $rowop["Class"];
  $assoc["Lvl"] = $rowop["cLevel"];
  $assoc["Res"] = $rowop[$top100["t100res_colum"]];
  $assoc["Str"] = $rowop["Strength"];
  $assoc["Agi"] = $rowop["Dexterity"];
  $assoc["Vit"] = $rowop["Vitality"];
  $assoc["Ene"] = $rowop["Energy"];
  $assoc["Com"] = $rowop["Leadership"];
  $assoc["Guild"] = $rowop["G_Name"];
  $assoc["MLvl"] = "-/-";

  //center center
  foreach ($columns as $id=>$val)
  {

   if ($val=="#")
   {
    $content->set('|style|', $bgcol);
    $val="num";
   }
   else $content->set('|style|', "");
   if (strlen($val)>25)
    $val="imggres";

   if ($val=="Name")
    $content->set('|data|', "<a href='".$config["siteaddress"]."/?p=search&caracter=".$assoc["Name"]."'>".$assoc["Name"]."</a>");
   else
    $content->set('|data|', $assoc[$val]);

   $content->out_content("theme/".$config["theme"]."/them/top100_c_c.html");
  }

  $rank++;
 }

 $content->out_content("theme/".$config["theme"]."/them/top100_f.html");
 timing($top100["t100cach"],$content);
 $temp = ob_get_contents();
 write_catch("_dat/cach/top_100".$okon,$temp);
 ob_clean();
}
else $temp = load_cache( "_dat/cach/top_100".$okon);
			