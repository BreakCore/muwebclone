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
require_once "configs/webshop_cfg.php";
$iObj = new rItem();

if(isset($_GET["act"]))
{
    switch($_GET["act"])
    {
        //отобразить топ
        case 1:
            $pNew = 1;
            $filtr = "";
            $ordb = "";

            $lvld = (isset($_GET["lvlfr"])) ? (int)$_GET["lvlfr"] : 0;
            $lvlu = (isset($_GET["lvlto"])) ? (int)$_GET["lvlto"] : 0;
            if($lvld<=$lvlu && $lvlu >0)
            {
                $filtr = " col_Level BETWEEN $lvld and $lvlu";
            }

            if(isset($_GET["iname"]) && $_GET["iname"]!="")
            {
                if(strlen($filtr)>0)
                    $filtr.=" AND ";

                $filtr.="  col_itemName like '%".substr($_GET["iname"],0,10)."%'";
            }

            if(isset($_GET["cls"]) && (int)$_GET["cls"] > 0)
            {
                $class = (int)$_GET["cls"];

                if(strlen($filtr)>0)
                    $filtr.=" AND ";

                switch($class)
                {
                    case 1 : $filtr.= "dw = 1"; break;
                    case 2 : $filtr.= "dk = 1"; break;
                    case 3 : $filtr.= "elf = 1"; break;
                    case 4 : $filtr.= "mg = 1"; break;
                    case 5 : $filtr.= "dl = 1"; break;
                    case 6 : $filtr.= "sum = 1"; break;
                    case 7 : $filtr.= "rf = 1"; break;
                }
            }

            if(isset($_GET["myitm"]) && isset($_SESSION["user"]))
            {
                if(strlen($filtr)>0)
                    $filtr.=" AND ";
                $filtr.=" col_isMy='1'";
            }
            else
            {
                if(strlen($filtr)>0)
                    $filtr.=" AND ";
                $filtr.=" was_drop != '1'";
            }

            if(isset($_GET["ispvp"]) && $_GET["ispvp"] == "1")
            {
                if(strlen($filtr)>0)
                    $filtr.=" AND ";
                $filtr.= " col_pvp = 1";
            }

            if(isset($_GET["isharmony"]) && $_GET["isharmony"] == "1")
            {
                if(strlen($filtr)>0)
                    $filtr.=" AND ";
                $filtr.= " col_harmony = 1";
            }

            if(isset($_GET["isharmony"]) && $_GET["isharmony"] == "1")
            {
                if(strlen($filtr)>0)
                    $filtr.=" AND ";
                $filtr.= " col_harmony = 1";
            }

            if(isset($_GET["isexc"]) && $_GET["isexc"] == "1")
            {
                if(strlen($filtr)>0)
                    $filtr.=" AND ";
                $filtr.= " col_excellent >0";
            }

            if(isset($_GET["issock"]) && $_GET["issock"] == "1")
            {
                if(strlen($filtr)>0)
                    $filtr.=" AND ";
                $filtr.= " col_socket >0";
            }

            if(isset($_GET["isopt"]) && $_GET["isopt"] == "1")
            {
                if(strlen($filtr)>0)
                    $filtr.=" AND ";
                $filtr.= " col_optLevel > 0";
            }

            if(isset($_GET["isanc"]) && $_GET["isanc"] == "1")
            {
                if(strlen($filtr)>0)
                    $filtr.=" AND ";
                $filtr.= " col_anc > 0";
            }

            if(isset($_GET["page_"]))
            {
                $pNew = (int)$_GET["page_"];
                if($pNew<1)
                    $pNew = 1;
            }
            if($filtr != "")
                $filtr = " WHERE $filtr";
                $count = $db->query("SELECT COUNT(*) as cnt FROM MWC_WEBSHOP $filtr")->FetchRow();

            $arr = paginate($count["cnt"],$webshop["itmPerPage"],$pNew);
            $q = $db->query("WITH CTEwResults AS(
            SELECT *,
            ROW_NUMBER() OVER (ORDER BY col_ShopID DESC) AS RowNum
             FROM MWC_WEBSHOP $filtr )
            SELECT * FROM CTEwResults WHERE RowNum BETWEEN {$arr["min"]} AND {$arr["max"]} ORDER BY col_IsMy DESC $ordb;");

            $bank = bankZ_show($db,0,1);
            //region "пагинатор"
            if ($arr["count"]>1)
            {
                $tmp = "";

                $pages = 2;
                $start = $pages+$pNew-1;

                $end = $arr["count"] - $pages;

                for ($i=1;$i<=$arr["count"];$i++)
                {

                    if($i == 2 && $pNew != $i && $pNew>1) //начало
                    {
                        $content->set("|p_num|","noselec_num");
                        $content->set("|nnum|","<b onclick='page=".($pNew-1).";filtr();return false;'><</b>");
                    }
                    else if ($pNew == $i) //текущая страница
                    {
                        $content->set("|p_num|","active");
                        $content->set("|nnum|",$i);
                    }
                    else if($i == 1) //начало
                    {
                        $content->set("|p_num|","noselec_num");
                        $content->set("|nnum|","<b  onclick='page=$i;filtr();return false;'><<</b>");
                    }
                    else if($i == $arr["count"]) //конец
                    {
                        $content->set("|p_num|","noselec_num");
                        $content->set("|nnum|","<b  onclick='page=$i;filtr();return false;'>>></b>");

                    }
                    else if (($i<=$start  &&  $i> $pNew) ||
                        ($i > $end - $pages ) ||
                        $i<=$pages || $i>= $end - $pages)
                    {
                        $content->set("|p_num|","noselec_num");
                        $content->set("|nnum|","<b onclick='page=$i;filtr();return false;'>$i</b>");

                    }
                    else
                    {
                        $content->set("|p_num|","");
                        $content->set("|nnum|","");

                    }

                    $tmp.= $content->out("p_numbers.html",1);
                }
                $content->set("|nubs|",$tmp);
                echo "<div style=\"margin:0;padding:0;display:inline;float:left;margin-top:5px; margin-bottom: 5px; margin-left: 4px; width:100%;\"  >".$content->out("p_main.html",1)."</div>";
            }
            //endregion

            while ($r = $q->FetchRow())
            {
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

            //region "пагинатор"
            if ($arr["count"]>1)
            {
                $content->set("|nubs|",$tmp);
                echo "<div style=\"margin:0;padding:0;display:inline;float:left;margin-top:5px; margin-bottom: 5px; margin-left: 4px; width:100%;\"  >".$content->out("p_main.html",1)."</div>";
            }
            //endregion
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
                $db->query("UPDATE  MWC_WEBSHOP SET was_drop = 1 WHERE col_shopID = ".(int)$_GET["itm"]);
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

