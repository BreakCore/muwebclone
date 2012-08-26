<?php
/*
* MWC database framework
* ver 0.7 [07.02.2012]
* coded by epmak
*/
class Connect
{
  private $res_id;               //ресурс, линк
  private $type;                 //тип подключения
  private $errors;               //уровень ошибок
  private $cur_query;            //текст текущего запроса
  private $errmsg;				 // текст шибки
  private $constate;
  private $result;              //
  /*
  * Конструктор класса 
  */
  function Connect ($conn_type="0", $host="0", $database="0", $user="0", $password="0", $driver, $err_lvl=1)
  {
   if ($conn_type == "0" || $host== "0" || $database== "0" || $user== "0" || $password=="0")
   {
    $this->logs(" не заполнены все поля, следует проверить настройки");
    return false;
   }  
   
   ($err_lvl>=1)? $this->errors =1:$this->errors=0; 
   $this->type = $conn_type;

   if ($conn_type=="SQL")
   {
    if (function_exists("mssql_connect"))
    {
     $conn = mssql_connect($host,$user,$password) or $this->logs("Can't connect to SQL!") ;

     if ($this->res_id = @mssql_select_db($database,$conn))
     {
       $this->constate = "<div style='font-weight:bold;color:green;'>Connection Work!</div>";
     }
     else
     {
      $this->logs("mssql_connect: no connect to tadabase in host: $host and user: $user");
      //echo "no db connect!";
     }
	}
	else
    {
	 $this->constate = "<div style='font-weight:bold;color:red;'>Connection <u>Not</u> Work!</div>";
     $this->logs("Can't execute mssql functions! ");
	}
   }
   elseif ($conn_type == "ODBC")
   {
    if (@strlen($driver)<1)
    {
     $driver="SQL Server";
     $this->logs("ODBC: Warning, no driver writen, set default: \"SQL Server\"");
    }
    $this->res_id  = @odbc_connect("Driver={".$driver."};Server=".$host.";Database=".$database.";",$user,$password) or  $this->logs(@odbc_errormsg($this->res_id)." no odbc connect!");
    if (odbc_error())
    {
     $this->logs(@odbc_errormsg($this->res_id)."  no odbc connect!");
	  $this->constate = "<div style='font-weight:bold;color:red;'>Connection <u>Not</u> Work!</div>";
	 unset($this->res_id);
     return false;
    }
	else  $this->constate = "<div style='font-weight:bold;color:green;'>Connection Work!</div>";
   }
  }
  
  /*
  * Проверка подключение и проверка подключения. возвращает тип подключения
  */
  function check_c()
  {
   return $this->constate;
  }
  
  /*
  * запрос
  */
  function query($query)
  {
   $this->cur_query = $query;
  // $this->logs($query);
   if ($this->type == "SQL")
   {
    if($n=@mssql_query($query))
    {
	 $this->result = $n;
     return $n;
    }
    else
    { 
     $this->getmsg();
     unset($n);
    }
   }
   else if ($this->type == "ODBC")
   {	
    $n = @odbc_exec($this->res_id,$query);
	$this->result = $n;
    if(@odbc_errormsg($this->res_id)) $this->getmsg();
    return $n;
   }
  }
  
  function numrows($query)
  {
   if(@strlen($query)>18)
	  $query=$this->query($query);
     
   if ($this->type == "SQL") return @mssql_num_rows($query);
   else if ($this->type == "ODBC")
   {
     $nr = @odbc_num_rows($query);
     if ($nr == -1)
     {
	 $i=0;
      while ($n=odbc_fetch_row($query))
      {
       ($n>0)? $i++:false;      
      }
      return $i;
     }
     else 
	 {
      return $nr;
	 }
	 return 0; 
   }
  }
  
  function fetchrow($query)
  {
    if(@strlen($query)>18)
	  $query=$this->query($query);

    if ($this->type == "SQL") return @mssql_fetch_row($query);
	else if ($this->type == "ODBC")
	{
	  $i = @odbc_num_fields($query);
	  $status = @odbc_fetch_row($query);
	  if ($status !=1) return false;
	  $field=1;
	  while ($field<=$i)
	  {
	    $arr[($field-1)] = @odbc_result($query,$field);
		$field++;
	  }
	  return $arr;
	}
  }
  
  function fetcharray($query)
  {
    if(@strlen($query)>18)
	  $query=$this->query($query);

    if ($this->type == "SQL") return @mssql_fetch_array($query);
	else if ($this->type == "ODBC")
	{
	 $status = @odbc_fetch_array($query);
	 if(is_array($status)) 
	 {
	  return $status;
	 }
	 else return false;
	}
  }
  
  function close()
  {
	if ($this->type == "SQL") 
	   @mssql_close($this->res_id);
	else
	   @odbc_close($this->res_id);
	unset($this->res_id);
  }

  function getmsg()
  {
   if ($this->type == "SQL")
      $n=@mssql_get_last_message();
	  
   elseif ($this->type == "ODBC")
      $n=@odbc_errormsg($this->res_id);

   if(@strlen($n)>1 && @strlen($this->cur_query)>1 && substr($n,0,2)!="0x")
   {
    if($this->errmsg != $n)
    {	   
     $this->logs("Found some errors: ".$n." query text: ".$this->cur_query." page:".$_GET["p"]);
     unset($this->cur_query);
     $this->errmsg = $n;
     if($this->errors==1)  echo $n;		
    }
    unset($n);
   }
  }
  
  function showmsg()
  {
   if ($this->type == "SQL")
   {
      $n=@mssql_get_last_message();
	  if (!$n) return mssql_result($this->result);
   }
	  
   elseif ($this->type == "ODBC")
   {
      $n=@odbc_errormsg($this->res_id);
	  if(!$n) return odbc_result_all($this->result,"align='center'");
   }
	return $n;
  }
  
  function lastmsg()
  {
   if ($this->type == "SQL") return mssql_get_last_message();
   elseif ($this->type == "ODBC") 
   {
	if (@odbc_error($this->res_id))return "error!";
   }
   else return false;
  }
  
  private function logs($message="nan")
  {
    if ($message=="nan") $message = "epmty message... check your confings and code.";
    if($handle = fopen('logZ/connect['.@date("d_m_Y", time()).'].log', 'a+'))
    {
      fwrite($handle, "[".@date("H:i:s", time())."] - >> ".$message." << \r\n");  
      fclose($handle);		
    }	
  }
}
