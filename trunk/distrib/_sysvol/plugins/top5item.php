<?php
/**
 * mwc153
 * 07.03.2015
 * by epmak
**/
$nowitime = time();
require "configs/top5item_cfg.php";




if(time() - load_cache("_dat/cach/{$_SESSION["mwclang"]}_top_5items",true) > $top5items["cach"])
{
    $Ireader = new rItem();
    ob_start();

    $content->out("top5_h.html");
    $item_hex_q = $db->query("SELECT  top 3 col_shopID,col_hex FROM MWC_WEBSHOP WHERE  was_drop !='1' ORDER BY col_shopID DESC");
    while ($res = $item_hex_q->FetchRow())
    {
        $itm = $Ireader->read($res["col_hex"]);
        $out = itemShow::show($itm);

        $content->set("|hext|",$res["col_hex"]);
        $content->set("|itm5|",$out["image"]); // берем только изображение
        $content->out("top5_c.html");
    }

    $content->out("top5_f.html");
    $temp = ob_get_contents();
    write_catch("_dat/cach/top_5items",$temp);
    ob_clean();
}
else
    $temp = load_cache("_dat/cach/top_5items");