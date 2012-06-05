<?php if (isset($_POST))
{
define('insite',1);
@require "opt.php";
if ($config["debug"]==0) error_reporting(0);
require "_sysvol/fsql.php";
require "configs/servis_cfg.php";

function WriteLogs($where,$content) 
{
	if(!$where) Die ("<div style='text-align: center; font-size: 18px; color: red;'>Log folder doesn't exist!</div>");
	if($handle = fopen('logZ/'.$where.'['.@date("d_m_Y", time()).'].log', 'a+'))
	{
		if (fwrite($handle, "[".@date("H:i:s", time())."] IP [".getenv("REMOTE_ADDR")."] \r\n Message: ".$content." \r\n адрес: '".$_SERVER['QUERY_STRING']."' \r\n рефер: '".getenv('HTTP_REFERER')."' \r\n браузер: '".$_SERVER['HTTP_USER_AGENT']."' \r\n\n") === FALSE) fclose($handle);		
	}
} 

foreach ($_POST as $id=>$val)
{
  $_POST[$id] = preg_replace("/[^[:digit:]A-Za-zА-Яа-я_@.,!?;:%\]\[ \-+@()=\s]/",'',$val);
}

 $search_id = substr((int)trim($_POST["smsdeluxe_uid"]),0,10);
 $date = strtotime($_POST["smsdeluxe_datepay"]);
 $country = substr($_POST["smsdeluxe_country"],0,5);
 $key = $_POST["smsdeluxe_skey_all"];
 $genkey = md5($servis["smsdelkey"].$_POST["smsdeluxe_uid"].$_POST["smsdeluxe_country"].$_POST["smsdeluxe_operator"].$_POST["smsdeluxe_number"]);
 if ($key==$genkey)
 {
  $db = new Connect ($config["ctype"], $config["db_host"], $config["db_name"], $config["db_user"], $config["db_upwd"],$config["odbc_driver"],$config["debug"]); 
  $credits=$db->fetchrow("SELECT credits,memb___id FROM smsdeluxe WHERE id='".(int)$search_id."'");
  if ($credits[0]>0)
  {
    $db->query("UPDATE smsdeluxe SET datepayed='".$date."',county='".$country."' WHERE id='".$search_id."' UPDATE MEMB_INFO SET credits=credits+'".$credits[0]."' WHERE memb___id='".$credits[1]."'");
    WriteLogs("SMSDeluxe","На аккаунт ".$credits[1]." успешно зачислены кредиты ".$credits[0]);
    WriteLogs("SMSquery","UPDATE smsdeluxe SET datepayed='".$date."',county='".$country."' WHERE id='".(int)$search_id."' UPDATE MEMB_INFO SET credits=credits+'".$credits[0]."' WHERE memb___id='".$credits[1]."'");
	$db->close();
  }
 }
 else WriteLogs("SMSDeluxe_error","сгенерированный ключ ".$genkey." не равен полученному ".$key.". Или ошибка скрипта или попытка подделать платеж");
 //WriteLogs("SMSDeluxe_error","сгенерироан ключик ".$genkey." пришел ключик ".$key." что пришло в пост для генерации: smsdeluxe_uid ".$_POST["smsdeluxe_uid"]."smsdeluxe_country ".$_POST["smsdeluxe_country"]."smsdeluxe_operator ".$_POST["smsdeluxe_operator"]."smsdeluxe_number ".$_POST["smsdeluxe_number"]);
}
else
die();