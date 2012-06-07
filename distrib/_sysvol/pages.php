<?php if (!defined('insite')) die("no access"); 

function showst($var)
{
 if (trim($var)=="baners")
 {
  $temp = file_get_contents("_dat/baners.dat");
 }
 else if (trim($var)=="lang_menu")
 {
  global $content;
  global $config;  
 //change lang menu
 ob_start();
 $content->out_content("theme/".$config["theme"]."/them/langmenu_h.html");
 $ld=opendir("lang");
 while (false !== ($file = readdir($ld))) 
 { 
  if (is_dir("lang/".$file) && $file!= "." && $file != "..") 
  {
   $content->set('|value|', $file);
   $content->set('|caption|', $file);
   if ($_SESSION["mwclang"]==$file) $content->set('|onsel|', "selected");
   else $content->set('|onsel|', "");
   $content->out_content("theme/".$config["theme"]."/them/langmenu_c.html");
  }  
 }
 $content->out_content("theme/".$config["theme"]."/them/langmenu_f.html");
 $temp = ob_get_contents();
 write_catch("_dat/cach/lang".substr($_SESSION["mwclang"],0,3),$temp);
 ob_end_clean(); 
 $temp = file_get_contents("_dat/cach/lang".substr($_SESSION["mwclang"],0,3));
//end change menu
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
