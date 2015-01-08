<?php if (!defined('insite')) die("no access"); 
ob_start();
error_reporting(E_ALL);

$content->out("amail_h.html");

$know_q = $db->query("Select id,date  FROM MWC_messages WHERE slave_id=0 and memb___id='{$_SESSION["user"]}'");
while($onshow = $know_q->FetchRow())
{
 $content->set('|title|',"Ticket ".$onshow["id"]);
 $content->set('|dat|',@date("H:i m,Y",$onshow["date"]));
 $content->set('|id|',$onshow["id"]);
 $content->out("amail_h_c.html");
}
//если нажали для выбора тикета
if (isset($_GET["t"]))
{
  $n =(int)$_GET["t"];
  $array = $db->query("SELECT * FROM MWC_messages WHERE id='$n' and memb___id='{$_SESSION["user"]}'")->FetchRow();
  
  if(!empty($array["message"]))
  {
   if ($array["memb___id"] != $_SESSION["user"] && !isset($_SESION["sadmin"]))
    $content->set('|name|', "Operator");
   else
    $content->set('|name|', $_SESSION["user"]);

   $content->set('|dat|', @date("H:i m,Y",$onshow["date"]));
   $content->set('|msg|', $array["message"]);
   $content->out("amail_c_c.html");
   $qq= $db->query("Select * FROM MWC_messages WHERE slave_id='".$n."' order by id asc");
   
   
   While($tar=$qq->FetchRow())
   {
    if ($tar["memb___id"] != $_SESSION["user"] && !isset($_SESION["sadmin"]))
     $content->set('|name|', "Operator");
    else
     $content->set('|name|', $_SESSION["user"]);

    $content->set('|dat|', @date("H:i m,Y",$tar["date"]));
    $content->set('|msg|', $tar["message"]);
    $content->out("amail_c_c.html");
   }
  }
}
if (!isset($_REQUEST["sendmail"]))
{
 $content->set('|addingmsg|', "");
}
else
{
 $msg = $_POST["Newmsg"];
 if(strlen($msg)>2)
 {
  if (isset($_GET["t"]))
  { 
    $n = (int)$_GET["t"];
    $array = $db->query("SELECT memb___id FROM MWC_messages WHERE id='$n' and memb___id='{$_SESSION["user"]}'")->FetchRow();
	
	if (!empty($array["memb___id"]))
	{
     if($db->query("INSERT INTO MWC_messages (memb___id,message,date,slave_id)VALUES('{$_SESSION["user"]}','".cyr_code($msg)."','".time()."','$n')"))
     {
	  $db->query("UPDATE MWC_messages SET isread='0' WHERE id='$n'");
      logs::WriteLogs("Admin_mail", $_SESSION["user"]." послал сообщение администратору");
      header("Location:".$content->getAdr()."/index.php?up=amail&act=1");
     }
	}
  }
  else
  {
   if($db->query("INSERT INTO MWC_messages (memb___id,message,date)VALUES('".$_SESSION["user"]."','".cyr_code($msg)."','".time()."')"))
   {
    WriteLogs("Admin_mail", $_SESSION["user"]." послал сообщение администратору");
    header("Location:".$content->getAdr()."/index.php?up=amail&act=1");
   }
   else
    $content->set('|addingmsg|', "Error");
  }
 }
 else
  $content->set('|addingmsg|', "");
  
}
if (isset($_GET["act"]) && $_GET["act"]==1)
 $content->set('|addingmsg|', "Done.");
$content->out("amail.html");

$temp = ob_get_contents();
ob_clean();