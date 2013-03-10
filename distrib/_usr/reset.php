<?php if (!defined('insite')) die("no access"); 
global $db;
global $content;
global $config;

require "configs/res_cfg.php";
require "configs/gres_cfg.php";
require "configs/top100_cfg.php";

$not_empty =0;
$reschar=0;
function know_points($personaz)
{	
 require "configs/res_cfg.php";
 if ($personaz>=0 && $personaz<=3) return $res["reset_sm"];
 elseif ($personaz>=16 && $personaz<=19) return $res["reset_bk"];
 elseif ($personaz>=32 && $personaz<=35) return $res["reset_elf"];
 elseif ($personaz>=48 && $personaz<=50) return $res["reset_mg"];
 elseif ($personaz>=64 && $personaz<=66) return $res["reset_dl"];
 elseif ($personaz>=80 && $personaz<=83) return $res["reset_bs"];
 elseif ($personaz>=96 && $personaz<=98) return $res["reset_rf"];
 else return 0;
}


if ($_GET["chname"])
{
 $reschar = substr($_GET["chname"],0,10);
 $_SESSION["character"] = $reschar;
}
elseif ($_POST["selectedchar"])
{
 $reschar = substr($_POST["selectedchar"],0,10);
 $_SESSION["character"] = $reschar; 
}
elseif(isset($_SESSION["character"])) $reschar = $_SESSION["character"];
else 
{
 header("Location:".$config["siteaddress"]."/?p=not&error=17"); 
 die();
}
 
 own_char($reschar,$_SESSION["user"]);	
 $reschar = substr($reschar,0,10);
 $check_char = $db->fetchrow($db->query("Select cLevel,class,".$top100["t100res_colum"].",Money,LevelUpPoint,pklevel,gr_res,inventory FROM Character Where Name='".$reschar."'"));

 if ($config["ctype"]=="SQL")
 {
  $Wquer = $db->query("declare @inv varbinary(1728); set @inv = (SELECT inventory FROM character WHERE name='".$reschar."'); print @inv");
  $check_char[7] = substr($db->lastmsg(),2);
 }
 else
  $check_char[7] = strtoupper(bin2hex($check_char[7]));

 for($i=0;$i<(strlen($check_char[7])/32);$i++)
 {
  $n_items = substr($check_char[7],($i*32), 32);
  if ($n_items!="FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF")
  {
   $not_empty=1; 
   break;
  }
 }
		
 if ($res["reset_point_add"]==1)
 {
   if ($res["reset_point_type"] == 1)
      $r_pointss=(know_points($check_char[1])*($check_char[2]+1));
   elseif($res["reset_point_type"] == 2)
      $r_pointss=know_points($check_char[1]);
  
   if ($check_char[6]>0)
   {
       if ($res["greset_coef"]>0)$koef = round( ((($check_char[6]*$check_char[2])/10)/$res["greset_coef"])*know_points($check_char[1]));
       else $koef =0;
	   
       switch ($gres["greset_type"])
       {
        case 1: $r_pointss += $check_char[6]*know_gpoints($check_char[1]); break;
        case 2: $r_pointss += know_gpoints($check_char[1]);break;
		default : $r_pointss += $check_char[6]*know_gpoints($check_char[1]); break;
       }
       $r_pointss += $koef;
   } 
 }
 else $r_pointss =0;
 
 if ($_REQUEST["do_reset"])
 {
  $reserror = 0;

  if ($check_char[0]>=know_level($check_char[1]))
  {
   if ($res["max_res"]>$check_char[2])
   {
     if($res["reset_pk"]==1)
	 {
	  if($check_char[5]>3)
	  {
	   $reserror=1; 
	   header("Location:".$config["siteaddress"]."/?p=not&error=0");
	   die();
	  }
	 }
	 
     $queryres = "UPDATE Character SET ";
     if ($res["reset_zen_type"]==1)    $Zen4res = ($check_char[2]+1)*$res["reset_prise"];
	 elseif($res["reset_zen_type"]==0) $Zen4res = $res["reset_prise"];
	 
	 //$newmoney = $check_char[3] - $Zen4res;
	 $newmoney = bankZ_show(0,1) - $Zen4res;
	 
     if ($newmoney < 0)
	 {
	  $reserror = 1; 
	  header("Location:".$config["siteaddress"]."/?p=not&error=1");
	  die();
	 }
	 //else $queryres.="Money =".$newmoney.", ";
	
     if ($res["reset_points"] == 1) $queryres.="Strength=25,Dexterity=25,Vitality=25,Energy=25,";
     if ($res["reset_point_add"]==1)
     { 
      $queryres.=" LevelUpPoint =".$r_pointss.",";
      if($check_char[1]== 64 or $check_char[1]==65 or $check_char[1]==66) 
	    $queryres.= " Leadership=".(25+($res["reset_com"]*($check_char[2]+1))).",";		
     }
     else $queryres.=" LevelUpPoint =0,";
						
     if ($res["reset_inv"]==1){$queryres.=" Inventory=0x".$res["empt_inv"].",";}
     if ($res["reset_skill"]==1){$queryres.=" MagicList=NULL,";}
     $queryres.=" ".$top100["t100res_colum"]." = ".$top100["t100res_colum"]."+1, cLevel=1,Experience=0  WHERE Name='".$reschar."' UPDATE MEMB_INFO SET bankZ='".$newmoney."' WHERE memb___id='".$_SESSION["user"]."'";					
   } 
   else
   {
    $reserror = 1; 
    header("Location:".$config["siteaddress"]."/?p=not&error=15");
    die();
   }
  }
  else
  {
   $reserror = 1; 
   header("Location:".$config["siteaddress"]."/?p=not&error=2");
   die();
  }
 
  if($reserror==0 && chck_online($_SESSION["user"])==0)
  { 
   if($db->query($queryres))
   {
    WriteLogs ("Reset_","Аккаунт ".$_SESSION["user"]." персонаж: ".$_SESSION["character"]." сделал ресет");
    unset($_REQUEST["do_reset"]);
    header("Location:".$config["siteaddress"]."/?p=usercp&up=stats");
    $reserror=1;
   }
   else 
     echo "error!Pleace, contact with administrator!";	
  }			
 }
 
 
 if ($res["reset_zen_type"]==1)    $content->set('|przen|', print_price(($check_char[2]+1)*$res["reset_prise"]));
 elseif($res["reset_zen_type"]==0) $content->set('|przen|', print_price($res["reset_prise"]));
 
 $content->set('|reset_inv|',swiched_val($res["reset_inv"]));
 $content->set('|reset_skill|',swiched_val($res["reset_skill"]));
 $content->set('|rlvl|',know_level($check_char[1]));
 $content->set('|reset_pts|',$r_pointss);
 
 if ($res["strong_inv"]==0 or ($res["strong_inv"]==1 && $not_empty==0))
    $content->set('|msg|',"<form method='POST' action=''><input type='submit' name='do_reset' class='t-button'></form>");
 else
    $content->set('|msg|',"Inventory is NOT empty!");
 
    
$content->out_content("theme/".$config["theme"]."/them/res.html");