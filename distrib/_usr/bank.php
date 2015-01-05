<?php if (!defined('insite')) die("no access"); 
global $db;
global $content;
global $config;
require "configs/bank_cfg.php";
ob_start();

//header
$content->out_content("theme/".$config["theme"]."/them/bank_h.html");

//center
switch(validate(substr($_GET["step"],0,10)))	
{
 //из банка в сундук
 case "zen2war":
  $content->out_content("theme/".$config["theme"]."/them/bank_b2w.html");
 if($_REQUEST["waregok"])
  {
   if(strlen($_POST["Zen2warhouse"])>0 && strlen($_POST["Zen2warhouse"])<20)
   {
    $zenbank = $db->fetchrow($db->query("SELECT bankZ FROM memb_info WHERE memb___id = '".validate($_SESSION["user"])."'"));
    $countzen = checknum(valute($_POST["Zen2warhouse"]));

    $whatlimit = $db->fetchrow($db->query("SELECT Money FROM warehouse WHERE AccountID='".validate($_SESSION["user"])."'"));	
    $vwareg = $zenbank[0] - $countzen;
    if ($vwareg<0){header("Location:".$config["siteaddress"]."/?p=not&error=4");}
    else
    {
     if (($countzen+$whatlimit[0])>$bank["maxwzen"] && $countzen>=0)
     {
	$vich =($countzen+$whatlimit[0])-$bank["maxwzen"];
	$countzen1 = $countzen - $vich;
     }
     else
        $countzen1=$countzen;
    if ($countzen1>0)
    {
     if($db->query("UPDATE memb_info SET bankZ=bankZ-".$countzen1." WHERE memb___id ='".validate($_SESSION["user"])."' 
     UPDATE warehouse SET Money=Money+".$countzen1." WHERE AccountID ='".validate($_SESSION["user"])."'"))
     {
     WriteLogs ("Bank_","јккаунт ".$_SESSION["user"]." зен в сундук в банке было: ".$zenbank[0].", осталось: ".$vwareg.", сн¤то: ".$countzen);
     unset($checkstep, $countzen, $_REQUEST["waregok"]);
     $vwareg= -1;
     header("Location:".$config["siteaddress"]."/index.php?up=bank");
     die();
     }
     echo "ошибка!";
    }
    }
   }
   else
   {
    header("Location:".$config["siteaddress"]."/?p=not&error=5");
    die(); 
   }
  }
 break;
 //из сундука в банк
 case "zen2bank":
  $content->out_content("theme/".$config["theme"]."/them/bank_w2b.html");
  if($_REQUEST["bankok"])
  {
   if(strlen($_POST["Zen2bank1"])>0 && strlen($_POST["Zen2bank1"])<20)
   {
    $countzen = checknum(valute($_POST["Zen2bank1"]));
    $zenwar = $db->fetchrow($db->query("SELECT Money FROM warehouse WHERE AccountID='".validate($_SESSION["user"])."'"));
    $vwareg = $zenwar[0] - $countzen;
    if($vwareg>=0 && $countzen>=0)
    {
     $updatess =$db->query("UPDATE memb_info SET bankZ=bankZ+$countzen WHERE memb___id ='".validate($_SESSION["user"])."' UPDATE warehouse SET Money=Money-$countzen WHERE AccountID ='".$_SESSION["user"]."'");
     WriteLogs ("Bank_","јккаунт ".$_SESSION["user"]." зен в банк в инвентаре было: ".$zenwar[0].", осталось: ".$vwareg);
     unset($updatess, $checkstep, $_REQUEST["bankok"],$countzen);
     header("Location:".$config["siteaddress"]."/index.php?up=bank");
    }
    else
    {
     header("Location:".$config["siteaddress"]."/?p=not&error=4");
     die(); 
    }
   }
   else
   {
    die();
    header("Location:".$config["siteaddress"]."/?p=not&error=5");
   }
  }
 break;
 case "bank_char":
  if ($_REQUEST["sel_chr"] && $_REQUEST["type_tr"] && $_REQUEST["do_trans"])
  {
   $ned_crh = validate(substr($_POST["sel_chr"],0,11));
   $money = checknum(valute(substr($_POST["m_tr"],0,11)));
   own_char($ned_crh,$_SESSION["user"]);
   $invzen = $db->fetchrow($db->query("Select Money FROM Character WHERE Name='".$ned_crh."'"));
   $bankzen = $db->fetchrow($db->query("Select bankZ FROM MEMB_INFO WHERE memb___id='".validate($_SESSION["user"])."'"));
   if ($_POST["type_tr"]=="bank")// in bank
   {
    if ($money>0 && $invzen[0]-$money >=0)
    {
     $upd_q = "UPDATE Character SET Money=Money-".$money." Where Name='".$ned_crh."'  UPDATE MEMB_INFO SET bankZ=bankZ+".$money." Where memb___id='".$_SESSION["user"]."'";
     $msg_log = "Zen из инвентар¤ ".$ned_crh." в банк";		  
    }
    else 
    {
     header("Location:".$config["siteaddress"]."/?p=not&error=16");
     die();
    }
   }
   elseif($_POST["type_tr"]=="inventory")//in inventory
   {
    if ($money>0 && ($bankzen[0]-$money >=0) && ($invzen[0]+$money <=2000000000))
    {
     $msg_log = "Zen из банка ".$ned_crh." в инвентарь";		  
     $upd_q = "UPDATE Character SET Money=Money+".$money." Where Name='".$ned_crh."' UPDATE MEMB_INFO SET bankZ=bankZ-".$money." Where memb___id='".$_SESSION["user"]."'";
    }
    else
    {    
     header("Location:".$config["siteaddress"]."/?p=not&error=16");
     die();
    }
   }
   else die("no parametr!");
  //-
  if($db->query($upd_q))
  {				
   echo "<script>alert('Done');</script>";
   WriteLogs ("Bank_",$msg_log);
   header("Location:".$config["siteaddress"]."/index.php?up=bank");
   die();
  }
 }

 $onchar="";
 $pers = $db->query("Select Name, Money FROM Character WHERE  AccountID='".validate($_SESSION["user"])."'");
 
 while ($ch_date=$db->fetchrow($pers))	$onchar.="<option value='".$ch_date[0]."'>".$ch_date[0].", ".print_price($ch_date[1])." Zen</value>";
 
 $content->set("|onselect|",$onchar);
 $content->out_content("theme/".$config["theme"]."/them/bank_chars.html");
 break;
 
 default: $content->out_content("theme/".$config["theme"]."/them/bank_links.html");
}
//footer
$content->out_content("theme/".$config["theme"]."/them/bank_f.html");
$temp = ob_get_contents();
ob_end_clean();