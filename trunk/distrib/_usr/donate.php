<?php
/**
 * mwc153
 * 27.02.2015
 * by epmak
**/
require "configs/ikpay_cfg.php";
if(isset($_GET["status"]) && $_GET["status"] == "1")
    $content->set("|msgg|","<b style='color:green'>Платеж прошел успешно!</b>");
else if(isset($_GET["status"]) && $_GET["status"] == "2")
    $content->set("|msgg|","<b style='color:red'>Ошибка платежа</b>");

$content->set("|rate|",$ikpay["ik_rate"]);
$content->out("donate_main.html");
