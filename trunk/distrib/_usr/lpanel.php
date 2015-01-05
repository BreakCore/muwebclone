<?php if (!defined('insite')) die("no access");
/**
 * user panel
 */
ob_start();

$planame = $db->query("SELECT memb_name FROM MEMB_INFO Where memb___id='{$_SESSION["user"]}'")->FetchRow();
$content->set('|planame0|', $planame["memb_name"]);
$content->out_content("theme/".$config["theme"]."/them/lpanel_h.html");

$checkU = chkc_char($_SESSION["user"]);

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
 $content->out_content("theme/".$config["theme"]."/them/lpanel_c.html");
}
else 
 $content->out_content("theme/".$config["theme"]."/them/lpanel_fail.html");


$content->set('|bankZ_show|', bankZ_show($db));
$content->set('|wareg_show|', wareg_show($db));
$content->set('|cred_show|', cred_show($db,$config));
$content->set('|getusrmenu|', getusrmenu($content,$config));
$content->out_content("theme/".$config["theme"]."/them/lpanel_f.html");
$temp = ob_get_contents();
ob_end_clean();