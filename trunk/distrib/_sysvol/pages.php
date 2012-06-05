<?php if (!defined('insite')) die("no access"); 

function showst($var)
{
 if (trim($var)=="baners")
 {
  $temp = file_get_contents("_dat/baners.dat");
 }
 else
 {
  if (file_exists("_sysvol/".$var.".php")) require "_sysvol/".$var.".php";
  else  $temp = "no module ".htmlspecialchars($var);
 }
 return $temp;
}

function showpt($var)
{
  if (file_exists("pages/".$var.".php")) require "pages/".$var.".php";
  else $temp = "no module ".htmlspecialchars($var);
 
 return $temp;
}
