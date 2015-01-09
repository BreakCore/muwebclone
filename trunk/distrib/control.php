<?php session_start();
if(isset($_SESSION["adm"]))
{
    ob_start();
    define ('insite', 1);
    define ('inpanel', 1);
    require_once "_sysvol/logs.php";
    require_once "_sysvol/security.php";
    require_once "opt.php";
    require_once "_sysvol/fsql.php";
    require_once "_sysvol/pages.php";
    require_once "_sysvol/them.php";
    $valid = new valid();

    if(!isset($_SESSION["mwclang"]))
        $config["def_lang"] = $_SESSION["mwclang"];

    $content = new content($config["siteaddress"],"admin",$_SESSION["mwclang"],1,"admin");
    $content->set('|siteaddress|', $config["siteaddress"]);

    if (isset($_REQUEST["logout"]))
    {
        unset($_SESSION["sadmin"],$_SESSION["spwd"],$_SESSION["adm"],$_SESSION["user"],$_SESSION["pwd"]);
        header("Location:".$config["siteaddress"]);
        die();
    }
    $db = new connect ($config["ctype"], $config["db_host"], $config["db_name"], $config["db_user"], $config["db_upwd"]);
    require_once "_sysvol/engine.php";
    require_once "_sysvol/amod.php";

    if (isadmin() == 1)
    {
        if (!isset($_GET["page"]))
        {
            if ($config["oporclos"]==1)
            {
                $content->set('|admmodules|',addnews());// <-не работает, заглушка
                $content->set('|admmap|',$content->getVal("lnews"));
            }
            else
                header("Location:".$config["siteaddress"]."/control.php?page=configs");
        }
        else
        {
            $content->set('|admmodules|',a_modul($_GET["page"],$db,$content));
            $content->set('|admmap|',show_t($_GET["page"],0));
        }

        $content->set('|admmenu|', getadmenu($config,$content));
        $content->set('|adminfo|', ainfo($db,$content));
        $content->out("index.html");
    }
    else
    {
        @require "errors/er403.html";
    }
    ob_end_flush();
}
else
    @include "errors/er403.html";
