<?php
/**
 * mwc153
 * 29.01.2015
 * by epmak
 * веб - магазин выставить на продажу
**/

if(chck_online($db,$_SESSION["user"])) //если онлайн, то отправляем на ошибку
{
    header("location: ".$content->getAdr()."/?p=not&error=20");
    die();
}

require "configs/webshop_cfg.php";
require_once "_sysvol/rItem.php";
$iObj = new rItem();

$wh = Warehouse($db,$_SESSION["user"],$webshop["hexlength"]); //узнаем сундук
if(!empty($wh))
{
    $itemList = array();
    $emptyItem = str_pad("", $webshop["hexlength"],"F",STR_PAD_LEFT);
    for($i=0;$i<120;$i++)
    {
        $curHex = substr($wh,$i*$webshop["hexlength"],$webshop["hexlength"]);
        if($curHex == $emptyItem)
            continue;
        $itm = $iObj->read($curHex);
        if(isset($itm["exc"]))
            $itm["name"] = "Excellent ".$itm["name"];
        if(isset($itm["level"]) && $itm["level"]>0)
            $itm["name"].=" +".$itm["level"];
        $itemList[$i] = $itm["name"];
    }
    debug($itemList);
}



