<?php
/**
 * mwc153
 * 07.03.2015
 * by epmak
**/

require "configs/scred_cfg.php";
$content->add_dict("cred");
if (isset($_REQUEST["ok_Ad"]))
{
    $needcr = (int)$_POST["cr_sum"];
    if ($needcr>know_kredits())
        $content->set("|error_msg|",$content->getVal("phrase_er1"));
    else
    {
        $db->query("UPDATE {$config["cr_table"]} SET {$config["cr_column"]}={$config["cr_column"]}-{$needcr} WHERE {$config["cr_acc"]}='{$_SESSION["user"]}'; UPDATE MEMB_INFO SET bankZ=bankZ+".($needcr*$scred["crate"])." WHERE memb___id='{$_SESSION["user"]}'");
        logs::WriteLogs("scred_", $_SESSION["user"]." обменял ".$needcr." на зены");
    }
}
$content->set("|cnum|",print_price($scred["crate"]));
$content->set("|cnums|",$scred["crate"]);
$content->out("sell_cred.html");
