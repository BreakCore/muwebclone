<?php if (!defined('inpanel')) die("no access"); 

/**
* создание комбобокса
* @fname = имя формы, в которой находится бокс
* @valarr массив со значениями значение => что показать на экране
* @name - имя элемента,@class=ксс класс,@type - делать ли сабмит
*/
function build_mt($fname,$valarr,$name,$class,$type=0,$selected="none")
{
 $buffer="<select name='".$name."' id='".$name."' class='".$class."'";
 if ($type==0)
    $buffer.="onChange='document.".$fname.".submit();'";
 $buffer.=">";
 foreach ($valarr as $id=>$show)
 {
  $buffer.="<option value='".$id."'";
  if($selected!="none" && $id==$selected)
     $buffer.=" selected ";
  $buffer.=">".$show."</option>";
 }
 $buffer.="</select>";
 return $buffer;
}


if (isset($_REQUEST["clear_m"]))
{
 $faqhandle = opendir("_dat/cach");
 $goz = "_dat/cach/";
 while (false !== ($file = readdir($faqhandle))) 
 { 
  if ($file != "." && $file != ".." && $file!=".htaccess") 
  {
   @unlink($goz.$file);
  } 			
 }
}
if (isset($_POST["typemanage"]))
{
 switch ($_POST["typemanage"])
 {
  case "pm":$_SESSION["typemanage"]="pm";break;
  case "upm":$_SESSION["typemanage"]="upm";break;
 }
}
else
{
 if (isset($_SESSION["typemanage"]) && strlen($_SESSION["typemanage"])<2)
    $_SESSION["typemanage"]="pm";
}



$content->set('|buildselect|', build_mt("typedit",array("pm" =>$content->getVal("mm_manager_type1"),"upm" =>$content->getVal("mm_manager_type2")),"typemanage","t-combx",0,$_SESSION["typemanage"]));
$content->out("mmodul_h.html");

$cfil=substr($_SESSION["typemanage"],0,3);

$pmfile = @file("_dat/".$cfil.".dat");
$btnval=" name='addmod' value='".$content->getVal("mm_manager_btn1")."'";$modname[0]="";$modname[1]="";

if(isset($_REQUEST["addmod"]))
{
    $modnamf = substr($_POST["mname"],0,10);
    $modopt = (int)$_POST["opttype"];
    $stringw=$modnamf."||".$modopt.chr(13).chr(10);
    $fh = fopen("_dat/".$cfil.".dat","a");
    fwrite($fh,$stringw);
    fclose($fh);
    header("location:".$config["siteaddress"]."/control.php?page=mmodul");
}
//удаляем модуль
if (isset($_GET["edit"]) && $_GET["edit"]==0)
{
  $p=(int)$_GET["pos"];
  $mcount= count($pmfile);
  $fonw = fopen("_dat/".$cfil.".dat","w");
  unset($pmfile[$p]);
  fputs($fonw, implode("",$pmfile));  
  fclose($fonw);
  $btnval=" name='addmod' value='".$content->getVal("mm_manager_btn1")."'";$modname[0]="";$modname[1]="";
  header("location:".$config["siteaddress"]."/control.php?page=mmodul");				
}
// если нажать "применить"
 if (isset($_REQUEST["okmod"]))
 {
   $cnt = count($pmfile);
   $fh = fopen("_dat/".$cfil.".dat","w");
   for ($i=0;$i<$cnt;$i++)
   {
    $list = explode("||",$pmfile[$i]);
    $pmfile[$i]= $list[0]."||".checknum($_POST["typmon".$i]).chr(13).chr(10);
    fwrite($fh,$pmfile[$i]);
   }
   fclose($fh);
   header("location: ".$config["siteaddress"]."/control.php?page=mmodul");
 }
$xo=0;
foreach ($pmfile as $n=>$s)
{
  $showmod = explode("||",$s);
  $content->set('|n|',$n);
  $content->set('|showmod|',$showmod[0]);
  $content->set('|xo|',$xo);
  $content->set('|show_st|', build_mt("opndp",array("0" =>$content->getVal("modul_status_off"),"1" =>$content->getVal("modul_status_on")),"typmon".$xo,"t-combx",1,$showmod[1]));
  $content->out("mmodul_c.html");
  $xo++;
}

$content->set('|mdname|',$modname[0]);
$content->out("mmodul_f.html");
