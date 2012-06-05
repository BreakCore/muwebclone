<?php
$time = @filemtime("_dat/updates/mlist");
global $config;
global $content;
require "_sysvol/imbrowser.php";

$nupdate = @file("_dat/modules.dat");
$i=0;
foreach ($nupdate as $n=>$v)
{
$anupd[$i]=$v;
$i++;
}
if($i==0)$anupd[0]=-1;


if(!$time or time() - $time >3600) //update updates list once at day
{
 //$str = unicontent("http://www.p4f.ru/mwc/modules.mwc"); 
 $str = unicontent("http://muwebclone.googlecode.com/svn/trunk/modules/modules.mwc"); 
 $handle = fopen("_dat/updates/mlist","w");
 fwrite ($handle,$str);
 fclose ($handle);
}
$file = @file("_dat/updates/mlist"); 
$count = @count($file);

ob_start();

if($count>0)
{
 if (isset($_GET["uid"])) //если нажата кнопулька установить/удалить
 {
   $id = checknum($_GET["uid"]);
   
   foreach ($file as $ii=>$v)
   {
    $array = explode("|",$v);
	if ((int)$array[3]==$id) break;
   }
   
   if(!in_array((int)$_GET["uid"],$anupd)) // устанваливаем
   {
    eval(unicontent($array[2]));
   }
   else //удаляем
   {
    eval(unicontent($array[2]."u"));
   }
 }
 else 
 {
  echo "<table align='center' valign=top' border='0' width='90%'>";
  for($i=0;$i<$count;$i++)
  {
   $array = explode("|",$file[$i]);
   
   if (in_array((int)$array[3],$anupd))
    $insert="<form method='POST' action='".$config["siteaddress"]."/control.php?page=modules&uid=".(int)$array[3]."'><input type='submit' value='Uninstall' class='button'></form>";
   else
    $insert="<form method='POST' action='".$config["siteaddress"]."/control.php?page=modules&uid=".(int)$array[3]."'><input type='submit' value='Install' class='button'></form>";
   
   echo "<tr><td style='font-weight:bold;' colspan='2'>".$array[0]."</td></tr>
       <tr><td style='font-style:italic;'>".$array[1]."</td><td align='center'>".$insert."</td></tr>
	   <tr><td height='20' colspan='2'>&nbsp;</td></tr>";
  }
  echo "</table>";
 }
}
else echo "<div align='center'>No available updates.</div>";


if ($_REQUEST["uplbtn"])
{
if (substr($_FILES['userfile']['name'],(strlen($_FILES['userfile']['name'])-3),3)=="mwc" && $_FILES["userfile"]["error"]==0)
{
 $lname = "_dat/updates/updm.tmp";
 @rename ($_FILES["userfile"]["tmp_name"],$lname);
 $contentF = file($lname);
 unlink($lname);
 $code="";
 $templf="";
 $iscode=0; //bool identif 
 $istfile=0; //bool identif 
 $iserror=0;
 foreach($contentF as $i=>$d)
 {
   switch (trim($d))
   {
    case "[file]":
	 if ($istfile==0 && $iscode==0)$istfile=1;
     $d="";
	break;
	
	case "[/file]":
	 if ($istfile==1 && $iscode==0) $istfile=0;
     $d="";
	break;
	
	case "[code]":
	 if($istfile==0 && $iscode==0)$iscode=1; 
	 $d="";
	break;
	case "[/code]":
	 if ($istfile==0 && $iscode==1 && $code!="")
	 {
	   $iscode=0;
       eval($code);
	   unset($code,$templf);
	 }
	 else
	 {
	  $content->set('|error_msg|', "Syntax error in downloaded file!");
	  $iserror=1;
	 }
    break;
   }
  if ($istfile==1) $templf.=$d;
  if ($iscode==1) $code.=$d;
  if ($iserror==1) break;
 }
 if ($iserror==0)  $content->set('|error_msg|', "Success!");
 unset($contentF);
}


}
else $content->set('|error_msg|', "");

$content->out_content("_sysvol/_a/theme/install.html");

$temp = ob_get_contents();
ob_end_clean(); 
