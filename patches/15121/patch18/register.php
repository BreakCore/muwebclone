<?php
/*
Скрипт регистрации
MWC 1.5.x
*/
if (!defined('insite')) die("no access"); 
require 'opt.php';
global $db;
global $content;

#region язык
if (strlen($_SESSION["mwclang"])>1)
{	 
	if(is_file("lang/".$_SESSION["mwclang"]."/".$_SESSION["mwclang"]."rules.txt")) $rules = file_get_contents("lang/".$_SESSION["mwclang"]."/".$_SESSION["mwclang"]."rules.txt");
	else $rules = file_get_contents("lang/".$config["def_lang"]."/".$config["def_lang"]."rules.txt");
}
else $rules = file_get_contents("lang/".$config["def_lang"]."/".$config["def_lang"]."rules.txt");
#end

#region вставки в шаблон
$content->set('|rules|', $rules);
$content->out_content("theme/".$config["theme"]."/them/reg_form.html");	

$content->set('|refer|',trim($_GET["f"]));
$content->out_content("theme/".$config["theme"]."/them/reg_refer.html");	


$content->set('|session_name|', session_name());
$content->set('|session_id|', session_id());
$content->out_content("theme/".$config["theme"]."/them/reg_f.html");
#end

if($_REQUEST['okreg'])
{
	$Error="non";

foreach ($_POST as $id=>$val)
	{
		if ($Error=="non")
		{
			switch($id)
			{
			case 'captcha':
				$captchaimg =substr(trim($_POST['captcha']),0,8);
				if($_SESSION['captcha_keystring'] != $captchaimg) $Error=$content->lng["reg_capE"]; 
				break;
				
			case 'ps_loginname':
				$loginname = validate(substr(trim($_POST['ps_loginname']),0,10));
				if (strlen($loginname)<3) $Error=$content->lng["reg_lognE"];
				break;
				
			case 'ps_name':
				$name = validate(substr(trim($_POST['ps_name']),0,10));
				if (strlen($name)<3) $Error=$content->lng["reg_nameE"];
				break;
				
			case 'ps_password':
				$password = validate(substr(trim($_POST['ps_password']),0,10));
				if (strlen($password)<3) $Error=$content->lng["reg_pwdE"];
				break;
			
			case 'ps_repassword':
				$repassword = validate(substr(trim($_POST['ps_repassword']),0,10));
				if (strlen($repassword)<3) $Error=$content->lng["reg_pwdE"];
				break;
				
			case 'ps_email':
				$email = checkwordm(substr(trim($_POST['ps_email']),0,50));
				if (strlen($email)<3) $Error=$content->lng["reg_mailE"];
				break;
				
			case 'secword':
				$secretword = validate(substr(trim($_POST['secword']),0,10));
				if (strlen($secretword)<3) $Error=$content->lng["reg_swE"];
				break;
				
			case 'refferal':
				$refer = validate(substr(trim($_POST['refferal']),0,10));
				break;
			}
		}
	}
	unset($_SESSION['captcha_keystring'],$_SESSION["qq"]);
	
	
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
				echo "<script>$(document).ready(function() {  apprise('Success! <br> <b>Login:</b> <u>".$loginname."</u><br> <b>Password:</b> <u>".$password."</u><br> <B>e-mail:</b> <u>".$email."</u><br> <b>Secret word:</b> <u>".$secretword."</u>'); });</script>";
			}
		}
		else header("Location: ".$config["siteaddress"]."/?p=not&error=18");
	} 
	else echo "<div class='warnms' align='center'>".$Error."</div>";
}