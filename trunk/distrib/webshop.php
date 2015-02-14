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
            $bank = bankZ_show($db,0,1);
            while ($r = $q->FetchRow())
            {
                //debug($r);
                $r["col_cost"] = print_price($r["col_cost"]);
                $content->add_dict($r);
                if(isset($_SESSION["user"]) && $r["col_user"] == $_SESSION["user"])
                {
                    $content->set("|options|","<div onclick='action_(\"item_{$r["col_shopID"]}\",{$r["col_shopID"]},1)'>Убрать вещь </div>");
                }
                else if (isset($_SESSION["user"]) && $bank>=$r["col_cost"])
                {
                    $content->set("|options|","<div onclick='action_(\"item_{$r["col_shopID"]}\",{$r["col_shopID"]},2)'>Купить вещь </div>");
                }
                else if (isset($_SESSION["adm"]))
                {
                    $content->set("|options|","<div onclick='action_(\"item_{$r["col_shopID"]}\",{$r["col_shopID"]},3)'>Убрать вещь </div>");
                }
                $content->out("wshop_c.html");
            }
            break;
        //отображение инфы по вещи
        case 2:
            $itm = $iObj->read($_GET["itmh"]);
            $out = itemShow::show($itm);

            $tm = "";
            foreach($out as $id => $val_)
            {
                $content->set("|customStyle|",$id);

                if($id == "exc")
                {
                    $tmp = "";
                    foreach($val_ as $excval){
                        $tmp.="<p>$excval</p>";
                    }
                    $val_ = $tmp;
                }

                $content->set("|whcontent|",$val_);
                $tm.=$content->out("webshopItem_c.html",1);
            }

            $content->set("|centerz|",$tm);
            $content->out("webshopItem.html");
            break;
        //дроп вещи
        case 3:
            require "configs/webshop_cfg.php";

            if($_GET["do"] == 1 && isset($_SESSION["user"]) && chck_online($db,$_SESSION["user"]) == 0)//дропнуть своб вещь
            {
                $itm = $db->query("SELECT col_hex FROM MWC_WEBSHOP WHERE col_user='{$_SESSION["user"]}' AND col_shopID = ".(int)$_GET["itm"])->FetchRow();
                $emptyItem = str_pad("", $webshop["hexlength"],"F",STR_PAD_LEFT);
                if(!empty($itm["col_hex"]) && $itm["col_hex"] != $emptyItem)
                {
                    $iObj = new rItem();
                    $wh = Warehouse($db,$_SESSION["user"],$webshop["hexlength"]); //узнаем сундук
                    if(!empty($wh))
                    {
                        $sItem = $iObj->read($itm["col_hex"]); //читаем вещь
                        $place = $iObj->search($wh,$sItem["x"],$sItem["y"]); //узнаем место
                        if($place>=0)
                        {
                            $wh = "0x".substr_replace($wh,$itm["col_hex"],($place*$webshop["hexlength"]),$webshop["hexlength"]);
                            $db->query("UPDATE warehouse SET Items = $wh WHERE AccountID = '{$_SESSION["user"]}'; DELETE FROM MWC_WEBSHOP WHERE col_shopID = ".(int)$_GET["itm"]);
                            logs::WriteLogs("WebShop","User {$_SESSION["user"]} drop {$itm["col_hex"]}");
                            echo "done";
                        }
                    }
                    else
                        echo "-0-";
                }
                else
                    echo "-0-";
            }
            else if($_GET["do"] == 2 && isset($_SESSION["user"]) && chck_online($db,$_SESSION["user"]))//купить вещь
            {
                $itm = $db->query("SELECT col_hex,col_user,col_cost FROM MWC_WEBSHOP WHERE col_shopID = ".(int)$_GET["itm"])->FetchRow();
                $emptyItem = str_pad("", $webshop["hexlength"],"F",STR_PAD_LEFT);
                $bank = bankZ_show($db,0,1);
                if(!empty($itm["col_hex"]) && $itm["col_hex"] != $emptyItem && $itm["col_cost"]<=$bank) //если денег хватает
                {
                    $iObj = new rItem();
                    $wh = Warehouse($db,$_SESSION["user"],$webshop["hexlength"]); //узнаем сундук
                    if(!empty($wh))
                    {
                        $sItem = $iObj->read($itm["col_hex"]); //читаем вещь
                        $place = $iObj->search($wh,$sItem["x"],$sItem["y"]); //узнаем место
                        if($place>=0)
                        {
                            $wh = "0x".substr_replace($wh,$itm["col_hex"],($place*$webshop["hexlength"]),$webshop["hexlength"]);
                            $db->query("UPDATE warehouse SET Items = $wh WHERE AccountID = '{$_SESSION["user"]}';UPDATE memb_info SET bankZ = bankZ - {$itm["col_cost"]} WHERE memb___id = '{$_SESSION["user"]}'; UPDATE memb_info SET bankZ = bankZ + {$itm["col_cost"]} WHERE memb___id = '{$itm["col_user"]}'; DELETE FROM MWC_WEBSHOP WHERE col_shopID = ".(int)$_GET["itm"]);
                            logs::WriteLogs("WebShop","User {$_SESSION["user"]} buy {$itm["col_hex"]}");
                            echo "done";
                        }
                    }
                    else
                        echo "-0-";
                }
                else
                    echo "-0-";
            }
            else if($_GET["do"] == 3 && isset($_SESSION["adm"]))//админ дропает вещь
            {
                $db->query("UPDATE  MWC_WEBSHOP SET col_isMy = 1 WHERE col_shopID = ".(int)$_GET["itm"]);
                echo "done";
            }
            //isset($_SESSION["adm"])
            else
                echo "-0-";
            break;
        default:
            die();
    }

}

