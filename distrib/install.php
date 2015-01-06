<?php session_start();
define ('insite',1);

ob_start();
include "opt.php";

$version = "1.5.3";

require_once "_sysvol/them.php";
require_once "_sysvol/logs.php";
require_once "_sysvol/security.php";
require_once "_sysvol/fsql.php";




if(!isset($_SESSION["mwcsaddr"]))
{
    $sname = explode("/", $_SERVER["SCRIPT_NAME"]);

    $_SESSION["mwcsaddr"] = "http://".$_SERVER["HTTP_HOST"];
    unset($sname[count($sname)-1]);

    foreach($sname as $v)
    {
        $_SESSION["mwcsaddr"] .="/".$v;
    }
}

if (!isset($_GET["p"]) || empty($_GET["p"]))
    $_GET["p"]=0;

$getprrem = $_GET["p"];

if(!isset($_SESSION["mwclang"]))
{
    switch($_GET["l"])
    {
        case 1: $_SESSION["mwclang"]="rus"; break;
        case 2: $_SESSION["mwclang"]="eng"; break;
        default:  $_SESSION["mwclang"]="rus";
    }
}

if (isset($_SESSION["mwclang"]))
    require_once "lang/".$_SESSION["mwclang"]."/".$_SESSION["mwclang"]."_install.php";




if(isset($config["ctype"]))
{
    $db = new connect ($config["ctype"], $config["db_host"], $config["db_name"], $config["db_user"], $config["db_upwd"]);
    $count = $db->query("SELECT count(*) as dms FROM MWC_admin")->FetchRow();
    $isreinsstall = $db->query("SELECT [value] as vals FROM MWC WHERE parametr='reinstall'")->FetchRow();
}
else
{
    $count["dms"] = 0;
    $isreinsstall["value"] == 0;
}

if (isset($_REQUEST["finish"]))
{
    $_SESSION["adm"]=1;
    // $db = new connect ($config["ctype"], $config["db_host"], $config["db_name"], $config["db_user"], $config["db_upwd"]);
    $db->query("UPDATE MWC SET value = '1' WHERE parametr='reinstall'");
    $db->close();
    unset($_SESSION["ulogin"],$_SESSION["upwd"],$_SESSION["udb"], $_SESSION["uhost"],$_SESSION["utype"],$_SESSION["md5"],$_SESSION["user"],$_SESSION["pwd"]);
    rename("install.php", "_dat/install.php");
    header("location: ".$_SESSION["mwcsaddr"]);
    die();
}

if ($count["dms"] == 0 or $isreinsstall["value"] == 0)
{
    $_SESSION["install"]=true;
    $content = new content("install",substr($_SESSION["mwclang"],0,3));
    $content->set('|siteaddress|', $_SESSION["mwcsaddr"]);
    $step = array(
        0=>$lang["inst_step0"],
        1=>$lang["inst_step1"],
        2=>$lang["inst_step2"],
        3=>$lang["inst_step3"],
        4=>$lang["inst_step4"]);

    ob_start();
    $content->set('|instep|', $step[$getprrem]);
    $on_screen = "<table border='0' class='posit' width='80%'>";

    if (isset($_REQUEST["addadm"]))//создание админа
    {
        $valid = new valid();
        $adminl = substr($_POST["adml"],0,10);
        $admind = substr($_POST["admp"],0,10);
        $admnn = substr($_POST["anick"],0,10);

        if (!empty($adminl) && !empty($admind) && !empty($admnn))
        {
            if ($count==0)
            {
                $admind = md5($admind);
                $step[0] = $lang["inst_step3"];
                $db->query("INSERT INTO dbo.MWC_admin ([name],[pwd],[nick],[access]) VALUES('$adminl','$admind','$admnn','100')") or $on_screen.= "<tr><td style='font-weight:bold;color:red;'>Can't create admin account</td></tr>";
                $on_screen.="<tr><td style='font-weight:bold;color:green;'>{$lang["inst_cadm"]}</td></tr>";
            }
            else
                $on_screen.="<tr><td style='font-weight:bold;color:red;'>{$lang["inst_eadm"]}</td></tr>";
            $content->set('|button|', "<a href=\"?p=4\"><img style=\"margin-right:20px; margin-top:5px; float:right;\" src=\"imgs/inst/stp_next.png\" alt=\"Далее\"></a>");
        }
        else
        {
            $on_screen.="<tr><td style='font-weight:bold;color:red;'>{$lang["inst_sym"]}</td></tr>";
            $content->set('|button|', "<a href='javascript:history.back();'><img style=\"margin-right:20px; margin-top:5px; float:right;\" src=\"imgs/inst/stp_back.png\" alt=\"Далее\"></a>");
        }
    }
    else
    {
        switch($getprrem)
        {
            case 0:
                $on_screen.="<tr><td align='center'><a href='?p=1&l=1'><img src='imgs/rus.png' border='0'></a><a href='?p=1&l=2'><img src='imgs/eng.png' border='0'></a></td></tr>";
                $content->set('|button|', "");
                break;

            case 1:
                $on_screen.= "
   <tr><td>".$lang["inst_ctype"]."</td><td><select name='contype' id='contype'><option value='1'>ODBC</option><option value='2'>MSSQL</option></select></td></tr>
   <tr><td>".$lang["inst_dblogin"]."</td><td><input type='text' value='".$_SESSION["ulogin"]."' name='dbusr' id='dbusr'></td></tr>
   <tr><td>".$lang["inst_dbpwd"]."</td><td><input type='text'  name='dbpwd' id='dbpwd'></td></tr>
   <tr><td>".$lang["inst_dbbd"]."</td><td><input type='text' value='".$_SESSION["udb"]."' name='dbdb' id='dbdb'></td><tr>
   <tr><td>".$lang["inst_dbhost"]."</td><td><input type='text' name='dbhost' value='".$_SESSION["uhost"]."' id='dbhost'></td><tr>
   <tr><td><input type='button' OnClick='cccc()' value='".$lang["inst_checkc"]."'></td><td> <span id='checkc'></span></td></tr>";
                $content->set('|button|', "");
                break;

            case 2:
                if (!isset($_SESSION["ulogin"]) or !isset($_SESSION["upwd"]) or !isset($_SESSION["udb"]) or !isset($_SESSION["uhost"]) or !isset($_SESSION["utype"]))
                    die("reinstall please.");
                $db = new connect ($_SESSION["utype"], $_SESSION["uhost"], $_SESSION["udb"], $_SESSION["ulogin"], $_SESSION["upwd"]);

                $result = $db->query("SELECT data_type FROM information_schema.columns WHERE table_name='MEMB_INFO' AND column_name='memb__pwd'")->FetchRow();
                $goon = 0;

                switch($result["data_type"])
                {
                    case "varchar":
                        $on_screen.= "<tr><td style='font-weight:bold;color:red;'>".$lang["inst_nomd5"]."</td></tr>";
                        $goon=1;
                        $_SESSION["md5"]="off";
                        break;
                    case "nvarchar":
                        $on_screen.= "<tr><td style='font-weight:bold;color:red;'>".$lang["inst_nomd5"]."</td></tr>";
                        $goon=1;
                        $_SESSION["md5"]="off";
                        break;
                    case "varbinary" or "binary":
                        $on_screen.= "<tr><td style='font-weight:bold;color:green;'>".$lang["inst_md5"]."</td></tr>";

                        $_SESSION["md5"]="on";
                        $goon=1;
                        $db->query("CREATE FUNCTION [dbo].[fn_md5] (@data VARCHAR(10), @data2 VARCHAR(10))
                        RETURNS BINARY(16) AS
                        BEGIN
                        DECLARE @hash BINARY(16)
                        EXEC master.dbo.XP_MD5_EncodeKeyVal @data, @data2, @hash OUT
                        RETURN @hash
                        END") or $error["fn_md5"]=1;
                        $db->query("Use master") or $error["master"]=1;
                        $db->query("exec sp_addextendedproc 'XP_MD5_EncodeKeyVal', 'WZ_MD5_MOD.dll'")or $error["xp_md5"]=1;
                        $db->query("Use ".$_SESSION["udb"]);
                        break;
                    default:  $on_screen.= "<tr><td style='font-weight:bold;color:red;'>unknown type!(".$result[0].") Check configs! it maybe some errors</td></tr>"; $goon=1;  $_SESSION["md5"]="off";
                }

                if ($goon==1)
                {
                    try{
                        $db->query("DROP TABLE [dbo].[MWC_admin]");
                        $db->query("drop table [dbo].[web_shop] ");
                        $db->query("drop table [dbo].[MWC]");
                        $db->query("DROP TABLE [dbo].[mwc_vote_top]");
                        $db->query("DROP TABLE [dbo].[mwc_vote_list]");
                        $db->query("DROP TABLE [dbo].[MWC_messages]");
                        $db->query("DROP TABLE [dbo].[alotery]");
                        $db->query("DROP TABLE [dbo].[MWC_questsystem]");
                        $db->query("DROP TABLE [dbo].[MWC_questwinners]");
                        $db->query("DROP TABLE [dbo].[smsdeluxe]");
                        $db->query("DROP TABLE [dbo].[MWC_invite]");
                        $db->query("alter table MEMB_INFO DROP COLUMN ban_des");
                    }
                    catch (Exception $ex)
                    {

                    }

                    try{
                        $db->query("alter table [character] add [Resets] int not null default('0')");
                    }
                    catch (Exception $ex)
                    {
                        $error["Resets"]=1;
                    }

                    try{
                        $db->query("alter table [character] add [gr_res] int not null default('0')");
                    }
                    catch (Exception $ex)
                    {
                        $error["gR_res"]=1;
                    }

                    try{
                        $db->query("alter table [memb_info] add [recpwd] varchar(12) null");
                    }
                    catch (Exception $ex)
                    {
                        $error["recpwd"]=1;
                    }

                    try{
                        $db->query("alter table [memb_info] add [isadmin] int null");
                    }
                    catch (Exception $ex)
                    {
                        $error["isadmin"]=1;
                    }

                    try{
                        $db->query("alter table [memb_info] add [bankZ] bigint not null default(0)");
                    }
                    catch (Exception $ex)
                    {
                        $error["bankZ"]=1;
                    }

                    try{
                        $db->query("alter table [memb_info] add [mwcban_time] varchar(17) not null default('0')");
                    }
                    catch (Exception $ex)
                    {
                        $error["mwcban_time"]=1;
                    }

                    try{
                        $db->query("alter table [memb_info] add [ban_des] varchar(255) null ");
                    }
                    catch (Exception $ex){
                        $error["ban_des"]=1;
                    }

                    try{
                        $db->query("alter table [memb_info] add [ref_acc] int null default((0))");
                    }
                    catch (Exception $ex){
                        $error["ref_acc"]=1;
                    }

                    try{
                        $db->query("alter table [memb_info] add [ok_inv] int null");
                    }
                    catch (Exception $ex){
                        $error["ok_inv"]=1;
                    }

                    try{
                        $db->query("alter table [memb_info] add [rdate] varchar(17) null");
                    }
                    catch (Exception $ex){
                        $error["rdate"]=1;
                    }

                    try{
                        $db->query("alter table [memb_info] add [credits] [int] NOT NULL default('0')");
                    }
                    catch (Exception $ex){
                        $error["credits"]=1;
                    }

                    try{
                        $db->query("alter table [memb_info] add [act_time] [varchar](17) NULL default('0')");
                    }
                    catch (Exception $ex){
                        $error["act_time"]=1;
                    }

                    try{
                        $db->query("alter table [memb_info] add [opt_inv] [int] NULL default('0')");
                    }
                    catch (Exception $ex){
                        $error["opt_inv"]=1;
                    }

                    try{
                        $db->query("CREATE TABLE [dbo].[smsdeluxe](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[memb___id] [varchar](10) NULL,
	[dateonpay] [varchar](50) NULL,
	[datepayed] [varchar](50) NULL default('0'),
	[county] [nchar](10) NULL,
	[credits] [nchar](10) NULL
) ON [PRIMARY]");
                    }
                    catch (Exception $ex){
                        $error["smsdeluxe"]=1;
                    }

                    try{
                        $db->query("CREATE TABLE [dbo].[MWC_invite](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[memb___id] [varchar](10) NULL,
	[inviter] [varchar](10) NULL,
    [done][char](1) NULL default('0')
) ON [PRIMARY]");
                    }
                    catch(Exception $ex){
                        $error["MWC_invite"]=1;
                    }
                    try{
                        $db->query("CREATE TABLE [dbo].[web_shop](
	[code] [int] IDENTITY(1,1) NOT NULL,
	[memb___id] [varchar](10) NULL,
	[class] [varchar](35) NULL,
	[price] [bigint] NULL,
	[cprice] [bigint] NULL,
	[sdate] [varchar](10) NULL,
	[item] [varchar](32) NULL,
	[igroup] [int] NULL,
	[iid] [int] NULL,
	[ilevel] [int] NULL,
	[iexc] [int] NULL,
	[ianc] [int] NULL,
	[ipvp] [int] NULL,
	[ihar] [int] NULL,
	[isock] [int] NULL,
	[typepay] [char](2) NULL,
	[typesave] [char](2) NULL,
	[item_name] [char](30) NULL,
	[was_dropd] [int]  NULL default('0')
        ) ON [PRIMARY]");
                    }
                    catch(Exception $ex){
                        $error["web_shop"]=1;
                    }

                    try{
                        $db->query("CREATE TABLE [dbo].[mwc_vote_top](
							[id] [int] IDENTITY (1, 1) NOT NULL,
							[memb___id] [varchar](10) NOT NULL,
							[top_id] [int] NULL,
							[ip] [varchar](15) NOT NULL,
							[last_voted] [varchar](12) NULL,
							[realvotes] [int] NULL default('0'),
							[clicks] [int] NULL
							) ON [PRIMARY]");
                    }
                    catch(Exception $ex){
                        $error["mwc_vote_top"]=1;
                    }

                    try{

                    }
                    catch(Exception $ex){

                    }

                    try{
                        $db->query("IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[MWC_chartoacc]') AND type in (N'P', N'PC'))DROP PROCEDURE [dbo].[MWC_chartoacc]");
                        $db->query("CREATE PROCEDURE [dbo].[MWC_chartoacc] @Name varchar(10) AS BEGIN
 SET NOCOUNT ON;
 declare @AccountID as varchar(10)
 Set @AccountID = '0'
 select @AccountID = AccountID FROM Character Where Name=@Name
 SELECT @AccountID
END ");
                    }
                    catch(Exception $ex){
                        $error["mwc_chartoacc"]=1;
                    }

                    try{
                        $db->query("CREATE TABLE [dbo].[mwc_vote_list](
			[id] [int] IDENTITY (1, 1) NOT NULL,
			[top_name] [varchar](15) NOT NULL,
			[top_addres] [varchar](100) NOT NULL,
			[top_pic] [varchar](100) NOT NULL,
			[flag] [int] NULL,
			[credits] [int] NULL
			) ON [PRIMARY]");
                    }
                    catch(Exception $ex){
                        $error["mwc_vote_list"]=1;
                    }

                    try{
                        $db->query("CREATE TABLE [dbo].[MWC](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[parametr] [varchar](50) NULL,
	[value] [int] default('0') NULL
       ) ON [PRIMARY]");
                    }
                    catch(Exception $ex){
                        $error["mwc"]=1;
                    }

                    try{
                        $db->query("INSERT INTO MWC (parametr)VALUES('Ovalue')");
                        $db->query("INSERT INTO MWC (parametr)VALUES('reinstall')");
                    }
                    catch(Exception $ex){

                    }

                    try{
                        $db->query("CREATE TABLE [dbo].[MWC_admin](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[name] [varchar](20) NULL,
	[pwd] [varchar](50) NULL,
	[lastdate] [datetime] NULL,
	[nick] [varchar](20) NULL,
	[access] [smallint] NULL
       ) ON [PRIMARY]");
                    }
                    catch(Exception $ex){
                        $error["MWC_admin"]=1;
                    }

                    try{
                        $db->query("CREATE TABLE [dbo].[MWC_messages](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[memb___id] [varchar](10) NULL,
	[message] [text] NULL,
	[Fromm] [varchar](10) NULL,
	[isread] [char](1) default('0') NULL,
	[date] [varchar](25) NULL,
	[slave_id] [int] NULL default((0))
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]");
                    }
                    catch(Exception $ex){
                        $error["MWC_message"]=1;
                    }

                    try{
                        $db->query("CREATE TABLE [dbo].[MWC_chat](
	[message] [text] NULL,
	[time] [varchar](20) NULL,
	[memb___id] [varchar](10) NULL,
	[nick] [varchar](20) NULL
      ) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]");
                    }
                    catch(Exception $ex){
                        $error["MWC_chat"]=1;
                    }

                    if ($error["MWC_admin"]!=1 && $error["mwc"]!=1)
                    {
                        $in_write = '<?php if (!defined("insite")){die("no access to config file!");}'.chr(13).chr(10);
                        $in_write.= '/**'.chr(13).chr(10).'* MuWebClone'.chr(13).chr(10).'* ver '.$version.chr(13).chr(10);
                        $in_write.= '* Special thx to: Magistr,{8bit}DoS.Ninja, eg-network.ru & Deathless(MuAntrum(DEW)),Vaflan(MyMuWeb), Platinum(for tests), muhard.ru'.chr(13).chr(10);
                        $in_write.= '**/'.chr(13).chr(10).'$config["db_host"]= "'.$_SESSION["uhost"].'";'.chr(13).chr(10);
                        $in_write.= '$config["db_user"]= "'.$_SESSION["ulogin"].'";'.chr(13).chr(10);
                        $in_write.= '$config["db_upwd"]= "'.$_SESSION["upwd"].'";'.chr(13).chr(10);
                        $in_write.= '$config["ctype"]= "'.$_SESSION["utype"].'";'.chr(13).chr(10);
                        $in_write.= '$config["db_name"]= "'.$_SESSION["udb"].'";'.chr(13).chr(10);
                        $in_write.= '$config["siteaddress"]= "'.$_SESSION["mwcsaddr"].'";'.chr(13).chr(10);
                        $in_write.= '$config["forum"]= "'.$_SESSION["mwcsaddr"].'/forum";'.chr(13).chr(10);
                        $in_write.= '$config["def_lang"]= "'.$_SESSION["mwclang"].'";'.chr(13).chr(10);
                        $in_write.= '$config["theme"]="castle";'.chr(13).chr(10);
                        $in_write.= '$config["odbc_driver"]="SQL Server";'.chr(13).chr(10);
                        $in_write.= '$config["debug"]=0;'.chr(13).chr(10);
                        $in_write.= '$config["ucapch"]=1;'.chr(13).chr(10);
                        $in_write.= '$config["md5use"]="'.$_SESSION["md5"].'";'.chr(13).chr(10);
                        $in_write.= '$config["description"]="";'.chr(13).chr(10);
                        $in_write.= '$config["keywords"]="";'.chr(13).chr(10);
                        $in_write.= '$config["under_rec"]=0;'.chr(13).chr(10);
                        $in_write.= '$config["server_name"]="p4f";'.chr(13).chr(10);
                        $in_write.= '$config["server_team"]="p4f";'.chr(13).chr(10);
                        $in_write.= '$config["description"]="MuWebClone";'.chr(13).chr(10);
                        $in_write.= '$config["keywords"]="MuWebClone";'.chr(13).chr(10);
                        $in_write.= '$config["mainmod"]="qinfo,strongest,questtop";'.chr(13).chr(10);
                        $in_write.= '$config["mainmod_def"]="qinfo,strongest,questtop,top5guild,baners";'.chr(13).chr(10);
                        $in_write.= '$config["cr_table"]="MEMB_INFO";'.chr(13).chr(10);
                        $in_write.= '$config["cr_column"]="credits";'.chr(13).chr(10);
                        $in_write.= '$config["cr_acc"]="memb___id";'.chr(13).chr(10);
                        $in_write.= '$config["oporclos"]=1;'.chr(13).chr(10);
                        $fhandler = fopen("opt.php","w");
                        fwrite($fhandler,$in_write);
                        fclose($fhandler);
                        if($count>0)
                            $content->set('|button|', "<a href=\"?p=4\"><img style=\"margin-right:20px; margin-top:5px; float:right;\" src=\"imgs/inst/stp_next.png\" alt=\"Далее\"></a>");
                        else
                            $content->set('|button|', "<a href=\"?p=3\"><img style=\"margin-right:20px; margin-top:5px; float:right;\" src=\"imgs/inst/stp_next.png\" alt=\"Далее\"></a>");
                    }
                    else
                    {
                        $content->set('|button|', "");
                        if($error["MWC_admin"]==1)
                            $on_screen.= "<tr><td style='font-weight:bold;color:red;'>Can't create MWC_admin check connection configs!</td></tr>";
                        if($error["MWC"]==1)
                            $on_screen.= "<tr><td style='font-weight:bold;color:red;'>Can't create MWC check connection configs!</td></tr>";
                    }
                }
                break;
            case 3:
                if ($count<1)
                {
                    $on_screen.= "
   <tr>
     <td>".$lang["inst_alogin"]."</td>
	 <td><input type='text' maxlength='10' name='adml' id='adml'></td>
   </tr> 
   <tr>
     <td>".$lang["inst_apwd"]."</td>
	 <td><input type='text' maxlength='10' name='admp' id='admp'></td>
   </tr> 
   <tr>
     <td>".$lang["inst_nick"]."</td>
	 <td><input type='text' maxlength='10' name='anick' id='anick'></td>
   </tr>";
                    $content->set('|button|', "<input type='submit' class='sbutton' name='addadm' id='addadm' value='.'>");
                }
                break;
            case 4:
                $on_screen.="<tr><td>".$lang["inst_fmsg"]."</td></tr>";
                $db->query("UPDATE MWC SET value = '1' WHERE parametr='reinstall'");
                $content->set('|button|', "<input type='submit' class='sbutton' name='finish' id='finish' value='.'>");
                break;
        }
    }
    $on_screen.="</table>";
    $content->set('|content|', $on_screen);
    $content->out_content("imgs/install.html");
}
else
    die("site already installed. ^_^");
ob_end_flush();