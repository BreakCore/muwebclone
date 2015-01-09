<?php session_start();
header("Content-type: text/html; charset=windows-1251");
if (isset($_SESSION["adm"]) && $_SESSION["sadmin"] >0)
{
    define ('insite', 1);
    define ('inpanel', 1);
 require_once "_sysvol/logs.php";
 require_once "_sysvol/security.php";
 require_once "_sysvol/fsql.php";
 require_once ('opt.php');
 require_once "_sysvol/engine.php";
 require_once '_sysvol/webv.php';
 require_once '_sysvol/them.php';
 require_once "_sysvol/amod.php";

 $content = new content($config["siteaddress"],"admin",$_SESSION["mwclang"],1,"admin");
 $db = new connect ($config["ctype"], $config["db_host"], $config["db_name"], $config["db_user"], $config["db_upwd"]);

 if(isset($_GET["action"]))
  echo a_modul($_GET["action"],$db,$content);
 elseif($_GET["title"])
 {
  show_t($_GET["title"]);
 }
 $db->close();
}
else
 require "errors/er403.html";