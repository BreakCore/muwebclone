<?php if (!defined('inpanel')) die("no access");
/**
* модуль изменения настроек модулей
* Mu Web Clone
**/
if (isset($_GET["edit"]) && $_GET["edit"] =="reinstall")
{
     echo "<script>if(confirm('Are you sure?'))  document.location.href='".$config["siteaddress"]."/control.php?page=configs&edit=reinstall&sure=ok'; else document.location.href='".$config["siteaddress"]."/control.php?page=configs'; </script>";
 if(isset($_GET["sure"]) && $_GET["sure"]=="ok")
 {
  $db->query("UPDATE MWC SET value = '0' WHERE parametr='reinstall' DELETE FROM MWC_admin");
  logs::WriteLogs ("Adm",$_SESSION["sadmin"]." решил переустановить сайт!");
  rename("_dat/install.php","install.php");
  header("location: ".$config["siteaddress"]."/install.php");
  die();
 }
}


$content->add_dict("titles");
$content->out("configs_h.html");
//строим меню
$Lhandle = opendir("configs/");
$noneed = array(".","..",".htaccess");


$content->replace("title_opt","cfgname");
$content->set('|pg|',"opt");
$content->out("configs_c.html");

$content->replace("title_reinst","cfgname");
$content->set('|pg|',"reinstall");
$content->out("configs_c.html");

while (($file = readdir($Lhandle))!== false) 
{    
 $n = strpos($file, ".php");
 if (!in_array($file,$noneed)&& strlen($file)>4 && $n>0) 
 { 
  if($content->getVal("title_".substr($file,0,$n-4))=="")
   $content->set('|cfgname|',"title_".substr($file,0,$n-4));
  else
   $content->set('|cfgname|',$content->getVal("title_".substr($file,0,$n-4)));

  $content->set('|pg|',substr($file,0,$n-4));
  $content->out("configs_c.html");
 }
}
//если надо сохранить кфг
if(isset($_REQUEST["aplcfg"]) && isset($_GET["edit"]))
{
 $mname = substr($_GET["edit"],0,10);


     if (file_exists("configs/{$mname}_cfg.php") || $mname=="opt")
     {
      $fileZ="";
      if ($mname!="opt")
      {
       require("configs/".$mname."_cfg.php");
      }
      else
      {
       require_once "opt.php";
       $mname="config";
       $fileZ = "opt.php";
      }

      $in_write = '<?php if (!defined("insite")) die("no access");'.chr(13).chr(10);

      $newCfgTick = 0;
      foreach ($_POST as $pid=>$pval)
      {
       if($pid == "aplcfg")
        continue;//шоб не запилить кнупку в конфиг о0)

       if($pid == "db_upwd" && $mname == "config")
       {
        $pval = $config["db_upwd"];
       }
       $pval_ = (int)$pval;

       if(gettype($pval) == "string" && strlen($pval_)!= strlen($pval))
        $in_write.='$'.$mname.'["'.$pid.'"] = "'.valid::decode($pval).'";'.chr(13).chr(10);
       else
        $in_write.='$'.$mname.'["'.$pid.'"] = '.$pval.';'.chr(13).chr(10);
       $newCfgTick++;
      }
      if($newCfgTick>0)
      {
       if($fileZ == "opt.php")
       {
        rename("opt.php","configs/bkps/opt.php");//бекапим напрочь
        $h = fopen("opt.php","w");
       }
       else
       {
        @rename("configs/".$mname."_cfg.php","configs/bkps/".$mname."_cfg.php");//бекапим напрочь
        $h = fopen("configs/".$mname."_cfg.php","w");
       }
       fwrite($h, $in_write);
       fclose($h);
      }
     }
     else
      echo "can't find config!";
}

if(isset($_GET["edit"]))//если модуль выбран
{
    $mname = substr($_GET["edit"],0,10);
    if (file_exists("configs/".$mname."_cfg.php") or $mname=="opt")
    {
    if ($mname!="opt")
    {
     require "configs/".$mname."_cfg.php";
     include "lang/".$_SESSION["mwclang"]."/".$_SESSION["mwclang"]."_cfg.php";
     $content->set('|pg|',$mname);
    }
    else
    {
     require $mname.".php";
     include "lang/".$_SESSION["mwclang"]."/opt_cfg.php";
     $content->set('|pg|',$mname);
     $mname = "config";
    }

     $content->out("configs_form_h.html");


     foreach($$mname as $pname => $val)
     {
      $desc = $content->getVal($pname);
      if(empty($desc)){
       $content->set("|desc|",$pname);
      }
      else
       $content->set("|desc|",$desc);


      if($pname=="db_upwd")
      {
       $content->set("|disabled|","disabled");
       $content->set("|elemn|","");
       $val="it is pwd";
      }
      elseif (substr($pname,(count($pname)-5))=="_def")
      {
       $content->set("|elem|",$val);
       $content->set("|elemn|",$pname);
       $content->out("configs_form_h_c.html");
       $content->set("|disabled|","disabled");
       $content->set("|elemn|","");
      }
      else
       $content->set("|disabled|","");

      $content->set("|elem|",$val);
      $content->set("|elemn|",$pname);
      $content->out("configs_form_c.html");
     }
     $content->out("configs_form_f.html");
   }
   else echo "no file!";
}

$content->out("configs_f.html");
