<?php if (!defined('inpanel'))die();
/**
* Парсинг рсс у ucoz форума
*@path = путь до форума
*/
function addnews($path="http://www.p4f.ru/forum/0-0-0-37")
{/*
 global $content;
 $temp = @file_get_contents($path);
 if($temp)
 {
 require "_sysvol/xml.php";
 $tt = new Xml();
 $text = $tt->start($temp);
 ob_start();
 foreach ($text["rss"]["channel"]["item"] as $id=>$v)
 {
     $content->set('|title|',"<a href='".$v["link"]."'>".iconv("UTF-8","Windows-1251",$v["title"])."</a>");
     $content->set('|time|',$v["pubDate"]);
     $content->set('|des|',iconv("UTF-8","Windows-1251",$v["description"]));
     $content->out_content("_sysvol/_a/theme/news.html");
 }   
 $temp = ob_get_contents();
 ob_end_clean();
 return $temp;
 }
 else return "<div align='center'>Can't open site</div>";*/
 return "on reconstruction";
}
/**
* конструктор меню администратора
*/
function getadmenu($config,$content)
{
 if(!isset($_SESSION["mwclang"]))
   $_SESSION["mwclang"] = $config["def_lang"]="rus";
	  
 $loadfile = @file("_dat/amenu.dat");
 $nowitime = time();
 $cachtime = @filemtime("_dat/menus/".$_SESSION["mwclang"]."_admmenu"); 

 if (empty($loadfile) or !$loadfile) echo "error menu loading!";
 else
 {
  if(!$cachtime || ($nowitime-$cachtime >3606))
  {
 /*  if ($config["oporclos"]==1)
   {
    if($_GET["page"]!="update" && $_GET["page"]!="modules") require "_sysvol/imbrowser.php";
    $str=unicontent("http://muwebclone.googlecode.com/svn/trunk/patches/updatelist.mwc");
    $handle = fopen("_dat/updates/updlist","w");
    fwrite ($handle,$str);
    fclose ($handle);
   }*/
   ob_start();
   include "lang/".$_SESSION["mwclang"]."/".$_SESSION["mwclang"]."_titles.php";

   foreach ($loadfile as $m)
   {
    $showarr = explode("::",$m);
    $showarr[1]=trim($showarr[1]);
    $content->set('|modulename|', $showarr[0]);
    $content->set('|modulecapt|', $lang[$showarr[1]]);
    $content->out("admmenu.html");
   }
    $content->set('|modulecapt|', $content->getVal("admm_onsite"));
    $content->out("admmenu_u.html");
   $bufer = ob_get_contents();
   write_catch("_dat/menus/".$_SESSION["mwclang"]."_admmenu",$bufer);
   ob_clean();
   return $bufer;	
  }
  else 
    return file_get_contents ("_dat/menus/".$_SESSION["mwclang"]."_admmenu");
 }
}
/*
* показать модули админа
*/
function a_modul($mname,$db,$content,$config)
{
 if( strlen($mname)>1)
 {
  $apages = preg_replace("/[^a-zA-Z0-9_-]/i", "", substr($mname,0,15));
  if(get_accesslvl($db) >= checacc($apages) && strlen($_SESSION["sadmin"])>3)
  {
   if(file_exists("_sysvol/_a/".$apages.".php"))
   {
    ob_start();
     require "_sysvol/_a/".$apages.".php";
	 $temp_a = ob_get_contents(); 
    ob_clean();
    if (empty($temp))
     return $temp_a;
    return $temp;
   }
   else return "wrong page!";
  }
  else
   return "<div align='center'>you haven't access</div>";
 }
 return "wrong page!";
	
}
/*
* инфо о(для) админе
*/
function ainfo($db,$content)
{
 $alert=0;
 
 $letters = $db->query("SELECT count(*) as cnt FROM MWC_messages WHERE slave_id=0 and isread=0 or slave_id=0 and isread=NULL")->FetchRow();
 ($letters["cnt"] < 1) ? $letters=0 : true;
 $content->set('|lnum|', $letters);
 $content->set('|admin|', $_SESSION["sadmin"]);
 $content->set('|gradm|', get_group($db,$content));
 $content->set('|upd_msg|',"");
 $warn = array("Inject","MaybeP","Connec","ALARMA","Pages_");
 $Lhandle = opendir("logZ");

 $gfile="";
 $id=0;
 if (get_accesslvl($db)>=checacc("lreader"))
 {
  while ($file = readdir($Lhandle))
  {
   if (in_array(substr($file,0,6),$warn))
   {
    $alert=1;
	$gfile=$file;
    break;
   }
  }
 }
 else
  $alert=0;
  
 if($alert>0)
 {

  $content->set('|alert_msg|', "<img src=\"imgs/x.gif\" border=\"0\" alt=\"сообщения\" style=\"position:relative;top:4px;left:0px;\">".$content->getVal("adm_warn"));
 $content->set('|vari|', 1);
 $content->set('|numb|', $gfile);
 }
 else
 {
  $content->set('|alert_msg|', "");
  $content->set('|numb|',"");
  $content->set('|vari|', 0);
 }
 //update

 if(time()-@filemtime("_dat/updates/ut")>3600)
 {
  $writers = @file("_dat/updates/ut");
  $cwriters = @file("_dat/updates/updlist");
  if (count($cwriters)>(int)$writers[0])
  {
    $content->set('|upd_msg|',$content->getVal("upd_msg"));
	$handle = fopen("_dat/updates/ut","w");
	fwrite($handle,count($cwriters));
	fclose($handle);
  }
 }
 return $content->out("ainfo.html",1);
}

/**
* узнать максимальный доступ у админа
*/
function get_accesslvl($db)
{
 if (isset($_SESSION["sadmin"]))
 {

  $level = $db->query("SELECT [access] FROM MWC_admin WHERE name='{$_SESSION["sadmin"]}'")->FetchRow();
  return $level["access"];
 }
 die("you don't access to this pages!");
}
/**
* отображение группы по провам доступа в админку (для админов и гм)
*/
function get_group($db,$content)
{
  if(isset($_SESSION["sadmin"]))
  {
   $acclevel = get_accesslvl($db);
   if ($acclevel>0 && $acclevel<=10) return $content->getVal("adm_gr1"); //gm
   elseif ($acclevel>10 && $acclevel<=20) return $content->getVal("adm_gr2"); //moders
   elseif ($acclevel>20 && $acclevel<=30) return $content->getVal("adm_gr3"); //adm helpers
   elseif ($acclevel>30 && $acclevel<=50) return $content->getVal("adm_gr4"); //adm
   elseif ($acclevel>50 && $acclevel<100) return "helper guy"; //adm ?
   elseif ($acclevel==100) return $content->getVal("adm_gr5"); //main adm
   else die("access error!");
  }
}

/**
* проверка на доступ
* @mname - навание модуля
* возвращает уровень тоступа
*/
function checacc($mname)
{
 $adb = @file("_dat/maccess.db");
 $level = 100;
 foreach($adb as $id=>$v)
 {
  $tmp = explode("::",$v);
  if($tmp[0] == $mname)
  {
   $level = (int)$tmp[1];
   break;
  }
 }
 return $level;
}

/**
* названия страничек
*/
function show_t($tt,$t=1)
{
 include "lang/".$_SESSION["mwclang"]."/".$_SESSION["mwclang"]."_titles.php";
 if ($t==1)
  echo  $lang["title_".trim($tt)];
 else
  return $lang["title_".trim($tt)];
}