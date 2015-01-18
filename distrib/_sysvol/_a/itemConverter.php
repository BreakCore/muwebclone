<?php
/**
 * mwc153
 * 18.01.2015
 * by epmak
 * импорт вещей в базу
**/

$content->out("itemConverter.html");
if(isset($_REQUEST["addbtn"]))
{
    require "_sysvol/iWork.php";



    $o = new readitem($_FILES["itemfile"]["tmp_name"]);
    $iter = new ArrayIterator($o->getItems());

    foreach($iter as $id=>$value)
    {
        $h = fopen("_sysvol/itemBase/itemGroup".$id,"w");
        foreach ($value as $cid=>$cval) {
            fwrite($h,implode("|",$cval)."\r\n");
        }
    }

    if(isset($h))
        fclose($h);

}
