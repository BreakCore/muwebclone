<?php if (!defined('inpanel')) die("no access"); 
/*
* менеджер меню
*/
global $config;
global $db;
global $content;

ob_start();
if (!isset($_GET["mtype"]))//если не выбрано меню
   $content->out_content("_sysvol/_a/theme/manm_menu.html");
else
{
 if ($_REQUEST["clear_m"] && isset($_GET["mtype"])) //очистить кеш
 {
  switch (checknum($_GET["mtype"],0,3))
  {
   case 1: @unlink("_dat/menus/".$_SESSION["mwclang"]."_mainmenu");break; 
   case 2: @unlink("_dat/menus/".$_SESSION["mwclang"]."_usermenu");break;
   case 3: @unlink("_dat/menus/".$_SESSION["mwclang"]."_admmenu");break;
  }

}
 
  
if (isset($_GET["del"]))
{

 switch (checknum($_GET["mtype"],0,3))
 { 
  case 1: $filemenu = @file("_dat/menu.dat");$fonw = fopen("_dat/menu.dat","w");break; 
  case 2: $filemenu = @file("_dat/umenu.dat");$fonw = fopen("_dat/umenu.dat","w");break;
  case 3: $filemenu = @file("_dat/amenu.dat");$fonw = fopen("_dat/amenu.dat","w");break;
  case 4: $filemenu = file("_dat/cmenu.dat");$fonw = fopen("_dat/cmenu.dat","w");break; 
  default: die('wrong parameter!');
 }
 
  $position=substr(checknum($_GET["del"]),0,3);
  $mcount= count($filemenu);
  unset($filemenu[$position]);

  fputs($fonw, implode("",$filemenu));
  fclose($fonw);
  header("Location:".$config["siteaddress"]."/control.php?page=manmod&mtype=".$_GET["mtype"]);
}
/**
* добавление модул¤ в меню сайта
*/
if($_REQUEST["add2menu"])
{	
 if (isset($_POST["mod_name"]) && isset($_POST["mod_define"]) && strlen($_POST["mod_name"])>1 && strlen($_POST["mod_define"])>1)
 {
  switch (checknum($_GET["mtype"],0,3))
  { 
   case 1: $fhandle = fopen("_dat/menu.dat","a");break; 
   case 2: $fhandle = fopen("_dat/umenu.dat","a");break;
   case 3: $fhandle = fopen("_dat/amenu.dat","a");break;
   case 4: $fhandle = fopen("_dat/cmenu.dat","a");break;
   default:die();
  }			
  if(fwrite($fhandle, validate(substr($_POST["mod_name"],0,20))."::".substr($_POST["mod_define"],0,20)."\r\n")) 
  { 
    //echo "<div align='center' class='succes'>".mm_modul_add_ok."</div><br>"; 
    WriteLogs ("Adm_","јккаунт ".$_SESSION["user"]." добавил пункт меню ".substr($_POST["mod_name"],0,20)." в _dat\menu.dat ");
  }
   fclose($fhandle);
  unset($_REQUEST["add2menu"],$fhandle);
  header("Location:".$config["siteaddress"]."/control.php?page=manmod&mtype=".checknum($_GET["mtype"],0,3));
 }
}
/**
* нажата стрелка "вверх"
*/
if (isset($_GET["mu"])) 
{
 $numhandler = substr($_GET["mu"],0,3); 
 if(!preg_match("/^[0-9]/",$numhandler)) echo "error";
 else
 {	
  switch ($_GET["mtype"])
  {
   case 1: $filemenu = @file("_dat/menu.dat");$fonw = fopen("_dat/menu.dat","w");break; 
   case 2: $filemenu = @file("_dat/umenu.dat");$fonw = fopen("_dat/umenu.dat","w");break;
   case 3: $filemenu = @file("_dat/amenu.dat");$fonw = fopen("_dat/amenu.dat","w");break;
   case 4: $filemenu = @file("_dat/cmenu.dat");$fonw =  fopen("_dat/cmenu.dat","w");break;
   default:die();
  }
  flock($fonw, LOCK_EX);

 if($numhandler==0)
 {
  $filemenu[count($filemenu)]=$filemenu[$numhandler];
  unset($filemenu[$numhandler]);
 }
 else
 {
   $tw = $filemenu[$numhandler-1];
   $filemenu[$numhandler-1]=$filemenu[$numhandler];
   $filemenu[$numhandler]=$tw;
 }

  for ($i=0; $i<=count($filemenu); $i++)
  {
   /*if($numhandler==0)
   {
    if($i!=(count($filemenu)-1))fwrite($fonw,$filemenu[$i+1]);
    else fwrite($fonw,$filemenu[0]);
   }
   else
   {				
    if ($numhandler-1==$i) fwrite($fonw,$filemenu[$numhandler]);
    elseif($numhandler==$i)fwrite($fonw,$filemenu[$numhandler-1]);
    else fwrite($fonw,$filemenu[$i]);
   }*/
   if (strlen($filemenu[$i])>1) fwrite($fonw,$filemenu[$i]);
  }
  flock($fonw, LOCK_UN);
  fclose($fonw);
  unset ($filemenu);
  header("Location:".$config["siteaddress"]."/control.php?page=manmod&mtype=".$_GET["mtype"]);
 }
}
/**
* нажата стрелка "вниз"
*/
elseif (isset($_GET["md"])) 
{
 $numhandler = substr($_GET["md"],0,3); 
 if(!preg_match("/^[0-9]/",$numhandler)) echo "error";
 else
 {	
  switch ($_GET["mtype"])
 {
  case 1: $filemenu = @file("_dat/menu.dat");$fonw = fopen("_dat/menu.dat","w");break; 
  case 2: $filemenu = @file("_dat/umenu.dat");$fonw = fopen("_dat/umenu.dat","w");break;
  case 3: $filemenu = @file("_dat/amenu.dat");$fonw = fopen("_dat/amenu.dat","w");break;
  case 4: $filemenu = @file("_dat/cmenu.dat");$fonw = fopen("_dat/cmenu.dat","w");break;
  default:die();
 }
 flock($fonw, LOCK_EX);
 for ($i=0; $i<count($filemenu); $i++)
 {
  if($numhandler==(count($filemenu)-1))//if element is last
  {
   if ($i==0) fwrite($fonw,$filemenu[$numhandler]);
   else fwrite($fonw,$filemenu[$i-1]);
  }
  else 
  {
   if ($i==$numhandler) fwrite($fonw,$filemenu[$numhandler+1]);
   elseif($i==$numhandler+1) fwrite($fonw,$filemenu[$numhandler]);
   else fwrite($fonw,$filemenu[$i]);
  }
 }	
  flock($fonw, LOCK_UN);
  fclose($fonw);
  unset ($filemenu);
  header("Location:".$config["siteaddress"]."/control.php?page=manmod&mtype=".$_GET["mtype"]);
 }
}
$content->out_content("_sysvol/_a/theme/manm_h.html");

 include "lang/".$_SESSION["mwclang"]."/".$_SESSION["mwclang"]."_titles.php";
$co =0;
switch ($_GET["mtype"])
{ 
 case 1:$filemenu = @file("_dat/menu.dat");break; 
 case 2:$filemenu = @file("_dat/umenu.dat");break;
 case 3:$filemenu = @file("_dat/amenu.dat");break;
 case 4:$filemenu = @file("_dat/cmenu.dat");break;
 default:die();
}
foreach ($filemenu as $m)
{
 $showarr = explode("::",$m);
 $showarr[1]=trim($showarr[1]);

 $content->set('|showarr|', $lang[$showarr[1]]);
 $content->set('|mtype|', substr($_GET["mtype"],0,3));
 $content->set('|co|', $co);
 $content->out_content("_sysvol/_a/theme/manm_c.html");
 $co++;
}
unset ($filemenu);
$content->out_content("_sysvol/_a/theme/manm_f.html");
}
$temp = ob_get_contents();
ob_end_clean(); 