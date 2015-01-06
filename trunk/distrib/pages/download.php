<?php 
if (!defined('insite')) die("no access"); 
$nowitime = time();

if (isset($_GET["link"]))
{
 $numb = checknum(substr($_GET["link"],0,3));
 $filedl = @file("_dat/dl.dat");
 if (is_numeric($numb) && strlen($filedl[$numb])>10)
 {
  $tempa = explode("||",$filedl[$numb]);
  $kd = $tempa[5]+1;
	
  $fne = fopen("_dat/dl.dat","w");
  flock($fne,LOCK_EX);
  
  for ($i=0; $i<count($filedl);$i++)
  {
   if ($i!=$numb) @fwrite($fne,$filedl[$i]);
   else 
   {
    if($numb ==(count($filedl)-1)) $wt = $tempa[0]."||".$tempa[1]."||".$tempa[2]."||".$tempa[3]."||".$tempa[4]."||".$kd;
    else $wt = $tempa[0]."||".$tempa[1]."||".$tempa[2]."||".$tempa[3]."||".$tempa[4]."||".$kd.chr(13).chr(10);
    @fwrite($fne,$wt);
   }
  }
  flock($fne,LOCK_UN);
  fclose($fne);
		
  @unlink("/_dat/cach/download");
  if (substr(trim($tempa[3]),0,4)!="http") $tempa[3]="http://".$tempa[3];
  header("location:".$tempa[3]);
 }
}
 $cachtime = load_cache("_dat/cach/".$_SESSION["mwclang"]."_download",true);
if($nowitime-$cachtime > 3)
{
 ob_start();
 $filedl = @file("_dat/dl.dat");
 if(count($filedl)> 0 or $filedl)
 { 
  if(isset($_GET["alar"]))
  {
   $position = checknum(substr($_GET["alar"],0,3));
   $temp = explode("||",$filedl[$position]);
   $titleZ=$temp[0];
   $ggiz=$temp[1];
   $linki=$temp[2];
   $glin=$temp[3];

   if(strlen($titleZ)>1 && strlen($glin)>1 && strlen($ggiz)>1 && strlen($linki)>1)
   {
    if($position ==(count($filedl))){$filedl[$position]= $titleZ."||".$ggiz."||".$linki."||".$glin."||1||".$temp[5];}
    else {$filedl[$position]= $titleZ."||".$ggiz."||".$linki."||".$glin."||1||".$temp[5].chr(13).chr(10);}
    $filedl[$position]= $titleZ."||".$ggiz."||".$linki."||".$glin."||1||".$temp[5];
    $fne = fopen("_dat/dl.dat","w");
    flock($fne,LOCK_EX);
    for ($i=0; $i<count($filedl);$i++){fwrite($fne,$filedl[$i]);}
    flock($fne,LOCK_UN);
    fclose($fne);
   }
  }
  $cs=0;
  if(!$filedl or empty($filedl))
   echo "<div align='center' class='warnms'>".$content->getVal("donwn_empty")."</div>";
   else
   {
  
    $content->out("donloadns_h.html");

    foreach ($filedl as $f=>$n)
    {
     $tempa = explode("||",$n);
     $content->set('|caption|', $tempa[0]);
     $content->set('|nom|', $cs);
     $content->set('|des|', $tempa[1]);
     $content->set('|dowload_dwn|', $tempa[5]);
     $content->set('|file|', $f);
     $content->set('|d_capt|', $tempa[2]);
     $content->out("donloadns_c.html");
     $cs++;
    }
    $content->out("donloadns_f.html");
   }
 }
 $temp = ob_get_contents();
 write_catch("_dat/cach/".$_SESSION["mwclang"]."_download",$temp);
 ob_clean();
}
else
 $temp = load_cache("_dat/cach/{$_SESSION["mwclang"]}_download");
//todo: переписать этот маразм НАФИГ!!!!