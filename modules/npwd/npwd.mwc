<?php if (!defined('insite')) die("no access"); 
global $db;
global $content;
global $config;
ob_start();

$content->out_content("theme/".$config["theme"]."/them/npwd.html");
$temp = ob_get_contents();

if($_REQUEST["cpwd"] && trim($_POST["opwd"])==$_SESSION["pwd"])
{
  $n_pwd = validate(substr($_POST["npwd"],0,10));
  if ($config["md5use"]=="on") $suserp = "[dbo].[fn_md5]('".$n_pwd."','".$_SESSION["user"]."')";
  else $suserp = "'". $n_pwd ."'";	
 $db->query("UPDATE MEMB_INFO SET memb__pwd=".$suserp.", recpwd ='".$n_pwd ."' WHERE memb___id='".$_SESSION["user"]."'");
 $_SESSION["pwd"]=$n_pwd;
 WriteLogs ("Chwpd_",$_SESSION["user"]." поменял пароль с ".trim($_POST["opwd"])." на ".$_SESSION["pwd"]);  
 echo "<script>alert('Password changed!')</script>";
}


ob_end_clean();
