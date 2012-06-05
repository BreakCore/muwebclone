<?php if (!defined('insite')) die("no access"); 
global $db;
global $content;
global $config;
require "configs/referal_cfg.php";

/**
 * строим панельку для вывода
 * @param массив с именами персонажей $users
 * @return возвращает изображение списка выбора и кнопку имя кнопки addwict, имя списка selchar
 */
function getusrbox($users)
{
 global $content;
 global $config;
 $onreturn="";
 foreach ($users as $value) $onreturn.="<option value='".$value."'>".$value."</option>";
 $content->set("|input|",$onreturn);
 
 return $content->out_content("theme/".$config["theme"]."/them/ref_form.html",1);
}

ob_start();

$accname = substr(validate($_SESSION["user"]),0,10);
$about = $db->fetchrow($db->query("SELECT memb_name,mail_addr,fpas_answ,rdate FROM memb_info WHERE memb___id='".$accname."'"));

$content->set('|name|', $about[0]);
$content->set('|mail|', $about[1]);
$content->set('|answer|', $about[2]);
$content->set('|date|', @date("Y-m-d, G:i",$about[3]));

$content->out_content("theme/".$config["theme"]."/them/usercp_h.html");

$s4u=$db->query("SELECT Name,class,clevel FROM character WHERE AccountID='".$accname."'");
$colvo =$db->numrows($db->query("SELECT Name FROM character WHERE AccountID='".$accname."'"));
	
$charmas = array();

if ($colvo>0)
{
 $getpr=0;
 $content->out_content("theme/".$config["theme"]."/them/usercp_c_h.html");
 for ($j=0;$j<$colvo;$j++)
 {
  $resultc = $db->fetchrow($s4u);
  $content->set('|classpicture|', classpicture($resultc[1]));
  $content->set('|Name|', $resultc[0]);
  $charmas[]=$resultc[0];
  $content->set('|Class|', classname($resultc[1]));
  $content->set('|Level|', $resultc[2]);
  $content->set('|getcharmenu|', getcharmenu(0,$resultc[0]));
  $content->out_content("theme/".$config["theme"]."/them/usercp_c_c.html");
  
  if ($referal["minlvl"]<=$resultc[2])  $getpr=1;
 }
 $content->out_content("theme/".$config["theme"]."/them/usercp_c_f.html");		
 
 //referal start
 $invited = $db->numrows("Select * FROM MWC_invite Where memb___id='".$accname ."' and done!='1'");
 $invited=($invited=='' || $invited<0) ? 0:$invited;
 
 if($invited>0)//выдача плюшек
 {
 	if ( $getpr==1) 
 	{
	 $who = $db->fetchrow("SELECT inviter FROM MWC_invite WHERE memb___id='".$accname."'");
	 $db->query("UPDATE MEMB_INFO SET ref_acc=ref_acc+1 WHERE memb___id='".$who[0]."'; UPDATE MWC_invite SET done='1' WHERE memb___id='".$accname."'");
    }
 }
 
 $getprice = $db->numrows("SELECT * FROM MWC_invite Where memb___id='".$accname ."' and done='1'");
 $getprice=($getprice=='' || $getprice<0) ? 0:$getprice;
 
 if ($getprice>0)//если приглашенный и готов получать приз
 {
 	if (!$_REQUEST["addwict"]) $out="<tr><td colspan='2'>".getusrbox($charmas)."</td></tr>";
 	else 
 	{
 	 $character = validate(substr($_POST["selchar"],0,10));
 	 own_char($character,$accname);
 	 $db->query("UPDATE Character Set LevelUpPoint=LevelUpPoint+".floor($referal["stats"]/2)." WHERE Name='".$character."' UPDATE MEMB_INFO SET bankZ=bankZ+".floor($referal["zen"]/2)." WHERE memb___id='".$accname."' DELETE FROM MWC_invite WHERE  memb___id='".$accname ."' and done='1'");
 	 WriteLogs("RefSys_", "Приглашенный ".$character." получил вознаграждение");
 	 header("Location:".$config["siteaddress"]."/?up=usercp");
 	}
 }
 else //не приглашен
 {
 	$wins = $db->fetchrow("SELECT ref_acc FROM MEMB_INFO WHERE memb___id='".$accname."'");
 	if ($wins[0]>0)//если пригласивший
 	{
 		if (!$_REQUEST["addwict"]) $out="<tr><td colspan='2'>".getusrbox($charmas)."</td></tr>";
 		else
 		{
 			$character = validate(substr($_POST["selchar"],0,10));
 			own_char($character,$accname);
 			$db->query("UPDATE Character Set LevelUpPoint=LevelUpPoint+".($referal["stats"]*$wins[0])." WHERE Name='".$character."' UPDATE MEMB_INFO SET bankZ=bankZ+".($referal["zen"]*$wins[0]).",ref_acc=ref_acc-".$wins[0]."  WHERE memb___id='".$accname."'");
 			WriteLogs("RefSys_", "Пригласивший ".$character." получил вознаграждение");
 			header("Location:".$config["siteaddress"]."/?up=usercp");	
 		}
 	}
 	else
    {
	 $nr =$db->numrows("SELECT * FROM MWC_invite Where inviter='".$accname ."'");
	 if ($nr>0) $content->set("|nothing|",$content->lng["rowZ"]." ".$nr);
	 $out= $content->out_content("theme/".$config["theme"]."/them/ref_empt.html",1);
 	}	
 }
 
 $content->set("|data|", $out);
 $content->out_content("theme/".$config["theme"]."/them/usercp_f.html");
 //referal end
}

$temp = ob_get_contents();
ob_end_clean();