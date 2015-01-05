<?php if (!defined('inpanel')) die("no access"); 
ob_start();
if (get_accesslvl()>49)
{
 global $db;
 global $content;
 global $config;
 $n="";
 $content->out_content("_sysvol/_a/theme/checkmail_h.html");
 //delete
 if($_GET["del"])
 {
  $n = checknum(substr($_GET["del"],0,5));
  $db->query("DELETE FROM MWC_messages WHERE id='".$n."'; DELETE FROM MWC_messages WHERE slave_id='".$n."'");
  WriteLogs("Messages","администратор ".$_SESSION["sadmin"]." удалил цепочку сообщений");
  header("Location:".$config["siteaddress"]."/control.php?page=checkmail");
 }

 if ($_REQUEST["commit"] && strlen(trim($_POST["answermsg"]))>0)
 {
    $msg = htmlspecialchars(trim($_POST["answermsg"]));
    $s_id = (int)$_POST["h_valid"];
	if($db->query("INSERT INTO MWC_messages (memb___id,message,date,slave_id,isread)VALUES('".$_SESSION["sadmin"]."','".cyr_code($msg)."','".time()."','".$s_id."','3')"))
	header("Location:".$config["siteaddress"]."/control.php?page=checkmail");
 }
 if ($_GET["mid"])
 {
   $n = checknum(substr($_GET["mid"],0,5));
   $array = $db->fetchrow("SELECT * FROM MWC_messages WHERE id='".$n."'");
   $db->query("UPDATE MWC_messages SET isread='1' WHERE id='".$n."' or slave_id='".$n."'");
   
   $content->set('|smail_Date|', @date("H:i d.m.Y",$array[5]));
   $content->set('|mnik|', $array[1]);
   $content->set('|msg|', $array[2]);
   $content->out_content("_sysvol/_a/theme/checkmail_form_c.html");
 
   
   $qq= $db->query("Select * FROM MWC_messages WHERE slave_id='".$n."' order by id asc");
   
   While($tar=$db->fetchrow($qq))
   {
    $content->set('|mnik|', $tar[1]);
    $content->set('|msg|', $tar[2]);
    $content->set('|id|', $tar[0]);
	$content->set('|smail_Date|', @date("H:i d.m.Y",$tar[5]));
    $content->out_content("_sysvol/_a/theme/checkmail_form_c.html");
   }
   $content->set('|id|', (int)$_GET["mid"]);
   $content->out_content("_sysvol/_a/theme/checkmail_form.html");
 }
 else
 {
  $content->out_content("_sysvol/_a/theme/checkmail_h1.html");
  $n=0;
  $query = $db->query("SELECT * FROM MWC_messages WHERE slave_id=0");
  while($myrows = $db->fetchrow($query))
  {
   
   $num = $db->fetchrow("SELECT [memb___id] FROM [MWC_messages] WHERE ([isread] ='0' and [slave_id]=".$myrows[0].")or ([isread] ='0' and [id]=".$myrows[0].")");
   $content->set('|m_name|', $myrows[1]);

   if (strlen($num[0])>0) $content->set('|m_let|', "<img src='imgs/letterZ.png' border='0'>");
   //if (isset($num) && $num[0]>=0) $content->set('|m_let|', "<img src='imgs/letterZ.png' border='0'>");
   else  $content->set('|m_let|', "-");
   $content->set('|m_date|', @date("H:i d.m.Y",$myrows[5]));
   $content->set('|id|', $myrows[0]);
   $content->out_content("_sysvol/_a/theme/checkmail_c.html");
   $n++;
  }
  if ($n==0) echo "<tr><td colspan='4'><div style='text-align:center'>You haven't mails</div></td></tr>";
 }


 $content->out_content("_sysvol/_a/theme/checkmail_f.html");
  
}
else
 echo "<div style='text-align:center'>You don't have access to use this module</div>";
 $temp = ob_get_contents();
ob_end_clean(); 
