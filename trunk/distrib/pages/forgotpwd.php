<?php if (!defined('insite')) die("no access");  

$temp = $content->out("forgotpwd_h.html",1);

if (!isset($_REQUEST["okchange"]))
{
 $content->set('|session_name|', session_name());
 $content->set('|session_id|', session_id());
 $temp.=$content->out("forgotpwd_c.html",1);
}
else
{
 $captch = substr($_POST["fcaptch"],0,7);
 if(isset($_SESSION['captcha_keystring']))
 {
  if($_SESSION['captcha_keystring'] == $captch)
  {
   unset($_SESSION['captcha_keystring']);
   $reclogin = substr($_POST["flogin"],0,10);
   $recmail = substr($_POST["fmail"],0,20);
   $recword = substr($_POST["fword"],0,20);
   $result = $db->query("SELECT recpwd FROM memb_info WHERE memb___id='$reclogin' and mail_addr='$recmail' and fpas_answ='$recword'")->FetchRow();
   if(isset($result["recpwd"]))
    $temp.="<tr><td align='center'>Passowrd is:</td><td class='succes' align='center'>{$result["recpwd"]}</td></tr>";
   else
    $temp.="<div class='warnms'>Error!</div>";
  }
  else
   $temp.="<div class='warnms'>Captcha error!</div>";
 }
}
$temp.=$content->out("forgotpwd_f.html",1);