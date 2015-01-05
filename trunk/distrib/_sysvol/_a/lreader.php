<?php 
if (!defined('inpanel')) die("no access");
global $config;
ob_start();
global $content;

if ($_POST["chosen"]=="a") unset($_POST["chosen"]);

if ($_REQUEST["delalog"])
{
 $Lhandle = opendir("logZ");
 while ($file = readdir($Lhandle))
 {
  if ($file != "." && $file != ".." && $file !=".htaccess" && substr($file,0,3)!="Adm") unlink("logZ/".$file);
 }
 WriteLogs("Adm_",$_SESSION["sadmin"]." удалил все логи");

}
if($_REQUEST["dellog"] && $_REQUEST["hvd"]) //удалить лог
{
 if(substr($_REQUEST["hvd"],0,3)=="Adm")
 { 
  WriteLogs("Adm_",$_SESSION["sadmin"]." попытвлся удалить ".htmlspecialchars($_REQUEST["hvd"])." эти логи нельзя удалить через сайт!");
 }
 else
 {
  @unlink("logZ/".$_REQUEST["hvd"]);
  WriteLogs("Adm_",$_SESSION["sadmin"]." удалил ".htmlspecialchars($_REQUEST["hvd"]));
 }
  header("Location: ".$config["siteaddress"]."/control.php?page=lreader");
}
if($_REQUEST["downl"] && $_REQUEST["hvd"]) //загрузить лог
{
 if (file_exists("logZ/".$_REQUEST["hvd"]))
 {
     header("Cache-Control: public");
     header("Content-Description: File Transfer");
     header("Content-Disposition: attachment; filename=".$_REQUEST["hvd"]);
     header("Content-Type: text/plain");
     header("Content-Transfer-Encoding: binary");
     readfile("logZ/".$_REQUEST["hvd"]);
 }
}
$shoZ = "<select name=\"chosen\" class=\"texbx\" style='width:120px;height:20px;' onchange=\"document.onsee.submit()\"><option value = \"a\" >. . . </option>";
$Lhandle = opendir("logZ");


while ($file = readdir($Lhandle))
{
 if ($file != "." && $file != ".." && $file !=".htaccess" ) $opnfilz[] = $file;
}

if ($_GET["hvd"])
{
 $is = array_search ($_GET["hvd"],$opnfilz);
 if ($is && $id>=0)
  $_POST["chosen"]=$is;
}
$id=0;
if (!isset($_POST["chosen"]))$select =-1; else $select = $_POST["chosen"];
foreach($opnfilz as $k)
{

  if ($select==$id) $seld = "selected";
  else $seld="";
  $shoZ.= "<option value='".$id."' ".$seld.">".$k."</option>";
  $id++;
}

$shoZ.= "</select>";
$content->set('|lreader_list|',  $shoZ);
$content->out_content("_sysvol/_a/theme/logreader_h.html");
$content->set('|button|', '');
$content->set('|dbutton|', '');


if (isset($_POST["chosen"])) 
{
 if (file_exists("logZ/".$opnfilz[$_POST["chosen"]]) && $opnfilz[$_POST["chosen"]]!="")
 {
  $tar = @file("logZ/".$opnfilz[$_POST["chosen"]]);
  $n=1;
  $temp="";
  foreach ($tar as $i=>$v)
  {
   if (strlen(trim(slogZ($v)))>0)
   {
    if ($n==1) $temp.= "<tr style='background-color:#DDFFEA'><td>";
    else $temp.="<br>";
 
    $temp.= slogZ($v);
    if ($n==5)
    { 
     $temp.= "</td></tr>";
     $n=0;
    }
    $n++;
   }
  }
  $content->set('|content|',  $temp);
  $content->set('|button|', '<input type="submit" class="button" value="Delete" name="dellog">');
  $content->set('|dbutton|', '<input type="submit" class="button" value="Download" name="downl">');
  $content->set('|hidlog|', $opnfilz[$_POST["chosen"]]);
  $content->out_content("_sysvol/_a/theme/logreader_c.html");
 }
 else
 {
  $content->set('|content|',  "");
  $content->set('|dbutton|', '');
  $content->set('|button|', '');
  $content->set('|hidlog|', "");
  $content->out_content("_sysvol/_a/theme/logreader_c.html");
 }
}
else
{
 $content->set('|content|',  "");
 $content->set('|dbutton|', '');
 $content->set('|hidlog|', "");
 $content->set('|button|', '<input type="submit" class="button" value="Delete All" name="delalog">');
 $content->out_content("_sysvol/_a/theme/logreader_c.html");
 }
function slogZ($text) 
{
 $code = array
 (
 "/\[(.*)\](.*?)\[(.*)\][\r]*[\n]*/" => "<span style='font-weight:bold;font-size:14px;color:black;'>$1</span> - (<span style='font-size:14px;color:red;'>$3</span>)",
 "/Message:(.*?)[\r]*[\n]*/" =>"<span style='font-weight:bold;font-size:14px;color:red;'><![CDATA[$1]]></span>",
 "/адрес: \'(.*?)\'[\r]*[\n]*/" => " параметры GET: <span style='font-size:14px;color:green;'><![CDATA[$1]]></span>",
 "/рефер: \'(.*?)\'[\r]*[\n]*/" => "откуда попал <span style='font-weight:bold;font-size:14px;color:black;'><a href='$1'>$1</a>",
 "/браузер: \'(.*?)\'[\r]*[\n]*/" => "<span style='font-size:10px;'>$1</span>"
  );
 $text = preg_replace(array_keys($code), array_values($code), $text);
 return $text;
}
$content->out_content("_sysvol/_a/theme/logreader_f.html");
$temp = ob_get_contents();
ob_end_clean(); 
