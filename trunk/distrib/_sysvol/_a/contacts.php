<?php if (!defined('inpanel')) die("no access");
error_reporting(E_ALL);
if (isset($_GET["del"]))
{
 $num =(int)$_GET["del"];
 $contacts = @file("_dat/contact.dat");
 $c_handle = @fopen("_dat/contact.dat","w");
 unset($contacts[$num]);
 fputs($c_handle, implode("",$contacts));
 fclose ($c_handle);
}
if (isset($_REQUEST["add_c"]))
{
 $type= substr($_POST["c-type"],0,5);
 if ($type == "gmail")
  $contact = $_POST["c_text"];
 else
  $contact = $_POST["c_text"];
 if(!empty($contact))
  {
	$c_handle = fopen("_dat/contact.dat","a");
	fwrite ($c_handle,$type."::".$contact."\r\n");
	fclose ($c_handle);
  }
}

 $content->out("contacts_h.html");
 $contacts = @file("_dat/contact.dat");

if (!empty($contacts))
{
 $i=0;
 foreach ($contacts as $id=>$val)
 {
  list($typeZ,$contactZ) = explode("::",$val);
  $content->set('|typeZ|',$typeZ);
  if ($typeZ!="gmail")
   $content->set('|contactZ|',$contactZ);
  else
   $content->set('|contactZ|','<a href="mailto:'.$contactZ.'">'.$contactZ.'</a>');
  $content->set('|i|',$i);
  $content->out("contacts_c.html");
  $i++;
 }
 }

$content->out("contacts_f.html");
