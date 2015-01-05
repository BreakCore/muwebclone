<?php session_start();
define ('insite', 1);
error_reporting(1);

if (isset($_SESSION["install"]))
{
 if (isset($_GET["cheack"]) && isset($_GET["host"]) &&  isset($_GET["db"]) && isset($_GET["usr"]) &&  isset($_GET["pwd"]))
 {
  require_once "_sysvol/fsql.php";

  switch($_GET["cheack"])
  {
   case 1: $ctype = "ODBC";break;
   case 2: $ctype = "SQL";break;
   default: die("<div style='color:red;font-weight:bold;'>no connection type!</div>");
  }

  if (get_magic_quotes_gpc()) {
   $_GET["host"] = stripcslashes($_GET["host"]);
  }

  try
  {
   $db = new connect ($ctype, $_GET["host"], $_GET["db"],$_GET["usr"], $_GET["pwd"]);


   $_SESSION["ulogin"] = $_GET["usr"];
   $_SESSION["upwd"] = $_GET["pwd"];
   $_SESSION["udb"] = $_GET["db"];
   $_SESSION["uhost"] = $_GET["host"];
   $_SESSION["utype"] = $ctype;
   echo "<div style='font-weight:bold;color:green;'>Connection Work!</div>";
  }
  catch(Exception $e)
  {
   echo "<div style='color:red;font-weight:bold;'>".$e->getMessage()."</div>";
  }
 }
 else
  echo "<div style='color:red;font-weight:bold;'>no data to connect</div>";
}
else die('not found 404!');
