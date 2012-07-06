<?php if (!defined('insite')) die("no access"); 
require "configs/qinfo_cfg.php";
unset($ntime);

$ntime = @filemtime("_dat/cach/".$_SESSION["mwclang"]."_quickinfo"); 
if (!$ntime or time()- $ntime >$qinfo["cach"])
{
  $list = explode(",",$qinfo["list"]);
  ob_start();
  global $content;
  global $db;
  global $config;


  $content->out_content("theme/".$config["theme"]."/them/qinfo_h.html");
  /**
  * server time
  */
  if(in_array("time",$list))
  {
   $content->set('|write|', $content->lng["online_time"]);
   $content->set('|value|', "<span id='srvtime' >&nbsp;</span>");
   $content->out_content("theme/".$config["theme"]."/them/qinfo_c.html"); 
  }
  /**
  * online/max box
  */
  if(in_array("online",$list))
  {
   $tmp = explode(";",$qinfo["server"]);
   $servs = count($tmp);
   $sql = $db->fetchrow($db->query("SELECT value FROM MWC WHERE parametr='ovalue'"));
   $maxconnect = $sql[0]; 
   $sql = $db->numrows( $db->query("SELECT memb___id FROM MEMB_STAT WHERE ConnectStat !=0 "));
   ($sql== "")? $sql=0:"";
   if ($sql > $maxconnect) 
   {
     $maxconnect = $sql;
     $db->query ("UPDATE MWC SET value='".$maxconnect."' WHERE parametr='ovalue'");
    $connected = "<a href='".$config["siteaddress"]."/?p=online'>".$sql."</a>(".$maxconnect.")";

   }
  else $connected = "<a href='".$config["siteaddress"]."/?p=online'>".$sql."</a>(".$maxconnect.")";

   unset ($sql);
   $content->set('|write|', $content->lng["online_msg"]);
   $content->set('|value|', $connected);
   $content->out_content("theme/".$config["theme"]."/them/qinfo_c.html");

   
   if(strlen($tmp[0])>1)
   {
    for($i=0;$i<$servs;$i+=3)
    {
	 if (trim($tmp[$i])!="online" && trim($tmp[$i])!="offline")
	 {
      if ($check=@fsockopen(trim($tmp[$i]),trim($tmp[$i+1]),$ERROR_NO,$ERROR_STR,(float)0.4)) 
      {
       fclose($check); 
       $statusQ="<span class='succes'>Online</span>";	
      }
      else
      {
       $connected ="<font color='red'>0(".$maxconnect.")</font>";
       $statusQ ="<span class='warnms'>Offline</span>";
      }
      $content->set('|write|', $content->lng["online_status"]." ".$tmp[$i+2]);
      $content->set('|value|', $statusQ);
	 }
	 else if (trim($tmp[$i])=="offline")
	 {
	  $connected ="<font color='red'>0(".$maxconnect.")</font>";
	  $content->set('|value|', "<span class='warnms'>Offline</span>");
	 }
	 else 
	 {
	  $content->set('|write|', $content->lng["online_status"]);
      $content->set('|value|', "<span class='succes'>Online</span>");
	 }
     $content->out_content("theme/".$config["theme"]."/them/qinfo_c.html");
    }
   }

  }
   /**
   * online for 24 hours
   **/  
  if (in_array("24online",$list))
  {
    $month_today = @date("M", time());
    $day_today = @date("j", time());
    $year_today = @date("Y", time());
    $online_today = $db->fetchrow($db->query("SELECT count(*) FROM MEMB_STAT WHERE ConnectTM LIKE '%".$month_today."%".$day_today."%".$year_today."%' OR DisConnectTM LIKE '%".$month_today."%".$day_today."%".$year_today."%'"));
    $content->set('|write|', $content->lng["online_today"]);
    $content->set('|value|', $online_today[0]);
    $content->out_content("theme/".$config["theme"]."/them/qinfo_c.html");
  }
  /**
  * Accounts
  */
  if(in_array("accs",$list))
  {
   ($sql = $db->fetchrow($db->query("SELECT Count(memb___id) FROM memb_info"))) ? $accnum = $sql[0] : $accnum = 0;
   unset ($sql);
   $content->set('|write|', $content->lng["online_accnum"]);
   $content->set('|value|', $accnum);
   $content->out_content("theme/".$config["theme"]."/them/qinfo_c.html");
  }
  /**
  * Characters
  */
  if(in_array("chars",$list))
  {
   ($sql = $db->fetchrow($db->query("SELECT Count(Name) FROM character"))) ? $charnum = $sql[0] : $charnum = 0;
   unset ($sql);
   $content->set('|write|', $content->lng["online_charnum"]);
   $content->set('|value|', $charnum);
   $content->out_content("theme/".$config["theme"]."/them/qinfo_c.html");
  }
   /*
   * Guilds
   */
  if(in_array("guild",$list))
  {
   ($sql = $db->fetchrow($db->query("SELECT Count(G_Name) FROM guild"))) ? $guildnum = $sql[0] : $guildnum = 0;
   unset ($sql);
   $content->set('|write|', $content->lng["online_guilds"]);
   $content->set('|value|', $guildnum);
   $content->out_content("theme/".$config["theme"]."/them/qinfo_c.html");
  }
  /**
  * Castle
  */
  if(in_array("cs",$list))
  {
    $cs_info = know_csstate();
    $castle="<a href='".$config["siteaddress"]."/?p=cs' class='forumnick' style='font-family:Arial;fonti-size:10px;' title='Castle Siege: ".$cs_info[1]."<br> Begin in: ".$cs_info[2]."<br> End in: ".$cs_info[3]."'>".$cs_info [0]."</a>";
    $content->set('|write|', $content->lng["online_castle"]);
    $content->set('|value|', $castle);
    $content->out_content("theme/".$config["theme"]."/them/qinfo_c.html");
  }
  /**
  * CryWolf
  */
  if(in_array("cw",$list))
  {
    $sql = $db->fetchrow($db->query("SELECT CRYWOLF_STATE from ".$qinfo["CW_table"].""));
    ($sql[0] == 1) ? $crywolf = '<span style="color:green;font-size:10px;">'.$content->lng["online_crywolf_1"].'</span>' : $crywolf = '<span style="color:red;font-size:10px;">'.$content->lng["online_crywolf_0"].'</span>';
    unset ($sql);
    $content->set('|write|', $content->lng["online_crywolf"]);
    $content->set('|value|', $crywolf);
    $content->out_content("theme/".$config["theme"]."/them/qinfo_c.html");
   }
  $content->out_content("theme/".$config["theme"]."/them/qinfo_f.html");
  $temp = ob_get_contents();
  write_catch ("_dat/cach/".$_SESSION["mwclang"]."_quickinfo",$temp);
  ob_end_clean();	   
}
else $temp = file_get_contents ('_dat/cach/'.$_SESSION["mwclang"].'_quickinfo');