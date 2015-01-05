<?php
/**
* модуль изменения параметров доступа к модулям 
* Mu Web Clone
**/
ob_start();
global $content; 
global $config;
$adb = @file("_dat/maccess.db");
require "lang/".$_SESSION["mwclang"]."/".$_SESSION["mwclang"]."_titles.php";
if($_REQUEST["add_a"])
{
 $neum = checknum(substr($_POST["a_num"],0,3));
 $id = checknum(substr($_POST["aid"],0,3));
 ($neum>100)?$neum=100:true;
 $tmp = explode("::",$adb[$id]);
 $adb[$id]=$tmp[0]."::".$neum."\r\n";
 $dhandler = fopen("_dat/maccess.db","w");
 fputs($dhandler, implode("",$adb));
 fclose($dhandler);
 WriteLogs ("Adm_","администратор ".$_SESSION["sadmin"]." изменил уровень доступа у ".$tmp[0]);
 header("Location: ".$config["siteaddress"]."/control.php?page=acccess");
}
elseif($_REQUEST["arefresh"])
{
 $dhandler = fopen("_dat/maccess.db","w");
 fclose($dhandler);
 WriteLogs ("Adm_","администратор ".$_SESSION["sadmin"]." сбросил все разрешения");
 header("Location: ".$config["siteaddress"]."/control.php?page=acccess");
}

if($_REQUEST["acrefr"]) $adb="";

if(strlen($adb)==0 or strlen(trim($adb[0]))==0) //если файла нет
{

 $whandle = fopen("_dat/maccess.db","w");
 $Lhandle = opendir("_sysvol/_a");
 $noneed = array(".","..",".htaccess");
 while (($file = readdir($Lhandle))!== false) 
 {    
  $n = strpos($file, ".php");
  if (!in_array($file,$noneed)&& strlen($file)>4 && $n>0) 
  { 
    fwrite($whandle, substr($file,0,$n)."::100\r\n");  
  }
 }
 fclose($whandle);
 $adb = @file("_dat/maccess.db");
}

if (strlen($_GET["ed"])<=0)
{
 $content->out_content("_sysvol/_a/theme/access_h.html");
 //$array = get_defined_constants(true);
 foreach($adb as $id=>$var)
 {
  $tmp = explode("::",$var);
  $content->set('|accs_nfile|',$tmp[0]);
  $content->set('|accs_nnum|',$tmp[1]);
  $content->set('|opt|',$id);
  //$content->set('|accs_ndes|',$array["user"]["title_".$tmp[0]]);
  $content->set('|accs_ndes|',$lang["title_".$tmp[0]]);
  $content->out_content("_sysvol/_a/theme/access_c.html");
 }
}
else
{
 $neum = checknum(substr($_GET["ed"],0,3));
 $tmp = explode("::",$adb[$neum]);
 $content->set('|nval|',$tmp[1]);
 $content->set('|hval|',$neum);
 $content->out_content("_sysvol/_a/theme/access_form.html");
}
$content->out_content("_sysvol/_a/theme/access_f.html");


$temp = ob_get_contents();
ob_end_clean();   