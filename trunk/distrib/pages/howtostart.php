<?php if (!defined('insite')) die("no access"); 
global $config;
global $content;
require "lang/".$_SESSION["mwclang"]."/".$_SESSION["mwclang"]."_errors.php";
ob_start();
$temp=$content->out_content("theme/".$config["theme"]."/them/how2stat_h.html");
$faqhandle = opendir("./faq"); 
require "lang/".$_SESSION["mwclang"]."/".$_SESSION["mwclang"]."_faq.php";
$i=0;
while (false !== ($file = readdir($faqhandle))) 
{ 
 if ($file != "." && $file != ".." && substr($file,4,3)!="php" && $file!=".htaccess") 
 {
  $content->set('|file|', $file);
  if ($faql[$file]!="")
  {
  if ($i==0) $content->set('|title_file|', $faql[$file]);
  elseif ($i%4==0 && $i>3) $content->set('|title_file|', "|&nbsp;".$faql[$file]);
  else $content->set('|title_file|', "|&nbsp;".$faql[$file]);
  $content->out_content("theme/".$config["theme"]."/them/how2stat_m.html");
  }
  $i++;
 } 			
}		
closedir($faqhandle);
$content->out_content("theme/".$config["theme"]."/them/how2stat_c.html");
if ($_GET["faq"])
{
 $pagefile = preg_replace("/[^a-zA-Z0-9_-]/i", "", substr($_GET["faq"],0,11)); 
 if(file_exists("faq/".$pagefile)) echo bbcode(file_get_contents("faq/".$pagefile));
 else echo "<div align='center' valign='center' class='warnms'>".$lang["error_no404"]."</div>";
}
else echo bbcode(file_get_contents("faq/aboutgame"));
$content->out_content("theme/".$config["theme"]."/them/how2stat_f.html");
$temp = ob_get_contents();
ob_end_clean();