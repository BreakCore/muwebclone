<?php if (!defined('insite')) die("no access"); 
global $config;
ob_start();
require "configs/gres_cfg.php";

if($gres["greset"]==1)
{	
 global $db;
 global $content;
 
 require "configs/res_cfg.php";
 require "configs/top100_cfg.php";
 if ($_GET["chname"])
 {
  $reschar = substr($_GET["chname"],0,10);
  own_char($reschar,$_SESSION["user"]);
  $_SESSION["character"] = $reschar;
 }
 elseif ($_POST["selectedchar"])
 {
  $reschar = substr($_POST["selectedchar"],0,10);
  own_char($reschar,$_SESSION["user"]);
  $_SESSION["character"] = $reschar; 
 }
 elseif(strlen($_SESSION["character"])>0)
 {
  $reschar = $_SESSION["character"];
  own_char($reschar,$_SESSION["user"]);
 }
 else
 { 
  header("Location:".$config["siteaddress"]."/?up=usercp");
  die();
 }

 $empt_inv = "FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF";
 $check_char = $db->fetchrow($db->query("Select cLevel,class,".$top100["t100res_colum"].",Money,LevelUpPoint,pklevel,gr_res,inventory FROM Character Where Name='".$reschar."'"));

 if ($gres["greset_type"]==1)$grzen = ($check_char[6]+1)*$gres["greset_zen"];
 elseif ($gres["greset_type"]==2)$grzen = $gres["greset_zen"];
 elseif ($gres["greset_type"]==3)$grzen = 0;
 


 if ($_REQUEST["do_greset"])
 {
  $reserror = 0;
  if ($check_char[0]>=$gres["greset_lvl"] && $check_char[2]>=$gres["greset_res"])
  {
    $reserror=0;
    if($res["reset_pk"]==1){if($check_char[5]>3){$reserror=1; header("Location:".$config["siteaddress"]."/?p=not&error=0");}}
		
    $queryres = "UPDATE Character SET ";
    $check_char[6]+=1;
    /*zen*/
    $knovzen = $db->fetcharray($db->query("SELECT bankZ FROM memb_info WHERE memb___id='".$_SESSION["user"]."'"));
    if ($knovzen["bankZ"]<$grzen)
	{
	 $reserror = 1; 
	 header("Location:".$config["siteaddress"]."/?p=not&error=1");
	}
    
	/*points*/
    $queryres.="Strength=25,Dexterity=25,Vitality=25,Energy=25"; if($check_char[1]== 64 or $check_char[1]==65 or $check_char[1]==66){$queryres.= ", Leadership=".(25+($config["reset_com"]*$check_char[2]));}
    $queryres.= ", LevelUpPoint =".(know_gpoints($check_char[1])*$check_char[6]);
    if ($gres["greset_clean"]==1) $queryres.=", Inventory=0x".$empt_inv.", MagicList=NULL";
    $queryres.=", ".$top100["t100res_colum"]." = 0, gr_res = gr_res+1, cLevel=1,Experience=0  WHERE Name='".$reschar."' UPDATE MEMB_INFO SET bankZ = bankZ-".$grzen." WHERE memb___id='".$_SESSION["user"]."'";
  }
  else
  {
   $reserror = 1; 
   header("Location:".$config["siteaddress"]."/?p=not&error=2");
   die();
  }//lvl
  
  if($reserror==0 && chck_online($_SESSION["user"])==0)
  { 
   if ($gres["greset_reward"]==1)
   {
    if ($config["ctype"]=="SQL")
    {
      $Wquer = $db->query("declare @Items varbinary(1920); set @Items = (SELECT Items FROM warehouse WHERE AccountID='".validate($_SESSION["user"])."'); print @Items");
      $plase = smartsearch($db->lastmsg(),1,1);
      $Wquery[0] = substr($db->lastmsg(),2);
    }
    else
    {
     $Wquery = $db->fetchrow($db->query("SELECT Items FROM warehouse WHERE AccountID='".validate($_SESSION["user"])."'"));
     $plase = smartsearch($Wquery[0],1,1);//check warehouse for free space
     $Wquery[0]=strtoupper(urlencode(bin2hex($Wquery[0])));
    }
    
    
    if ($plase!=-1) //if all ok
    {
     $ring = array(8,9,21,22,23,24);	
     $exeopt = array(2,4,6,8,10,12,14,16,18,30,32,34,36,40,58,60); 
     $ilvl = rand(1,11);// item's level
     $n = rand (0,5); // what is ring?) 
     $getserial = $db->fetchrow($db->query("exec WZ_GetItemSerial"));
     $git["id"] =sprintf("%02X", $ring[$n],00); 
     $git["group"] ="D0"; 
     $git["opt"]= sprintf("%02X",((8*$ilvl)+3),00);
     $git["exopt"]=sprintf("%02X",$exeopt[rand(0,15)],00);
     $git["serial"] = sprintf("%08X",$getserial[0],00);
	
     $newitem = $git["id"].$git["opt"].$git["serial"]."00".$git["exopt"]."00".$git["group"]."00"."0000000000"; //generaton new item hex-code
     $out_nitems="";
     
     for ($i=0;$i<120;$i++)
     {				
	$n_items = substr($Wquery[0],($i*32), 32);
	if ($i==$plase) $out_nitems.= $newitem;
	else $out_nitems.=$n_items;	
     }
     $newh = $db->query("UPDATE warehouse SET Items=0x".$out_nitems." WHERE AccountID='".validate($_SESSION["user"])."'");
     WriteLogs ("GReset_","������� ".$_SESSION["user"]." ��������: ".$_SESSION["character"]." ������ ������ ������, ������� ������������� � �������� � ������. Hex:".$newitem);
   }
   else 
     die("no free space in warehouse!");	
  }
  elseif($gres["greset_reward"]==2)
  {
   if ($db->query("UPDATE ".$config["cr_table"]." SET ".$config["cr_column"]." = ".$config["cr_column"]."+ '".$gres["credits_reward"]."' WHERE ".$config["cr_acc"]."='".$_SESSION["user"]."'"))
   WriteLogs ("GReset_","������� ".$_SESSION["user"]." ��������: ".$_SESSION["character"]." ������ ������ ������, ������� ".$gres["credits_reward"]." credits");
  }
	
  if($db->query($queryres))
  {
   WriteLogs ("GReset_","������� ".$_SESSION["user"]." ��������: ".$_SESSION["character"]." ������ ������ >".$gres["greset_reward"]);
   unset($_REQUEST["do_greset"]);header("Location:".$config["siteaddress"]."/?p=usercp&up=stats");$reserror=1;
  }
  else echo"error!";	
 }
}
else
{
 $content->set('|gresprice|', print_price($grzen));
 $content->set('|greset_lvlz|', $gres["greset_lvl"]);
 $content->set('|grespoints|', know_gpoints($check_char[1])*($check_char[6]+1));
 
 if ($check_char[2]>=$gres["greset_res"] and $gres["greset_lvl"]<= $check_char[0]) 
     $content->set('|grbutton|', "<form method='POST' action=''><input type='submit' name='do_greset' class='t-button'></form>");
  else
     $content->set('|grbutton|', "");
 $content->out_content("theme/".$config["theme"]."/them/greset.html");
}
} 
else  echo "<div align='center' valign='center' style='color:red;font-weight:bold;'>module is off</div>";
$temp = ob_get_contents();
ob_end_clean();