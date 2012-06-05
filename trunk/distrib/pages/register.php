<?php
if (!defined('insite')) die("no access"); 
require 'opt.php';
global $db;
global $content;
 if (strlen($_SESSION["mwclang"])>1)
 {	 
  if(is_file("lang/".$_SESSION["mwclang"]."/".$_SESSION["mwclang"]."rules.txt")) $rules = file_get_contents("lang/".$_SESSION["mwclang"]."/".$_SESSION["mwclang"]."rules.txt");
  else $rules = file_get_contents("lang/".$config["def_lang"]."/".$config["def_lang"]."rules.txt");
 }
 else $rules = file_get_contents("lang/".$config["def_lang"]."/".$config["def_lang"]."rules.txt");

 $refer=$_GET["f"];
 $content->set('|rules|', $rules);
 $temp = $content->out_content("theme/".$config["theme"]."/them/reg_form.html",1);	

 $content->set('|refer|', $refer);
 $temp.=$content->out_content("theme/".$config["theme"]."/them/reg_refer.html",1);	


$content->set('|session_name|', session_name());
$content->set('|session_id|', session_id());
$temp.=$content->out_content("theme/".$config["theme"]."/them/reg_f.html",1);

if($_REQUEST['okreg'])
{
 $Error="non";
 (strlen($_POST['captcha'])>3 || strlen($_POST['captcha'])<8) ? $captchaimg =substr($_POST['captcha'],0,7) : $Error=$content->lng["reg_capE"];
	
 if (strlen($_POST['ps_loginname'])<3 || strlen($_POST['ps_loginname'])>10) $Error=$content->lng["reg_lognE"];
 elseif($Error=="non") $loginname = validate(substr($_POST['ps_loginname'],0,10));
	
 if (strlen($_POST['ps_name'])<3 || strlen($_POST['ps_name'])>10) $Error=$content->lng["reg_nameE"];
 elseif($Error=="non") $name = validate(substr($_POST['ps_name'],0,10));	

	
 if (strlen($_POST['ps_password'])<3 || strlen($_POST['ps_password'])>10) $Error=$content->lng["reg_pwdE"];
 elseif($Error=="non") $password = validate(substr($_POST['ps_password'],0,10));

	
 if (strlen($_POST['ps_repassword'])<3 || strlen($_POST['ps_password'])>10) $Error=$content->lng["reg_repwdE"];
 elseif($Error=="non") $repassword = validate(substr($_POST['ps_repassword'],0,10));	

		
 if (strlen($_POST['ps_email'])<3 || strlen($_POST['ps_email'])>30) $Error=$content->lng["reg_mailE"];
 elseif($Error=="non") $email = checkwordm(substr($_POST['ps_email'],0,30));	
 
 if (strlen($_POST['secword'])<3 || strlen($_POST['secword'])>12) $Error=$content->lng["reg_swE"];
 elseif($Error=="non") $secretword = validate(substr($_POST['secword'],0,10));	

 if(isset($_SESSION['captcha_keystring']))
 {
   if($_SESSION['captcha_keystring'] != $captchaimg) $Error=$content->lng["reg_capE"];; 
   unset($_SESSION['captcha_keystring'],$_SESSION["qq"]);
   
 }else $Error=$content->lng["reg_capE"];
		
 if (isset($_POST["refferal"]))  $refer = validate(substr($_POST["refferal"],0,10)); 
 else $refer="non";
		
  if ($Error=="non")
  {
   $chk_mail = $db->numrows($db->query("SELECT mail_addr FROM MEMB_INFO WHERE mail_addr='".$email."'"));
   $chk_login = $db->numrows($db->query("SELECT memb___id FROM MEMB_INFO WHERE memb___id='".$loginname."'"));
		
   if($chk_login > 0)$Error="Login already exists";
   if($chk_mail > 0)$Error="Mail already exists";
   if($password != $repassword)$Error=$content->lng["reg_repwdE"];
					
   if ($Error=="non")
   {				  
    if($config["md5use"]=="off"){$adpwd="'".$password."'";}
    elseif($config["md5use"]=="on"){$adpwd="[dbo].[fn_md5]('".$password."','".$loginname."')";}
    else die("wrong parametr md5");

    if ($db->query("INSERT INTO MEMB_INFO (memb___id,memb__pwd,memb_name,sno__numb,bloc_code,ctl1_code, mail_addr,fpas_answ,recpwd,rdate)VALUES('".$loginname."',".$adpwd.",'".$name."','1','0','1','".$email."','".$secretword."','".$password."','".time()."')"))
    {
     if ($refer!="non" ) 
     {
	  require "configs/referal_cfg.php";
	  require "configs/top100_cfg.php";
	  $row = $db->fetchrow($db->query("SELECT AccountID, cLevel, ".$top100["t100res_colum"]." FROM Character WHERE Name='".$refer."'"));/*проверяем, что за перс пригласил*/
      $check = $db->fetchrow("SELECT memb___id FROM MWC_invite WHERE memb___id='".$row[0]."'");
	  if( $check[0]!=$row[0])
	  {
	   if($row[1]>=$referal["minlvl"] || $row[2]>0);
	   {
	    $db->query("INSERT INTO MWC_invite (memb___id,inviter)VALUES('".$loginname."','".$row[0]."')");
		WriteLogs ("RefSys_",$row[0]." пригласил $loginname");
	   }
	  }
     }
	 $temp.= "<script>$(document).ready(function() {  apprise('Success! <br> <b>Login:</b> <u>".$loginname."</u><br> <b>Password:</b> <u>".$password."</u><br> <B>e-mail:</b> <u>".$email."</u><br> <b>Secret word:</b> <u>".$secretword."</u>'); });</script>";
    }
   }
   else header("Location: ".$config["siteaddress"]."/?p=not&error=18");
  } 
  else $temp.= "<div class='warnms' align='center'>".$Error."</div>";
}