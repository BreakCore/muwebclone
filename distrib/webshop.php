<?php session_start();
/**
 * mwc153
 * 31.01.2015
 * by epmak
**/
define ('insite', 1);
define ('inpanel', 1);

require_once "_sysvol/logs.php";
require_once "_sysvol/security.php";
require_once "_sysvol/fsql.php";
require_once 'opt.php';
require_once "_sysvol/engine.php";
require_once '_sysvol/them.php';

$valid = new valid();
$db = new connect ($config["ctype"], $config["db_host"], $config["db_name"], $config["db_user"], $config["db_upwd"]);
$content = new content($config["siteaddress"],"site",substr($_SESSION["mwclang"],0,3),0,$config["theme"]);

if(isset($_GET["act"]))
{
    switch($_GET["act"])
    {
        //отобразить топ
        case 1:
            $q = $db->query("SELECT TOP 100 * FROM MWC_WEBSHOP");
            while ($r = $q->FetchRow())
            {
                debug($r);
            }
            break;
        default:
            die();
    }

}

