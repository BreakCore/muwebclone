<?php
/**
 * Created by epmak
 * Date: 22.01.2015
 * Time: 13:59
 * MuWebClone
 */
if (isset($_GET["item"]))
{
    require_once "_sysvol/rItem.php";
    $Ireader = new rItem();
    print "<pre>";
    print_r($Ireader->read(substr(trim($_GET["item"]),0,64)));
    print "</pre>";
}