<?php if (!defined('insite')) die("no access");
require "configs/referal_cfg.php";
error_reporting(E_ALL);
/**
 * строим панельку дл¤ вывода
 * @param массив с именами персонажей $users
 * @return возвращает изображение списка выбора и кнопку им¤ кнопки addwict, им¤ списка selchar
 */
function getusrbox($users,$content,$config)
{
 $onreturn="";
 foreach ($users as $value) $onreturn.="<option value='".$value."'>".$value."</option>";
 $content->set("|input|",$onreturn);
 
 return $content->out("ref_form.html",1);
}


//ob_start();
$out = "";
$accname = substr($_SESSION["user"],0,10);
$about = $db->query("SELECT memb_name,mail_addr,fpas_answ,rdate FROM memb_info WHERE memb___id='$accname'")->FetchRow();

$content->set('|name|', $about["memb_name"]);
$content->set('|mail|', $about["mail_addr"]);
$content->set('|answer|', $about["fpas_answ"]);
$content->set('|date|', @date("Y-m-d, G:i",$about["rdate"]));

$content->out("usercp_h.html");

$characters = $db->query("SELECT Name,class,clevel FROM character WHERE AccountID='$accname'");
$content->out("usercp_c_h.html");
$getpr=0;

while($res = $characters->FetchRow())
{
    $content->set('|classpicture|', classpicture($res["class"]));
    $content->set('|Name|', $res["Name"]);
    $charmas[]=$res["Name"];
    $content->set('|Class|', classname($res["class"]));
    $content->set('|Level|', $res["clevel"]);
    $content->set('|getcharmenu|', getcharmenu($config,0,$res["Name"]));
    $content->out("usercp_c_c.html");
    if ($referal["minlvl"]<=$res["clevel"])
        $getpr=1;
}
$content->out("usercp_c_f.html");


$invited = $db->query("Select count(*) as cnt FROM MWC_invite Where memb___id='$accname ' and done!='1'")->FetchRow();


if($invited["cnt"] > 0)//выдача плюшек
{
    if ( $getpr==1)
    {
        $who = $db->query("SELECT inviter FROM MWC_invite WHERE memb___id='$accname'")->FetchRow();
        $db->query("UPDATE MEMB_INFO SET ref_acc=ref_acc+1 WHERE memb___id='{$who["inviter"]}';UPDATE MWC_invite SET done='1' WHERE memb___id='$accname'");
    }
}

$getprice = $db->query("SELECT count(*) as cnt FROM MWC_invite Where memb___id='".$accname ."' and done='1'")->FetchRow();
if ($getprice["cnt"] > 0)//если приглашенный и готов получать приз
{
    if (!isset($_REQUEST["addwict"]))
        $out="<tr><td colspan='2'>".getusrbox($charmas,$content,$config)."</td></tr>";
    else
    {
        $character = substr($_POST["selchar"],0,10);
        own_char($character,$accname);
        $db->query("UPDATE Character Set LevelUpPoint=LevelUpPoint+".floor($referal["stats"]/2)." WHERE Name='".$character."' UPDATE MEMB_INFO SET bankZ=bankZ+".floor($referal["zen"]/2)." WHERE memb___id='".$accname."' DELETE FROM MWC_invite WHERE  memb___id='".$accname ."' and done='1'");
        logs::WriteLogs("RefSys_", "ѕриглашенный $character получил вознаграждение");
        header("Location:".$config["siteaddress"]."/?up=usercp");
    }
}
else //не приглашен
{
    $wins = $db->query("SELECT count(*) as cnt FROM MEMB_INFO WHERE memb___id='".$accname."'")->FetchRow();
    if ($wins["cnt"]>0)//если пригласивший
    {
        if (!isset($_REQUEST["addwict"]))
            $out="<tr><td colspan='2'>".getusrbox($charmas,$content,$config)."</td></tr>";
        else
        {
            $character = validate(substr($_POST["selchar"],0,10));
            own_char($character,$accname);
            $db->query("UPDATE Character Set LevelUpPoint=LevelUpPoint+".($referal["stats"]*$wins["cnt"])." WHERE Name='".$character."' UPDATE MEMB_INFO SET bankZ=bankZ+".($referal["zen"]*$wins["cnt"]).",ref_acc=ref_acc-".$wins["cnt"]."  WHERE memb___id='".$accname."'");
            WriteLogs("RefSys_", "ѕригласивший ".$character." получил вознаграждение");
            header("Location:".$config["siteaddress"]."/?up=usercp");
        }
    }
    else
    {
        $nr = $db->query("SELECT COUNT (*) as cnt FROM MWC_invite Where inviter='".$accname ."'")->FetchRow();
        if ($nr["cnt"]>0)
            $content->set("|nothing|",$content->getVal("rowZ")." ".$nr["cnt"]);
        $out= $content->out("ref_empt.html",1);
    }

}

$content->set("|data|", $out);


$content->out("usercp_f.html");