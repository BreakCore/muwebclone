<?php  if (!defined('insite')) die("no access");

require "configs/news_cfg.php";

$inpage = $news["shownews"]; /*количество новостей на странице*/
$t = $news["shownews"];
$linksv = $news["newsf"];
$linklen = $news["liklen"];
$shownes = $news["shownews"];

if (isset($_GET["n"]) && $_GET["n"]==0)
 unset($_GET["n"]);

$NewsBase=file("_dat/news.dat"); 
$NewsCount = count($NewsBase);
ob_start();
if ($NewsCount>0)
{
 if (isset($_GET["n"])) $inpost=($NewsCount-1)-($shownes*checknum(substr($_GET["n"],0,5)));
 else $inpost = ($NewsCount-1);

 if (!isset($_GET["news"]))
 {
  for ($i=$inpost;$i>=0;$i--)
  {
   if ($inpage !=0)
    {
	 list($title,$news,$flinkz,$date)=split("::",$NewsBase[$i],4); 
	 if ($linksv=="on" && $flinkz!="none" && strlen(trim($flinkz))>0 && (strlen($flinkz) <=$linklen)) $flinkz="<a href='".$flinkz."'>".$content->getVal("inforum_link")."</a>";
	 else $flinkz="";

	 $title = bbcode($title);
	 $date = @date('d.m.Y',$date);
	 $news = unhtmlentities(bbcode($news));
	 $content->set('|title|', $title);
	 $content->set('|date|', $date);
	 $content->set('|news|', $news);
	 $content->set('|flink|', $flinkz);
	 $content->out_content("theme/".$config["theme"]."/them/news.html");	
	 $inpage--;
	}
	else break;			 	  
  }
 }
 else
 {
  $i = checknum($_GET["news"]);
  if(!empty($NewsBase[$i]))
  {
   list($title,$news,$flinkz,$date)=split("::",$NewsBase[$i],4);
   if ($linksv=="on" && strlen($flinkz)>3 && $flinkz!="none" && $flinkz!="" && (strlen($flinkz) <=$linklen)) $flinkz="<a href='".$flinkz."'>".$content->getVal("inforum_link")."</a>";else $flinkz="";
   $title = bbcode($title);
   $date = @date('d.m.Y',$date);
   $news = unhtmlentities(bbcode($news));
   $content->set('|title|', $title);
   $content->set('|date|', $date);
   $content->set('|news|', $news);
   $content->set('|flink|', $flinkz);
   $content->out_content("theme/".$config["theme"]."/them/news.html");	
   $inpage--;
  }
  else echo "<div class='werms'>no news</span>";
				
 }
 if($t<$NewsCount)
 {
  $content->out_content("theme/".$config["theme"]."/them/paginator_h.html");	
  $pnum=ceil($NewsCount/(int)$t);
 
  for ($i=0;$i<$pnum;$i++)
  {
   if ((!$_GET["n"] && $i==0) || $i==$_GET["n"])
   {
    $content->set("|i|",($i+1));
	$content->set("|ccl|","pgenum");
   }							
   else 
   {
    $content->set("|i|","<a href='".$config["siteaddress"]."/?n=".$i."'>".($i+1)."</a>");
	$content->set("|ccl|","pgnum");
   }
   $content->out_content("theme/".$config["theme"]."/them/paginator_push.html");	
  }
  $content->out_content("theme/".$config["theme"]."/them/paginator_f.html");	
 }
}
else echo "<div align='center'>No avaliable news, sorry.</div>";	

 $temp = ob_get_contents();
 ob_end_clean();