<?php if (!defined('insite')) die("no access");
/**
 * user panel
 */
error_reporting(E_ALL);

$checkU = chkc_char($_SESSION["user"],$db);
$planame = $db->query("SELECT memb_name FROM MEMB_INFO Where memb___id='{$_SESSION["user"]}'")->FetchRow();
$content->set('|planame0|', $planame["memb_name"]);
$content->out("lpanel_h.html");



if($checkU>0)
{ 
 $mmm = $db->query("SELECT Name FROM character WHERE AccountID='{$_SESSION["user"]}'");
 $tempo ="";
 while($resultc = $mmm->FetchRow())
 {
  $tempo.="<option value=".$resultc["Name"];
  if(isset($_SESSION["character"]) && $_SESSION["character"] == $resultc["Name"])
   $tempo.=" selected";

  $tempo.=">".$resultc["Name"]."</option>";
 }
 $content->set('|option|', $tempo);
 $content->out("lpanel_c.html");
}
else 
 $content->out("lpanel_fail.html");


$content->set('|bankZ_show|', bankZ_show($db));
$content->set('|wareg_show|', wareg_show($db));
$content->set('|cred_show|', cred_show($db,$config));
$content->set('|getusrmenu|', getusrmenu($content,$config,$db));
$content->out("lpanel_f.html");
