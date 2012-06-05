<?php if (!defined('inpanel')) die("no access"); 
global $config;
global $content; 
ob_start();

if (isset($_GET["del"]))
{
 $num =(int)$_GET["del"];
 $contacts = @file("_dat/contact.dat");
 $c_handle = @fopen("_dat/contact.dat","w");
 unset($contacts[$num]);
 fputs($c_handle, implode("",$contacts));
	/*for ($i=0;$i<count($contacts);$i++)	
	{
		if($i!=$num) fwrite ($c_handle,$contacts[$i]);	
	}*/
 fclose ($c_handle);
}
if ($_REQUEST["add_c"])
{
 $type= substr($_POST["c-type"],0,5);
 if ($type == "gmail") $contact = checkwordm(substr($_POST["c_text"],0,20));
 else $contact = validate(substr($_POST["c_text"],0,20));
 if(strlen($contact)>2)
  {
	$c_handle = fopen("_dat/contact.dat","a");
	fwrite ($c_handle,$type."::".$contact."\r\n");
	fclose ($c_handle);
  }
}

 $content->out_content("_sysvol/_a/theme/contacts_h.html");
 $contacts = @file("_dat/contact.dat");
 if ($contacts)
 {
  $i=0;
  foreach ($contacts as $temp)
  {
   list($typeZ,$contactZ) = split("::",$temp);
   $content->set('|typeZ|',$typeZ);
   if ($typeZ!="gmail")
      $content->set('|contactZ|',$contactZ);
   else
      $content->set('|contactZ|','<a href="mailto:'.$contactZ.'">'.$contactZ.'</a>');
   $content->set('|i|',$i);
   $content->out_content("_sysvol/_a/theme/contacts_c.html");
   $i++;
  }
 }
$content->out_content("_sysvol/_a/theme/contacts_f.html");
$temp = ob_get_contents();
ob_end_clean(); 