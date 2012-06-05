<?php  if (!defined('insite')) die("no access"); 
$contacts = @file("_dat/contact.dat");
$temp="";
if ($contacts)
{
 global $content;
 global $config;

 $temp=$content->out_content("theme/".$config["theme"]."/them/contacts_h.html",1);
 foreach ($contacts as $templ)
 {
  list($typeZ,$contactZ) = split("::",$templ);
  if ($typeZ=="skype") $contactZ="<a href ='skype:".$contactZ."'>".$contactZ."</a>";
  elseif ($typeZ=="gmail") $contactZ= "<a href='mailto:".$contactZ."'>".$contactZ."</a>";
  $content->set('|type|', $typeZ);
  $content->set('|contact|', $contactZ);		
  $temp.=$content->out_content("theme/".$config["theme"]."/them/contacts_c.html",1);
 }
 $temp.=$content->out_content("theme/".$config["theme"]."/them/contacts_f.html",1);
}
