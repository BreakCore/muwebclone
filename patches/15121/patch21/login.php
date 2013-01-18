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
else die('not found 404!');
