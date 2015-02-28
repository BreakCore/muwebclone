<?php
/**
 * mwc153
 * 31.01.2015
 * by epmak
 * веб магаз
**/
if(!isset($_SESSION["user"]))
    $content->set("|stl|","display:none");
else
    $content->set("|stl|","");

$content->out("wshop.html");