<?php
/**
* модуль изменения параметров доступа к модулям 
* Mu Web Clone
**/
$adb = @file("_dat/maccess.db");

require "lang/{$_SESSION["mwclang"]}/{$_SESSION["mwclang"]}_titles.php";

if(isset($_REQUEST["add_a"]))
{
 $neum = (int)$_POST["a_num"];
 $id = (int)$_POST["aid"];
 ($neum>100) ? $neum=100 : true;
 $tmp = explode("::",$adb[$id]);
 $adb[$id]=$tmp[0]."::".$neum."\r\n";
 $dhandler = fopen("_dat/maccess.db","w");
 fputs($dhandler, implode("",$adb));
 fclose($dhandler);
 logs::WriteLogs ("Adm_","администратор ".$_SESSION["sadmin"]." изменил уровень доступа у ".$tmp[0]);
 header("Location: ".$config["siteaddress"]."/control.php?page=acccess");
}
elseif(isset($_REQUEST["arefresh"]))
{
 $dhandler = fopen("_dat/maccess.db","w");
 fclose($dhandler);
 logs::WriteLogs ("Adm_","администратор ".$_SESSION["sadmin"]." сбросил все разрешения");
 header("Location: ".$config["siteaddress"]."/control.php?page=acccess");
}

if(isset($_REQUEST["acrefr"]))
 $adb="";

if(isset($adb) && !empty($adb)) //если файла нет
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

if (!isset($_GET["ed"]))
{
 $content->out("access_h.html");
 //$array = get_defined_constants(true);
 foreach($adb as $id=>$var)
 {
  $tmp = explode("::",$var);
  $content->set('|accs_nfile|',$tmp[0]);
  $content->set('|accs_nnum|',$tmp[1]);
  $content->set('|opt|',$id);
  //$content->set('|accs_ndes|',$array["user"]["title_".$tmp[0]]);
  if(isset($lang["title_".$tmp[0]]))
   $content->set('|accs_ndes|',$lang["title_".$tmp[0]]);
  else
   $content->set('|accs_ndes|',"title_".$tmp[0]);
  $content->out("access_c.html");
 }
}
else
{
 $neum = (int)$_GET["ed"];
 $tmp = explode("::",$adb[$neum]);
 $content->set('|nval|',$tmp[1]);
 $content->set('|hval|',$neum);
 $content->out("access_form.html");
}
$content->out("access_f.html");
