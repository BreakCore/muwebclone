<?php
session_start();
header("Content-type: text/html; charset=windows-1251");
if ($_SESSION["adm"]>0 && $_SESSION["sadmin"])
{
define ('insite', 1);
define ('inpanel', 1);
require ("_sysvol/security.php");
require "_sysvol/fsql.php";
require ('opt.php');
require "_sysvol/engine.php";
require '_sysvol/webv.php';
require '_sysvol/them.php';
require "_sysvol/amod.php";
$content=new content("admin",$_SESSION["mwclang"],1);
 $content->set('|siteaddress|', $config["siteaddress"]);
$db = new Connect ($config["ctype"], $config["db_host"], $config["db_name"], $config["db_user"], $config["db_upwd"],$config["odbc_driver"],$config["debug"]); 

if($_GET["action"])
   echo a_modul($_GET["action"]);
elseif($_GET["title"])
{
  show_t($_GET["title"]);
}
elseif(strlen(trim($_GET["chat"]))>0)
{
 switch($_GET["chat"])
 {
  case "add":
    $time = time();
    
   $badwords = array("'",'"',"\"","/",'chr(', 'chr=', 'chr%20', '%20chr', 'wget%20', '%20wget', 'wget(','cmd=', '%20cmd', 'cmd%20', 'rush=', '%20rush', 'rush%20','union%20', '%20union', 'union(', 'union=', 'echr(', '%20echr', 'echr%20', 'echr=','esystem(', 'esystem%20', 'cp%20', '%20cp', 'cp(', 'mdir%20', '%20mdir', 'mdir(','mcd%20', 'mrd%20', 'rm%20', '%20mcd', '%20mrd', '%20rm','mcd(', 'mrd(', 'rm(', 'mcd=', 'mrd=', 'mv%20', 'rmdir%20', 'mv(', 'rmdir(','chmod(', 'chmod%20', '%20chmod', 'chmod(', 'chmod=', 'chown%20', 'chgrp%20', 'chown(', 'chgrp(','locate%20', 'grep%20', 'locate(', 'grep(', 'diff%20', 'kill%20', 'kill(', 'killall','passwd%20', '%20passwd', 'passwd(', 'telnet%20', 'vi(', 'vi%20','insert%20into', 'select%20', 'nigga(', '%20nigga', 'nigga%20', 'fopen', 'fwrite', '%20like', 'like%20','$_request', '$_get', '$request', '$get', '.system', 'HTTP_PHP', '&aim', '%20getenv', 'getenv%20','new_password', '&icq','/etc/password','/etc/shadow', '/etc/groups', '/etc/gshadow','HTTP_USER_AGENT', 'HTTP_HOST', '/bin/ps', 'wget%20', 'uname\x20-a', '/usr/bin/id','/bin/echo', '/bin/kill', '/bin/', '/chgrp', '/chown', '/usr/bin', 'g\+\+', 'bin/python','bin/tclsh', 'bin/nasm', 'perl%20', 'traceroute%20', 'ping%20', '.pl', '/usr/X11R6/bin/xterm', 'lsof%20','/bin/mail', '.conf', 'motd%20', 'HTTP/1.', '.inc.php', 'config.php', 'cgi-', '.eml','file\://', 'window.open', '<SCRIPT>', 'javascript\://','img src', 'img%20src','.jsp','ftp.exe','xp_enumdsn', 'xp_availablemedia', 'xp_filelist', 'xp_cmdshell', 'nc.exe', '.htpasswd','servlet', '/etc/passwd', 'wwwacl', '~root', '~ftp', '.js', '.jsp', '.history','bash_history', '.bash_history', '~nobody', 'server-info', 'server-status', 'reboot%20', 'halt%20','powerdown%20', '/home/ftp', '/home/www', 'secure_site, ok', 'chunked', 'org.apache', '/servlet/con','<script', '/robot.txt' ,'/perl' ,'mod_gzip_status', 'db_mysql.inc', '.inc', 'select%20from','select from', 'drop%20', '.system', 'getenv', 'http_', '_php', 'php_', 'phpinfo()', 'DELETE FROM', 'MEMB_INFO', 'Character','AccountCharacter', 'MEMB_CREDITS', 'VI_CURR_INFO', '.exe', '<?php', '?>', 'sql=','../','..\\','"','&lt','&gt'); 
   $text= $_GET["smasg"];
   if (strlen($text)>0)
   {
    foreach($badwords as $word) 
    { 
     if(substr_count(strtolower($text), strtolower($word)) > 0) 
     {
      WriteLogs ("ALARMA_Inject","запрещенные символы в чате админов ". $_GET["smasg"]);
      $text = "sorry, i using dangeruos symbols! :(";
      break;
     }
    }

    if($db->query("INSERT INTO MWC_chat (message,time,memb___id,nick)VALUES('".cyr_code(htmlspecialchars($text))."','".$time."','".$_SESSION["sadmin"]."','".$_SESSION["snick"]."')"))
       echo "[".@date("H:i",$time)."]".$_SESSION["snick"].": ".$text."\r\n";
    else
       echo "error!";
   }
  break;
  case "refresh":
   $needt = time()-10;//за 10 секунд
   $query = $db->query("SELECT * FROM MWC_chat WHERE time>=".$needt." and memb___id!='".$_SESSION["sadmin"]."'");
   while($result = $db->fetchrow($query))
   {
    if (strlen($result[0])>0)
        echo "[".@date("H:i",$result[1])."]".$result[3].": ".$result[0]."\r\n";
   }
  break;
 }
}
$db->close();
}else require "errors/er403.html";