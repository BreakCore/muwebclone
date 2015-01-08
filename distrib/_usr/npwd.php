<?php if (!defined('insite')) die("no access"); 


$content->out("npwd.html");


if(isset($_REQUEST["cpwd"]) && $_POST["opwd"] == $_SESSION["pwd"])
{
  $n_pwd = substr($_POST["npwd"],0,10);
  if ($config["md5use"]=="on")
      $suserp = "[dbo].[fn_md5]('".$n_pwd."','".$_SESSION["user"]."')";
  else
      $suserp = "'". $n_pwd ."'";
 $db->query("UPDATE MEMB_INFO SET memb__pwd=$suserp, recpwd ='$n_pwd ' WHERE memb___id='{$_SESSION["user"]}'");
 $_SESSION["pwd"] = $n_pwd;
 logs::WriteLogs ("Chwpd_",$_SESSION["user"]." поменял пароль с ".$_POST["opwd"]." на ".$_SESSION["pwd"]);
 echo "<script>alert('Password changed!')</script>";
}

