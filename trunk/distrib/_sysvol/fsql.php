<?php

include "adodb5/adodb-exceptions.inc.php";
include "adodb5/adodb.inc.php";
error_reporting(E_ALL);
class connect
{
    private $resId; // идентификатор ресурсов
    private $iserror; //идентификатор ошибки
    private $btype; //тип подключения
    private $lastq;//последний запрос
    private $ntype;
    private $cons = array(
        "SQL",
        "MPDO",
        "ODBC"
    );

    /**
     * @param $type
     * @param $host
     * @param $base
     * @param $user
     * @param $pwd
     * @throws Exception
     */
    public function __construct ($type,$host,$base,$user,$pwd)
    {
        $this->iserror=false;
        $this->btype=$type;
        global $ADODB_FETCH_MODE;
        $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

        switch ($type)
        {
            case "SQL":
            case 1:
                $this->mmsql($host,$base,$user,$pwd);$this->ntype=1; break;    //ms sql connection
            case "MPDO":
            case 2:
                $this->pdo_mssql($host,$base,$user,$pwd);$this->ntype=2;break; //pdo ms sql connection
            case "ODBC":
            case 3:
                $this->odbc_mmsql($host,$base,$user,$pwd);$this->ntype=3;break; //odbc mssql connection
            default:
                throw new Exception("Unknown connect Type");
        }
    }

    /**
     * возвращает поддерживаемые подключения
     * @return array
     */
    public function SupportedCon()
    {
        return $this->cons;
    }

    public function ConType()
    {
        return $this->ntype;
    }

    /**
     * функция возвращает последний insert id
     * @param string $tbname - название таблицы, куда была последняя вставка
     * @return int id
     */
    public function lastId($tbname=null)
    {
        if (!$tbname)
            return NULL;

        $res = self::query("SELECT IDENT_CURRENT('{$tbname}') as lastid")->FetchRow();
        return $res["lastid"];
    }

    /**
     * MS SQL подключение к базе данных
     * @param $host
     * @param $base
     * @param $user
     * @param $pwd
     * @throws Exception
     */
    private function mmsql($host,$base,$user,$pwd)
    {
        if (function_exists("mssql_connect"))
        {
            $this->resId = ADONewConnection('mssql');
            $this->resId->PConnect($host,$user,$pwd,$base);
        }
        else
            throw new Exception("mssql_connect is NOT supported!");
    }


    /**
     * ODBC подключение к базе данных
     * @param $host
     * @param $base
     * @param $user
     * @param $pwd
     * @throws Exception
     */
    private function odbc_mmsql($host,$base,$user,$pwd)
    {

        if (function_exists("odbc_connect"))
        {
            $this->resId = &ADONewConnection('odbc_mssql');
            $dsn = "Driver={SQL Server};Server=$host;Database=$base;";
            $this->resId->debug=false;
            $this->resId->PConnect($dsn,$user,$pwd);
        }
        else
            throw new Exception("odbc_connect is NOT supported!");
    }


    /**
     * PDO mssql подключение к базе данных
     * @param $host
     * @param $base
     * @param $user
     * @param $pwd
     * @throws Exception
     */
    private function pdo_mssql($host,$base,$user,$pwd)
    {
        $drivers = PDO::getAvailableDrivers();
        if (in_array("mssql",$drivers))
        {
            $this->resId =&NewADOConnection("pdo_mssql://{$user}:{$pwd}@{$host}/{$base}");
        }
        else
            throw new Exception("PDO_mssql is NOT supported!");
    }


    public function Msg()
    {
        return $this->resId->ErrorMsg();
    }

    public function query($qtext)
    {
        try
        {
            $this->lastq = $qtext;
            return $this->resId->Execute($qtext);
        }
        catch (Exception $ex)
        {
            throw $ex;
        }
    }

    public function  getQuery()
    {
        return $this->lastq;
    }

    public function close()
    {
        $this->resId->Close();
    }
}