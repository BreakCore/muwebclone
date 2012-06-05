<?php if (!defined('insite')) die("no access"); 
/**
* Услуги
**/
global $config;
global $db;
global $content;
$content->add_dict($_SESSION["mwclang"],"servis");
require "configs/servis_cfg.php";
$knowopt = $db->fetcharray("SELECT opt_inv FROM MEMB_INFO WHERE memb___id='".$_SESSION["user"]."'");

if(isset($_POST["hidech"]))
{
 $value = substr($_POST["hidech"],0,1);
 
 $credits = know_kredits();
 
 if ($knowopt["opt_inv"]!=$value && $value == 1)//если включаем
 {
  if ($credits>=$servis["servis_credit"])
  {
   $credits-=$servis["servis_credit"];
   $db->query("UPDATE MEMB_INFO SET  opt_inv='1' WHERE memb___id='".$_SESSION["user"]."' UPDATE ".$config["cr_table"]." SET ".$config["cr_column"]." ='".$credits."' WHERE ".$config["cr_acc"]."='".$_SESSION["user"]."'");
   WriteLogs("Servises_",$_SESSION["user"]." спрятал в топе статы");
  }
 }
 else if ($knowopt["opt_inv"]!=$value && $value == 0)//выключаем
 {
    $db->query("UPDATE MEMB_INFO SET opt_inv='0' WHERE memb___id='".$_SESSION["user"]."'");
   WriteLogs("Servises_",$_SESSION["user"]." сделал видимыми в топе статы");
 }
 header("Location: ".$config["siteaddress"]."/?up=servis");
}

ob_start();
//генерация страницы
$content->out_content("theme/".$config["theme"]."/them/servis_h.html");

if(!$_REQUEST["deluxe1"])
{
$content->set('|num|',$servis["servis_credit"]);
$content->set('|selectelem|', build_box("hidech",array(0=>"Off",1=>"On"),"combx",$knowopt["opt_inv"],false,"document.hidec.submit()"));
$content->out_content("theme/".$config["theme"]."/them/servis_c.html");
}
else
{
 $getcred = checknum(substr($_POST["selcr"],0,4));
 if ($getcred>0)
 {
   $getcred=floor($getcred/10);
   $timZ = @time();
   $db->query("INSERT INTO smsdeluxe (memb___id,dateonpay,credits)VALUES('".validate($_SESSION["user"])."','".$timZ."','".($getcred*10)."')");
   $uid=$db->fetchrow("Select id FROM smsdeluxe WHERE memb___id='".validate($_SESSION["user"])."' and dateonpay='".$timZ."'");
   $db->query("DELETE FROM smsdeluxe WHERE datepayed='0' and  dateonpay!='".$timZ."' and memb___id='".validate($_SESSION["user"])."'");
   WriteLogs("SMSDeluxe", $_SESSION["user"]." перешел на сайт платежной системы для плаежа, уникальный номер операции ".$uid[0]);
   if($uid[0])
   {
   $getcred*=$servis["smscost"];
   echo "
    <form id='pay' name='pay' method='POST' action='http://merchant.smsdeluxe.ru/merchant.html'>
   <table>
   <tr>
    <td></td>
    <td >

	<input type='hidden' name='SMSDELUXE_DESC' value='".$servis["smsdeldesc"]."'>
	<input type='hidden' name='SMSDELUXE_UID' value='".$uid[0]."'>
	<input type='hidden' name='SMSDELUXE_AMOUNT' id='SMSDELUXE_AMOUNT' value='".$getcred."'>
	<input type='hidden' name='SMSDELUXE_ACC' value='".$servis["smsdelacc"]."'>
	</td>
   </tr>
   </table>
   </form>
   <script>
   document.pay.submit();
   </script>
   ";
   }
   else die("database erorr!");
 }
 else die("wrong parametr !");
}
$content->out_content("theme/".$config["theme"]."/them/servis_f.html");
$temp = ob_get_contents();
ob_end_clean();