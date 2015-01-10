<?php if (!defined('inpanel')) die("no access"); 

$infile = @file("_dat/baners.dat");

if (is_array($infile))
{
 $handle = fopen("_dat/baners.dat","w+");
 fclose($handle);
}

if(isset($_REQUEST["add"]) && isset($_POST["nlink"]))
{
 $handle = fopen("_dat/baners.dat","w");
 if(get_magic_quotes_gpc()==1)  fwrite($handle,stripslashes($_POST["nlink"]));
 else  fwrite($handle,$_POST["nlink"]);
 fclose($handle);
 logs::WriteLogs("Adm_",$_SESSION["sadm"]." обновил банеры");
 header("Location: ".$config["siteaddress"]."/control.php?page=baners");
 die();
}
else
{
 $tt = file_get_contents("_dat/baners.dat");
 $content->set("|text|",$tt);
}

$content->out("baners.html");
