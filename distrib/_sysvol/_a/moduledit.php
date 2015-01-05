<?php
if (!defined('inpanel')) die("no access"); 
global $config;
global $db;
global $content;


ob_start();
$content->set("|value|","");
$content->set("|qmsg|","");
$directories= array("pages","_usr","lang");
$content->set("|file|","");
$content->set("|path|","");

if(isset($_GET["f"]) && isset($_GET["path"]) && (in_array($_GET["path"],$directories)|| ($_GET["path"]=="_dat" && $_GET["f"]=="market")))
 {
  
  if ($_GET["path"]=="lang")  $path =$_GET["path"]."/".$_SESSION["mwclang"];
  else  $path =$_GET["path"];

 
  if (file_exists($path."/".$_GET["f"].".php")||(substr($_GET["f"],3)=="rules"))
  {
   if(substr($_GET["f"],3)=="rules") $content->set("|value|",file_get_contents($path."/".$_GET["f"].".txt"));
   else $content->set("|value|",file_get_contents($path."/".$_GET["f"].".php"));
   $_POST["dirs"]=$_GET["path"];
   $content->set("|file|",$_GET["f"]);
   $content->set("|path|",$_GET["path"]);
  }
  elseif($_GET["f"]=="market" && $path=="_dat")
  {
   $content->set("|value|",file_get_contents($path."/".$_GET["f"].".db"));
	//$_POST["dirs"]=$_GET["path"];
	$content->set("|file|",$_GET["f"]);
	$content->set("|path|",$_GET["path"]);
  }
  else 
  {
   $content->set("|value|","Can't open file!");
  }
 }

 if($_REQUEST["editmodul"] && strlen($_POST["SQLq"])>3 )
 {
  if ($_GET["path"]=="lang") $path=$_GET["path"]."/".$_SESSION["mwclang"];
  else $path=$_GET["path"];
  
  if (file_exists($path."/".$_GET["f"].".php") && in_array($_GET["path"],$directories) || ($_GET["path"]=="_dat" && $_GET["f"]="market")||(substr($_GET["f"],3)=="rules"))
  {
    if($_GET["path"]=="_dat")$handle = fopen($path."/".$_GET["f"].".db","w+");
	else if (substr($_GET["f"],3)=="rules") $handle = fopen($path."/".$_GET["f"].".txt","w+");
    else $handle = fopen($path."/".$_GET["f"].".php","w+");
	 $query = $_POST["SQLq"];
	 if(get_magic_quotes_gpc()==1)  $query=stripslashes($query);
	 fwrite($handle,$query);
	fclose($handle);
	$content->set("|qmsg|","File edited.");
	WriteLogs("Adm_",$_SESSION["sadmin"]." »зменил модуль".$path."/".$_GET["f"].".php");
	if($_GET["path"]=="_dat") $content->set("|value|",file_get_contents($path."/".$_GET["f"].".db"));
	elseif(substr($_GET["f"],3)=="rules") $content->set("|value|",file_get_contents($path."/".$_GET["f"].".txt"));
	else $content->set("|value|",file_get_contents($path."/".$_GET["f"].".php"));
  }
 }

foreach($directories as $v)
{
 if ($_POST["dirs"]==$v) $seld = "selected";
  else $seld="";
  $shoZ.= "<option value='".$v."' ".$seld.">".$v."</option>";
}

if (isset($_POST["dirs"]) && in_array($_POST["dirs"],$directories))
{
if ($_POST["dirs"]!="lang") $odir= opendir($_POST["dirs"]);
else $odir= opendir($_POST["dirs"]."/".$_SESSION["mwclang"]);

 $fname="";
 $ind =0;

 require "lang/".$_SESSION["mwclang"]."/".$_SESSION["mwclang"]."_titles.php";;
 while ($file = readdir($odir))
 {
  if ($file != "." && $file != ".." && $file !=".htaccess") 
  {
  if ($_POST["dirs"]!="lang") $fname.="&nbsp; <a href='".$config["siteaddress"]."/control.php?page=moduledit&path=".$_POST["dirs"]."&f=".substr($file,0,-4)."'>".$lang["title_".substr($file,0,-4)]."</a>";
  else  $fname.="&nbsp; <a href='".$config["siteaddress"]."/control.php?page=moduledit&path=".$_POST["dirs"]."&f=".substr($file,0,-4)."'>".$file."</a>";
   if ($ind%5==0 && $ind>4) $fname.="<br>";
   $ind++;
  }
 }
}
$content->set("|dnames|",$shoZ);
$content->set("|fnames|",$fname);

$content->out_content("_sysvol/_a/theme/modulead.html");
$temp = ob_get_contents();
ob_end_clean();