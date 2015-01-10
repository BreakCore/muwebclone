<?php 
if (!defined('insite')) die("no access"); 
$nowitime = time();
if(!file_exists("_dat/dl.dat"))
{
 $fne = fopen("_dat/dl.dat","w");
 fclose($fne);
}

$downloads = array();
$listCont = file_get_contents("_dat/dl.dat");
if(!empty($listCont))
{
 $downloads = unserialize($listCont);
 /*
  * если есть загрузки, ТО достаем список вида
  * array[номер загрузки]
  * =>[title] заголово
  * =>[desc] описание
  * =>[link] линк на скачивание
  * =>[linkName] название линка
  * =>[aways] кликов
  */
}


if (isset($_GET["link"]))
{
 $numb = (int)$_GET["link"];

 if (isset($downloads[$numb]))
 {
  $downloads[$numb]["aways"]++;
  $fne = fopen("_dat/dl.dat","w");
  @fwrite($fne,serialize($downloads));
  fclose($fne);
  @unlink("/_dat/cach/download");
  if (substr($downloads[$numb]["link"],0,4)!="http") $downloads[$numb]["link"]="http://".$downloads[$numb]["link"];
  header("location:".$downloads[$numb]["link"]);
  die();
 }
}

$cachtime = load_cache("_dat/cach/".$_SESSION["mwclang"]."_download",true);
if($nowitime - $cachtime > 3)
{
 ob_start();
 if(count($downloads)> 0)
 {
  $content->out("donloadns_h.html");

  foreach ($downloads as $id=>$array)
  {
     $content->set('|caption|', $array["title"]);
     $content->set('|nom|', $id);
     $content->set('|des|', $array["desc"]);
     $content->set('|dowload_dwn|',$array["aways"]);
     $content->set('|file|', $id);
     $content->set('|d_capt|', $array["linkName"]);
     $content->out("donloadns_c.html");
    }
    $content->out("donloadns_f.html");

 }
 $temp = ob_get_contents();
 write_catch("_dat/cach/".$_SESSION["mwclang"]."_download",$temp);
 ob_clean();
}
else
 $temp = load_cache("_dat/cach/{$_SESSION["mwclang"]}_download");
