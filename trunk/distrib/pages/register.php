<?php
/*
—крипт регистрации
MWC 1.5.x
*/
if (!defined('insite'))
	die("no access");

require_once 'opt.php';


#region ¤зык
if (isset($_SESSION["mwclang"]))
{	 
	if(is_file("lang/".$_SESSION["mwclang"]."/".$_SESSION["mwclang"]."rules.txt"))
		$rules = file_get_contents("lang/".$_SESSION["mwclang"]."/".$_SESSION["mwclang"]."rules.txt");
	else
		$rules = file_get_contents("lang/".$config["def_lang"]."/".$config["def_lang"]."rules.txt");
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

if(isset($_REQUEST['okreg']))
{
	$Error="non";

	foreach ($_POST as $id=>$val)
	{
		if ($Error=="non")
		{
			switch($id)
			{
			case 'captcha':
				$captchaimg =substr($_POST['captcha'],0,8);
				if($_SESSION['captcha_keystring'] != $captchaimg) $Error=$content->getVal("reg_capE");
				break;
				
			case 'ps_loginname':
				$loginname = substr($_POST['ps_loginname'],0,10);
				if (strlen($loginname)<3) $Error=$content->getVal("reg_lognE");
				break;
				
			case 'ps_name':
				$name = substr($_POST['ps_name'],0,10);
				if (strlen($name)<3) $Error=$content->getVal("reg_nameE");
				break;
				
			case 'ps_password':
				$password = substr($_POST['ps_password'],0,10);
				if (strlen($password)<3) $Error=$content->getVal("reg_pwdE");
				break;

			case 'ps_repassword':
				$repassword = substr($_POST['ps_repassword'],0,10);
				if (strlen($repassword)<3) $Error = $content->getVal("reg_pwdE");
				break;
				
			case 'ps_email':
				$email = substr($_POST['ps_email'],0,50);
				if (strlen($email)<3) $Error=$content->getVal("reg_mailE");
				break;
				
			case 'secword':
				$secretword = substr($_POST['secword'],0,10);
				if (strlen($secretword)<3) $Error=$content->getVal("reg_swE");
				break;
				
			case 'refferal':
				$refer = substr($_POST['refferal'],0,10);
				break;
			}
		}
	}
	unset($_SESSION['captcha_keystring'],$_SESSION["qq"]);
	
	
	if ($Error == "non")
	{
		$checks = $db->query("SELECT (SELECT count(*) FROM MEMB_INFO WHERE mail_addr='$email') as mail, (SELECT count(*) FROM MEMB_INFO WHERE memb___id='$loginname') as login")->FetchRow();

		if($checks["login"] > 0) $Error="Login already exists";
		if($checks["mail"] > 0)$Error="Mail already exists";
		if($password != $repassword)$Error=$content->getVal("reg_repwdE");
		
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
					$row = $db->query("SELECT AccountID, cLevel, {$top100["t100res_colum"]} FROM Character WHERE Name='$refer'")->FetchRow;/*провер¤ем, что за перс пригласил*/
					$check = $db->query("SELECT memb___id FROM MWC_invite WHERE memb___id='{$row["AccountID"]}'")->FetchRow();
					if( $check["memb___id"]!= $row["AccountID"])
					{
						if($row["cLevel"] >= $referal["minlvl"] || $row[$top100["t100res_colum"]]>0);
						{
							$db->query("INSERT INTO MWC_invite (memb___id,inviter)VALUES('".$loginname."','".$row[0]."')");
							logs::WriteLogs ("RefSys_",$row["AccountID"]." пригласил $loginname");
						}
					}
				}
				echo "<script>$(document).ready(function() {  apprise('Success! <br> <b>Login:</b> <u>".$loginname."</u><br> <b>Password:</b> <u>".$password."</u><br> <B>e-mail:</b> <u>".$email."</u><br> <b>Secret word:</b> <u>".$secretword."</u>'); });</script>";
			}
		}
		else
			header("Location: ".$config["siteaddress"]."/?p=not&error=18");
	} 
	else echo "<div class='warnms' align='center'>".$Error."</div>";
}