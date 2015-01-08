<?php if (!defined('insite')) die("no access"); 
/**
* ”слуги
**/

$content->add_dict("servis");
require "configs/servis_cfg.php";

$knowopt = $db->query("SELECT opt_inv FROM MEMB_INFO WHERE memb___id='{$_SESSION["user"]}'")->FetchRow();

if(isset($_POST["hidech"]))
{
    $value = substr($_POST["hidech"],0,1);
    $credits = know_kredits($db,$config);

    if ($knowopt["opt_inv"]!=$value && $value == 1)//если включаем
    {
        if ($credits >= $servis["servis_credit"])
        {
            $credits-=$servis["servis_credit"];
            $db->query("UPDATE MEMB_INFO SET  opt_inv='1' WHERE memb___id='{$_SESSION["user"]}'; UPDATE {$config["cr_table"]} SET {$config["cr_column"]} = $credits WHERE {$config["cr_acc"]} = '{$_SESSION["user"]}'");
            logs::WriteLogs("Servises_",$_SESSION["user"]." спр¤тал в топе статы");
        }
    }
    else if ($knowopt["opt_inv"]!=$value && $value == 0)//выключаем
    {
        $db->query("UPDATE MEMB_INFO SET opt_inv='0' WHERE memb___id='".$_SESSION["user"]."'");
        logs::WriteLogs("Servises_",$_SESSION["user"]." сделал видимыми в топе статы");
    }
    header("Location: ".$config["siteaddress"]."/?up=servis");
    die();
}


//генераци¤ страницы
$content->out("servis_h.html");

if(!isset($_REQUEST["deluxe1"]))
{
    $content->set('|num|',$servis["servis_credit"]);
    $content->set('|selectelem|', build_box("hidech",array(0=>"Off",1=>"On"),"combx",$knowopt["opt_inv"],false,"document.hidec.submit()"));
    $content->out("servis_c.html");
}
else
{
    $getcred = (int)$_POST["selcr"];

    if ($getcred>0)
    {
        $getcred=floor($getcred/10);
        $timZ = @time();
        $db->query("INSERT INTO smsdeluxe (memb___id,dateonpay,credits)VALUES('".validate($_SESSION["user"])."','".$timZ."','".($getcred*10)."')");
        $uid=$db->query("Select id FROM smsdeluxe WHERE memb___id='{$_SESSION["user"]}' and dateonpay='$timZ'")->FetchRow();
        $db->query("DELETE FROM smsdeluxe WHERE datepayed='0' and  dateonpay!='$timZ' and memb___id='{$_SESSION["user"]}'");
        logs::WriteLogs("SMSDeluxe", $_SESSION["user"]." перешел на сайт платежной системы дл¤ плаежа, уникальный номер операции ".$uid["id"]);
        if(!empty($uid["id"]))
        {
            $getcred*=$servis["smscost"];
   echo "
    <form id='pay' name='pay' method='POST' action='http://merchant.smsdeluxe.ru/merchant.html'>
   <table>
   <tr>
    <td></td>
    <td >

	<input type='hidden' name='SMSDELUXE_DESC' value='".$servis["smsdeldesc"]."'>
	<input type='hidden' name='SMSDELUXE_UID' value='".$uid["id"]."'>
	<input type='hidden' name='SMSDELUXE_AMOUNT' id='SMSDELUXE_AMOUNT' value='".$getcred."'>
	<input type='hidden' name='SMSDELUXE_ACC' value='".$servis["smsdelacc"]."'>
	</td>
   </tr>
   </table>
   </form>
   <script>
   document.pay.submit();
   </script>";
   }
   else die("database erorr!");
 }
 else die("wrong parametr !");
}
$content->out("servis_f.html");
