<?php session_start(); 
ob_start();
error_reporting(E_ALL);
define ('insite', 1);
$starttime = explode(' ', microtime());
$starttime = $starttime[1] + $starttime[0];
//$tstart = microtime(true);
$allok=0;

require_once "_sysvol/logs.php";
require_once "_sysvol/security.php";
$valid = new valid();

if(isset($_SESSION["install"]))
 unset($_SESSION["lang"],$_SESSION["mwcsaddr"],$_SESSION["install"]);


if(!file_exists("opt.php"))
{

 if(file_exists("_dat/install.php"))
 {
  rename("_dat/install.php","install.php");
 }

 if(file_exists("install.php"))
 {
  header("location: install.php");
 }
 else
 {
  die("<h2>Please put install.php in main folder!</h2>");
 }
}
else
{
 require "opt.php";
 if(!is_array($config) or empty($config))
 {
  if(file_exists("_dat/install.php"))
  {
   rename("_dat/install.php","install.php");
  }

  if(file_exists("install.php"))
  {
   header("location: install.php");
  }
  else
  {
   die("<h2>Please put install.php in main folder!</h2>");
  }
 }
 //if ($config["debug"]==0)
 // error_reporting(0);

 if(!isset($_SESSION["mwclang"]) or strlen($_SESSION["mwclang"])!=3) //если язык не выбран или не состоит из 3(!) букв
  $_SESSION["mwclang"]=$config["def_lang"];


 require_once "_sysvol/engine.php"; //основные функции
 require_once "_sysvol/fsql.php"; //работа с бд
 require_once "_sysvol/them.php"; //шаблонизатор

 require_once "_sysvol/pages.php";
 require_once "_sysvol/boxes.php";

 try
 {

  $db = new connect ($config["ctype"], $config["db_host"], $config["db_name"], $config["db_user"], $config["db_upwd"]);
  $content = new content($config["siteaddress"],"site",substr($_SESSION["mwclang"],0,3),0,$config["theme"]);

  autobans($db);
  if ($config["under_rec"]==1)
  {
   if (isadmin()!=1)
   {
    $content->set('|team|', $config["server_team"]);
    $content->set('|yy|', @date('Y'));
    $content->out("noaccess.html");
    echo login($content,$db,$config,"close");
   }
   else $allok=1;
  }

  if ($allok==1 or $config["under_rec"]==0)
  {
   if (file_exists("theme/".$config["theme"]."/index.php"))
   {

    if (isset($_POST["selectedchar"]))
     $_SESSION["character"] = substr($_POST["selectedchar"],0,10);

    if (!isset($_GET["p"]) || $_GET["p"]!="register")
     unset($_SESSION["captcha_keystring"]);

    $content->set('|description|', $config["description"]);
    $content->set('|keywords|', $config["keywords"]);
    $content->set('|theme|', $config["theme"]);
    $content->set('|dateZtm|', @date("F d, Y H:i:s"));
    $content->set('|timev|', @date("H:i:s"));
    $content->set('|titles|', titles($config,true));
    $content->set('|m_titles|', titles($config));
    $content->set('|theme|', $config["theme"]);
    $content->set('|getmenutitles|', getmenutitles($config,$content));
    $content->set('|login|', login($content,$db,$config));
    $content->set('|loginin|', show_login($config,$db,$content));

    $contacts = @file("_dat/contact.dat");
    $ct="";
    if ($contacts)
    {
     $ct=$content->out("contacts_h.html",1);
     foreach ($contacts as $templ)
     {
      list($typeZ,$contactZ) = explode("::",$templ);//??

      if ($typeZ=="skype")
       $contactZ="<a href ='skype:".$contactZ."'>".$contactZ."</a>";
      elseif ($typeZ=="gmail")
       $contactZ= "<a href='mailto:".$contactZ."'>".$contactZ."</a>";

      $content->set('|type|', $typeZ);
      $content->set('|contact|', $contactZ);
      $ct.=$content->out("contacts_c.html",1);
     }
     $ct.=$content->out("contacts_f.html",1);
    }

    $content->set('|contact|', $ct);
    $inc_mod = explode(",",$config["mainmod"]);

    foreach($inc_mod as $i=>$v)
    {
     $content->set('|'.$v.'|', showst(trim($v),$content,$config,$db));
    }

    if (isset($_GET["p"]) && !isset($_GET["up"]) || !isset($_GET["up"]) && !isset($_GET["p"]))
     $content->set('|pages|', pages($config,$db,$content));

    elseif (isset($_GET["up"]))
    {
     //страницы пользователя
     chk_user($config,$db,1);
     if (chkc_char($_SESSION["user"],$db)==0)
      $content->set('|pages|', "<div align='center'>Your account haven't characters!</div>");
     else
     {
      switch (chk_user($config,$db))
      {
       case 0:
        header("Location:".$config["siteaddress"]."/?p=not&error=7"); die(); break;
       case 1:
        if(chck_online($db,$_SESSION["user"])!=1) $content->set('|pages|', userpages($config,$db,$content));
        else  header("location:".$config["siteaddress"]."/?p=not&error=20");
        break;
       case 3: header("location:".$config["siteaddress"]."/?p=not&error=14"); die(); break;
       case 4: header("location:".$config["siteaddress"]."/?p=not&error=7"); break;
      }
     }


    }
    $content->set('|server_name|', $config["server_name"]);
    $content->set('|team|', $config["server_team"]);

    $mtime = explode(' ', microtime());
    $totaltime = $mtime[0] + $mtime[1] - $starttime;
    $content->set('|generation|', round($totaltime,3)." seconds");

    $content->out("main.html");
   }
   else die("theme file is not found, check your site!");
  }

 }
 catch(Exception $ex)
 {
  $info =  $ex->getTrace();
  logs::WriteLogs("errors",$ex->getMessage()." Frile:".$info[0]["file"]." Line:".$info[0]["line"]);
  die("Wooow... something went wrong! check logs!");
 }
}
ob_end_flush();