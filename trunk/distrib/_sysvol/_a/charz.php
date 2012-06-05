<?php 
global $config; 
global $db;
global $content;
ob_start();
require "configs/charz_cfg.php";
$content->out_content("_sysvol/_a/theme/charz_h.html");
$charz["num_adm_col"] = explode(",",$charz["num_adm_col"]);
$charz["stats_n"] = explode(",",$charz["stats_n"]);

if ($_REQUEST["edchr"] && strlen($_SESSION["ched"])>2)
{
 $ololo = $db->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.Columns where TABLE_NAME = 'Character'");
 $col_num = $db->fetchrow($db->query("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_NAME='Character'"));
  
 for($i=0;$i<$col_num[0];$i++)
 {
  $result = $db->fetchrow($ololo);
  if (in_array($i,$charz["num_adm_col"]))
  $colums_inf[]=$result[0];
 }
 $qque="";
 $q=count($charz["num_adm_col"])-1;
 foreach ($_POST as $id=>$val)
 {
  if (in_array($id,$colums_inf))
  {
   $tempo = substr($_POST[$id],0,11);
   if (in_array($id,$charz["stats_n"])) $tempo=restats65($tempo);
   $qque.=" $id='".$tempo."'";
   if ($q>=1) $qque.=",";
   $q--;
  }
 }
	  
 if ($db->query("UPDATE Character SET ".$qque." WHERE Name='".$_SESSION["ched"]."'"))
 {

    WriteLogs ("Adm_","Аккаунт ".$_SESSION["user"]." изменил параметры персонажу ".$_SESSION["ched"]);
    unset($_SESSION["ched"]);
 }
}
if ($_REQUEST["edacc"] && $_SESSION["accz"])
{
 $acount["answer"] = validate(substr($_POST["fpas_answ"],0,11));
 $acount["bankz"] = checknum(substr($_POST["bankZ"],0,11));
 $acount["credits"] = checknum(substr($_POST["credits"],0,11));
 $acount["opt_inv"] = checknum(substr($_POST["opt_inv"],0,1));
 if($db->query("UPDATE MEMB_INFO SET fpas_answ='".$acount["answer"]."',bankZ='".$acount["bankz"]."',opt_inv='".$acount["opt_inv"]."' WHERE memb___id='".$_SESSION["accz"]."'"))
 {
  $db->query("UPDATE ".$config["cr_table"]." SET ".$config["cr_column"]."='".$acount["credits"]."' WHERE ".$config["cr_acc"]."='".$_SESSION["accz"]."'");
  WriteLogs ("Adm_","Аккаунт ".$_SESSION["user"]." изменил параметры аккаунту ".$_SESSION["accz"]);
  unset($_SESSION["accz"],$_SESSION["ched"]);
 }
}
if ($_REQUEST["st1"] or $_GET["char"])
{
 
 if ($_GET["char"] and !$_POST["chak"])
 {
  $chack_name = validate(substr($_GET["char"],0,11));
  $type=0;
 }
 else 
 {
  $type = checknum(substr($_POST["stype"],0,1));
  if($type =="" or strlen($type)==0)$type=0;
  $chack_name = checkword(substr($_POST["chak"],0,11));
 }
	
 if ($type==0) /*выбралои персонажа*/
 {
  $_SESSION["ched"]=$chack_name;
  $ololo = $db->query("SELECT COLUMN_NAME FROM  INFORMATION_SCHEMA.Columns where TABLE_NAME = 'Character'");
  $col_num = $db->fetchrow($db->query("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_NAME='Character'"));
  
  for($i=0;$i<$col_num[0];$i++)
  {
   $result = $db->fetchrow($ololo);
   $colums_inf[]=$result[0];
  }
  unset($character);
  $character=$db->fetcharray($db->query("SELECT * FROM Character WHERE Name='".$chack_name."'")); 
	
  if(strlen($character["Name"])>1)
  {
   $content->set('|Name|', $character["Name"]);
   $content->out_content("_sysvol/_a/theme/charz_c_h.html");
	
   foreach($colums_inf as $id=>$v)
   { 
    if (in_array($id,$charz["num_adm_col"]))
    {
     if(in_array($v,$charz["stats_n"])) $character[$v]=stats65($character[$v]); 
     $content->set('|id|', $id);
     $content->set('|v|', $v);
     $content->set('|character|', $character[$v]);
     $content->out_content("_sysvol/_a/theme/charz_c_c.html");
    }
   }
   $content->out_content("_sysvol/_a/theme/charz_c_f.html");
  }
 }
 elseif($type==1)
 {
  $chack_name = checkword(substr($_POST["chak"],0,11));

  unset($account);
  $account = $db->fetcharray($db->query("SELECT fpas_answ,recpwd,bankZ,opt_inv,mail_addr FROM MEMB_INFO WHERE memb___id='".$chack_name."'"));  
  $account["credits"] = know_kredits($chack_name);
  if(strlen($account["mail_addr"])>3)
  {
   $_SESSION["accz"]=$chack_name;
   $content->set('|chack_name|', $chack_name);
   $content->out_content("_sysvol/_a/theme/charz_form_h.html");

   foreach($account as $id=>$v)
   {
    $content->set('|id|', $id);
    $content->set('|v|', $v);
    $content->out_content("_sysvol/_a/theme/charz_form_c.html");
   }
   $charsQ = $db->query("SELECT Name FROM Character WHERE AccountID='".$chack_name."'");
   $content->out_content("_sysvol/_a/theme/charz_form_c1.html");
   while($res =$db->fetchrow($charsQ) )
   {
    $content->set('|res|', $res[0]);
    $content->out_content("_sysvol/_a/theme/charz_form_c2.html");
   }
   $content->out_content("_sysvol/_a/theme/charz_form_f.html");	
  }
}	
}
$temp = ob_get_contents();
ob_end_clean(); 	
