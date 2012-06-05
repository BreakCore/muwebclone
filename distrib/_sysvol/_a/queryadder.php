<?php
if (!defined('inpanel')) die("no access"); 
global $config;
global $db;
global $content;


ob_start();
if (!$_REQUEST["addQ"])
{
 $content->set("|value|"," ");
 $content->set("|qmsg|"," ");
}
else
{
 $query = $_POST["SQLq"];
 if(get_magic_quotes_gpc()==1)  $query=stripslashes($query);
 WriteLogs("Adm",$_SESSION["sadm"]." addquery: ".$query);
 $db->query($query);
 ob_start();
 $db->showmsg();
 $t = ob_get_contents();
 ob_end_clean();
 $content->set("|value|",$query);
 $content->set("|qmsg|",$t);
}
 
 $content->out_content("_sysvol/_a/theme/queryad.html");
$temp = ob_get_contents();
ob_end_clean();