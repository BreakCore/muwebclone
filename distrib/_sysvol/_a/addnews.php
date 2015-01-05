<?php 
if (defined('inpanel'))
{
 ob_start();
 global $config;
 global $content;
 require "configs/news_cfg.php";
 $nforumlink = "none";
 if(!function_exists('get_magic_quotes_gpc'))
 {
  function get_magic_quotes_gpc() {return false;}
 }
 $newsDB=file("_dat/news.dat");
 $content->out_content("_sysvol/_a/theme/addnews_h.html");
	$content->set('|news_content|',"");
  if (!$_GET["act"])
  {
   if (($_REQUEST["addnews"])&&(!empty($_POST['NewNews']))||(!empty($_POST['NewTitle']))) /*запись самих новостей*/
   {
    $mnews = bugsend(str_replace("\n","[br]",stripslashes($_POST["NewNews"])));
    if ($news["newsf"]=="on") $nforumlink = htmlspecialchars(substr($_POST["flink"],0,$news["liklen"]));
    else $nforumlink = "none";

    if (!get_magic_quotes_gpc()) $mnews = addslashes($mnews); 
			
    $OpenNewsFile=fopen("_dat/news.dat",'a');
    if (!get_magic_quotes_gpc()) $addn = addslashes(bugsend(htmlspecialchars($_POST["NewTitle"])));
    else $addn = bugsend(htmlspecialchars($_POST["NewTitle"]));
			
    fwrite($OpenNewsFile,$addn."::".$mnews."::".$nforumlink."::".time().chr(13).chr(10));
    fclose($OpenNewsFile);	
    unset($mnews,$_REQUEST["addnews"],$addn);
    header("Location:".$config["siteaddress"]."/control.php?page=addnews");
   }
  }	 
  if ($_REQUEST["edited"])/*если нажата кнопка "редактировать"*/
  {
   $title = htmlspecialchars($_POST["NewTitle"]);
   if ($news["newsf"]=="on") $nforumlink = htmlspecialchars(substr($_POST["flink"],0,$news["liklen"]));
   else $nforumlink = "none";
   $newsc = bugsend(str_replace("\n","[br]",stripslashes($_POST["NewNews"])));
   $nom = checknum($_POST["new"]);
   $nom=$_SESSION["newsnum"];
   $newsF="_dat/news.dat";
   if (!get_magic_quotes_gpc()) $newsc = addslashes($newsc); 
   $newsDB[$nom]=$title."::".$newsc."::".$nforumlink."::".time().chr(13).chr(10);		
   $fne = fopen($newsF,"w");
   for ($i=0; $i<count($newsDB);$i++)
   {fwrite($fne,$newsDB[$i]);}
   fclose($fne);
   $nforumlink = "none";
   unset($title,$newsc,$_POST["NewNews"],$_REQUEST["edited"]);
   header("Location:".$config["siteaddress"]."/control.php?page=addnews");
  }
  if ($_GET["act"]=="edit") /*поле редактирования*/
  {
			$nom = checknum(substr($_GET["new"],0,5));
			$_SESSION["newsnum"] = $nom;
			$openfile=file("_dat/news.dat");
			list($title,$newsc,$nforumlink,$date)=split("::",$openfile[$nom]);
			$content->set('|button_v|', news_editbtn." name='edited'"); 
  }	
  else
	  $content->set('|button_v|', news_addbtn." name='addnews'");
	
 if ($_GET["act"]=="del")
	 {
		$nom = validate($_GET["new"]);
		$lnum = count($newsDB);
		unset ($newsDB[$nom]);
		$newsF="_dat/news.dat";	
		$fdel = fopen($newsF,"w");
		for ($i=0; $i<$lnum;$i++)
		{
			if (!$newsDB[$i]){ $i++; fwrite($fdel,$newsDB[$i]);}
			else {fwrite($fdel,$newsDB[$i]);}
		}
		fclose($fdel);
		unset($_SESSION["newsnum"]);
		header("Location:".$config["siteaddress"]."/control.php?page=addnews");
	 }
	 

	 $content->set('|content_title|', $title); 
	 if (strlen($nforumlink)>4 ) $content->set('|checked|', "checked");
	 else $content->set('|checked|', "");
	 
	 $content->set('|nforumlink|', $nforumlink);
	 $content->set('|news_content|', unhtmlentities(str_replace("[br]","\n",stripslashes($newsc)))); 
	 
	 //if($_REQUEST["editnews"]) 
	// else 

	 $bdnews=file("_dat/news.dat");
	 $kol=count($bdnews)-1;
	 $content->out_content("_sysvol/_a/theme/addnews_c.html");
	for ($i=$kol;$i>=0;$i--)
	{
	 list($title,$newsc,$nforumlink,$date)=split("::",$bdnews[$i]);

	 $content->set('|i|', $i);
	 $content->set('|date|', @date('d.m.Y',$date));
	 $content->set('|titleZ|', bbcode(unhtmlentities($title)));
	 $content->out_content("_sysvol/_a/theme/addnews_list.html");
	}
	$content->out_content("_sysvol/_a/theme/addnews_f.html");
 

 $temp = ob_get_contents();
 ob_end_clean(); 
}
else die();