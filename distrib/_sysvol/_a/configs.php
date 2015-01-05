<?php if (!defined('inpanel')) die("no access");
/**
* модуль изменения настроек модулей
* Mu Web Clone
**/
if ($_GET["edit"]=="reinstall")
{
 global $config;
 echo "<script>if(confirm('Are you sure?'))  document.location.href='".$config["siteaddress"]."/control.php?page=configs&edit=reinstall&sure=ok';
 else document.location.href='".$config["siteaddress"]."/control.php?page=configs'; </script>";
 
 if($_GET["sure"]=="ok")
 {
  global $db;
  $db->query("UPDATE MWC SET value = '0' WHERE parametr='reinstall' DELETE FROM MWC_admin");
  WriteLogs ("Adm",$_SESSION["sadmin"]." решил переустановить сайт!");
  rename("_dat/install.php","install.php");
  header("location: ".$config["siteaddress"]."/install.php");
 }
}
ob_start();
global $content;
include "lang/".$_SESSION["mwclang"]."/".$_SESSION["mwclang"]."_titles.php";

$content->out_content("_sysvol/_a/theme/configs_h.html");
//строим меню
$Lhandle = opendir("configs/");
$noneed = array(".","..",".htaccess");

$content->set('|cfgname|',$lang["title_opt"]);
$content->set('|pg|',"opt");
$content->out_content("_sysvol/_a/theme/configs_c.html");
$content->set('|cfgname|',$lang["title_reinst"]);
$content->set('|pg|',"reinstall");
$content->out_content("_sysvol/_a/theme/configs_c.html");

while (($file = readdir($Lhandle))!== false) 
{    
 $n = strpos($file, ".php");
 if (!in_array($file,$noneed)&& strlen($file)>4 && $n>0) 
 { 
  if($lang["title_".substr($file,0,$n-4)]=="")$content->set('|cfgname|',"title_".substr($file,0,$n-4));
  else $content->set('|cfgname|',$lang["title_".substr($file,0,$n-4)]);
  $content->set('|pg|',substr($file,0,$n-4));
  $content->out_content("_sysvol/_a/theme/configs_c.html");
 }
}
//если надо сохранить кфг
if($_REQUEST["aplcfg"] && $_GET["edit"])
{
  $mname = substr($_GET["edit"],0,10);
  
  if (file_exists("configs/".$mname."_cfg.php") || $mname=="opt")
  {
  
   $fileZ="";
   if ($mname!="opt")  require("configs/".$mname."_cfg.php");
   else 
   {
    require "opt.php";
	$mname="config";
	 $fileZ = "opt.php";
   }

   foreach ($_POST as $pid=>$pval)
   {
     eval('
	   if (strlen($'.$mname.'["'.$pid.'"])>0)
	   {
	    $'.$mname.'["'.$pid.'"]="'.$pval.'";
	   }');
   }
   $in_write = '<?php if (!defined("insite")) die("no access");'.chr(13).chr(10);

    eval('foreach ($'.$mname.' as $id=>$value)
      {
	   $v = (int)$value;
	   if($id=="db_upwd") $value="'.$config["db_upwd"].'";
	   if(strlen($v)<strlen($value))
	   {
	    if ($'.$mname.'[$id]=="") $in_write.=\'$'.$mname.'["\'.$id.\'"]="nothinghere";\'.chr(13).chr(10);
	    else $in_write.=\'$'.$mname.'["\'.$id.\'"]="\'.$value.\'";\'.chr(13).chr(10);
	   }
	   else
	   {
	    if ($'.$mname.'[$id]=="") $in_write.=\'$'.$mname.'["\'.$id.\'"]=0;\'.chr(13).chr(10);  
        else $in_write.=\'$'.$mname.'["\'.$id.\'"]=\'.$value.\';\'.chr(13).chr(10);  
       }		
	  }');
	  if (strlen($fileZ)>0)  $h = fopen("opt.php","w");
	  else $h = fopen("configs/".$mname."_cfg.php","w");
	  fwrite($h, $in_write);
	  fclose($h);
  }
  else echo "can't find config!";
}

if($_GET["edit"])//если модуль выбран
{
 $mname = substr($_GET["edit"],0,10);
 if (file_exists("configs/".$mname."_cfg.php") or $mname=="opt")
 {
 if ($mname!="opt")
 {
  require "configs/".$mname."_cfg.php";
  include "lang/".$_SESSION["mwclang"]."/".$_SESSION["mwclang"]."_cfg.php";
  $content->set('|pg|',$mname);
 }
 else 
 {
  require $mname.".php";
  include "lang/".$_SESSION["mwclang"]."/opt_cfg.php"; 
  $content->set('|pg|',$mname);
  $mname = "config";
 }
 

  
  $content->out_content("_sysvol/_a/theme/configs_form_h.html");
  eval('foreach ($'.$mname.' as $id=>$value)
      {
	   $content->set("|desc|",$lang[$id]);
	   if (is_array($value))
	   {
	    $value = implode(",",$value);
	   }
	   $content->set("|elemn|",$id);
	   if($id=="db_upwd")
	   {
	    $content->set("|disabled|","disabled");
		$content->set("|elemn|","");
		$value="it is pwd";
	   }
	   elseif (substr($id,(count($id)-5))=="_def") 
       {	   
	    $content->set("|elem|",$value);
	    $content->set("|elemn|",$id);
		$content->out_content("_sysvol/_a/theme/configs_form_h_c.html");
	    $content->set("|disabled|","disabled");
		$content->set("|elemn|","");
	   }
       else  $content->set("|disabled|","");
	   $content->set("|elem|",$value);
	   
 	   $content->out_content("_sysvol/_a/theme/configs_form_c.html");
	  }');
  $content->out_content("_sysvol/_a/theme/configs_form_f.html");
 }
 else echo "no file!";
}

$content->out_content("_sysvol/_a/theme/configs_f.html");
$temp = ob_get_contents();
ob_end_clean(); 