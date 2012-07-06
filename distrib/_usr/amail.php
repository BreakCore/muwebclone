<?php if (!defined('insite')) die("no access"); 
ob_start();

global $db;
global $content;
global $config;
$content->out_content("theme/".$config["theme"]."/them/amail_h.html");
$know_q = $db->query("Select id,date  FROM MWC_messages WHERE slave_id=0 and memb___id='".$_SESSION["user"]."'");
while($onshow = $db->fetchrow($know_q))
{
 $content->set('|title|',"Ticket ".$onshow[0]);
 $content->set('|dat|',@date("H:i m,Y",$onshow[1]));
 $content->set('|id|',$onshow[0]);
 $content->out_content("theme/".$config["theme"]."/them/amail_h_c.html");
}
//если нажали для выбора тикета
if ($_GET["t"])
{
  $n = checknum(substr($_GET["t"],0,5));
  $array = $db->fetchrow("SELECT * FROM MWC_messages WHERE id='".$n."' and memb___id='".$_SESSION["user"]."'");
  
  if(!empty($array[2]))
  {
   if (trim($array[1])!=$_SESSION["user"] && !$_SESION["sadmin"]) $content->set('|name|', "Operator");
   else $content->set('|name|', $_SESSION["user"]);
   $content->set('|dat|', @date("H:i m,Y",$onshow[5]));
   $content->set('|msg|', $array[2]);
   $content->out_content("theme/".$config["theme"]."/them/amail_c_c.html"); 
   $qq= $db->query("Select * FROM MWC_messages WHERE slave_id='".$n."' order by id asc");
   
   
   While($tar=$db->fetchrow($qq))
   {

    if (trim($tar[1])!=$_SESSION["user"] && !$_SESION["sadmin"]) $content->set('|name|', "Operator");
    else $content->set('|name|', $_SESSION["user"]);
    $content->set('|dat|', @date("H:i m,Y",$tar[5]));
    $content->set('|msg|', $tar[2]);
    $content->out_content("theme/".$config["theme"]."/them/amail_c_c.html"); 
   }
  }
}
if (!$_REQUEST["sendmail"])
{
 $content->set('|addingmsg|', "");
}
else
{
 $msg = htmlspecialchars(trim($_POST["Newmsg"]));
 if(strlen($msg)>2)
 {
  if ($_GET["t"])
  { 
    $n = checknum(substr($_GET["t"],0,5));
    $array = $db->fetchrow("SELECT memb___id FROM MWC_messages WHERE id='".$n."' and memb___id='".$_SESSION["user"]."'");
	
	if (!empty($array[0]))
	{
     if($db->query("INSERT INTO MWC_messages (memb___id,message,date,slave_id)VALUES('".$_SESSION["user"]."','".cyr_code($msg)."','".time()."','".$n."')"))
     {
	  $db->query("UPDATE MWC_messages SET isread='0' WHERE id='".$n."'");
      WriteLogs("Admin_mail", $_SESSION["user"]." послал сообщение администратору");
      header("Location:".$config["siteaddress"]."/index.php?up=amail&act=1");
     }
	}
  }
  else
  {
   if($db->query("INSERT INTO MWC_messages (memb___id,message,date)VALUES('".$_SESSION["user"]."','".cyr_code($msg)."','".time()."')"))
   {
    WriteLogs("Admin_mail", $_SESSION["user"]." послал сообщение администратору");
    header("Location:".$config["siteaddress"]."/index.php?up=amail&act=1");
   }
   else
    $content->set('|addingmsg|', "Error");
  }
 }
 else
  $content->set('|addingmsg|', "");
  
}
if ($_GET["act"]==1) $content->set('|addingmsg|', "Done.");
$content->out_content("theme/".$config["theme"]."/them/amail.html");

$temp = ob_get_contents();
ob_end_clean();