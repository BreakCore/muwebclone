<?php session_start();
/**
 * mwc153
 * 28.02.2015
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
$content->add_dict("donate");
if(!isset($_SESSION["user"]))
    die();// только залогиненые

switch($_GET["act"])
{
    //выбор ик
    case 1:
        if($_GET["type"] == "1")
            $content->out("ikpay1.html");
        break;
    case 2:
        if($_GET["type"] == "1" && isset($_GET["sum"]))
        {
            $sum = (float)$_GET["sum"];
            require "configs/ikpay_cfg.php";
            $sum *= $ikpay["ik_rate"];
            $db->query("DELETE FROM MWC_ikpay WHERE col_memb_id = '{$_SESSION["user"]}' AND col_state='0'; INSERT INTO MWC_ikpay (col_memb_id,col_sum,col_state)VALUES('{$_SESSION["user"]}',$sum,'0')");
            $id =  $db->lastId("MWC_ikpay");
            $content->set("|s_id|",$ikpay["ik_shop_id"]);
            $content->set("|sum|",$sum);
            $content->set("|pid|",$id);
            $content->set("|servernum|",1);
            $content->out("ikpay_form.html");
        }
        break;
    default:
        echo "??";
        break;
}
