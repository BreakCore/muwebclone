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
require_once "_sysvol/itemShow.php";

$valid = new valid();
$db = new connect ($config["ctype"], $config["db_host"], $config["db_name"], $config["db_user"], $config["db_upwd"]);
$content = new content($config["siteaddress"],"site",substr($_SESSION["mwclang"],0,3),0,$config["theme"]);
require_once "_sysvol/rItem.php";
$iObj = new rItem();

if(isset($_GET["act"]))
{
    switch($_GET["act"])
    {
        //отобразить топ
        case 1:
            $q = $db->query("SELECT TOP 100 * FROM MWC_WEBSHOP");
            while ($r = $q->FetchRow())
            {
                //debug($r);
                $content->add_dict($r);
                $content->out("wshop_c.html");
            }
            break;
        //отображение инфы по вещи
        case 2:
            $itm = $iObj->read($_GET["itmh"]);
            itemShow::show($itm);
            break;
        default:
            die();
    }

}

