<?php if (!defined('inpanel')) die("no access"); /*bansystem*/global $config;global $db;global $content; ob_start();$content->out_content("_sysvol/_a/theme/bansystem_h.html");if($_REQUEST["bstep1"]){ $banerror=0; $character = validate(substr($_POST["charname"],0,10)); if (strlen($character)>2 && strlen($character)<11) {  $prichina = cyr_code(substr($_POST["forwhat"],0,25)); /*���� ��-������*/  $timeban = checknum(substr($_POST["ban_timeZ"],0,4));  $timeban *= 3600;  $nowdate = (int)time();  $queryban = "Update memb_info SET mwcban_time='".($nowdate+$timeban)."', ban_des='".$prichina."' ";  if($_POST["type_search"]==1)  {   $whom = $db->fetchrow($db->query("Select AccountID from character where name='".$character."'"));   if ($whom==NULL or $whom ==0 or empty($whom)){$banerror=1; echo "<tr><td align='center' class='warnms'>".$content->lng["ban_chr_msg"]."<br> <a href='javascript:history.back();'>".$content->lng["login_back"]."</a></td></tr>";}   else $queryban.=" WHERE memb___id='".$whom[0]."' UPDATE character Set CtlCode=1 WHERE Name='".$character."' and accountid='".$whom[0]."'";  }  elseif($_POST["type_search"]==2)  {   $whom = $db->fetchrow($db->query("Select memb___id from memb_info where memb___id='".$character."'"));   if ($whom==NULL or $whom ==0 or empty($whom)){$banerror=1; echo "<tr><td align='center' class='warnms'>".$content->lng["ban_chr_msg"]."<br> <a href='javascript:history.back();'>".$content->lng["login_back"]."</a></td></tr>";}   $queryban.=", bloc_code=1 WHERE memb___id='".$character."'";	  }  if($banerror==0)  {					   if ($db->query($queryban))   {     WriteLogs ("Ban_", $_SESSION["user"]." ������� ".$character);     header("Location:".$config["siteaddress"]."/control.php?page=bans");   }   else   {    WriteLogs ("Ban_",$_SESSION["user"]." ������� �������� ".$character.", ������� �� ������(������)");   }   unset($_REQUEST["bstep1"],$timeban,$character);  } } else  {  $content->set('|msg|', $content->lng["ban_table_chr_empty"]."<br> <a href='javascript:history.back();'>".$content->lng["login_back"]."</a>");  $content->out_content("_sysvol/_a/theme/bansystem_msg.html"); }}if ($_GET["order"] && $_GET["type"]){ $banname = validate(substr($_GET["order"],0,10)); $chek_typeb = $db->fetchrow($db->query("SELECT bloc_code,mwcban_time,ban_des FROM MEMB_INFO where memb___id='".$banname."'"));  if (empty($chek_typeb[0])) {  $chek_typeb =  $db->fetchrow($db->query("SELECT AccountID FROM Character WHERE Name='".$banname."'"));/*���� ������� ���*/  if (!empty($chek_typeb[0]))  {   $chek_typeb1 = $db->fetchrow($db->query("SELECT bloc_code,mwcban_time,ban_des FROM MEMB_INFO where memb___id='".$chek_typeb[0]."'"));   $db->query("UPDATE MEMB_INFO SET mwcban_time='0',ban_des='0' WHERE memb___id='".$chek_typeb[0]."'; UPDATE Character SET CtlCode='0' WHERE AccountID='".$chek_typeb[0]."'");   WriteLogs ("Ban_",$_SESSION["user"]." �������� ".$banname);  }  else   {   $content->set('|msg|', "No char!<br> <a href='javascript:history.back();'>".$content->lng["login_back"]."</a>");   $content->out_content("_sysvol/_a/theme/bansystem_msg.html");  } } else {  $db->query("UPDATE MEMB_INFO SET mwcban_time=0,ban_des=0, bloc_code=0	WHERE memb___id='".$banname."'");  WriteLogs ("Ban_",$_SESSION["user"]." �������� ".$chek_typeb[0]); } autobans(true);}$content->out_content("_sysvol/_a/theme/bansystem_c_h.html");$query_s = $db->query("SELECT memb___id,bloc_code,mwcban_time,ban_des FROM memb_info where ban_des!='0' and ban_des!=NULL");$accs=array();$chars = array();while ($show_ar = $db->fetcharray($query_s)){ if ($show_ar["bloc_code"]==0)  {  $b_chr = $db->fetchrow($db->query("SELECT Name FROM Character WHERE CtlCode = 1 and AccountID='".$show_ar["memb___id"]."'"));  $show_ar["memb___id"] = $b_chr[0];  $chars[]=$show_ar["memb___id"]; } else $accs[]=$show_ar["memb___id"];  if ($show_ar["mwcban_time"]==0) $show_ar["mwcban_time"]="never";  $content->set("|memb___id|", $show_ar["memb___id"]); $content->set('|ban_des|', $show_ar["ban_des"]); $content->set('|date|', date('G:i j-m-Y',$show_ar["mwcban_time"])); $content->out_content("_sysvol/_a/theme/bansystem_c.html");}$content->out_content("_sysvol/_a/theme/bansystem_f.html");if (count($accs)>0 || count($chars)>0){ $fhandle = fopen("_dat/autobans.dat","w"); foreach ($accs as $v) {  fwrite($fhandle,$v."|:1\r\n"); } foreach ($chars as $v) {  fwrite($fhandle,$v."|:2\r\n"); } fclose($fhandle); @unlink("_dat/cach/".$_SESSION["mwclang"]."_ban");}$temp = ob_get_contents();ob_end_clean(); 