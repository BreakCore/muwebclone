<?php if (!defined('insite')) die("no access");
/**
 * топ сильнейших
 */

require "configs/strongest_cfg.php";
error_reporting(E_ALL);
if(is_array($strongest))
{
    if(time() - load_cache("_dat/cach/top_strongest",true) > $strongest["cach"])
    {
        if ($strongest["show_adm"]==0)
            $show_gm="CtlCode !=32 and";
        else
            $show_gm="";

        $templ = explode(",",$strongest["stron_hiden"]);
        $hiden="";
        $jj = 0;

        foreach ($templ as $tmp) {
            if($jj>0)
                $hiden.=",";
            $hiden.="'$tmp'";
            $jj++;
        }


        if (count($templ) > 0)
            $hiden = " and ch.Name NOT IN ($hiden) ";
        else
            $hiden="";

        $resulttop5 = $db->query("SELECT TOP 5
 ch.Name,
 ch.{$strongest["res_colum"]},
 ch.cLevel,
 ch.AccountID,
 ch.Class,
 ch.gr_res,
 gm.G_Name,
 CONVERT(varchar(max),gld.G_Mark,2) as g_mark,
 ms.ConnectStat,
 CONVERT(varchar(max),ms.ConnectTM,120) as ConnectTM,
 CONVERT(varchar(max), ms.DisConnectTM ,120) as DisConnectTM
FROM [Character] ch
 left join [GuildMember] gm ON gm.Name = ch.Name
 left join Guild gld on gm.G_Name = gld.G_Name
 inner join MEMB_STAT ms on ms.memb___id COLLATE DATABASE_DEFAULT = ch.AccountID COLLATE DATABASE_DEFAULT
WHERE
 $show_gm CtlCode != 1 $hiden
 and CtlCode != 17  ".$strongest["str_sort"]);

        ob_start();

        if ($strongest["top_type"]==1)
        {
            while($res = $resulttop5->FetchRow())
            {
               // debug($res);

                $res["Class"] = classname($res["Class"]);
                if(empty($res["G_Name"]))
                    $res["G_Name"]="-/-";

                if($res["ConnectStat"] == 1)
                {
                    $status = "<span style=color:#04C200;font-weight:bold;font-size:12px;>Online</span> ".$content->getVal("sreongest_pr")." ".$res["ConnectTM"];
                }
                else
                    $status = "<span style=color:#FF0505;font-weight:bold;font-size:12px;>Offline</span> ";

                if ($strongest["greset"]==1 && $strongest["greset_st"]==1)
                {
                    $gr_star="&nbsp;";
                    while ($res["gr_res"] > 0)
                    {
                        $gr_star.="<img src=\"imgs/gres.gif\"  border=\"0\" />";
                        $res["gr_res"]--;
                    }
                }

                $oinfo = $content->getVal("sreongest_guild")." <i> {$res["G_Name"]}</i><br>".$content->getVal("sreongest_class")." <i>{$res["Class"]}</i><br>".$content->getVal("sreongest_status")." <i>$status</i>";
                $content->set("|oinfo|", $oinfo);
                $content->set("|level|", $res["cLevel"]);
                $content->set("|reset|", $res[$strongest["res_colum"]]);
                $content->set("|gstar|", $gr_star);
                $content->set("|cname|", $res["Name"]);
                $content->out("strongest.html");
            }
        }
        elseif ($strongest["top_type"]==2)
        {
            $class_list = explode(",", $strongest["stron_sh"]);
            $st_show="";
            foreach ($class_list as $n=>$v)
            {

                switch ($v)
                {
                    case 0:$ch_name = "Dark Wizard";$ch_class="Class=0";break;
                    case 1:$ch_name = "Soul Master";$ch_class="Class=1";break;
                    case 2:$ch_name = "Grand Master"; $ch_class="Class=2";break;
                    case 3:$ch_name = "Grand Master";$ch_class="Class=3";break;
                    case 16:$ch_name = "Dark Knight";$ch_class="Class=16";break;
                    case 17:$ch_name = "Blade Knight";$ch_class="Class=17";break;
                    case 18:$ch_name = "Blade Master";$ch_class="Class=18";break;
                    case 19:$ch_name = "Blade Master";$ch_class="Class=19";break;
                    case 32:$ch_name = "Fairy Elf";$ch_class="Class=32";break;
                    case 33:$ch_name = "Muse Elf";$ch_class="Class=33";break;
                    case 34:$ch_name = "High Elf";$ch_class="Class=34";break;
                    case 35:$ch_name = "High Elf";$ch_class="Class=35";break;
                    case 48:$ch_name = "Magic Gladiator";$ch_class="Class=48";break;
                    case 49:$ch_name = "Duel Master";$ch_class="Class=49";break;
                    case 50:$ch_name = "Duel Master";$ch_class="Class=50";break;
                    case 64:$ch_name = "Dark Lord";$ch_class="Class=64";break;
                    case 65:$ch_name = "Lord Emperor";$ch_class="Class=65";break;
                    case 66:$ch_name = "Lord Emperor";$ch_class="Class=66";break;
                    case 80:$ch_name = "Summoner";$ch_class="Class=80";break;
                    case 81:$ch_name = "Bloody Summoner";$ch_class="Class=81";break;
                    case 82:$ch_name = "Dimension Master";$ch_class="Class=82";break;
                    case 83:$ch_name = "Dimension Master";$ch_class="Class=83";break;
                    case 96:$ch_name = "Rage Fighter";$ch_class="Class=96";break;
                    case 97:$ch_name = "Fist Master";$ch_class="Class=97";break;
                    case 98:$ch_name = "Fist Master";$ch_class="Class=98";break;
                    default: return "<br>Wrong Character class!";
                }

                $sho_t = $db->query("SELECT TOP 1
                ch.Name,ch.{$strongest["res_colum"]},ch.cLevel, ch.AccountID,ch.gr_res ,
                gm.G_Name
                FROM [Character] ch left join [GuildMember] gm ON gm.Name = ch.Name -- guild
                WHERE  CtlCode != 1 and CtlCode != 17 and ".$ch_class." ".$strongest["str_sort"])->FetchRow();

                if(empty($sho_t["G_Name"]))
                    $sho_t["G_Name"] = "no guild";
                if(empty($sho_t["Name"]))
                    $sho_t["Name"] = "no one";
                $gr_star="&nbsp;";
                if ($strongest["greset"]==1 && $strongest["greset_st"]==1)
                {
                    while ($sho_t[4]>0)
                    {
                        $gr_star.="<img src=\"imgs/gres.gif\"  border=\"0\" />";
                        $sho_t["gr_res"]--;
                    }
                }
                else if ($strongest["greset"]==1 && $strongest["greset_st"]==0)
                {
                    $gr_star=" <br>Grand Reset: ".$sho_t["gr_res"];
                }
                if ($sho_t["Name"] != "no one" && isset($sho_t["cLevel"]) && isset($sho_t[$strongest["res_colum"]]))
                    $st_show.= "<br>".$ch_name." : <span title='Level: ".$sho_t["cLevel"]."<br>Reset: ".$sho_t[$strongest["res_colum"]].$gr_star."<br>Guild: {$sho_t["G_Name"]}'>{$sho_t["Name"]}</span>";
                else
                    $st_show.= "<br>".$ch_name." : {$sho_t["Name"]}";
            }
            echo $st_show;
        }
        else
            echo "error: in top_type!";
        $temp = ob_get_contents();
        write_catch ("_dat/cach/top_strongest",$temp);
        ob_clean();
    }
    else
        $temp = load_cache("_dat/cach/top_strongest");
}
else
{
    echo "wrong strongest config!";
}
