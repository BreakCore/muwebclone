<?php session_start(); 
ob_start();
$starttime = explode(' ', microtime()); $starttime = $starttime[1] + $starttime[0];
define ('insite', 1);
$allok=0;
@include "opt.php";
if ($config["debug"]==0) error_reporting(0);
if(isset($_SESSION["install"]))  unset($_SESSION["lang"],$_SESSION["mwcsaddr"],$_SESSION["install"]);


if(!is_array($config)) 
{
 if(file_exists("install.php"))
 {
  header("location: install.php");
  die("<h2>nothing here</h2>");
 }
 else 
 {
  if(file_exists("_dat/install.php"))
  {
   rename("_dat/install.php","install.php");
   header("location: ".$config["siteaddress"]."/install.php");
  }
  else
  {
   die("<h2>Please put install.php in main folder!</h2>");
  }
 }
}
else
{
if(strlen($_SESSION["mwclang"])==0 or !isset($_SESSION["mwclang"]))  
 $_SESSION["mwclang"]=$config["def_lang"];
require "_sysvol/security.php";

require "_sysvol/engine.php";
require "_sysvol/fsql.php";
require "_sysvol/them.php";
require "_sysvol/pages.php";
require "_sysvol/boxes.php";

WriteLogs("All_","");

$db = new Connect ($config["ctype"], $config["db_host"], $config["db_name"], $config["db_user"], $config["db_upwd"],$config["odbc_driver"],$config["debug"]); 
$content = new content("site",substr($_SESSION["mwclang"],0,3),0);
autobans();
 
if (isset($_POST["chsl"]) && $_SESSION["mwclang"]!=$_POST["chsl"])
{
 $fln = substr($_POST["chsl"],0,3);
 if (is_dir("lang/".$fln))$_SESSION["mwclang"]=$fln;
}



if ($config["under_rec"]==1)
{
 if (isadmin()!=1)
 {
  $content->set('|team|', $config["server_team"]);
  $content->set('|yy|', @date('Y'));
  $content->out_content("theme/".$config["theme"]."/them/noaccess.html");
  echo login("close");
 } 
 else $allok=1;
}

if ($allok==1 or $config["under_rec"]==0) 
{
 if (file_exists("theme/".$config["theme"]."/index.php")) 
 {
  if ($config["show_adm"]==0) $show_gm="CtlCode !=32 and"; else $show_gm="";
  if ($_POST["selectedchar"]) $_SESSION["character"] = validate(substr($_POST["selectedchar"],0,10),0);
  if ($_GET["p"]!="register") unset($_SESSION["captcha_keystring"]);
  require "_sysvol/webv.php";
 
 
  $content->set('|description|', $config["description"]);
  $content->set('|keywords|', $config["keywords"]);
  $content->set('|theme|', $config["theme"]);
  $content->set('|dateZtm|', @date("F d, Y H:i:s"));
  $content->set('|timev|', @date("H:i:s"));
  $content->set('|titles|', titles(true));
  $content->set('|m_titles|', titles());
  $content->set('|siteaddress|', $config["siteaddress"]);
  $content->set('|theme|', $config["theme"]);
  $content->set('|getmenutitles|', getmenutitles());
  $content->set('|lang_menu|', $tmt);
  $content->set('|login|', login());
  $content->set('|loginin|', show_login());
  //$content->set('|baners|', @file_get_contents("_dat/baners.dat"));
 
 $contacts = @file("_dat/contact.dat");
 $ct="";
 if ($contacts)
 {
 global $content;
 global $config;

 $ct=$content->out_content("theme/".$config["theme"]."/them/contacts_h.html",1);
 foreach ($contacts as $templ)
 {
  list($typeZ,$contactZ) = split("::",$templ);
  if ($typeZ=="skype") $contactZ="<a href ='skype:".$contactZ."'>".$contactZ."</a>";
  elseif ($typeZ=="gmail") $contactZ= "<a href='mailto:".$contactZ."'>".$contactZ."</a>";
  $content->set('|type|', $typeZ);
  $content->set('|contact|', $contactZ);		
  $ct.=$content->out_content("theme/".$config["theme"]."/them/contacts_c.html",1);
 }
 $ct.=$content->out_content("theme/".$config["theme"]."/them/contacts_f.html",1);
 }
    $content->set('|contact|', $ct);
  $inc_mod = explode(",",$config["mainmod"]);
  
  foreach($inc_mod as $i=>$v)
  {
   $content->set('|'.$v.'|', showst(trim($v)));
  }
  
  if (isset($_GET["p"]) && !isset($_GET["up"]) || !isset($_GET["up"]) && !isset($_GET["p"]))
     $content->set('|pages|', pages());
  elseif (isset($_GET["up"]))
  {
   //страницы пользователя
   chk_user(1);
   if (chkc_char($_SESSION["user"])==0)
       $content->set('|pages|', "<div align='center'>Your account haven't characters!</div>");
   else
   {
    switch (chk_user())
    {
     case 0: header("Location:".$config["siteaddress"]."/?p=not&error=7"); die(); break;
     case 1: 
           if(chck_online($_SESSION["user"])!=1) $content->set('|pages|', userpages());
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

  $content->out_content("theme/".$config["theme"]."/them/main.html");
 }
 else die("theme file is not found, check your site!");
}
$db->close();
}
ob_end_flush();