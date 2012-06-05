<?php if (!defined('insite')) die("no access"); 
global $config;
global $db;
global $content;
ob_start();

$planame = $db->fetchrow($db->query("SELECT memb_name FROM MEMB_INFO Where memb___id='".substr($_SESSION["user"],0,10)."'")); 
$content->set('|planame0|', $planame[0]);
$content->out_content("theme/".$config["theme"]."/them/lpanel_h.html");

$checkU = chkc_char(validate($_SESSION["user"]));

if($checkU>0)
{ 
 $mmm=$db->query("SELECT Name FROM character WHERE AccountID='".validate($_SESSION["user"])."'");
 $tempo ="";
 while($resultc = $db->fetchrow($mmm))
 {
  $tempo.="<option value=".$resultc[0]; if($_SESSION["character"]==$resultc[0]){$tempo.=" selected";} $tempo.=">".$resultc[0]."</option>";
 }
 $content->set('|option|', $tempo);
 $content->out_content("theme/".$config["theme"]."/them/lpanel_c.html");
}
else 
 $content->out_content("theme/".$config["theme"]."/them/lpanel_fail.html");


$content->set('|bankZ_show|', bankZ_show());
$content->set('|wareg_show|', wareg_show());
$content->set('|cred_show|', cred_show());
$content->set('|getusrmenu|', getusrmenu());
$content->out_content("theme/".$config["theme"]."/them/lpanel_f.html");
$temp = ob_get_contents();
ob_end_clean();