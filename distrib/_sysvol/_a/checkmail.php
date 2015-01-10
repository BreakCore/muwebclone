<?php if (!defined('inpanel')) die("no access"); 

if (get_accesslvl($db)>49)
{
    $n="";
    $content->out("checkmail_h.html");

    if(isset($_GET["del"]))
    {
        $n = (int)$_GET["del"];
        $db->query("DELETE FROM MWC_messages WHERE id='$n'; DELETE FROM MWC_messages WHERE slave_id='$n'");
        logs::WriteLogs("Messages","администратор ".$_SESSION["sadmin"]." удалил цепочку сообщений");
        header("Location:".$config["siteaddress"]."/control.php?page=checkmail");
    }

    if (isset($_REQUEST["commit"]) && !empty($_POST["answermsg"]))
    {
        $msg = $_POST["answermsg"];
        $s_id = (int)$_POST["h_valid"];
        if($db->query("INSERT INTO MWC_messages (memb___id,message,date,slave_id,isread)VALUES('".$_SESSION["sadmin"]."','".cyr_code($msg)."','".time()."','".$s_id."','3')"))
            header("Location:".$config["siteaddress"]."/control.php?page=checkmail");
    }

    if (isset($_GET["mid"]))
    {
        $n = (int)$_GET["mid"];
        $array = $db->query("SELECT * FROM MWC_messages WHERE id='$n'")->FetchRow();
        $db->query("UPDATE MWC_messages SET isread='1' WHERE id='".$n."' or slave_id='".$n."'");

        $content->set('|smail_Date|', @date("H:i d.m.Y",$array["date"]));
        $content->set('|mnik|', $array["memb___id"]);
        $content->set('|msg|', $array["message"]);
        $content->out_content("checkmail_form_c.html");

        $qq = $db->query("Select * FROM MWC_messages WHERE slave_id='$n' order by id asc");

        while($tar=$qq->FetchRow())
        {
            $content->set('|mnik|', $tar["memb___id"]);
            $content->set('|msg|', $tar["message"]);
            $content->set('|id|', $tar["id"]);
            $content->set('|smail_Date|', @date("H:i d.m.Y",$tar["date"]));
            $content->out("checkmail_form_c.html");
        }
        $content->set('|id|', (int)$_GET["mid"]);
        $content->out("checkmail_form.html");
    }
    else
    {
        $content->out("checkmail_h1.html");
        $n=0;
        $query = $db->query("SELECT * FROM MWC_messages WHERE slave_id=0");

        while($myrows = $query->FetchRow())
        {
            $num = $db->query("SELECT [memb___id] FROM [MWC_messages] WHERE ([isread] ='0' and [slave_id]=".$myrows["id"].")or ([isread] ='0' and [id]=".$myrows["id"].")")->FetchRow();
            $content->set('|m_name|', $myrows["memb___id"]);
            if (isset($num["memb___id"]) && !empty($num["memb___id"]))
                $content->set('|m_let|', "<img src='imgs/letterZ.png' border='0'>");
            else
                $content->set('|m_let|', "-");

            $content->set('|m_date|', @date("H:i d.m.Y",$myrows["date"]));
            $content->set('|id|', $myrows["id"]);
            $content->out("checkmail_c.html");
            $n++;
        }
        if ($n==0)
            echo "<tr><td colspan='4'><div style='text-align:center'>You haven't mails</div></td></tr>";
    }

    $content->out("checkmail_f.html");
}
else
    echo "<div style='text-align:center'>You don't have access to use this module</div>";

