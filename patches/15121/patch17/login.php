<?php session_start();
define ('insite', 1);

if ($_SESSION["install"]==true)
{
 if ($_GET["cheack"] && $_GET["host"] &&  $_GET["db"] && $_GET["usr"] &&  $_GET["pwd"])
 {
  require "_sysvol/fsql.php";
  switch($_GET["cheack"])
  {
   case 1: $ctype = "ODBC";break;
   case 2: $ctype = "SQL";break;
   default: die("<div style='color:red;font-weight:bold;'>no connection type!</div>");
  }
  $db = new Connect ($ctype, $_GET["host"], $_GET["db"],$_GET["usr"], $_GET["pwd"],"SQL Server",0); 
  echo $db->check_c();
  $_SESSION["ulogin"] = $_GET["usr"];
  $_SESSION["upwd"] = $_GET["pwd"];
  $_SESSION["udb"] = $_GET["db"];
  $_SESSION["uhost"] = $_GET["host"];
  $_SESSION["utype"] = $ctype;
 }
 else echo "<div style='color:red;font-weight:bold;'>no data to connect</div>";
}
else
{
require ("_sysvol/security.php");
require "_sysvol/fsql.php";
require ('opt.php');
require "_sysvol/engine.php";
require '_sysvol/webv.php';

		
 $db = new Connect ($config["ctype"], $config["db_host"], $config["db_name"], $config["db_user"], $config["db_upwd"],$config["odbc_driver"],$config["debug"]); 
#region проверка ников
 if(isset($_GET['acc']))
 {
  $account = validate(substr(trim($_GET['acc']),0,10));
  if(strlen($account>2))
   {
    $getUser_sql = $db->numrows($db->query("SELECT memb___id FROM MEMB_INFO WHERE memb___id='".$account."'"));
	if ($getUser_sql>0){echo "<img src='imgs/x.gif' alt='already exists'/>";}else{echo "<img src='imgs/y.png' alt='already exists'/> account name \"$account\" can be used";}
   }
   else echo "<img src='imgs/x.gif' alt='already exists'/>";
  
 }
#end
#region mail
 elseif(isset($_GET['mail']))
 {
  $email1 = checkwordm(substr($_GET['mail'],0,20));
  $lmail = strlen($email1);
  $getUser_sql = $db->numrows($db->query("SELECT mail_addr FROM MEMB_INFO WHERE mail_addr='".$email1."'"));
  if ($getUser_sql>0 && $lmail<21){echo "<img src='imgs/x.gif' alt='already exists'/>";}else{echo "<img src='imgs/y.png' alt='all ok'/>";}	
 }
#end
#region проверка рефералов
 elseif(isset($_GET['ref']))
 {
  $refferal = validate(substr($_GET['ref'],0,10));
  $lrefferal = strlen($refferal);
  if(($lrefferal>2 && $lrefferal<11))
  {	
   require "configs/referal_cfg.php";
   require "configs/top100_cfg.php";
   $row = $db->fetchrow($db->query("SELECT cLevel, ".$top100["t100res_colum"]." FROM Character WHERE Name ='".$refferal."' "));//$db->close();
   if(($row[0] >=$referal["minlvl"]) || ($row[1]>=1)) echo "<img src='imgs/y.png' alt='all ok'/>";
   else echo "<img src='imgs/x.gif' alt='no!'/>"; 
  }
  else echo "<img src='imgs/x.gif' alt='already exists'/>";		
 }
#end
 else die('not found 404!');
 
 $db->close();

	// WriteLogs("ajax_","Aккаунт ".$_SESSION["user"]);
}
