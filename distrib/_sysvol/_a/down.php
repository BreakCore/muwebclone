<?php if (!defined('inpanel')) die("no access"); 
ob_start();
global $config;
global $content;

$button = "name='addlink' value=".$content->lng["dl_manager_bt1"]."";


$filedl = @file("_dat/dl.dat");
$linkZ = "_dat/dl.dat";

$content->out_content("_sysvol/_a/theme/donwload_h.html");

if ($_REQUEST["addlink"])//добавить ссыль на скачку
{
 $titleZ = htmlspecialchars($_POST["title"]);
  if(get_magic_quotes_gpc()==1)  $glin=stripslashes($glin);
 $glin = htmlspecialchars($_POST["nlink"]);
 if(substr($glin,0,4)=="http") $glin = substr($glin,7);
 $ggiz = htmlspecialchars($_POST["opis"]);
 $linki = $_POST["links"];

 if(strlen($titleZ)>1 && strlen($glin)>1 && strlen($ggiz)>1 && strlen($linki)>1)
 {
   $button = "name='addlink' value=".$content->lng["dl_manager_bt1"]."";		
   $dhandler = fopen($linkZ,"a+");
   fwrite($dhandler,$titleZ."||".$ggiz."||".$linki."||".$glin."||0||0 \n");
   fclose($dhandler);
 }
 header("Location:".$config["siteaddress"]."/control.php?page=down"); 
}

if($_GET["edit"]==1 && isset($_GET["pos"]) && !$_REQUEST["editlink"])
{	
 $position = checknum(substr($_GET["pos"],0,3));
 $temp = explode("||",$filedl[$position]);
 $titleZ=$temp[0];$ggiz=$temp[1];$linki=$temp[2];$glin=$temp[3];
 $button = "name='editlink' value=".$content->lng["dl_manager_bt2"]."";
}

if($_GET["edit"]==1 && $_REQUEST["editlink"])
{
 $button = "name='addlink' value=".$content->lng["dl_manager_bt1"]."";
 $position = checknum(substr($_GET["pos"],0,3));
 $titleZ = htmlspecialchars($_POST["title"]);
  if(get_magic_quotes_gpc()==1)  $glin=stripslashes($glin);
 $glin = htmlspecialchars($_POST["nlink"]);
 if(substr($glin,0,4)=="http") $glin = substr($glin,7);
 $ggiz = htmlspecialchars($_POST["opis"]);
 $linki = $_POST["links"];
 
 if(strlen($titleZ)>1 && strlen($glin)>1 && strlen($ggiz)>1 && strlen($linki)>1)
 {
  $position = (count($filedl)-1);
  if($position>=0)
     $filedl[$position]= $titleZ."||".$ggiz."||".$linki."||".$glin."||0||0 \n";

  $dhandler = fopen($linkZ,"w");
  fputs($dhandler, implode("",$filedl));
  fclose($dhandler);
  header("Location:".$config["siteaddress"]."/control.php?page=down"); 
 }
 else
  echo "некоторые поля остались незаполненными";
 
}
if($_GET["edit"]==0 && isset($_GET["pos"]))
{
 $position = checknum(substr($_GET["pos"],0,3));
 unset($filedl[$position]);
 $dhandler = fopen($linkZ,"w");
 fwrite($dhandler, implode("",$filedl));
 //fputs($dhandler, implode("",$filedl));
 //fputs($dhandler, "\r\n");
 fclose($dhandler);
 header("Location:".$config["siteaddress"]."/control.php?page=down"); 
}
	
if(count($filedl)> 0 or $filedl)
{
 $cs=0;
 foreach ($filedl as $f=>$n)
 {
  $tempa = explode("||",$n);
  if ($tempa[4]==1) $tempa[4]=$content->lng["dl_manager_msg"];
  else $tempa[4]="";
  if (substr(trim($tempa[3]),0,4)!="http") $tempa[3] = "http://".$tempa[3];
  $content->set('|value1|', $tempa[0]);			
  $content->set('|value2|', $tempa[1]);			
  $content->set('|value3|', $tempa[3]);			
  $content->set('|value4|', $tempa[2]);			
  $content->set('|value5|', $tempa[4]);			
  $content->set('|cs|', $cs);			
  $content->out_content("_sysvol/_a/theme/donwload_c.html");
  $cs++;
 }		
}

$content->set('|titleZ|', $titleZ);	
$content->set('|ggiz|', $ggiz);	
$content->set('|glin|', $glin);	
$content->set('|linki|', $linki);	
$content->set('|button|', $button);	
$content->out_content("_sysvol/_a/theme/donwload_f.html");
$temp = ob_get_contents();
ob_end_clean();