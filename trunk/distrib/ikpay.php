<?php
/**
 * Created by epmak
 * Date: 24.01.14
 * Time: 14:31
 * MuWebClone
 * прием оплаты
 */

define ('insite', 1);
define ('inpanel', 1);

require_once "_sysvol/logs.php";
require_once "_sysvol/security.php";
require_once "_sysvol/fsql.php";
require_once 'opt.php';
require_once "_sysvol/engine.php";


$valid = new valid();
$db = new connect ($config["ctype"], $config["db_host"], $config["db_name"], $config["db_user"], $config["db_upwd"]);
$content = new content($config["siteaddress"],"site",substr($_SESSION["mwclang"],0,3),0,$config["theme"]);
$dataSet = array();

try
{
    $server = (int)$_REQUEST["ik_x_server"];
    if(empty($server))
        $server = 0;

    foreach ($_REQUEST as $key => $value)
        {
            if (!preg_match('/ik_/', $key))
                continue;
            $dataSet[$key] = $value;
        }

        $ik_sign = $dataSet['ik_sign'];
        unset($dataSet['ik_sign']);

        ksort($dataSet, SORT_STRING); // сортируем по ключам в алфавитном порядке элементы массива
        array_push($dataSet, $ikpay["ik_secretkay"]); // добавляем в конец массива "секретный ключ"
        $signString = implode(':', $dataSet); // конкатенируем значения через символ ":"
        $sign = base64_encode(md5($signString, true)); // берем MD5 хэш в бинарном виде по сформированной строке и кодируем в BASE64


        if($ik_sign == $sign && $dataSet["ik_co_id"] == $ikpay["ik_shop_id"])
        {

            $info = $db->query("SELECT col_memb_id,col_sum,col_state FROM MWC_ikpay WHERE col_ik_id='{$dataSet["ik_pm_no"]}'")->FetchRow();
            if(isset($info["col_sum"]) && $info["col_state"] == '0')
            {
                if($info["col_sum"] ==  $dataSet["ik_am"])
                {
                    $db->query("UPDATE MWC_ikpay SET col_state = '1', col_DateCoplete = GETDATE() WHERE col_ik_id='{$dataSet["ik_pm_no"]}';
                    UPDATE memb_info SET credits = credits +".round($info["col_sum"]/$ikpay["ik_rate"])." WHERE memb__id ='{$info["col_memb_id"]}'");
                    logs::WriteLogs("ikpay","Pay id={$dataSet["ik_pm_no"]} column sum {$info["col_sum"]} acc {$info["col_memb_id"]} credits: ".round($info["col_sum"]/$ikpay["ik_rate"]),"pays",41);
                    header("HTTP/1.0 200 OK");
                }
                else{
                    $db->query("UPDATE MWC_ikpay SET col_state = '2', col_DateCoplete = GETDATE() WHERE col_ik_id='{$dataSet["ik_pm_no"]}'"); //log that fail
                    logs::WriteLogs("ikpay","Pay id={$dataSet["ik_pm_no"]} column sum {$info["col_sum"]} !={$dataSet["ik_am"]} ","pays",41);
                    header('HTTP/1.0 400 Bad Request');
                }
            }
            else
            {
                logs::WriteLogs("ikpay","Pay id={$dataSet["ik_pm_no"]} NOT found or status ({$info["col_state"]}) not 'wait' state :{$dataSet["ik_inv_st"]} ","pays",41);
                header('HTTP/1.0 400 Bad Request');
            }
        }
        else
        {
            logs::WriteLogs("ikpay","Pay id={$dataSet["ik_pm_no"]} NOT found or {$ik_sign} != {$sign} && {$dataSet["ik_co_id"]} != {$ikpay["ik_shop_id"]}, state :{$dataSet["ik_inv_st"]}","pays",41);
            header('HTTP/1.0 400 Bad Request');
        }
}
catch(ADODB_Exception $ex)
{
    $agr = $ex->getTrace();
    $msg = " Connection type: [b]{$agr[0]["args"][0]}[/b], Error: [b]".iconv("Windows-1251","UTF-8",$agr[0]["args"][3])."[/b] ";
    logs::WriteLogs("SQL",$msg);
    die("sql err");
}