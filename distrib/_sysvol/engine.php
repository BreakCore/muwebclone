<?php  if (!defined('insite'))die("no access");
/**
* MuWebClone engine script 1.5.x
* current 1.5.3
**/

/**
* отображение панели пользователя 
*/
function show_login($config,$db,$content)
{
 if(isset($_SESSION["user"]) && isset($_SESSION["pwd"]) && !isset($_REQUEST["usrout"]))
  {
   require_once "_usr/lpanel.php";
   return $temp;
  }
}

/**
* функция аунтивикации
**/
function login($content,$db,$config,$type="open")
{
    if (isset($_REQUEST["usrout"]))
    {
        if (adm_check($db) == 1)
            logs::WriteLogs("site","администратор вышел: ".$_SESSION["user"]);

        unset($_SESSION["user"],$_SESSION["pwd"],$_SESSION["character"]);

        header("Location: ".$config["siteaddress"]);
        die();
    }
    elseif(!isset($_SESSION["user"]) && !isset($_SESSION["pwd"]))
    {
        if(isset($_REQUEST["loginbtn"]))
        {
            if ($config["ucapch"]==1)
            {
                if($_SESSION['qq'] != substr($_POST['uscpt'],0,7) || !isset($_SESSION['qq']))
                {
                    unset($_SESSION['captcha_keystring'],$_SESSION["qq"]);
                    header("Location:".$config["siteaddress"]."/index.php?err=1");
                    die();
                }
            }
            unset($_SESSION['captcha_keystring'],$_SESSION["qq"]);

            $userl = substr(trim($_POST["usrlogin"]),0,10);
            $userp = substr(trim($_POST["usrpwd"]),0,10);

            if ($config["md5use"]=="on")
                $suserp = "[dbo].[fn_md5]('".$userp."','".$userl."')";
            else
                $suserp = "'".$userp."'";

            $check = $db->query(" SELECT
 (SELECT COUNT(*) FROM MWC_admin WHERE name='$userl' and pwd='".md5($userp)."') as isadmin,
 (SELECT COUNT(*) FROM MEMB_INFO WHERE memb___id ='$userl' and memb__pwd = $suserp) as numrow")->FetchRow();

            if ($check["isadmin"]>0) // если админ
            {
                $_SESSION["sadmin"] = $userl;
                $_SESSION["spwd"] = $userp;
                $_SESSION["adm"] = 1;
                $chnick = $db->query("SELECT nick FROM MWC_admin WHERE name='{$_SESSION["sadmin"]}'")->FetchRow();
                $_SESSION["snick"] = $chnick["nick"];
                logs::WriteLogs("site","администратор вошел: ".$_SESSION["sadmin"]);

                if ($check["numrow"] !=1) //если нет пользователя, то сразу в админку
                {
                    header("Location:".$config["siteaddress"]."/control.php");
                    die();
                }
            }

            if ($config["under_rec"]==1 && $check["isadmin"]!=1) //если сайт "временно недоступен", то сбрасываем авторизацию, не админ - нефиг делать
                $check["numrow"] = 0;

            if ($check["numrow"] == 1)
            {
                $_SESSION["user"] = $userl;
                $_SESSION["pwd"]=$userp;
                $now = time();
                $chk_result = $db->query("SELECT mwcban_time, bloc_code,ban_des FROM memb_info WHERE memb___id='$userl'")->FetchRow();

                if ($now >= $chk_result["mwcban_time"] && $chk_result["ban_des"]!="0" && $chk_result["bloc_code"]!=0)/*если время бана вышло*/
                {
                    if ($chk_result["bloc_code"]==0)/*если забанен персонаж*/
                    {
                        $db->query("UPDATE MEMB_INFO SET mwcban_time='0',ban_des='0' WHERE memb___id='{$_SESSION["user"]}'; UPDATE Character SET CtlCode='0' WHERE AccountID='{$_SESSION["user"]}'");
                        logs::WriteLogs("Ban_","Время бана истекло, аккаунт ".$_SESSION["user"]);
                    }
                    else
                    {
                        $upd = $db->query("UPDATE memb_info SET mwcban_time=0, bloc_code=0,ban_des='0' WHERE memb___id='$userl'");
                        $chk_result["bloc_code"] = 0;
                        WriteLogs("Ban_","Время бана истекло, аккаунт ".$_SESSION["user"]);
                    }
                }

                unset($upd);

                if ($chk_result["bloc_code"]==1)
                    return $content->out_content("theme/".$config["theme"]."/them/login_fail.html",1);
                else
                {
                    header("Location:".$config["siteaddress"]."/?up=usercp");
                    die();
                }
            }
            else
            {
                header("Location:".$config["siteaddress"]."/index.php?err=2");
                die();
            }
        }
        elseif (!isset($_REQUEST["loginbtn"]))
        {
            $content->set('|session_name|', session_name());
            $content->set('|session_id|', session_id());
            if ($type!="close")
                return $content->out_content("theme/".$config["theme"]."/them/login.html",1);
            else
                return $content->out_content("theme/".$config["theme"]."/them/r_login.html",1);
        }
        elseif($config["under_rec"]==1)
        {
            if ($type!="close")
                return $content->out_content("theme/".$config["theme"]."/them/login.html",1);
            else
                return $content->out_content("theme/".$config["theme"]."/them/r_login.html",1);
        }
        elseif($type=="inpage")
        {
            return $content->out_content("theme/".$config["theme"]."/them/logininpage.html",1);
        }
        else
            unset($_SESSION["user"],$_SESSION["pwd"]);
    }
}

/**
* проверяет, песонаж относится к аккаунту
**/
function own_char($charnames,$accchar,$db,$config)
{
    $char_nameCHK = $db->query("SELECT count(*) as cnt From Character WHERE Name='".substr($charnames,0,10)."' and AccountID='".substr($accchar,0,10)."'")->FetchRow();
    if ($char_nameCHK["cnt"] <=0)
	{
        header("Location:".$config["siteaddress"]."/?p=not&error=6");
        die("epic fail!");
    }
}

/**
* проверка логина и пароля у пользователя на валидность
*/
function chk_user($config,$db,$type=0)
{
    if (isset($_SESSION["user"]) && isset($_SESSION["pwd"]))
    {
        //$userl = $_SESSION["user"];
        //$userp = $_SESSION["pwd"];

      /*  $use1="";
        if ($config["md5use"]=="off")
            $use1 = "SELECT count(*) as cnt FROM MEMB_INFO Where memb___id='$userl' and memb__pwd='$userp'";
        elseif ($config["md5use"]=="on")
            $use1 = "SELECT count(*) as cnt FROM MEMB_INFO Where memb__pwd=[dbo].[fn_md5]('$userp','$userl') and memb___id='$userl'";
        else
            die('error md5 config!');

        $qregum = $db->query($use1)->FetchRow();
        if ($qregum["cnt"] !=1)
        {//фейл логин
            unset($_SESSION["user"],$_SESSION["pwd"],$_SESSION["character"]);
            return 0;
        }*/

        if(!empty($_SESSION["user"]))
        {
            $chek_ban = $db->query("SELECT bloc_code FROM MEMB_INFO WHERE memb___id='{$_SESSION["user"]}'")->FetchRow();
            if($chek_ban["bloc_code"]==1)
                return 3;
        }
    }
    else
    {
        if ($type==0)
            return 4;
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
function pages($config,$db,$content)
{
    $pmnfile=file("_dat/pm.dat");
    if(isset($_GET["p"]))
    {
        $pagefile = preg_replace("/[^a-zA-Z0-9_-]/i", "", substr($_GET["p"],0,11));
    }
    else
        $pagefile = "home";

    $pracces=0;
    $temp="";
    if(!isset($_GET["p"]) || $pagefile == "home")
    {
        require_once("_sysvol/news.php");
        return $temp;
    }
    elseif($pagefile=="theme")
    {
        require("theme/".$config["theme"]."/index.php");
        return $temp;
    }
    else if(file_exists("pages/".$pagefile.".php"))
    {
        foreach ($pmnfile as $num=>$str)
        {
            $pacces = explode("||",$str);
            if($pacces[0] == $pagefile && $pacces[1] == 1)
            {
                $pracces=1;
                break;
            }
            elseif($pacces[0] == $pagefile && $pacces[1] == 0)
            {
                $pracces=0;
                break;
            }
            else
                $pracces=0;
        }
        if($pracces == 1)
        {
            ob_start();
            require_once("pages/".$pagefile.".php");
            $temp_p = ob_get_contents();
            ob_end_clean();
            if (!isset($temp) || empty($temp))
                return $temp_p;

            return $temp;
        }
        else
            return "<div align='center' valign='center'>No page exists</div>";
    }
    else
    {
        if(isset($_SESSION["user"]))
            logs::WriteLogs("Pages_","несуществующая страница '{$pagefile}', возможно изучают сайт, возможный аккаунт ({$_SESSION["user"]})");
        else
            logs::WriteLogs("Pages_","несуществующая страница '{$pagefile}', возможно изучают сайт, возможный аккаунт");
        require("pages/not.php");
        return $temp;
    }
}

function show_chars($accname,$db)
{
    $accname = substr($accname,0,10);
    $query = $db->query("SELECT Name FROM character WHERE AccountID='$accname'");
    $i=0;
    $names = array();
    while ($result = $query->FetchRow())
    {
        $names[$i]=$result["Name"];
        $i++;
    }
    return $names;
}

function userpages()
{
    if(isset($_GET["up"]))
    {
        $upmnfile = file("_dat/upm.dat");
        $userpage = preg_replace("/[^a-zA-Z0-9_-]/i", "", substr($_GET["up"],0,11));
        $pracces=0;
        if(is_file("_usr/".$userpage.".php"))
        {
            foreach ($upmnfile as $num=>$str)
            {
                $pacces=explode("||",$str);
                if($pacces[0] == $userpage && $pacces[1] == 1)
                {
                    $pracces=1;
                    break;
                }
                elseif($pacces[0] == $userpage && $pacces[0] == 1)
                {
                    $pracces=0;
                    break;
                }
                else
                    $pracces=0;
            }
            if($pracces == 1)
            {
                ob_start();
                require "_usr/".$userpage.".php";
                $tempZ = ob_get_contents();
                ob_end_clean();
                if (!isset($temp))
                    return $tempZ;
                return $temp;
            }
            else
            {
                logs::WriteLogs("DeniedPages_","доступ к неразрешенной страницы пользователя '$userpage', возможный аккаунт (".$_SESSION["user"].")");
                return "<div align='center' valign='center'>Access denied</div>";
            }
        }
        else
        {
            logs::WriteLogs("Pages_","несуществующая страница пользователя '".$userpage."', возможно изучают сайт, возможный аккаунт (".$_SESSION["user"].")");
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

function q_chr_top($class,$config,$db)
{


	
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

/**
 * возвраает строку с названием страницы
 * @param $config
 * @param bool $type true - только текст, false - html код с ссылкой на страницу
 * @return string
 */
function titles($config,$type=false)
{
    ob_start();
    if (isset($type))
        echo $config["server_name"];
    else echo  "<a href='".$config["siteaddress"]."'>".$config["server_name"]."</a>";

    require "lang/".$_SESSION["mwclang"]."/".$_SESSION["mwclang"]."_titles.php";

    if (isset($_GET["p"]))
    {
        $pagefile = preg_replace("/[^a-zA-Z0-9_-]/i", "", substr($_GET["p"],0,11));

        if($pagefile=="theme")
            echo $config["theme"];
        else
        {
            if (!isset($lang["title_".$pagefile]))
            {
                if (isset($type))
                    echo " - title_".$pagefile;
                else
                    echo " - <a href='".$config["siteaddress"]."/?p=".$pagefile."'>title_$pagefile</a>";
            }
            else
            {
                if (isset($type))
                    echo " - ".$lang["title_".$pagefile];
                else
                    echo " - <a href='".$config["siteaddress"]."/?p=".$pagefile."'>".$lang["title_".$pagefile]."</a>";
            }
        }
    }
    else if(isset($_GET["up"]))
    {
        $upagefile = preg_replace("/[^a-zA-Z0-9_-]/i", "", substr($_GET["up"],0,11));
        if ($upagefile!="usercp")
        {
            if(isset($type))
                echo " - ".$lang["title_usercp"];
            else
                echo " - <a href='".$config["siteaddress"]."/?up=usercp'>".$lang["title_usercp"]."</a>";
        }
        if(isset($type))
            echo " - ".$lang["title_".$upagefile];
        else
            echo " - <a href='".$config["siteaddress"]."/?up=".$upagefile."'>".$lang["title_".$upagefile]."</a>";
    }
    $bufer = ob_get_contents();
    ob_end_clean();

    return $bufer;
}


function adm_check ($db,$admin_name=0,$type=0)
{
    if (isset($_SESSION["sadmin"]) && $_SESSION["adm"]==1)
    {
        $validadm = $db->query("SELECT count(*) as cnt FROM MWC_admin WHERE name='{$_SESSION["sadmin"]}' and pwd='".md5($_SESSION["spwd"])."'")->FetchRow();
        if ($validadm["cnt"] == 1)
            return 1;
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
    die("under construction level_check");
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


function print_price($params)
{
    return number_format($params, 2, ',', ' ');
}

/**
 * заменят в сумме букву k на 000
 * @param $word
 * @return mixed
 */
function valute($word)
{
    return str_replace("k","000",$word);
}

/**
 * закрывает часть слова $word звесдочками: пример -> пр***р
 * @param $word
 * @return string
 */
function hide_acc($word)
{
    $length=strlen($word);
    return substr($word,0,1).str_repeat("*",$length-3).substr($word,$length-2);
}

/**
 * подсвечивает сумму, если $numbers 0 то берет вместо суммы банк из базы данных
 * @param $db
 * @param int $numbers 0 - из базы, 1 - то, что подсовывают
 * @param int $type
 * @return int|string
 */
function bankZ_show($db,$numbers=0,$type=0)
{
	if(isset($_SESSION["user"]))
	{
		if ($numbers == 0)
            $Bzen = $db->query("SELECT bankZ FROM memb_info WHERE memb___id='{$_SESSION["user"]}'")->FetchRow();
		else
            $Bzen["bankZ"] = $numbers;
		
		if ($type==1)
            return $Bzen["bankZ"];
		
		if ($Bzen["bankZ"] <1000000)
            $color="color:#E0BA14";
		elseif($Bzen["bankZ"] >=1000000 && $Bzen["bankZ"] < 10000000)
            $color="color:#00AE00";
		elseif($Bzen["bankZ"] >=10000000 && $Bzen["bankZ"] < 100000000)
            $color="color:#428200";
		elseif($Bzen["bankZ"] >=100000000 && $Bzen["bankZ"] < 1000000000)
            $color="color:#800009";
		elseif($Bzen["bankZ"] >=1000000000)
            $color="color:#516EFF";

		$Bzen["bankZ"] = "<span style='$color;font-weight:bold;'>".print_price($Bzen["bankZ"])."</span>";

		return $Bzen["bankZ"];
	}
}

/**
 * покахывает кредиты из базы даннык с подсветкой (зависит от числа)
 * @param $db
 * @param $config
 * @return bool
 */
function cred_show($db,$config)
{
	if(isset($_SESSION["user"]))
	{
		$Bzen = $db->query("SELECT {$config["cr_column"]} FROM {$config["cr_table"]} WHERE {$config["cr_acc"]} = '{$_SESSION["user"]}'")->FetchRow();
		if ($Bzen[$config["cr_column"]] <1000000)
            $color="color:#E0BA14";
		elseif($Bzen[$config["cr_column"]] >=1000000 && $Bzen[$config["cr_column"]] < 10000000)
            $color="color:#00AE00";
		elseif($Bzen[$config["cr_column"]] >=10000000 && $Bzen[$config["cr_column"]] < 100000000)
            $color="color:#428200";
		elseif($Bzen[$config["cr_column"]] >=100000000 && $Bzen[$config["cr_column"]] < 1000000000)
            $color="color:#800009";
		elseif($Bzen[$config["cr_column"]] >=1000000000)
            $color="color:#516EFF";

		$Bzen[$config["cr_column"]] = "<span style='$color;font-weight:bold;'>".print_price($Bzen[$config["cr_column"]])."</span>";

		return $Bzen[$config["cr_column"]];
	}
    return false;
}

/**
 * возвращает количество кредитов
 * @param $db
 * @param $config
 * @param string $accname
 * @return bool|int
 */
function know_kredits($db,$config,$accname="no")
{
	if($accname == "no")
    {
        if(isset($_SESSION["user"]))
        {
            $accname = $_SESSION["user"];
        }
        else
            return false;
    }

    $credits = $db->query("SELECT {$config["cr_column"]} FROM {$config["cr_table"]} WHERE {$config["cr_acc"]} = '{$accname}'")->FetchRow();
    return $credits[$config["cr_column"]];
}

/**
 * возвраает подсвеченное кол-во зен из сундука
 * @param $db
 * @return bool|string
 */
function wareg_show($db)
{
	if(isset($_SESSION["user"]))
	{
		$Bzen = $db->query("SELECT Money FROM warehouse WHERE AccountID ='{$_SESSION["user"]}'")->FetchRow();

		if ($Bzen["Money"] <1000000)
            $color="color:#E0BA14";
		elseif($Bzen["Money"] >=1000000 && $Bzen["Money"] < 10000000)
            $color="color:#00AE00";
		elseif($Bzen["Money"] >=10000000 && $Bzen["Money"] < 100000000)
            $color="color:#428200";
		elseif($Bzen["Money"] >=100000000 && $Bzen["Money"] < 1000000000)
            $color="color:#800009";
		elseif($Bzen["Money"] >=1000000000)
            $color="color:#516EFF";

		$Bzen["Money"] = "<span style='$color;font-weight:bold;'>".print_price($Bzen["Money"])."</span>";

		return $Bzen["Money"];
	}
    return false;
}

/**
 * проверяет, есть ли на аккаунте данный персонаж
 * @param string $login аккаунт
 * @param $db
 * @return int 1/0
 */
function chkc_char($login,$db)
{
 $chk_count =  $db->query("SELECT Name FROM Character WHERE AccountID='".substr($login,0,10)."'")->FetchRow();
 if (isset($chk_count["Name"]) && !empty($chk_count["Name"]))
     return 1;

 return 0;
}

/**
 * @param $db
 * @param $login логин
 * @return int 1/0
 */
function chck_online($db,$login)
{
	$chk_count = $db->query("SELECT ConnectStat FROM memb_stat WHERE memb___id='".substr($login,0,10)."' and ConnectStat>0")->FetchRow();
	if (!isset($chk_count["ConnectStat"]) || empty($chk_count["ConnectStat"]) || $chk_count["ConnectStat"] == 0)
        return 0;
	else
        return 1;
}

function mod_status ($stat)
{
	if($stat==1)
        return "<span style='font-weight:bold;color:green'>On</span>";
    return "<span style='font-weight:bold;color:red'>Off</span>";
}

/**
 * конструтор главного меню
 * @param $config
 * @param $content
 * @return string
 */
function getmenutitles($config,$content)
{
    $loadfile = @file("_dat/menu.dat");
    $nowitime = time();
    $cachtime = @filemtime("_dat/menus/".$_SESSION["mwclang"]."_mainmenu");
    if (empty($loadfile) or !$loadfile)
        return "error menu loading!";
    else
    {
        if(!$cachtime || ($nowitime-$cachtime > 3600))
        {
            include "./lang/".$_SESSION["mwclang"]."/".$_SESSION["mwclang"]."_titles.php";
            ob_start();

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
            write_catch("_dat/menus/".$_SESSION["mwclang"]."_mainmenu",$bufer);
            ob_end_clean();
            return $bufer;
        }
        else
            return file_get_contents( "_dat/menus/".$_SESSION["mwclang"]."_mainmenu");
    }
}

/**
* конструктор меню персонажа
*/
function getcharmenu($config,$type=0, $name="non")
{
    $loadfile = @file("_dat/cmenu.dat");

    if (empty($loadfile) or !$loadfile)
        echo "error menu loading!";
    else
    {
        if ($name != "non")
            $namel = "&chname=".$name;
        unset($name);
        include ("lang/".$_SESSION["mwclang"]."/".$_SESSION["mwclang"]."_titles.php");
        $let_num = count($loadfile);
        $j=0;
        $show = '<table width="100%" align="center" class="lighter1">';
        foreach ($loadfile as $m)
        {
            $showarr = explode("::",$m);
            $showarr[1]=trim($showarr[1]);
            if ($type == 0)
                $show .= "<tr><td height='15' align='center' ><a href='".$config["siteaddress"]."/?up=".$showarr[0].$namel."' >".$lang[$showarr[1]]."</a></td></tr>";
            else if ($type==1)
            {
                if ($j%2 == 0)$show .= "<tr>";
                $show .= "<td";
                if($j==($let_num-1) && ($j % 2) == 0)
                    $show .=" colspan='2' style='text-align: justify;'";
                $show .=" height='15' ><a href='".$config["siteaddress"]."/?up=".$showarr[0].$namel."'>".$lang[$showarr[1]]."</a></td>";
                if ($j%2!=0) $show .="</tr>";
                $j++;
            }
        }
        if ($type==1 && ($let_num % 2)!=0)
            $show .="</tr>";
        $show .="</table>";
        return $show;
    }
}

/**
* конструктор меню пользователя
*/
function getusrmenu($content,$config,$db)
{
    $loadfile = @file("_dat/umenu.dat");
    $nowitime = time();
    $cachtime = @filemtime("_dat/menus/".$_SESSION["mwclang"]."_usermenu");

    if (empty($loadfile) or !$loadfile)
        echo "error menu loading!";
    else
    {
        if(!$cachtime || ($nowitime-$cachtime > 3600))
        {
            ob_start();
            include("lang/".$_SESSION["mwclang"]."/".$_SESSION["mwclang"]."_titles.php");
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

            if (adm_check($db)==1)
                return $bufer.$content->out_content("theme/".$config["theme"]."/them/usermenu_a.html",1);
            return $bufer;
        }
        else
        {
            if (adm_check($db)==1)
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
	/* $bug = str_replace("<","&lt;",$bug);
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
	 $bug = str_replace('\\\"',"&quot;",$bug);*/
	 return htmlspecialchars($bug,ENT_QUOTES);
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
        if (ord($in_text{$i})>191)
        {
            $output.="&#".(ord($in_text{$i})+848).";";
        }
        else
        {
            if (array_search($in_text{$i}, $other)===false)
                $output.=$in_text{$i};
            else
                $output.="&#".array_search($in_text{$i}, $other).";";
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
 * функция вытаскивания контента
 * придумана специально для профессора, чтобы вписывал сервера в write_catch и load_cache
 * @param $path адрес до файла
 * @param bool $istime если true, то возрващает время создания в секундах от 1970 года
 * @return int|string содержимое файла или время создания в секундах с 1970
 */
function load_cache($path,$istime = false)
{
    if(!$istime)
    {
        if(file_exists($path))
        {
            return file_get_contents($path);
        }
        return "no cache file";
    }
    else
    {
        if(file_exists($path))
        {
            return filemtime($path);
        }
        return 0;
    }
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
    if (substr($hex,0,2)=="0x")
        $hex = strtolower(substr($hex,2));
    else
        $hex = urlencode(bin2hex($hex));

    $pixelSize	= $size / 8;
    $img = ImageCreate($size,$size);
    $ftime = @filemtime("imgs/guilds/".$name."-".$size.".png");
    if(file_exists("imgs/guilds/".$name."-".$size.".png") && (time() - $ftime <= $livetime))
    {
        return "<img alt=\"\" src=\"imgs/guilds/".$name."-".$size.".png\">";
    }
    else
    {
        if(@preg_match('/[^a-zA-Z0-9]/',$hex) || $hex == '')
            $hex = '0044450004445550441551554515515655555566551551660551166000566600';
        else
            $hex = stripslashes($hex);

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
function timing($toptime,$content,$type=1)
{
    $forms=array( $content->getVal("caching_mins1"),  $content->getVal("caching_mins2"),  $content->getVal("caching_mins3"));
    if ($type==1)
    {
        $toptime = round(($toptime/60),2);
        echo "<div align=\"center\" class=\"cathtime\">*".$content->getVal("caching_time")." ".$toptime." ".($toptime%10==1&&$toptime%100!=11?$forms[0]:($toptime%10>=2&&$toptime%10<=4&&($toptime%100<10||$toptime%100>=20)?$forms[1]:$forms[2]))."</div>";
    }
    else
        return $toptime." ".($toptime%10==1&&$toptime%100!=11?$forms[0]:($toptime%10>=2&&$toptime%10<=4&&($toptime%100<10||$toptime%100>=20)?$forms[1]:$forms[2]));
}

function know_level($personaz)
{
    if(file_exists("configs/res_cfg.php"))
    {
        require "configs/res_cfg.php";
        if ($personaz>=0 && $personaz<=3)
            return $res["reset_sm_lvl"];
        elseif ($personaz>=16 && $personaz<=19)
            return $res["reset_bk_lvl"];
        elseif ($personaz>=32 && $personaz<=35)
            return $res["reset_elf_lvl"];
        elseif ($personaz>=48 && $personaz<=50)
            return $res["reset_mg_lvl"];
        elseif ($personaz>=64 && $personaz<=66)
            return $res["reset_dl_lvl"];
        elseif ($personaz>=80 && $personaz<=83)
            return $res["reset_bs_lvl"];
        elseif ($personaz>=96 && $personaz<=98)
            return $res["reset_rf_lvl"];
        else
            return 1000;
    }
    return 1000;
}

function know_gpoints($personaz)
{
    if(file_exists("configs/gres_cfg.php"))
    {
        require "configs/gres_cfg.php";
        if ($personaz >= 0 && $personaz <= 3)
            return $gres["greset_dw"];
        elseif ($personaz >= 16 && $personaz <= 19)
            return $gres["greset_dk"];
        elseif ($personaz >= 32 && $personaz <= 35)
            return $gres["greset_elf"];
        elseif ($personaz >= 48 && $personaz <= 50)
            return $gres["greset_mg"];
        elseif ($personaz >= 64 && $personaz <= 66)
            return $gres["greset_dl"];
        elseif ($personaz >= 80 && $personaz <= 83)
            return $gres["greset_s"];
        elseif ($personaz >= 96 && $personaz <= 98)
            return $gres["greset_rf"];
        else die("not supported classtype!");
    }
    else
        die("not supported classtype!");
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
function is_wh($db)
{
    if (isset($_SESSION["user"]))
    {
        $q = $db->query("SELECT count(*) as cnt FROM warehouse WHERE AccountID='".substr($_SESSION["user"],0,11)."'")->FetchRow();
        return $q["cnt"];
    }
    return 0;
}

/*
* узнать максимальне число итемов в магазине для акка
*
*/
function knowmaxit($db)
{
    if (isset($_SESSION["user"]))
    {
        $q = $db->query("SELECT count(*) as cnt FROM web_shop WHERE memb___id='".substr($_SESSION["user"],0,10)."'")->FetchRow();
        return $q["cnt"];
    }
    return 0;
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

function know_csstate($db,$content)
{
    $info_ar=array();
    $CS_GUILD = $db->query("SELECT OWNER_GUILD,CONVERT(CHAR(19), SIEGE_START_DATE, 120) as SIEGE_START_DATE,CONVERT(CHAR(19), SIEGE_END_DATE, 120) as SIEGE_END_DATE FROM MuCastle_DATA")->FetchRow();
    if (!isset($CS_GUILD["OWNER_GUILD"]) || empty($CS_GUILD["OWNER_GUILD"]))
        $info_ar[0]="-/-";
    else
        $info_ar[0] = $CS_GUILD["OWNER_GUILD"];

    $Current_Time = time();

    if((@strtotime($CS_GUILD["SIEGE_START_DATE"])+86400) > $Current_Time)
        $info_ar[1] = $content->getVal("cs_period");       /* 0 00:00 - 0 23:59 */
    elseif	((@strtotime($CS_GUILD["SIEGE_START_DATE"])+432000) > $Current_Time)
        $info_ar[1] = $content->getVal("cs_period1"); /* 1 00:00 - 4 23:59 */
    elseif	((@strtotime($CS_GUILD["SIEGE_START_DATE"])+500400) > $Current_Time)
        $info_ar[1] = $content->getVal("cs_period2"); /* 5 00:00 - 5 19:00 */
    elseif	((@strtotime($CS_GUILD["SIEGE_START_DATE"])+586800) > $Current_Time)
        $info_ar[1] = $content->getVal("cs_period3"); /* 5 19:00 - 6 19:00 */
    elseif	((@strtotime($CS_GUILD["SIEGE_START_DATE"])+594000) > $Current_Time)
        $info_ar[1] = $content->getVal("cs_period4"); /* 6 19:00 - 6 21:00 */
    else
        $info_ar[1] = $content->getVal("cs_period5");

    $info_ar[2] = parsetime($CS_GUILD["SIEGE_START_DATE"],0,"d.m.Y");
    $info_ar[3] = parsetime($CS_GUILD["SIEGE_END_DATE"],0,"d.m.Y");

    return $info_ar;
}

function debug($s)
{
    print "<pre>";print_r($s);print "</pre>";
}
/**
* проверка на администратора
*/
function isadmin()
{
    if (isset($_SESSION["sadmin"]))
    {
        return 1;
       /* global $db;
        $validadm = $db->numrows($db->query("SELECT name,pwd FROM MWC_admin WHERE name='".validate($_SESSION["sadmin"])."' and pwd='".md5($_SESSION["spwd"])."'"));

        if ($validadm==1) return 1;
        else
        {
            unset($_SESSION["sadmin"],$_SESSION["spwd"],$_SESSION["adm"]);
            return 0;
        }  */
    }
    return 0;
} 

/**
* Проверка на баны, разбан в случае, если бан истек
**/
function autobans($db,$nocach=false)
{
    $ntime = @filemtime("_dat/cach/bc");
    $now = time();
    if(!$ntime or time() - $ntime >3600 or $nocach) //проверка раз в час
    {
        $filrb = @file("_dat/autobans.dat");

        if (count($filrb>0))
        {
            foreach($filrb as $m)
            {
                $tempA = explode("|:",$m);
                if ($tempA[1]!=1)
                {
                    $name = $tempA[0];
                    $tt = $db->query("SELECT AccountID FROM Character WHERE Name='{$tempA[0]}'")->FetchRow();
                    $tempA[0]=$tt["AccountID"];
                }

                $chk_result = $db->query("SELECT mwcban_time, bloc_code,ban_des FROM memb_info WHERE memb___id='{$tempA[0]}'")->FetchRow();

                if ($now>=$chk_result["mwcban_time"] && $chk_result["ban_des"]!="0" && $chk_result["mwcban_time"]!=0)/*если время бана вышло*/
                {
                    if ($chk_result["bloc_code"]==0)/*если забанен персонаж*/
                    {
                        $db->query("UPDATE MEMB_INFO SET mwcban_time='0',ban_des='0' WHERE memb___id='{$tempA[0]}'; UPDATE Character SET CtlCode='0' WHERE Name='{$name}'");
                        logs::WriteLogs("Ban_","Время бана истекло, персонаж ".$name);
                    }
                    else
                    {
                        $db->query("UPDATE memb_info SET mwcban_time=0, bloc_code=0,ban_des='0' WHERE memb___id='{$tempA[0]}'");
                        $chk_result["bloc_code"] = 0;
                        logs::WriteLogs("Ban_","Время бана истекло, аккаунт ".$tempA[0]);
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
            while ($show_ar = $query_s->FetchRow())
            {
                if ($show_ar["bloc_code"]==0)
                {
                    $b_chr = $db->query("SELECT Name FROM Character WHERE CtlCode = 1 and AccountID='{$show_ar["memb___id"]}'")->FetchRow();
                    $show_ar["memb___id"] = $b_chr["Name"];
                    $chars[]=$show_ar["memb___id"];
                }
                else
                    $accs[]=$show_ar["memb___id"];
            }

            if (count($accs)>0 || count($chars)>0)
            {
                $fhandle = fopen("_dat/autobans.dat","w");
                foreach ($accs as $v)
                    fwrite($fhandle,$v."|:1\r\n");
                foreach ($chars as $v)
                    fwrite($fhandle,$v."|:2\r\n");
                fclose($fhandle);
                $h=fopen("_dat/cach/bc","w");
                fclose($h);
                @unlink("_dat/cach/".$_SESSION["mwclang"]."_ban");
            }
        }
    }
}