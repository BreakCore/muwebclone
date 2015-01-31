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
$max = $db->query("SELECT MAX(cLevel) as lvl, MAX({$webshop["resColumn"]}) as res FROM Character WHERE AccountID = '{$_SESSION["user"]}'")->FetchRow();

if($webshop["minlvl"] > $max["lvl"] && $webshop["minres"] > $max["res"])
{
    header("location: ".$content->getAdr()."/?p=not&error=21");
    die();
}
else
{
    require_once "_sysvol/rItem.php";
    $iObj = new rItem();
    $wh = Warehouse($db,$_SESSION["user"],$webshop["hexlength"]); //узнаем сундук
    if(!empty($wh))
    {
        $emptyItem = str_pad("", $webshop["hexlength"],"F",STR_PAD_LEFT);
        if(!isset($_REQUEST["selbtn"])) //список вещей для выставления на продажу
        {

            if(isset($_GET["itms"]))
                $si = (int)$_GET["itms"];

            $content->add_dict("webshop");
            $content->set("|msg|",$content->getVal("itmz").$si." ".$content->getVal("itmzb"));

            $itemList = array();

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


            $content->set("|ilist|",bSel($itemList,0,"toSelitm","class='selectbox'"));
            $content->out("webshop.html");
        }
        else if(isset($_POST["price"]) && !empty($_POST["price"]) && $_POST["toSelitm"]>=0) //нажата кнопка "продать"
        {
            $valuta = (int)valute($_POST["price"]); //если есть кк в цене, то заменяем на нули
            $itm = (int)$_POST["toSelitm"];
            if($valuta>0 && $itm>=0)
            {
                $item = substr($wh,($itm * $webshop["hexlength"]),$webshop["hexlength"]);
                if($item != $emptyItem) // если это не пустое пространство, а реально вешь
                {
                    $ii = $iObj->read($item); //читаем выбранную вещь
                    if(!isset($ii["level"]))
                        $ii["level"] = 'NULL';
                    if(!isset($ii["lifeLvl"]))
                        $ii["lifeLvl"] = 'NULL';
                    if(!isset($ii["exc"]))
                        $ii["exc"] = 'NULL';
                    else
                        $ii["exc"] = 1;

                    if(!isset($ii["pvp"]))
                        $ii["pvp"] = 'NULL';
                    else
                        $ii["pvp"] = 1;

                    if(!isset($ii["harmony"]))
                        $ii["harmony"] = 'NULL';
                    else
                        $ii["harmony"] = 1;

                    if(!isset($ii["anc"]))
                        $ii["anc"] = 'NULL';
                    else
                        $ii["anc"] = 1;

                    if($ii["sockHex"] !="FFFFFFFFFF")
                        $ii["sockHex"] = 1;
                    else
                        $ii["sockHex"] = 'NULL';

                    $allchar = array("dk","dw","elf","mg","dl","sum","rf");
                    $q="";
                    if(isset($ii["equipmenta"]))
                    {
                        foreach($allchar as $idd=>$v)
                        {
                            if(isset($ii["equipmenta"][$v."req"]) && $ii["equipmenta"][$v."req"] > 0)
                            {
                                if($idd>0)
                                    $q.=",";
                                $q.="1";
                            }
                            else
                            {
                                if($idd>0)
                                    $q.=",";
                                $q.="0";
                            }

                        }
                    }
                    else
                        $q = "0,0,0,0,0,0,0";

                    $query = "INSERT INTO [MWC_WEBSHOP] ([col_itemName],[col_Level],[col_hex],[col_optLevel],[col_excellent],[col_pvp],[col_harmony],[col_socket],[col_anc],[col_costType],[col_cost],[dk],[dw],[elf],[mg],[dl],[sum],[rf],col_user) VALUES('".htmlspecialchars($ii["name"])."',{$ii["level"]},'{$item}',{$ii["lifeLvl"]},{$ii["exc"]},{$ii["pvp"]},{$ii["harmony"]},{$ii["sockHex"]},{$ii["anc"]},1,$valuta,$q,'{$_SESSION["user"]}')";
                    $db->query($query);
                    $lid = $db->lastId("MWC_WEBSHOP");
                    putToWh($db,$_SESSION["user"],$wh,$emptyItem,$itm);

                    header("location: ".$content->getAdr()."/?up=webshop&itms=$lid");
                    die();
                }
                header("location: ".$content->getAdr()."/?up=webshop");
                die();
            }
            header("location: ".$content->getAdr()."/?up=webshop");
            die();
        }
        else
        {
            header("location: ".$content->getAdr()."/?up=webshop");
            die();
        }
    }
}




