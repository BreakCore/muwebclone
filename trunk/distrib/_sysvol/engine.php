<?php  if (!defined('insite'))die("no access");
/**
* MuWebClone engine script 1.5.x
* http://p4f.ru
**/

/**
* отображение панели пользователя 
*/
function show_login()
{
 if(strlen($_SESSION["user"])>=3 && strlen($_SESSION["pwd"])>=3 && !$_REQUEST["usrout"])
  {
   require "_usr/lpanel.php";
   return $temp;
  }
}

/**
* функция аунтивикации
**/
function login($type="open")
{
 global $content; 
 global $db;	
 global $config;

 if ($_REQUEST["usrout"])
 {
  if (adm_check()==1) WriteLogs("site","администратор вышел: ".$_SESSION["user"]);
  unset($_SESSION["user"],$_SESSION["pwd"],$_SESSION["character"]); 
  header("Location: ".$config["siteaddress"]);
 }
 elseif(!isset($_SESSION["user"]) && !isset($_SESSION["pwd"]))
 {
  if($_REQUEST["loginbtn"])
  { 		
   if (strlen($_POST["usrlogin"])>=3 && strlen($_POST["usrlogin"])<=10 && strlen($_POST["usrpwd"])>=3 && strlen($_POST["usrpwd"])<=10 )
   {
    if(isset($_SESSION['qq']))
    {
     if($_SESSION['qq'] != substr($_POST['uscpt'],0,7)) return "<span style='color:red;font-weight:bold;font-size:10px;'>&nbsp;&nbsp;&nbsp;Captcha is incorrect!</span>";
     unset($_SESSION['captcha_keystring'],$_SESSION["qq"]);
    
     $userl = validate(substr(trim($_POST["usrlogin"]),0,10));
     $userp = validate(substr(trim($_POST["usrpwd"]),0,10));
    	
     if ($config["md5use"]=="on") $suserp = "[dbo].[fn_md5]('".$userp."','".$userl."')";
     else $suserp = "'".$userp."'";	
     $validadm = $db->numrows($db->query("SELECT name,pwd FROM MWC_admin WHERE name='".$userl."' and pwd='".md5($userp)."'")); 

     $queryregNum = $db->numrows($db->query("SELECT memb___id, memb__pwd FROM MEMB_INFO WHERE memb___id ='".$userl."' and memb__pwd = ".$suserp.""));

    if ($validadm==1)
      {  
       $_SESSION["sadmin"]=$userl;
       $_SESSION["spwd"]=$userp;
       $_SESSION["adm"]=1;
       $chnick = $db->fetchrow("SELECT nick FROM MWC_admin WHERE name='".$_SESSION["sadmin"]."'");
       $_SESSION["snick"] = $chnick[0];
       $db->query("INSERT INTO MWC_chat (message,time,memb___id,nick)VALUES('I am online :) ','".time()."','".$_SESSION["sadmin"]."','".$_SESSION["snick"]."')");
       WriteLogs("site","администратор вошел: ".$_SESSION["sadmin"]);
       if ($queryregNum !=1)  header("Location:".$config["siteaddress"]."/control.php");
      }
    if ($config["under_rec"]==1 && $validadm!=1) $queryregNum=0;

    if ($queryregNum == 1)	
    {	
     $_SESSION["user"] = $userl; $_SESSION["pwd"]=$userp;
     
     $now = time();
     $chk_result = $db->fetchrow($db->query("SELECT mwcban_time, bloc_code,ban_des FROM memb_info WHERE memb___id='".$userl."'"));
     if ($now>=$chk_result[0] && $chk_result[2]!="0" && $chk_result[0]!=0)/*если время бана вышло*/
     {
      if ($chk_result[1]==0)/*если забанен персонаж*/
      {
	   $db->query("UPDATE MEMB_INFO SET mwcban_time='0',ban_des='0' WHERE memb___id='".$_SESSION["user"]."'; UPDATE Character SET CtlCode='0' WHERE AccountID='".$_SESSION["user"]."'");
	   WriteLogs("Ban_","Время бана истекло, аккаунт ".$_SESSION["user"]);				 
      }
      else
      {
	   $upd = $db->query("UPDATE memb_info SET mwcban_time=0, bloc_code=0,ban_des='0' WHERE memb___id='".$userl."'");
	   $chk_result[1] = 0;
	   WriteLogs("Ban_","Время бана истекло, аккаунт ".$_SESSION["user"]);			
      }
     }
     unset($upd);
    
     if ($chk_result[1]==1)
     { 
      return $content->out_content("theme/".$config["theme"]."/them/login_fail.html",1);
     }
     else 
     {
      header("Location:".$config["siteaddress"]."/?up=usercp");
     }
    }
    else return "<span style='color:red;font-weight:bold;font-size:10px;'>".$content->lng["login_fail"]."</span><br> <a href='javascript:history.back();'>".$content->lng["login_back"]."</a>";
    }
    else return "<span style='color:red;font-weight:bold;font-size:10px;'>&nbsp;&nbsp;&nbsp;Captcha is incorrect!</span>";
   }
   else return"<span style='color:red;font-weight:bold;'>".$content->lng["login_length"]."</span>";	
  }
  elseif (!$_REQUEST["loginbtn"])
  {
   $content->set('|session_name|', session_name());
   $content->set('|session_id|', session_id());
   if ($type!="close") return $content->out_content("theme/".$config["theme"]."/them/login.html",1);
   else return $content->out_content("theme/".$config["theme"]."/them/r_login.html",1);
  }
  elseif($config["under_rec"]==1)
  {
   if ($type!="close") return $content->out_content("theme/".$config["theme"]."/them/login.html",1);
   else return $content->out_content("theme/".$config["theme"]."/them/r_login.html",1);
  }
  elseif($type=="inpage")
  {
   return $content->out_content("theme/".$config["theme"]."/them/logininpage.html",1);
  }
  else unset($_SESSION["user"],$_SESSION["pwd"]);
 }
}

/**
* проверяет, песонаж относится к аккаунту
**/
function own_char($charnames,$accchar)
{
	global $db;
	
	$char_nameCHK = $db->numrows($db->query("SELECT Name From Character WHERE Name='".substr($charnames,0,10)."' and AccountID='".substr($accchar,0,10)."'")); 
	if ($char_nameCHK <=0)
	{
	 global $config;
	 header("Location:".$config["siteaddress"]."/?p=not&error=6");
	 die("epic fail!");
	}
}

/**
* проверка логина и пароля у пользователя на валидность
*/
function chk_user($type=0)
{
 global $config;
 if ((strlen($_SESSION["user"])>=3 && strlen($_SESSION["pwd"])>=3))
 {
  $userl = validate($_SESSION["user"]);
  $userp = validate($_SESSION["pwd"],1);
		
  global $db;
  $use1="";
  if ($config["md5use"]=="off") $use1= "SELECT count(*) FROM MEMB_INFO Where memb___id='$userl' and memb__pwd='$userp'";
  elseif ($config["md5use"]=="on") $use1="SELECT count(*) FROM MEMB_INFO Where memb__pwd=[dbo].[fn_md5]('$userp','$userl') and memb___id='$userl'";	
  else die('no md!');
  
  $qregum = $db->fetchrow($db->query($use1));
  if ($qregum[0] !=1)
  {//фейл логин
   unset($_SESSION["user"],$_SESSION["pwd"],$_SESSION["character"]);
   return 0;
  }
  if($userl)
  {
   $chek_ban = $db->fetchrow($db->query("SELECT bloc_code FROM MEMB_INFO WHERE memb___id='".$userl."'"));
   if($chek_ban[0]==1) 
     return 3;
  }	
 }
 else 
 {
  if ($type==0) return 4;
  else
  {
   header("Location: ".$config["siteaddress"]."/?p=not&error=19");
   die();
  }
 }
 return 1;
}

/**
* отображение страниц
**/
function pages()
{
  $pmnfile=file("_dat/pm.dat");
  $pagefile = preg_replace("/[^a-zA-Z0-9_-]/i", "", substr($_GET["p"],0,11)); 
  $pracces=0;
  $temp="";
  if(!isset($_GET["p"])||$pagefile == "home")
  {
   require("_sysvol/news.php");
   return $temp;
  }
  elseif($pagefile=="theme")
  {
    global $config;
    require("theme/".$config["theme"]."/index.php");
    return $temp;
  }  
  else if(file_exists("pages/".$pagefile.".php"))
  {
    foreach ($pmnfile as $num=>$str)
    {
     $pacces=explode("||",$str);
     if($pacces[0]==$pagefile && $pacces[1]==1){$pracces=1;break;}
     elseif(($pacces[0]==$pagefile && $pacces[1]==0)){$pracces=0;break;}
     else{$pracces=0;}
    }
    if($pracces==1)
    {
	 ob_start();
     require("pages/".$pagefile.".php");
	 $temp_p = ob_get_contents(); 
     ob_end_clean();
     if (!$temp) return $temp_p;
     return $temp;
    }
    else return "<div align='center' valign='center'>No page</div>";
  }
  else
  {
   WriteLogs("Pages_","несуществующая страница '".$pagefile."', возможно изучают сайт, возможный аккаунт (".$_SESSION["user"].")");				 
   require("pages/not.php");
   return $temp;
  }
}

function show_chars($accname)
{
 global $db;
 $accname = substr($accname,0,10);
 $query = $db->query("SELECT Name FROM character WHERE AccountID='$accname'");
 $i=0;
 while ($result = $db->fetchrow($query))
 {
  $names[$i]=$result[0];
  $i++;
 }
 return $names;
}
function userpages()
{
 if(isset($_GET["up"]))
 {
  $upmnfile=file("_dat/upm.dat");
  $userpage = preg_replace("/[^a-zA-Z0-9_-]/i", "", substr($_GET["up"],0,11));
  $pracces=0;
  if(is_file("_usr/".$userpage.".php"))
  {
   foreach ($upmnfile as $num=>$str)
   {
	$pacces=explode("||",$str);
	if($pacces[0]==$userpage && $pacces[1]==1){$pracces=1;break;}
	elseif($pacces[0]==$userpage && $pacces[0]==1){$pracces=0;break;}
	else $pracces=0;
   }
   if($pracces==1)
   {
    ob_start();
    require "_usr/".$userpage.".php";
	$tempZ = ob_get_contents(); 
    ob_end_clean();
    if (!$temp) return $tempZ;
    return $temp;
   }
   else
   { 
    WriteLogs("DeniedPages_","доступ к неразрешенной страницы пользователя '".$userpage."', возможный аккаунт (".$_SESSION["user"].")");
    return "<div align='center' valign='center'>Access denied</div>";
   }
  }
  else
  {
   WriteLogs("Pages_","несуществующая страница пользователя '".$userpage."', возможно изучают сайт, возможный аккаунт (".$_SESSION["user"].")");				 
   require("pages/not.php");
   return $temp;
  }
 }
}
function classname($classnum)
{
	switch($classnum)
	{
		case 0:$classnum = "Dark Wizard";break;		case 16:$classnum = "Dark Knight";break;
		case 1:$classnum = "Soul Master";break;		case 17:$classnum = "Blade Knight";break;
		case 2:$classnum = "Grand Master";break;	case 18:$classnum = "Blade Master";break;
		case 3:$classnum = "Grand Master";break;	case 19:$classnum = "Blade Master";break;
		
		case 32:$classnum = "Fairy Elf";break;		case 48:$classnum = "Magic Gladiator";break;
		case 33:$classnum = "Muse Elf";break;		case 49:$classnum = "Duel Master";break;
		case 34:$classnum = "High Elf";break;		case 50:$classnum = "Duel Master";break;
		case 35:$classnum = "High Elf";break;		
		
		case 64:$classnum = "Dark Lord";break;		case 80:$classnum = "Summoner";break;
		case 65:$classnum = "Lord Emperor";break;	case 81:$classnum = "Bloody Summoner";break;
		case 66:$classnum = "Lord Emperor";break;	case 82:$classnum = "Dimension Master";break;
													case 83:$classnum = "Dimension Master";break;
		case 96:$classnum = "Rage Fighter";break;			
		case 97:$classnum = "Fist Master";break;
		case 98:$classnum = "Fist Master";break;	
		default:$classnum="unknown";break;
	}
  return $classnum;
}
function q_chr_top($class)
{
	global $config;
	global $db;
		switch ($class)
		{
		case 0:$ch_name = "Dark Wizard";$ch_class="Class=0";break;		
		case 1:$ch_name = "Soul Master";$ch_class="Class=1";break;		
		case 2:$ch_name = "Grand Master"; $ch_class="Class=2";break;		
		case 3:$ch_name = "Grand Master";$ch_class="Class=3";break;		
		case 16:$ch_name = "Dark Knight";$ch_class="Class=16";break;
		case 17:$ch_name = "Blade Knight";$ch_class="Class=17";break;
		case 18:$ch_name = "Blade Master";$ch_class="Class=18";break;
		case 19:$ch_name = "Blade Master";$ch_class="Class=19";break;
		case 32:$ch_name = "Fairy Elf";$ch_class="Class=32";break;		
		case 33:$ch_name = "Muse Elf";$ch_class="Class=33";break;		
		case 34:$ch_name = "High Elf";$ch_class="Class=34";break;		
		case 35:$ch_name = "High Elf";$ch_class="Class=35";break;		
		case 48:$ch_name = "Magic Gladiator";$ch_class="Class=48";break;
		case 49:$ch_name = "Duel Master";$ch_class="Class=49";break;
		case 50:$ch_name = "Duel Master";$ch_class="Class=50";break;
		case 64:$ch_name = "Dark Lord";$ch_class="Class=64";break;		
		case 65:$ch_name = "Lord Emperor";$ch_class="Class=65";break;	
		case 66:$ch_name = "Lord Emperor";$ch_class="Class=66";break;	
		case 80:$ch_name = "Summoner";$ch_class="Class=80";break;
		case 81:$ch_name = "Bloody Summoner";$ch_class="Class=81";break;
		case 82:$ch_name = "Dimension Master";$ch_class="Class=82";break;
		case 83:$ch_name = "Dimension Master";$ch_class="Class=83";break;
		case 96:$ch_name = "Rage Fighter";$ch_class="Class=96";break;			
		case 97:$ch_name = "Fist Master";$ch_class="Class=97";break;
		case 98:$ch_name = "Fist Master";$ch_class="Class=98";break;	
		default: return "<br>Wrong Character class!";
		}
	require "configs/strongest_cfg.php";
	$sho_t = $db->fetchrow($db->query("SELECT TOP 1 Name,".$strongest["res_colum"].",cLevel, AccountID,gr_res from Character WHERE  CtlCode != 1 and CtlCode != 17 and ".$ch_class." ".$strongest["str_sort"]));

	$g_name_s = $db->fetchrow($db->query("SELECT G_Name FROM GuildMember WHERE Name='$sho_t[0]'"));
	if (strlen($g_name_s[0])<=1) $g_name_s[0]="no guild";
	if (empty($sho_t[0]) or strlen($sho_t[0])<=1) {unset($sho_t);$sho_t[0]="no one"; }
	$gr_star="&nbsp;";
	if ($strongest["greset"]==1 && $strongest["greset_st"]==1)
	{
	 while ($sho_t[4]>0)
	 {
	  $gr_star.="<img src=\"imgs/gres.gif\"  border=\"0\" />";
	  $sho_t[4]--;
	 }
	}
	else if ($strongest["greset"]==1 && $strongest["greset_st"]==0)
	{
		$gr_star=" <br>Grand Reset: ".$sho_t[4];
	}
	if (isset($sho_t[2]) && isset($sho_t[1])) return "<br>".$ch_name." : <span title='Level: ".$sho_t[2]."<br>Reset: ".$sho_t[1].$gr_star."<br>Guild: ".$g_name_s[0]."'>".$sho_t[0]."</span>";
	else return "<br>".$ch_name." : ".$sho_t[0]."";
	
}
function classpicture($classpic)
{
  switch ($classpic)
  {
	case 0:$classpic = "wiz";break;		case 16:$classpic = "bk";break;
	case 1:$classpic = "wiz";break;		case 17:$classpic = "bk";break;
	case 2:$classpic = "wiz";break;		case 18:$classpic = "bk";break;
	case 3:$classpic = "wiz";break;		case 19:$classpic = "bk";break;
	
	case 32:$classpic = "elf";break;	case 48:$classpic = "mg";break;
	case 33:$classpic = "elf";break;	case 49:$classpic = "mg";break;
	case 34:$classpic = "elf";break;	case 50:$classpic = "mg";break;
	case 35:$classpic = "elf";break;	
	
	case 64:$classpic = "dl";break;		case 80:$classpic = "su";break;
	case 65:$classpic = "dl";break;		case 81:$classpic = "su";break;
	case 66:$classpic = "dl";break;		case 82:$classpic = "su";break;
	
	case 83:$classpic = "su";break;
	case 96:$classpic = "RF";break;
        case 97:$classpic = "RF";break;
	case 98:$classpic = "RF";break;
  }
  return $picture="<img src='imgs/".$classpic.".png' border='0'>";
}

function titles($type=false)
{
 global $config;
 ob_start();
 if ($type) echo $config["server_name"];
 else echo  "<a href='".$config["siteaddress"]."'>".$config["server_name"]."</a>";
 include "lang/".$_SESSION["mwclang"]."/".$_SESSION["mwclang"]."_titles.php";
 if ($_GET["p"])
 {
  $pagefile = preg_replace("/[^a-zA-Z0-9_-]/i", "", substr($_GET["p"],0,11)); 
  if($pagefile=="theme") echo $config["theme"];
  else
  { 
   if ($lang["title_".$pagefile]=="")
   {
    if ($type)
     echo " - title_".$pagefile;
	else
	 echo " - <a href='".$config["siteaddress"]."/?p=".$pagefile."'>".$lang["title_".$pagefile]."</a>";
   }
   else
   {
    if ($type)
     echo " - ".$lang["title_".$pagefile];
	else
	 echo " - <a href='".$config["siteaddress"]."/?p=".$pagefile."'>".$lang["title_".$pagefile]."</a>";
   }
  }  
 }
 else if($_GET["up"])
 {
  $upagefile = preg_replace("/[^a-zA-Z0-9_-]/i", "", substr($_GET["up"],0,11)); 
  if ($upagefile!="usercp")
  {
   if($type)
    echo " - ".$lang["title_usercp"];
   else
    echo " - <a href='".$config["siteaddress"]."/?up=usercp'>".$lang["title_usercp"]."</a>";
  }
  if($type)
   echo " - ".$lang["title_".$upagefile];
  else
   echo " - <a href='".$config["siteaddress"]."/?up=".$upagefile."'>".$lang["title_".$upagefile]."</a>";
 }
 $bufer = ob_get_contents();
 ob_end_clean(); 
 return $bufer;
}

function adm_check ($admin_name=0,$type=0)
{

 if (isset($_SESSION["sadmin"]) && $_SESSION["adm"]==1)
 {
   global $db;
   $validadm = $db->numrows($db->query("SELECT name,pwd FROM MWC_admin WHERE name='".$_SESSION["sadmin"]."' and pwd='".md5($_SESSION["spwd"])."'")); 
   if ($validadm==1) return 1;
   else
   {
    unset($_SESSION["sadmin"],$_SESSION["spwd"]);
    return 0;
   }    
  }
  else
   return 0;
}

function level_check()
{
 if ($_SESSION["user"])
 {
  global $db;
  global $config;
  require "configs/top100_cfg.php";
  require "configs/wshop_cfg.php";
  $usr = substr($_SESSION["user"],0,10);
  $know = $db->query("SELECT clevel,".$top100["t100res_colum"]." FROM Character WHERE AccountID='".$usr."'");
 
  while ($lvl = $db->fetchrow($know))
  {
   if($lvl[0]>=$wshop["allow_lvl"] or $lvl[1]>0) return 1; 
  }
  return 0;
 }
 else return 0;
}



function print_price($params) {return str_replace(' ', ' ', number_format($params, 0, '.', ' '));}

function valute($word){return str_replace("k","000",$word);}

function hide_acc($word)
{
$length=strlen($word);
return substr($word,0,1).str_repeat("*",$length-3).substr($word,$length-2);
}

function bankZ_show($numbers=0,$type=0)
{
	if(strlen($_SESSION["user"])>1)
	{
	 $usr = substr($_SESSION["user"],0,10);
		if ($numbers == 0)
		{
		 global $db;
		$Bzen = $db->fetchrow($db->query("SELECT bankZ FROM memb_info WHERE memb___id='".$usr."'"));
		}
		else $Bzen[0] = $numbers;
		
		if ($type==1) return $Bzen[0];
		
		if ($Bzen[0] <1000000) {$color="color:#E0BA14";}
		elseif($Bzen[0] >=1000000 && $Bzen[0] < 10000000){$color="color:#00AE00";}
		elseif($Bzen[0] >=10000000 && $Bzen[0] < 100000000){$color="color:#428200";}
		elseif($Bzen[0] >=100000000 && $Bzen[0] < 1000000000){$color="color:#800009";}
		elseif($Bzen[0] >=1000000000){$color="color:#516EFF";}
		$Bzen[1] = "<span style='".$color.";font-weight:bold;'>".print_price($Bzen[0])."</span>";
		return $Bzen[1];
	}
}

function cred_show()
{
	if(strlen($_SESSION["user"])>1)
	{
	global $config;
	global $db;
		$Bzen = $db->fetchrow($db->query("SELECT ".$config["cr_column"]." FROM ".$config["cr_table"]." WHERE ".$config["cr_acc"]."='".$_SESSION["user"]."'"));
		if ($Bzen[0] <1000000) {$color="color:#E0BA14";}
		elseif($Bzen[0] >=1000000 && $Bzen[0] < 10000000){$color="color:#00AE00";}
		elseif($Bzen[0] >=10000000 && $Bzen[0] < 100000000){$color="color:#428200";}
		elseif($Bzen[0] >=100000000 && $Bzen[0] < 1000000000){$color="color:#800009";}
		elseif($Bzen[0] >=1000000000){$color="color:#516EFF";}
		$Bzen[1] = "<span style='".$color.";font-weight:bold;'>".print_price($Bzen[0])."</span>";
		return $Bzen[1];
	}
}

function know_kredits($accname="no")
{
	if($accname=="no") $accname = validate($_SESSION["user"]);

	if(strlen($accname)>2)
	{
		global $db;
		global $config;
		$credits = $db->fetchrow($db->query("SELECT ".$config["cr_column"]." FROM ".$config["cr_table"]." WHERE ".$config["cr_acc"]."='".$accname."'"));
		return $credits[0];
	}
}

function wareg_show()
{
	if(strlen($_SESSION["user"])>1)
	{
		global $db;
		$Bzen = $db->fetchrow($db->query("SELECT Money FROM warehouse WHERE AccountID='".validate($_SESSION["user"])."'"));
		if ($Bzen[0] <1000000) {$color="color:#E0BA14";}
		elseif($Bzen[0] >=1000000 && $Bzen[0] < 10000000){$color="color:#00AE00";}
		elseif($Bzen[0] >=10000000 && $Bzen[0] < 100000000){$color="color:#428200";}
		elseif($Bzen[0] >=100000000 && $Bzen[0] < 1000000000){$color="color:#800009";}
		elseif($Bzen[0] >=1000000000){$color="color:#516EFF";}
		$Bzen[1] = "<span style='".$color.";font-weight:bold;'>".print_price($Bzen[0])."</span>";
		return $Bzen[1];
	}
}

function chkc_char($login)
{
 global $db;
 $chk_count =  $db->numrows($db->query("SELECT Name FROM Character WHERE AccountID='".substr($login,0,10)."'"));
 if ($chk_count>0)  return 1;
 return 0;
}


function chck_online($login)
{
	global $db;
	$chk_count = $db->numrows($db->query("SELECT ConnectStat FROM memb_stat WHERE memb___id='".substr($login,0,10)."' and ConnectStat>0"));
	if ($chk_count<=0 or empty($chk_count)) return 0;
	else return 1;
}

function mod_status ($stat)
{
	if($stat==1) $m="<span style='font-weight:bold;color:green'>On</span>";
	else $m="<span style='font-weight:bold;color:red'>Off</span>";
	return $m;
}

/*
* конструтор главного меню
*/
function getmenutitles()
{
 $loadfile = @file("_dat/menu.dat");
 $nowitime = time();
 $cachtime = @filemtime("_dat/menus/".$_SESSION["mwclang"]."_mainmenu"); 
 if (empty($loadfile) or !$loadfile) return "error menu loading!";
 else
 {
  if(!$cachtime || ($nowitime-$cachtime > 3600))
  {	
   ob_start();
   global $config;
   global $content;

   include "./lang/".$_SESSION["mwclang"]."/".$_SESSION["mwclang"]."_titles.php";

   $content->set('|siteaddress|', $config["siteaddress"]);
   foreach ($loadfile as $m)
   {
    $showarr = explode("::",$m);
    $showarr[1]=trim($showarr[1]);
    $content->set('|modulename|', $showarr[0]);
    $content->set('|modulecapt|', $lang[$showarr[1]]);
    $content->out_content("theme/".$config["theme"]."/them/mainmenu.html");	
   }
   $bufer = ob_get_contents();
   write_catch("_dat/menus/".$_SESSION["mwclang"]."_mainmenu",$bufer);ob_end_clean(); return $bufer;
  }else return file_get_contents( "_dat/menus/".$_SESSION["mwclang"]."_mainmenu");
 }
}

/**
* конструктор меню персонажа
*/
function getcharmenu($type=0, $name="non")
{
 $loadfile = @file("_dat/cmenu.dat");

 if (empty($loadfile) or !$loadfile) echo "error menu loading!";
 else
 {
  global $config;
  if ($name != "non") $namel = "&chname=".$name; 
  unset($name);
  include ("./lang/".$_SESSION["mwclang"]."/".$_SESSION["mwclang"]."_titles.php");
  $let_num = count($loadfile);
  $j=0;
  $show = '<table width="100%" align="center" class="lighter1">';
  foreach ($loadfile as $m)
  {
   $showarr = explode("::",$m);
   $showarr[1]=trim($showarr[1]);
   if ($type == 0)$show .= "<tr><td height='15' align='center' ><a href='".$config["siteaddress"]."/?up=".$showarr[0].$namel."' >".$lang[$showarr[1]]."</a></td></tr>";
   else if ($type==1)
   {
    if ($j%2 == 0)$show .= "<tr>"; 
    $show .= "<td";
    if($j==($let_num-1) && ($j % 2) == 0) $show .=" colspan='2' style='text-align: justify;'"; 
    $show .=" height='15' ><a href='".$config["siteaddress"]."/?up=".$showarr[0].$namel."'>".$lang[$showarr[1]]."</a></td>";
    if ($j%2!=0) $show .="</tr>";
    $j++;
   }
  }
  if ($type==1 && ($let_num % 2)!=0) $show .="</tr>";
  $show .="</table>"; 
  return $show;
 }
}

/**
* конструктор меню пользователя
*/
function getusrmenu()
{
 $loadfile = @file("_dat/umenu.dat");
 $nowitime = time();
 
 $cachtime = @filemtime("_dat/menus/".$_SESSION["mwclang"]."_usermenu"); 

 global $config;
 global $content;
 if (empty($loadfile) or !$loadfile) echo "error menu loading!";
 else
 {
  if(!$cachtime || ($nowitime-$cachtime > 3600))
  {
   ob_start();
   include("./lang/".$_SESSION["mwclang"]."/".$_SESSION["mwclang"]."_titles.php");
   foreach ($loadfile as $m)
   {
    $showarr = explode("::",$m);
    $content->set('|modulename|', $showarr[0]);
    $content->set('|modulecapt|', $lang[trim($showarr[1])]);
    $content->out_content("theme/".$config["theme"]."/them/usermenu.html");	
   }
   $content->set('|modulename|', "usercp");
   $content->set('|modulecapt|', $lang["title_usercp"]);
   $content->out_content("theme/".$config["theme"]."/them/usermenu.html");
   $bufer = ob_get_contents();
   write_catch("_dat/menus/".$_SESSION["mwclang"]."_usermenu",$bufer);
   ob_end_clean();
   if (adm_check()==1)return $bufer.$content->out_content("theme/".$config["theme"]."/them/usermenu_a.html",1);
   return $bufer;	
  }
  else
  {
   if (adm_check()==1) 
    return file_get_contents("_dat/menus/".$_SESSION["mwclang"]."_usermenu").$content->out_content("theme/".$config["theme"]."/them/usermenu_a.html",1);
   
   return file_get_contents("_dat/menus/".$_SESSION["mwclang"]."_usermenu");
  }				
 }
}
/**
* проверка на поддрежку 65к в стате
*/
function stats65 ($stat){ return $stat = ($stat <0) ? 65535+ $stat : $stat; }
function restats65($var){ return $var =($var>32767) ? $var -65535 : $var;}
/**
* html-символы - экран
*/
function bugsend($bug) 
	{
	 $bug = str_replace("<","&lt;",$bug);
	 $bug = str_replace('"',"&quot;",$bug);
	 $bug = str_replace(">","&gt;",$bug);
	 $bug = str_replace("!","&#033;",$bug);
	 $bug = str_replace("%","&#037;",$bug);
	 $bug = str_replace("'","&#039;",$bug);
	 $bug = str_replace('"',"&quot;",$bug);
	 $bug = str_replace(" +$"," ",$bug);
	 $bug = str_replace("^ +"," ",$bug);
	 $bug = str_replace("\r"," ",$bug);
	 //$bug = str_replace("\n","&lt;br&gt;",$bug);
	 $bug = str_replace('\\\"',"&quot;",$bug);
	 return $bug;
	}
/**
* шифруем латинские символы для корректного отображения
*/
function cyr_code ($in_text)
{
$output="";
$other[1025]="Ё";
$other[1105]="ё";
$other[1028]="Є";
$other[1108]="є";
$other[1031]="Ї";
$other[1111]="ї";

for ($i=0; $i<strlen($in_text);$i++) 
{
if (ord($in_text{$i})>191){$output.="&#".(ord($in_text{$i})+848).";";} 
else 
	{
		if (array_search($in_text{$i}, $other)===false)	$output.=$in_text{$i};
		else $output.="&#".array_search($in_text{$i}, $other).";";
	}
}
$output =str_replace("'","&#039;",$output);
return $output;
}
/**
* кеширование
*/
function write_catch($file,$content)
{
	$handle = fopen($file,"w");
	flock($handle,LOCK_EX);
	fwrite ($handle,$content);
	flock($handle,LOCK_UN);
	fclose($handle);
}

/**
* bb-codes catch
*/
function bbcode($text) {
 $bbcode = array(
 "/\[url\=(.*?)\](.*?)\[\/url\]/is" => "<a target=\"_blank\" href=\"$1\">$2</a>",
 "/\[img\](.*?)\[\/img\]/is" => "<img src=\"$1\" border=\"0\">",
 "/\[b\](.*?)\[\/b\]/is" => "<b>$1</b>",
 "/\[c\](.*?)\[\/c\]/is" => "<div align=\"center\">$1</div>",
 "/\[i\](.*?)\[\/i\]/is" => "<i>$1</i>",
 "/\[u\](.*?)\[\/u\]/is" => "<u>$1</u>",
 "/\[o\](.*?)\[\/o\]/is" => "<span style=\"text-decoration: overline;\">$1</span>",
 "/\[l\](.*?)\[\/l\]/is" => "<div align=\"left\">$1</div>",
 "/\[r\](.*?)\[\/r\]/is" => "<div align=\"right\">$1</div>",
 "/\[hr\]/is" => "<hr>",
 "/\[br\]/is" => "<br>",
 "/\[sup\](.*?)\[\/sup\]/is" => "<sup>$1</sup>",
 "/\[sub\](.*?)\[\/sub\]/is" => "<sub>$1</sub>",//подстрочный
 "/\[size\=(.*?)\](.*?)\[\/size\]/is" => "<span style=\"font-size:$1pt;\">$2</span>",
  "/\[color\=(.*?)\](.*?)\[\/color\]/is" => "<font color=\"#$1\">$2</font>",
  "/\[sml\](.*?)\[\/sml\]/is" => "<img src=\"$1\" style=\"position:relative; bottom: -4px;\" border=\"0\">"
 );
 
 $text = preg_replace(array_keys($bbcode), array_values($bbcode), $text);
 return $text;
}

/**
* redecode html
*/
function unhtmlentities ($str)
{
  $trans_tbl = get_html_translation_table (HTML_ENTITIES);
  $trans_tbl = array_flip ($trans_tbl);
  return strtr ($str, $trans_tbl);
}
/**
* guild's logo
*/
function GuildLogo($hex,$name,$size=64,$livetime) 
{
 if (substr($hex,0,2)=="0x") $hex = strtolower(substr($hex,2));
 else $hex = urlencode(bin2hex($hex));
		
 $pixelSize	= $size / 8;
 $img = ImageCreate($size,$size);
 $ftime = @filemtime("imgs/guilds/".$name."-".$size.".png");
 if(file_exists("imgs/guilds/".$name."-".$size.".png") && (time() - $ftime <= $livetime)) 
 {
   return "<img alt=\"\" src=\"imgs/guilds/".$name."-".$size.".png\">";
 }
 else 
 {
  if(@preg_match('/[^a-zA-Z0-9]/',$hex) || $hex == '') $hex = '0044450004445550441551554515515655555566551551660551166000566600';
  else $hex = stripslashes($hex);
	
  for ($y = 0; $y < 8; $y++) 
  {
   for ($x = 0; $x < 8; $x++) 
   {
	 $offset	= ($y*8)+$x;
	 if(substr($hex, $offset, 1) == '0')	{$c1 = "0";		$c2 = "0"; 		$c3 = "0";		}
	 elseif	(substr($hex, $offset, 1) == '1')	{$c1 = "0";		$c2 = "0"; 		$c3 = "0";		}
	 elseif	(substr($hex, $offset, 1) == '2')	{$c1 = "128"; 	$c2 = "128"; 	$c3 = "128";	}
	 elseif	(substr($hex, $offset, 1) == '3')	{$c1 = "255"; 	$c2 = "255"; 	$c3 = "255";	}
	 elseif	(substr($hex, $offset, 1) == '4')	{$c1 = "255"; 	$c2 = "0"; 		$c3 = "0";		}
	 elseif	(substr($hex, $offset, 1) == '5')	{$c1 = "255"; 	$c2 = "128"; 	$c3 = "0";		}
	 elseif	(substr($hex, $offset, 1) == '6')	{$c1 = "255"; 	$c2 = "255"; 	$c3 = "0";		}
	 elseif	(substr($hex, $offset, 1) == '7')	{$c1 = "128"; 	$c2 = "255"; 	$c3 = "0";		}
	 elseif	(substr($hex, $offset, 1) == '8')	{$c1 = "0"; 	$c2 = "255"; 	$c3 = "0";		}
	 elseif	(substr($hex, $offset, 1) == '9')	{$c1 = "0"; 	$c2 = "255"; 	$c3 = "128";	}
	 elseif	(substr($hex, $offset, 1) == 'a')	{$c1 = "0"; 	$c2 = "255";	$c3 = "255";	}
	 elseif	(substr($hex, $offset, 1) == 'b')	{$c1 = "0"; 	$c2 = "128"; 	$c3 = "255";	}
	 elseif	(substr($hex, $offset, 1) == 'c')	{$c1 = "0"; 	$c2 = "0"; 		$c3 = "255";	}
	 elseif	(substr($hex, $offset, 1) == 'd')	{$c1 = "128"; 	$c2 = "0"; 		$c3 = "255";	}
	 elseif	(substr($hex, $offset, 1) == 'e')	{$c1 = "255"; 	$c2 = "0"; 		$c3 = "255";	}
	 elseif	(substr($hex, $offset, 1) == 'f')	{$c1 = "255"; 	$c2 = "0"; 		$c3 = "128";	}
	 else										{$c1 = "255"; 	$c2 = "255"; 	$c3 = "255";	}
	 $row[$x] 		= $x*$pixelSize;
	 $row[$y] 		= $y*$pixelSize;
	 $row2[$x] 		= $row[$x] + $pixelSize;
	 $row2[$y]		= $row[$y] + $pixelSize;
	 $color[$y][$x]	= imagecolorallocate($img, $c1, $c2, $c3);
	 imagefilledrectangle($img, $row[$x], $row[$y], $row2[$x], $row2[$y], $color[$y][$x]);
	}
  }
  Imagepng($img,"imgs/guilds/".$name."-".$size.".png");
  Imagedestroy($img);
  return "<img border=\"0\" src=\"imgs/guilds/".$name."-".$size.".png\">";
 }
}
	
/*
* выводит на экран время кеша
*/
function timing($toptime,$type=1)
{
 global $content;

 $forms=array( $content->lng["caching_mins1"],  $content->lng["caching_mins2"],  $content->lng["caching_mins3"]);
 
 if ($type==1)
 {
 $toptime = round(($toptime/60),2);
  echo "<div align=\"center\" class=\"cathtime\">*".$content->lng["caching_time"]." ".$toptime." ".($toptime%10==1&&$toptime%100!=11?$forms[0]:($toptime%10>=2&&$toptime%10<=4&&($toptime%100<10||$toptime%100>=20)?$forms[1]:$forms[2]))."</div>";
 }
 else
  return $toptime." ".($toptime%10==1&&$toptime%100!=11?$forms[0]:($toptime%10>=2&&$toptime%10<=4&&($toptime%100<10||$toptime%100>=20)?$forms[1]:$forms[2])); 
}
	
function know_level($personaz)
{	
 require "configs/res_cfg.php";
 if ($personaz>=0 && $personaz<=3) return $res["reset_sm_lvl"];
 elseif ($personaz>=16 && $personaz<=19) return $res["reset_bk_lvl"];
 elseif ($personaz>=32 && $personaz<=35) return $res["reset_elf_lvl"];
 elseif ($personaz>=48 && $personaz<=50) return $res["reset_mg_lvl"];
 elseif ($personaz>=64 && $personaz<=66) return $res["reset_dl_lvl"];
 elseif ($personaz>=80 && $personaz<=83) return $res["reset_bs_lvl"];
 elseif ($personaz>=96 && $personaz<=98) return $res["reset_rf_lvl"];
 else return 1000;
}

function know_gpoints($personaz)
{	
  require "configs/gres_cfg.php";
 if ($personaz>=0 && $personaz<=3) return $gres["greset_dw"];
 elseif ($personaz>=16 && $personaz<=19) return $gres["greset_dk"];
 elseif ($personaz>=32 && $personaz<=35) return $gres["greset_elf"];
 elseif ($personaz>=48 && $personaz<=50) return $gres["greset_mg"];
 elseif ($personaz>=64 && $personaz<=66) return $gres["greset_dl"];
 elseif ($personaz>=80 && $personaz<=83) return $gres["greset_s"];
 elseif ($personaz>=96 && $personaz<=98) return $gres["greset_rf"];
 else die("not supported classtype!");
}

function swiched_val($value)
{
 switch ($value)
  {
   case 0: return "<span style='color:red;font-weight:bold;'>Off</span>";break;
   case 1: return "<span style='color:green;font-weight:bold;'>On</span>";break;
   default: "error!";
  }
}

/*
* проверяет, есть ли сундук, если нет возвращает 0
*/
function is_wh()
{
 if ($_SESSION["user"])
 {
   global $config;
   global $db;
   return $db->numrows($db->query("SELECT AccountID FROM warehouse WHERE AccountID='".substr($_SESSION["user"],0,11)."'"));
 }
 else return 0;
}

/*
* узнать максимальне число итемов в магазине для акка
*
*/
function knowmaxit()
{
  if ($_SESSION["user"])
  {
    global $config;
    global $db;
    return $db->numrows($db->query("SELECT code FROM web_shop WHERE memb___id='".substr($_SESSION["user"],0,10)."'"));
  } 
  else return 0;  
}

/*
* работает с парсом времени со скула
*@point - данные с базы
*@tpat - шаблон времени
*@type - тип 1 - возвратит кол-во секунд, 0 вернет время в нужном шаблоне
*/
function parsetime($point,$type=0,$tpat="none")
{
if ($tpat=="none") 
 $tpat = "H:i d-M";
if ($type==0)
   return @date($tpat,strtotime($point));
else 
   return strtotime($point);
}
/*
* цвет цены
*/
function pod_price ($price)
{
		if ($price <1000000) {$color="color:#E0BA14";}
		elseif($price >=10000000 && $price < 100000000){$color="color:#00AE00";}
		elseif($price >=100000000 && $price < 1000000000){$color="color:#428200";}
		elseif($price >=1000000000 && $price < 10000000000){$color="color:#800009";}
		elseif($price >=10000000000){$color="color:#516EFF";}
		$price = "<span style='".$color.";font-weight:bold;'>".print_price($price)."</span>";
		return $price;
}


/*
* дает краткую информацию о осаде
* возвращает массив, 0 член - имя владельцев замка, 1 - текущий период, 2 - начало 3- конец
*/

function know_csstate()
{
 global $db;
 global $content;
 $info_ar=array(); 
 $CS_GUILD = $db->fetchrow($db->query("SELECT OWNER_GUILD,CONVERT(CHAR(19), SIEGE_START_DATE, 120),CONVERT(CHAR(19), SIEGE_END_DATE, 120) FROM MuCastle_DATA"));
 if (strlen($CS_GUILD[0])<3) $info_ar[0]="-/-";
 else $info_ar[0]=$CS_GUILD[0];
 
 if((@strtotime($CS_GUILD[1])+86400) > $Current_Time) $info_ar[1] = $content->lng["cs_period"];       /* 0 00:00 - 0 23:59 */
 elseif	((@strtotime($CS_GUILD[1])+432000) > $Current_Time) $info_ar[1] = $content->lng["cs_period1"]; /* 1 00:00 - 4 23:59 */
 elseif	((@strtotime($CS_GUILD[1])+500400) > $Current_Time) $info_ar[1] = $content->lng["cs_period2"]; /* 5 00:00 - 5 19:00 */
 elseif	((@strtotime($CS_GUILD[1])+586800) > $Current_Time) $info_ar[1] = $content->lng["cs_period3"]; /* 5 19:00 - 6 19:00 */
 elseif	((@strtotime($CS_GUILD[1])+594000) > $Current_Time) $info_ar[1] = $content->lng["cs_period4"]; /* 6 19:00 - 6 21:00 */
 else $info_ar[1] = $content->lng["cs_period5"];
 
 $info_ar[2] = parsetime($CS_GUILD[1],0,"d.m.Y");
 $info_ar[3] = parsetime($CS_GUILD[2],0,"d.m.Y");
 return $info_ar;
}


/**
* построение диаграммы
*/
function Diagramm($im,$VALUES,$LEGEND) 
{
 // Зададим цвета элементов
 $COLORS[0] = imagecolorallocate($im, 255, 203, 3);
 $COLORS[1] = imagecolorallocate($im, 220, 101, 29);
 $COLORS[2] = imagecolorallocate($im, 189, 24, 51);
 $COLORS[3] = imagecolorallocate($im, 214, 0, 127);
 $COLORS[4] = imagecolorallocate($im, 98, 1, 96);
 $COLORS[5] = imagecolorallocate($im, 0, 62, 136);
 $COLORS[6] = imagecolorallocate($im, 0, 102, 179);
 $COLORS[7] = imagecolorallocate($im, 0, 145, 195);
 $COLORS[8] = imagecolorallocate($im, 0, 115, 106);
 $COLORS[9] = imagecolorallocate($im, 178, 210, 52);
 $COLORS[10] = imagecolorallocate($im, 137, 91, 74);
 $COLORS[11] = imagecolorallocate($im, 82, 56, 47);
 $COLORS[12] = imagecolorallocate($im, 68, 255, 48);
 $COLORS[13] = imagecolorallocate($im, 0, 255, 255);
 $COLORS[14] = imagecolorallocate($im, 63, 211, 44);
 $COLORS[15] = imagecolorallocate($im, 255, 160, 165);
 $COLORS[16] = imagecolorallocate($im, 124, 38, 255);
 $COLORS[17] = imagecolorallocate($im, 182, 93, 0);
 $COLORS[18] = imagecolorallocate($im, 255, 251, 0);
 $COLORS[19] = imagecolorallocate($im, 169, 192, 198);
 $COLORS[20] = imagecolorallocate($im, 164, 116, 113);
 // Зададим цвета теней элементов
 $SHADOWS[0] = imagecolorallocate($im, 205, 153, 0);
 $SHADOWS[1] = imagecolorallocate($im, 170, 51, 0);
 $SHADOWS[2] = imagecolorallocate($im, 139, 0, 1);
 $SHADOWS[3] = imagecolorallocate($im, 164, 0, 77);
 $SHADOWS[4] = imagecolorallocate($im, 48, 0, 46);
 $SHADOWS[5] = imagecolorallocate($im, 0, 12, 86);
 $SHADOWS[6] = imagecolorallocate($im, 0, 52, 129);
 $SHADOWS[7] = imagecolorallocate($im, 0, 95, 145);
 $SHADOWS[8] = imagecolorallocate($im, 0, 65, 56);
 $SHADOWS[9] = imagecolorallocate($im, 128, 160, 2);
 $SHADOWS[10] = imagecolorallocate($im, 87, 41, 24);
 $SHADOWS[11] = imagecolorallocate($im, 32, 6, 0);	
 $SHADOWS[12] = imagecolorallocate($im, 40, 150, 28);	
 $SHADOWS[13] = imagecolorallocate($im, 0, 127, 127);	
 $SHADOWS[14] = imagecolorallocate($im, 40, 135, 28);	
 $SHADOWS[15] = imagecolorallocate($im, 193, 122, 125);	
 $SHADOWS[16] = imagecolorallocate($im, 67, 21, 142);	
 $SHADOWS[17] = imagecolorallocate($im, 122, 61, 0);	
 $SHADOWS[18] = imagecolorallocate($im, 130, 127, 0);	
 $SHADOWS[19] = imagecolorallocate($im, 108, 123, 127);	
 $SHADOWS[20] = imagecolorallocate($im, 86, 61, 59);	
 $black=ImageColorAllocate($im,0,0,0);

 // Получим размеры изображения
 $W=ImageSX($im);                 
 $H=ImageSY($im);
 // Вывод легенды 
 // Посчитаем количество пунктов, от этого зависит высота легенды
 $legend_count=count($LEGEND);
 // Посчитаем максимальную длину пункта, от этого зависит ширина легенды
 $max_length=0;
 foreach($LEGEND as $v) if ($max_length<strlen($v)) $max_length=strlen($v);
 // Номер шрифта, котором мы будем выводить легенду
 $FONT=2;
 $font_w=ImageFontWidth($FONT);
 $font_h=ImageFontHeight($FONT);
 // Вывод прямоугольника - границы легенды 

 $l_width=($font_w*$max_length)+$font_h+10+5+10;
 $l_height=$font_h*$legend_count+10+10;
		
 // Получим координаты верхнего левого угла прямоугольника - границы легенды
 $l_x1=$W-10-$l_width;
 $l_y1=($H-$l_height)/2;
 
 // Выводя прямоугольника - границы легенды
 ImageRectangle($im, $l_x1, $l_y1, $l_x1+$l_width, $l_y1+$l_height, $black);

 // Вывод текст легенды и цветных квадратиков
 $text_x=$l_x1+10+5+$font_h;
 $square_x=$l_x1+10;
 $y=$l_y1+10;
 $i=0;
 foreach($LEGEND as $v) 
 {
  $dy=$y+($i*$font_h);
  ImageString($im, $FONT, $text_x, $dy, $v, $black);
  ImageFilledRectangle($im,
  $square_x+1,$dy+1,$square_x+$font_h-1,$dy+$font_h-1, $COLORS[$i]);
  ImageRectangle($im, $square_x+1,$dy+1,$square_x+$font_h-1,$dy+$font_h-1, $black);
  $i++;
 }
 // Вывод круговой диаграммы 

 $total=array_sum($VALUES);
 $anglesum=$angle=Array(0);
 $i=1;
 // Расчет углов
 while ($i<count($VALUES)) 
 {
  $part=$VALUES[$i-1]/$total;
  $angle[$i]=floor($part*360);
  $anglesum[$i]=array_sum($angle);
  $i++;
 }
 $anglesum[]=$anglesum[0];
 // Расчет диаметра
 $diametr=$l_x1-10-10;
 // Расчет координат центра эллипса
 $circle_x=($diametr/2)+10;
 $circle_y=$H/2-10;

 // Поправка диаметра, если эллипс не помещается по высоте
 if ($diametr>($H*2)-10-10) $diametr=($H*2)-20-20-40;
 // Вывод тени
 for ($j=20;$j>0;$j--)
  for ($i=0;$i<count($anglesum)-1;$i++)
      ImageFilledArc($im,$circle_x,$circle_y+$j,$diametr,$diametr/2,$anglesum[$i],$anglesum[$i+1], $SHADOWS[$i],IMG_ARC_PIE);
 // Вывод круговой диаграммы
 for ($i=0;$i<count($anglesum)-1;$i++)
     ImageFilledArc($im,$circle_x,$circle_y, $diametr,$diametr/2,$anglesum[$i],$anglesum[$i+1],$COLORS[$i],IMG_ARC_PIE);
}

/**
* проверка на администратора
*/
function isadmin()
{
 if (isset($_SESSION["sadmin"]))
 {
   global $db;
 
   $validadm = $db->numrows($db->query("SELECT name,pwd FROM MWC_admin WHERE name='".validate($_SESSION["sadmin"])."' and pwd='".md5($_SESSION["spwd"])."'")); 
   
   if ($validadm==1) return 1;
   else
   {
    unset($_SESSION["sadmin"],$_SESSION["spwd"],$_SESSION["adm"]);
    return 0;
   }  
 }
 return 0;
} 

/**
* Проверка на баны, разбан в случае, если бан истек
**/
function autobans($nocach=false)
{
 $ntime = @filemtime("_dat/cach/bc"); 
 $now = time();
 if(!$ntime or time() - $ntime >3600 or $nocach) //проверка раз в час 
 {
  $filrb = @file("_dat/autobans.dat");
  global $db;
  
  if (count($filrb>0))
  {
   foreach($filrb as $m)
   {
    $tempA = explode("|:",$m);
    if ($tempA[1]!=1)
    {
     $name = $tempA[0];
     $tt = $db->fetchrow("SELECT AccountID FROM Character WHERE Name='".$tempA[0]."'");
     $tempA[0]=$tt[0];
    }

    $chk_result = $db->fetchrow($db->query("SELECT mwcban_time, bloc_code,ban_des FROM memb_info WHERE memb___id='".$tempA[0]."'"));
    if ($now>=$chk_result[0] && $chk_result[2]!="0" && $chk_result[0]!=0)/*если время бана вышло*/
    {
     if ($chk_result[1]==0)/*если забанен персонаж*/
     {
      $db->query("UPDATE MEMB_INFO SET mwcban_time='0',ban_des='0' WHERE memb___id='".$tempA[0]."'; UPDATE Character SET CtlCode='0' WHERE Name='".$name."'");
      WriteLogs("Ban_","Время бана истекло, персонаж ".$name);	
     }
     else
     {
      $upd = $db->query("UPDATE memb_info SET mwcban_time=0, bloc_code=0,ban_des='0' WHERE memb___id='".$tempA[0]."'");
	  $chk_result[1] = 0;
	  WriteLogs("Ban_","Время бана истекло, аккаунт ".$tempA[0]);
     }
    }
   }
   @unlink("_dat/cach/".$_SESSION["mwclang"]."_ban");	  
  
   $fhandle = fopen("_dat/autobans.dat","w");	
   fclose($fhandle);   
   $h=fopen("_dat/cach/bc","w");  
   fclose($h);

  }
  else
  {
   $query_s = $db->query("SELECT memb___id,bloc_code,mwcban_time,ban_des FROM memb_info where ban_des!='0'");
   $accs=array();
   $chars = array();
   while ($show_ar = $db->fetcharray($query_s))
   {
    if ($show_ar["bloc_code"]==0) 
    {
     $b_chr = $db->fetchrow($db->query("SELECT Name FROM Character WHERE CtlCode = 1 and AccountID='".$show_ar["memb___id"]."'"));
     $show_ar["memb___id"] = $b_chr[0];
     $chars[]=$show_ar["memb___id"];
    }
    else $accs[]=$show_ar["memb___id"];
   }
   if (count($accs)>0 || count($chars)>0)
   {
    $fhandle = fopen("_dat/autobans.dat","w");
    foreach ($accs as $v)  fwrite($fhandle,$v."|:1\r\n"); 
    foreach ($chars as $v) fwrite($fhandle,$v."|:2\r\n");
    fclose($fhandle);
	$h=fopen("_dat/cach/bc","w");  
    fclose($h);
    @unlink("_dat/cach/".$_SESSION["mwclang"]."_ban");
   }
  }
 }
}