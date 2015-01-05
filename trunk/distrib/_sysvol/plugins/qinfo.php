<?php if (!defined('insite')) die("no access"); 
require_once "configs/qinfo_cfg.php";
$ntime = @filemtime("_dat/cach/".$_SESSION["mwclang"]."_quickinfo");
if(!is_array($qinfo))
{
    echo "wrong config for qinfo";
}
else
{
    if (!$ntime or time()- $ntime >$qinfo["cach"])
    {
        $list = explode(",",$qinfo["list"]);
        ob_start();

        $content->out("qinfo_h.html");
        /**
         * server time
         */
        if(in_array("time",$list))
        {
            $content->set('|write|', $content->getVal("online_time"));
            $content->set('|value|', "<span id='srvtime' >&nbsp;</span>");
            $content->out("qinfo_c.html");
        }
        /**
         * online/max box
         */
        if(in_array("online",$list))
        {
            $tmp = explode(";",$qinfo["server"]);
            $servs = count($tmp);
            $sql = $db->query("SELECT
(SELECT [value] FROM MWC WHERE parametr='ovalue') as maxcon,
(SELECT count(*) as cnt FROM MEMB_STAT WHERE ConnectStat !=0) as conon")->FetchRow();
            $maxconnect = $sql["maxcon"];
            
            if ($sql["conon"] > $maxconnect)
            {
                $maxconnect = $sql["conon"];
                $db->query ("UPDATE MWC SET value='$maxconnect' WHERE parametr='ovalue'");
                $connected = "<a href='".$config["siteaddress"]."/?p=online'>{$sql["conon"]}</a>(".$maxconnect.")";
            }
            else
                $connected = "<a href='".$config["siteaddress"]."/?p=online'>{$sql["conon"]}</a>(".$maxconnect.")";

            unset ($sql);
            $content->set('|write|', $content->getVal("online_msg"));
            $content->set('|value|', $connected);
            $content->out("qinfo_c.html");


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
                        $content->set('|write|', $content->getVal("online_status")." ".$tmp[$i+2]);
                        $content->set('|value|', $statusQ);
                    }
                    else if (trim($tmp[$i])=="offline")
                    {
                        $connected ="<font color='red'>0(".$maxconnect.")</font>";
                        $content->set('|value|', "<span class='warnms'>Offline</span>");
                    }
                    else
                    {
                        $content->set('|write|', $content->getVal("online_status"));
                        $content->set('|value|', "<span class='succes'>Online</span>");
                    }
                    $content->out("qinfo_c.html");
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
            $online_today = $db->query("SELECT count(*) as perday FROM MEMB_STAT WHERE ConnectTM LIKE '%".$month_today."%".$day_today."%".$year_today."%' OR DisConnectTM LIKE '%".$month_today."%".$day_today."%".$year_today."%'")->FetchRw();
            $content->set('|write|', $content->getVal("online_today"));
            $content->set('|value|', $online_today["perday"]);
            $content->out("qinfo_c.html");
        }
        /**
         * Accounts
         */
        if(in_array("accs",$list))
        {
            $sql = $db->query("SELECT Count(memb___id) as cnt FROM memb_info")->FetchRow();
            $content->set('|write|', $content->getVal("online_accnum"));
            $content->set('|value|', $sql["cnt"]);
            $content->out("qinfo_c.html");
        }
        /**
         * Characters
         */
        if(in_array("chars",$list))
        {
            $sql = $db->query("SELECT Count(Name) as cnt FROM [character]")->FetchRow();
            $content->set('|write|', $content->getVal("online_charnum"));
            $content->set('|value|', $sql["cnt"]);
            $content->out("qinfo_c.html");
        }
        /*
        * Guilds
        */
        if(in_array("guild",$list))
        {
            $sql = $db->query("SELECT Count(G_Name) as glds FROM guild")->FetchRow();
            $content->set('|write|', $content->getVal("online_guilds"));
            $content->set('|value|', $sql["glds"]);
            $content->out("qinfo_c.html");
        }
        /**
         * Castle
         */
        if(in_array("cs",$list))
        {
            $cs_info = know_csstate($db,$content);
            $castle="<a href='".$config["siteaddress"]."/?p=cs' class='forumnick' style='font-family:Arial;fonti-size:10px;' title='Castle Siege: ".$cs_info[1]."<br> Begin in: ".$cs_info[2]."<br> End in: ".$cs_info[3]."'>".$cs_info [0]."</a>";
            $content->set('|write|', $content->getVal("online_castle"));
            $content->set('|value|', $castle);
            $content->out("qinfo_c.html");
        }
        /**
         * CryWolf
         */
        if(in_array("cw",$list))
        {
            $sql = $db->query("SELECT CRYWOLF_STATE from ".$qinfo["CW_table"]."")->FetchRow();
            ($sql["CRYWOLF_STATE"] == 1) ? $crywolf = '<span style="color:green;font-size:10px;">'.$content->getVal("online_crywolf_1").'</span>' : $crywolf = '<span style="color:red;font-size:10px;">'.$content->getVal("online_crywolf_0").'</span>';
            $content->set('|write|', $content->getVal("online_crywolf"));
            $content->set('|value|', $crywolf);
            $content->out("qinfo_c.html");
        }
        $content->out("qinfo_f.html");
        $temp = ob_get_contents();
        write_catch ("_dat/cach/".$_SESSION["mwclang"]."_quickinfo",$temp);
        ob_end_clean();
    }
    else $temp = file_get_contents ('_dat/cach/'.$_SESSION["mwclang"].'_quickinfo');
}
