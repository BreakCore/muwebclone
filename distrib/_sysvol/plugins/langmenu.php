<?php
/**
 * mwc153
 * 21.02.2015
 * by epmak
**/

$l = scandir("lang");

unset($l[0],$l[1],$l[2]);
$lng = array();
$selected = -1;
foreach ($l as $i=>$v)
{
    if(strlen($v) == 3)
    {
        $lng[$i] = $v;
    }

    if($_SESSION["mwclang"] == $v)
        $selected = $i;
}

if(isset($_POST["mwcl"]))
{
    $choose = (int)$_POST["mwcl"];
    if(isset($lng[$choose]))
    {
        $_SESSION["mwclang"] = $lng[$choose];
        header("location: ".$content->getAdr());
        die();
    }
}

$content->set("|langList|",bSel($lng,$selected,"mwcl","class='selectbox'","Onchange=\"document.getElementById('lselsct').submit();\""));
$content->out("langmenu.html");
