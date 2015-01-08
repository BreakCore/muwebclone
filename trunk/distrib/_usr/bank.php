<?php if (!defined('insite')) die("no access");
require "configs/bank_cfg.php";
error_reporting(E_ALL);

//header
$content->out("bank_h.html");
if(!isset($_GET["step"]))
 $_GET["step"] = "";
//center
switch(substr($_GET["step"],0,10))
{
 //из банка в сундук
 case "zen2war":
  $content->out("bank_b2w.html");
 if(isset($_REQUEST["waregok"]))
  {
   if(isset($_POST["Zen2warhouse"]))
   {
    $zenbank = $db->query("
SELECT
mi.bankZ,
wh.Money
FROM
memb_info mi,
warehouse wh
WHERE mi.memb___id = '{$_SESSION["user"]}' and wh.AccountID='{$_SESSION["user"]}'")->FetchRow();

    $countzen = (int)valute($_POST["Zen2warhouse"]);

    $whatlimit = $db->fetchrow($db->query("SELECT Money FROM warehouse WHERE AccountID='".validate($_SESSION["user"])."'"));	
    $vwareg = $zenbank["bankZ"] - $countzen;
    if ($vwareg<0)
     header("Location:".$config["siteaddress"]."/?p=not&error=4");
    else
    {
     if (($countzen+$zenbank["Money"]) > $bank["maxwzen"] && $countzen>=0)
     {
      $vich =($countzen+$zenbank["Money"]) - $bank["maxwzen"];
      $countzen1 = $countzen - $vich;
     }
     else
        $countzen1=$countzen;
    if ($countzen1>0)
    {
     if($db->query("UPDATE memb_info SET bankZ=bankZ - $countzen1 WHERE memb___id ='{$_SESSION["user"]}'
     UPDATE warehouse SET Money=Money + $countzen1  WHERE AccountID ='{$_SESSION["user"]}'"))
     {
      logs::WriteLogs ("Bank_","јккаунт ".$_SESSION["user"]." зен в сундук в банке было: ".$zenbank["bankZ"].", осталось: ".$vwareg.", снято: ".$countzen);
      header("Location:".$config["siteaddress"]."/index.php?up=bank");
      die();
     }
     echo "error";
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
  $content->out("bank_w2b.html");
  if(isset($_REQUEST["bankok"]))
  {
   if(isset($_POST["Zen2bank1"]))
   {
    $countzen = (int)valute($_POST["Zen2bank1"]);
    $zenwar = $db->query("SELECT Money FROM warehouse WHERE AccountID='".validate($_SESSION["user"])."'")->FetchRow();
    $vwareg = $zenwar["Money"] - $countzen;
    if($vwareg>=0 && $countzen>=0)
    {
     $updatess =$db->query("UPDATE memb_info SET bankZ=bankZ+$countzen WHERE memb___id ='{$_SESSION["user"]}' UPDATE warehouse SET Money=Money-$countzen WHERE AccountID ='{$_SESSION["user"]}'");
     logs::WriteLogs ("Bank_","јккаунт ".$_SESSION["user"]." зен в банк в инвентаре было: ".$zenwar[0].", осталось: ".$vwareg);
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
    header("Location:".$config["siteaddress"]."/?p=not&error=5");
    die();
   }
  }
 break;
 case "bank_char":
  if (isset($_REQUEST["sel_chr"]) && isset($_REQUEST["type_tr"]) && isset($_REQUEST["do_trans"]))
  {
   $ned_crh = substr($_POST["sel_chr"],0,11);
   $money = (int)valute(substr($_POST["m_tr"],0,11));
   own_char($ned_crh,$_SESSION["user"],$db,$config);
   $zenbank = $db->query("
SELECT
mi.bankZ,
ch.Money
FROM
memb_info mi,
Character ch
WHERE mi.memb___id = '{$_SESSION["user"]}' and ch.Name='".$ned_crh."'")->FetchRow();


   $invzen = $db->fetchrow($db->query("Select Money FROM Character WHERE Name='".$ned_crh."'"));
   $bankzen = $db->fetchrow($db->query("Select bankZ FROM MEMB_INFO WHERE memb___id='".validate($_SESSION["user"])."'"));
   if (isset($_POST["type_tr"]) && $_POST["type_tr"] == "bank")// in bank
   {
    if ($money>0 && $zenbank["Money"]-$money >=0)
    {
     $upd_q = "UPDATE Character SET Money=Money-$money Where Name='$ned_crh';  UPDATE MEMB_INFO SET bankZ=bankZ+$money Where memb___id='{$_SESSION["user"]}'";
     $msg_log = "Zen из инвентаря $ned_crh в банк";
    }
    else 
    {
     header("Location:".$config["siteaddress"]."/?p=not&error=16");
     die();
    }
   }
   elseif($_POST["type_tr"]=="inventory")//in inventory
   {
    if ($money>0 && ($zenbank["bankZ"]-$money >=0) && ($zenbank["Money"]+$money <=2000000000))
    {
     $msg_log = "Zen из банка $ned_crh в инвентарь";
     $upd_q = "UPDATE Character SET Money=Money+$money Where Name='$ned_crh'; UPDATE MEMB_INFO SET bankZ=bankZ-$money Where memb___id='{$_SESSION["user"]}'";
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
   logs::WriteLogs ("Bank_",$msg_log);
   header("Location:".$config["siteaddress"]."/index.php?up=bank");
   die();
  }
 }

 $onchar="";
 $pers = $db->query("Select Name, Money FROM Character WHERE  AccountID='{$_SESSION["user"]}'");
 
 while ($ch_date = $pers->FetchRow())
  $onchar.="<option value='".$ch_date["Name"]."'>".$ch_date["Name"].", ".print_price($ch_date["Money"])." Zen</value>";
 
 $content->set("|onselect|",$onchar);
 $content->out("bank_chars.html");
 break;
 
 default: $content->out("bank_links.html");
}
//footer
$content->out("bank_f.html");
