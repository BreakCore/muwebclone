<?php if (!defined('inpanel')) die("no access"); 
global $config;
global $content; 
global $config;
ob_start();

$infile = @file("_dat/baners.dat");

if ($infile[0]=="" or empty($infile[0]))
{
 $handle = fopen("_dat/baners.dat","w+");
 fclose($handle);
}

if($_REQUEST["add"] && isset($_POST["nlink"]))
{
 $handle = fopen("_dat/baners.dat","w");
 if(get_magic_quotes_gpc()==1)  fwrite($handle,stripslashes($_POST["nlink"]));
 else  fwrite($handle,$_POST["nlink"]);
 fclose($handle);
 WriteLogs("Adm_",$_SESSION["sadm"]." обновил банеры");
 header("Location: ".$config["siteaddress"]."/control.php?page=baners");
 die();
}
else
{
 $tt =file_get_contents("_dat/baners.dat");
 $content->set("|text|",$tt);
}

$content->out_content("_sysvol/_a/theme/baners.html");
$temp = ob_get_contents();
ob_end_clean(); 