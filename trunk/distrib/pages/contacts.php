<?php  if (!defined('insite')) die("no access"); 
$contacts = @file("_dat/contact.dat");

if (!empty($contacts))
{
     $content->out("contacts_h.html");
     foreach ($contacts as $templ)
     {
      list($typeZ,$contactZ) = explode("::",$templ);
      if ($typeZ=="skype")
       $contactZ="<a href ='skype:".$contactZ."'>".$contactZ."</a>";
      elseif ($typeZ=="gmail")
       $contactZ= "<a href='mailto:".$contactZ."'>".$contactZ."</a>";
      $content->set('|type|', $typeZ);
      $content->set('|contact|', $contactZ);
      $content->out("contacts_c.html");
     }
 $content->out("contacts_f.html");
}
