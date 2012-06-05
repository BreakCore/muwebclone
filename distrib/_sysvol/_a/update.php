<?php
/*
0 title
1 des
2 number
3 ver
*/
global $config;
require "_sysvol/imbrowser.php";
if ($_REQUEST["refresh"]) @unlink("_dat/updates/updlist");
$time = @filemtime("_dat/updates/updlist");


$ver = @file("_dat/v");
($ver[0]>0)? true : $ver[0]=0;
$nupdate = @file("_dat/updates.dat");
foreach ($nupdate as $n=>$v)
{
 $tmpz = explode(",",$v);
 $anupd[]=$tmpz[0]; //номер патча
 $avupd[]=$tmpz[1]; //версия сайта
}



if(!$time or time() - $time >3600) //update updates list once per hour
{
 $str=unicontent("http://muwebclone.googlecode.com/svn/trunk/patches/updatelist.mwc");
 $handle = fopen("_dat/updates/updlist","w");
 fwrite ($handle,$str);
 fclose ($handle);
}
$file = @file("_dat/updates/updlist"); 
$count = @count($file);

ob_start();
if($count>0)
{
 if (isset($_GET["uid"]))//установка модуля
 {
  $id = checknum($_GET["uid"]);
  $array = explode("|",$file[$id]);
  if(trim((int)$array[3])==(int)$ver[0])//версия сайта подходит
    eval(unicontent("http://muwebclone.googlecode.com/svn/trunk/patches/".(int)$ver[0]."/patch".trim($array[2])."/patch.mwc"));
  
  else echo "error during update".$id;
 }
 else if (!isset($_GET["uid"]))
 {
  echo "<table align='center' valign=top' border='0' width='90%'>";
  for($i=0;$i<$count;$i++)
  {
   $array = explode("|",$file[$i]);

   if($ver[0]==trim($array[3]))//если версии совпадают
   {
    //@in_array((int)$array[3],$avupd) &&
    if (@in_array((int)$array[2],$anupd))//если есть в списке установленных
     $insert="<div align='center' style='font-weight:bold;color:green'>Installed</div>";
    else if ($array[2]==-1)//новость
   	 $insert="<div align='center' style='font-weight:bold;color:red'>News</div>";
	else if (((int)$array[2])>0 && @in_array((int)$array[2]-1,$anupd) || ((int)$array[2])==0)
	 $insert="<form method='POST' action='".$config["siteaddress"]."/control.php?page=update&uid=".$i."'><input type='submit' value='Install' class='button'></form>";
	else
     $insert="<div align='center' style='font-weight:bold;font-style:italic;font-size:12px;'>First install patch ".((int)$array[2]-1)." </div>";
   
   echo "<tr><td style='font-weight:bold;' colspan='2'>".$array[0]."</td></tr>
       <tr><td style='font-style:italic;'>".$array[1]."</td><td align='center'>".$insert."</td></tr>
	   <tr><td height='20' colspan='2'>&nbsp;</td></tr>";
   }
  }
  echo "<tr><td colspan='2' align='center'><form method='POST' action='".$config["siteaddress"]."/control.php?page=update'><input type='submit' value='Refresh' name='refresh' class='button'></form></td></tr></table>";
 }
}
else echo "<div align='center'>No available updates.</div>";
$temp = ob_get_contents();
ob_end_clean(); 
