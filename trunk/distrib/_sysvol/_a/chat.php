<?php if (!defined('inpanel')) die("no access");
global $content;
global $db;

ob_start();

if(!$_GET["show"])
  $needt = time()-3600;
else if (trim($_GET["show"])!=99)
{
 $needt = time()-(86400*(int)substr($_GET["show"],0,2));
}
else
{
  if(get_accesslvl()==100)
  {
   if($db->query("DELETE FROM MWC_chat WHERE time<".(time()-20).""))
      WriteLogs("chat","администратор ".$_SESSION["sadmin"]." очистил историю чата");
   else
      WriteLogs("chat","администратор ".$_SESSION["sadmin"]." gпытался очистить историю чата, но произошла ошибка");
  }
  $needt = time()-3600;
}
$query = $db->query("Select * from MWC_chat where time >=".$needt);
$msg="";
while($result = $db->fetchrow($query))
{
  $msg.= "[".@date("H:i",$result[1])."]".$result[3].": ".$result[0]."\r\n";
}

$content->set('|chat_msg|', $msg);

$content->out_content("_sysvol/_a/theme/chat.html");
$temp = ob_get_contents();
ob_end_clean(); 