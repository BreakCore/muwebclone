<?php/** MuWebClone validate functions �*/$parray = array("Newmsg","NewNews","fileconst","hvd","smsdelkey","siteaddress","forum","nlink","flink","SQLq","topadress","picadress","topgcache","mmotoplink");foreach ($_POST as $id=>$val){ if (!in_array($id,$parray)) {  $_POST[$id] = validpost($val);  if ($_POST[$id]!= $val && strlen($_POST[$id])>5) WriteLogs ("MaybePOSTInject","����������� ������� � POST: ".$id."-".$val); }}$garray = array("smasg");foreach ($_GET as $id=>$val){ if (!in_array($id,$garray)) {  $_GET[$id] = validget($val);  if ($_GET[$id]!= $val && strlen($val)>0) WriteLogs ("Inject","����������� ������� � GET: ".$id." = ".htmlspecialchars($val)); }}function WriteLogs($where,$content=" ") {	if(!$where) Die ("<div style='text-align: center; font-size: 18px; color: red;'>Log folder doesn't exist!</div>");	//$h = fopen("C:/intel/Logs/".$where.'['.@date("d_m_Y", time()).'].log', 'a+');	//fwrite($h, "[".@date("H:i:s", time())."] IP [".getenv("REMOTE_ADDR")."] \r\n Message: ". htmlspecialchars($content)." \r\n �����: '".$_SERVER['QUERY_STRING']."' \r\n �����: '".getenv('HTTP_REFERER')."' \r\n �������: '".$_SERVER['HTTP_USER_AGENT']."' \r\n");	//fclose($h);	if($handle = fopen('logZ/'.$where.'['.@date("d_m_Y", time()).'].log', 'a+'))	{		if (fwrite($handle, "[".@date("H:i:s", time())."] IP [".getenv("REMOTE_ADDR")."] \r\n Message: ". htmlspecialchars($content)." \r\n �����: '".$_SERVER['QUERY_STRING']."' \r\n �����: '".getenv('HTTP_REFERER')."' \r\n �������: '".$_SERVER['HTTP_USER_AGENT']."' \r\n\n") === FALSE) fclose($handle);			}} function validate($var){	if (is_array($var))		{			$outval = array();			foreach ($var as $k => $v){$outvar[$k] = validate($v);}			return $outval;		}	else		{			$outval = htmlspecialchars($var, ENT_QUOTES);			$outval = trim($outval);			$outval = checkword($outval);			return $outval;		}			}function checknum($var){ $varr =preg_replace("/[^0-9]/", 0, $var);   return $varr;		}function checkword($var,$id=1){	$badwords = array(";","'","delete","union","update","insert","drop","shutdown","<script>","</script>","script","%","$",",","`","system","/",'chr(', 'chr=', 'chr%20', '%20chr', 'wget%20', '%20wget', 'wget(','cmd=', '%20cmd', 'cmd%20', 'rush=', '%20rush', 'rush%20','union%20', '%20union', 'union(', 'union=', 'echr(', '%20echr', 'echr%20', 'echr=','esystem(', 'esystem%20', 'cp%20', '%20cp', 'cp(', 'mdir%20', '%20mdir', 'mdir(','mcd%20', 'mrd%20', 'rm%20', '%20mcd', '%20mrd', '%20rm','mcd(', 'mrd(', 'rm(', 'mcd=', 'mrd=', 'mv%20', 'rmdir%20', 'mv(', 'rmdir(','chmod(', 'chmod%20', '%20chmod', 'chmod(', 'chmod=', 'chown%20', 'chgrp%20', 'chown(', 'chgrp(','locate%20', 'grep%20', 'locate(', 'grep(', 'diff%20', 'kill%20', 'kill(', 'killall','passwd%20', '%20passwd', 'passwd(', 'telnet%20', 'vi(', 'vi%20','insert%20into', 'select%20', 'fopen', 'fwrite', '%20like', 'like%20','$_request', '$_get', '$request', '$get', '.system', 'HTTP_PHP', '&aim', '%20getenv', 'getenv%20','/etc/password','/etc/shadow', '/etc/groups', '/etc/gshadow','HTTP_USER_AGENT', 'HTTP_HOST', '/bin/ps', 'wget%20', 'uname\x20-a', '/usr/bin/id','/bin/echo', '/bin/kill', '/bin/', '/chgrp', '/chown', '/usr/bin', 'g\+\+', 'bin/python','bin/tclsh', 'bin/nasm', 'perl%20', 'traceroute%20', 'ping%20', '.pl', '/usr/X11R6/bin/xterm', 'lsof%20','/bin/mail', '.conf', 'motd%20', 'HTTP/1.', '.inc.php', 'config.php', 'cgi-', '.eml','file\://', 'window.open', 'javascript\://','img src', 'img%20src','.jsp','ftp.exe','xp_enumdsn', 'xp_availablemedia', 'xp_filelist', 'xp_cmdshell', 'nc.exe', '.htpasswd','servlet', '/etc/passwd', 'wwwacl', '~root', '~ftp', '.js', '.jsp', '.history','bash_history', '.bash_history', '~nobody', 'server-info', 'server-status', 'reboot%20', 'halt%20','powerdown%20', '/home/ftp', '/home/www', 'secure_site, ok', 'chunked', 'org.apache', '/servlet/con','<script', '/robot.txt' ,'/perl' ,'mod_gzip_status', 'db_mysql.inc', '.inc', 'select%20from','select from', 'drop%20', '.system', 'getenv', 'http_', '_php', 'php_', 'phpinfo()', 'DELETE%20FROM', 'MEMB_INFO', 'Character','AccountCharacter', 'MEMB_CREDITS', 'VI_CURR_INFO', '.exe', '<?php', '?>', 'sql=','../','..\\','"','&lt','&gt'); 	foreach($badwords as $word) 	{ 		if(substr_count(strtolower($var), strtolower($word)) > 0) 			{				WriteLogs ("Inject","����������� �������: ".htmlspecialchars($var));				$var=0;			}	if($id==1) $var = trim(preg_replace("/[^a-z[A-Z]0-9_!.-]/","", $var));	}		if (strlen($var)>0) return htmlspecialchars($var);		return $var;		}function is_email($email){   if (function_exists("filter_var")){     $s=filter_var($email, FILTER_VALIDATE_EMAIL);     return !empty($s);   }   $p = '/^[a-z0-9!#$%&*+-=?^_`{|}~]+(\.[a-z0-9!#$%&*+-=?^_`{|}~]+)*';   $p.= '@([-a-z0-9]+\.)+([a-z]{2,3}';   $p.= '|info|arpa|aero|coop|name|museum|mobi)$/ix';   return preg_match($p, $email); }function checkwordm($var){	if (strlen(trim($var))>0)$var = htmlspecialchars($var);	$badwords = array(";","'","insert","update","shutdown","<",">","script","%","$",",","`","(",")","*"); 	$sch=0;	foreach($badwords as $word) { if(substr_count($var, $word) > 0) {$sch++;}}if($sch>0){WriteLogs ("Inject","����������� ������� mail: ".$var);die("attack detected!");}	if (is_email($var)) return $var;	else die("email is wrong!");}function test_xss (){	if ($_SERVER['HTTP_REFERER'])	{		$refer = parse_url($_SERVER['HTTP_REFERER']);		if($refer["host"]!=$_SERVER['HTTP_HOST'])		{			if ($_POST)			{				WriteLogs("XSS_","���������� �� xss �����: '".$_SERVER['REQUEST_URI']."'");				die("Maybe xss, sorry :)");			}		}	}}test_xss ();/*** ������� ��������� ������ ������ POST. �������� ��� ������.* @word - �����, �����, ����������.**/function validpost($word){ //return preg_replace("/[^[:digit:]A-Za-z�-��-�_\:\?\]\[\/\!-+()@=.,& \s]/",'',$word); return htmlspecialchars(preg_replace("/[^[:digit:]A-Za-z�-��-�_@\-+:;,\!?.#()=\s\]\[]/",'',$word));}/*** ������� ��������� ������ ������ GET. �������� ��� ������.* @word - �����, �����, ����������.**/function validget($word){ $word = htmlspecialchars(preg_replace("/[^[:digit:]A-Za-z�-��-�_@.\!\]\[ \-+()=]/",'',$word));  return str_replace(' ','',$word);}function validZ($word){$word1 = htmlspecialchars(preg_replace("/[^[:digit:]A-Za-z�-��-�_@.\]\[\!\:;-+(),.=\s]/",'',$word));if ($word1!=$word)   WriteLogs ("Inject","����������� �������: ���� ".htmlspecialchars($word)." ����� ".$word1);  return str_replace(' ','',$word1);}/** validate functions end �*/?>