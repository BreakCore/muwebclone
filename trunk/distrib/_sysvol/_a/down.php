<?php if (!defined('inpanel')) die("no access"); 
error_reporting(E_ALL);
$button = "name='addlink' value=".$content->getVal("dl_manager_bt1")."";
$titleZ = "";
$ggiz = "";
$glin = "";
$linki = "";

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


$content->out("donwload_h.html");

if (isset($_REQUEST["addlink"]))//добавить ссыль на скачку
{
 $titleZ = htmlspecialchars($_POST["title"]);
  if(get_magic_quotes_gpc()==1)
   $glin = stripslashes($_POST["nlink"]);
  else
   $glin = $_POST["nlink"];

 $glin = valid::decode($glin);

 if(substr($glin,0,4)=="http")
  $glin = substr($glin,7);
 $ggiz = $_POST["opis"];
 $linki = $_POST["links"];

 if(strlen($titleZ)>1 && strlen($glin)>1 && strlen($ggiz)>1 && strlen($linki)>1)
 {
  $button = "name='addlink' value=".$content->getVal("dl_manager_bt1")."";
  $downloads[] = array(
      "title" => $titleZ,
      "desc" => $ggiz,
      "link" => $glin,
      "linkName" =>$linki,
      "aways" => 0
  );
  $dhandler = fopen("_dat/dl.dat","w");
  fwrite($dhandler,serialize($downloads));
  fclose($dhandler);
 }
 header("Location:".$config["siteaddress"]."/control.php?page=down");
 die();
}

if(isset($_GET["edit"]) && $_GET["edit"]==1 && isset($_GET["pos"]) && !isset($_REQUEST["editlink"]))//редактирование
{	
 $position = (int)$_GET["pos"];
 if(isset($downloads[$position]))
 {
  $titleZ = $downloads[$position]["title"];
  $ggiz = $downloads[$position]["desc"];
  $linki = $downloads[$position]["linkName"];
  $glin = $downloads[$position]["link"];
  $button = "name='editlink' value=".$content->getVal("dl_manager_bt2")."";
 }
 else
 {
  header("Location:".$config["siteaddress"]."/control.php?page=down");
  die();
 }
}

if(isset($_GET["edit"]) && $_GET["edit"] == 1 && isset($_REQUEST["editlink"]))
{

 $button = "name='addlink' value=".$content->getVal("dl_manager_bt1")."";
 $position = (int)$_GET["pos"];
 $titleZ = $_POST["title"];
  if(get_magic_quotes_gpc()==1)
   $glin=stripslashes($_POST["nlink"]);
 else
  $glin = $_POST["nlink"];
 $glin = valid::decode($glin);
 if(substr($glin,0,4)=="http")
  $glin = substr($glin,7);
 $ggiz = $_POST["opis"];
 $linki = $_POST["links"];
 
 if(strlen($titleZ)>1 && strlen($glin)>1 && strlen($ggiz)>1 && strlen($linki)>1)
 {
  $position = count($downloads);

  $downloads[] = array(
      "title" => $titleZ,
      "desc" => $ggiz,
      "link" => $glin,
      "linkName" => $linki,
      "aways" => 0
  );
  $dhandler = fopen("_dat/dl.dat","w");
  fwrite($dhandler,serialize($downloads));
  fclose($dhandler);

  header("Location:".$config["siteaddress"]."/control.php?page=down");
  die();
 }
 else
  echo "некоторые поля остались незаполненными";
 
}
if(isset($_GET["edit"]) && $_GET["edit"]==0 && isset($_GET["pos"])) //удаление
{

 $position = (int)$_GET["pos"];
 unset($downloads[$position]);
 $dhandler = fopen("_dat/dl.dat","w");
 fwrite($dhandler,serialize($downloads));
 fclose($dhandler);
 header("Location:".$config["siteaddress"]."/control.php?page=down");
 die();
}
	

foreach ($downloads as $id=>$array)
{
 if (substr($array["link"],0,4)!="http")
  $array["link"] = "http://".$array["link"];

  $content->set('|value1|', $array["title"]);
  $content->set('|value2|', $array["desc"]);
  $content->set('|value3|', $array["link"]);
  $content->set('|value4|', $array["linkName"]);
  $content->set('|value5|', $array["aways"]);
  $content->set('|cs|', $id);
  $content->out("donwload_c.html");
}


$content->set('|titleZ|', $titleZ);	
$content->set('|ggiz|', $ggiz);	
$content->set('|glin|', $glin);	
$content->set('|linki|', $linki);	
$content->set('|button|', $button);	
$content->out("donwload_f.html");
