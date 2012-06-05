<?php if (!defined('insite')) die("no access");  
global $content;
global $config;
$temp = $content->out_content("theme/".$config["theme"]."/them/forgotpwd_h.html",1);

if (!$_REQUEST["okchange"])
{
 $content->set('|session_name|', session_name());
 $content->set('|session_id|', session_id());
 $temp.=$content->out_content("theme/".$config["theme"]."/them/forgotpwd_c.html",1);
}
else
{
 $captch = validate(substr($_POST["fcaptch"],0,7));
 if(isset($_SESSION['captcha_keystring']))
 {
  if($_SESSION['captcha_keystring'] == $captch)
  {
   unset($_SESSION['captcha_keystring']);
   $reclogin = validate(substr($_POST["flogin"],0,10));
   $recmail = checkwordm(substr($_POST["fmail"],0,20));
   $recword = validate(substr($_POST["fword"],0,20));
   $result = mssql_fetch_row(mssql_query("SELECT recpwd FROM memb_info WHERE memb___id='".$reclogin."' and mail_addr='".$recmail."' and fpas_answ='".$recword."'"));
   if($result[0]) $temp.="<tr><td align='center'>Passowrd is:</td><td class='succes' align='center'>$result[0]</td></tr>";
   else $temp.="<div class='warnms'>Error!</div>";
  }
  else $temp.="<div class='warnms'>Captcha error!</div>";	
 }
}
$temp.=$content->out_content("theme/".$config["theme"]."/them/forgotpwd_f.html",1); 