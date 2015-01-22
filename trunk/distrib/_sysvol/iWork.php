<?php
/**
* Класс для чтения атрибутов вещей из Item(kor).txt
* by [p4f] epmak 
* v 1.8
* last update 23.02.13
**/


#region Описание Возможностей
# Класс предназначен для чтения атрибутов из базы вещей для MuOnline
# подходит для серверов до появления Rage Fighter (s6)
# Читает файл типа Item(kor).txt/itemsettyoe.txt/itemsetoption.txt/skill.txt/itemaddoption.txt и возвращает ассоциативный массив с информацией по каждой вещи. 
# Как применять:
/*
include "<путь_до_файла_класса>";
$robj = new readitem("Item.txt"); //читаем базу
print "<pre>";
print_r($robj->item); //переменная содержит полученные данные
print "</pre>";
*/
# Формат возвращаеммого массива:
/*      [Weapons]
[index] => 0       Index    
[slot] => 0        Slot    
[skill] => 0       Skill (name or number)       
[x] => 1           X    
[y] => 2           Y    
[serial] => 1      Serial  
[option] => 1      Option   
[drop] => 1        Drop      
[level] => 6       Level  
[dMin] => 6        MinDmg             
[dMax] => 11       MaxDmg             
[speed] => 50      AttSpeed             
[dur] => 20        Dur   
[mdur] => 0        MagDur            
[mpow] => 0        MagPower
[lreq] => 0        LvlReq  
[sreq] => 40       StrReq  
[areq] => 40       AgiReq  
[ereq] => 0        EneReq  
[vreq] => 0        VitReq  
[creq] => 0        CmdReq 
[sattr] => 1       SetAttr  
[dw] => 1          DW/SM   
[dk] => 1          DK/BK   
[elf] => 1         ELF/ME    
[mg] => 1          MG   
[dl] => 1          DL   
[sum] => 1         SUM 
[name] => Kris     Name         
[group] => 0       Item Group
[pvp] = >x|y       pvp option
[anc] = > x|y      ancient set
*/
/*
skill[номер скилла]
name название
rLevel	уровень, с которого доступен
damage  максимальный урон
mana - сколько маны тратится
BP		=	Agility Gauge Usage (Requirement)
rEnergy - сколько ене нужно
rLeader - сколько цмд нужно
*/
#endregion

/*
директива для медленных серверов
*/
//ini_set("max_execution_time","60");
#region claas readitem
class readitem
{
	#region переменные
	var $item; //массив с данными о вещах

	private $firstS = -1; //идентификатор для корректного  отображения вещей при парсинге игцн (ничего более умного пока не придумал)

	#region "базы" атрибутов для гупп вещей
	protected $gW = array("index", "slot", "skill","x","y","serial","option","drop","name","level","dMin","dMax","speed","dur","mdur","mpow","lreq","sreq","areq","ereq","vreq","creq","sattr","dw","dk","elf","mg","dl","sum","rf");//weap
	protected $gA = array("index","slot","skill","x","y","serial","option","drop","name","level","def","block","dur","lreq","sreq","areq","ereq","vreq","creq","sattr","dw","dk","elf","mg","dl","sum","rf"); //arm
	protected $gWn = array("index","slot","skill","x","y","serial","option","drop","name","level","def","dur","lreq","ereq","sreq","areq","creq","buymoney","dw","dk","elf","mg","dl","sum","rf");//12 ICGN
	//protected $gWn = array("index","slot","skill","x","y","serial","option","drop","name","level","def","dur","lreq","ereq","sreq","areq","creq","unknown","buymoney","dw","dk","elf","mg","dl","sum","rf");//12
	protected $gP = array("index","slot","skill","x","y","serial","option","drop","name","level","dur","lreq","ereq","sreq","areq","vreq","creq","Res7","sattr","dw","dk","elf","mg","dl","sum","rf");//13
	protected $gJ = array("index","slot","skill","x","y","serial","option","drop","name","value","level");//14
	protected $gS = array("index","slot","skill","x","y","serial","option","drop","name","level","lreq","ereq","BuyMoney","dw","dk","elf","mg","dl","sum","rf");//15
	#endregion
	#endregion

	/**
	* Конструктор класса
	* @file - адрес до файла с вещами
	* @createphp bool создавать или нет короткий файл с вещами (название и размеры)
	**/
	function __construct($file,$createphp = false)
	{
		$content="";
		if (file_exists($file))
		{
			$dataAr = file($file);

			$i=0;
			$iter = new ArrayIterator($dataAr);
			$cGroup =0; //группа вещи

			foreach($iter as $id=>$value)
			{
				$value = trim($value);
				if (substr($value,0,2)!="//" && strlen($value)>0 && $value!="end")
				{
					if (strlen($value)>0 && strlen($value)<3)
						$cGroup = $value;
					else
					{
						switch ($cGroup)
						{
							case 0:
							case 1:
							case 2:
							case 3:
							case 4:
							case 5:
								$tmp = self::readWeapons($value);
								$tmp["name"] = htmlspecialchars(substr(substr($tmp["name"],1),0,-1));
								$this->item[$cGroup][$tmp["index"]] = $tmp;
								$content.='$items['.$cGroup.']['.$tmp["index"].']["name"]="'.$tmp["name"].'";$items['.$cGroup.']['.$tmp["index"].']["x"]='.$tmp["x"].';$items['.$cGroup.']['.$tmp["index"].']["y"]='.$tmp["y"].';'."\r\n";

								break;
							case 6:
							case 7:
							case 8:
							case 9:
							case 10:
							case 11:
								$tmp = self::readArmors($value);
								$tmp["name"] = htmlspecialchars(substr(substr($tmp["name"],1),0,-1));
								$this->item[$cGroup][$tmp["index"]] = $tmp;
								$content.='$items['.$cGroup.']['.$tmp["index"].']["name"]="'.$tmp["name"].'";$items['.$cGroup.']['.$tmp["index"].']["x"]='.$tmp["x"].';$items['.$cGroup.']['.$tmp["index"].']["y"]='.$tmp["y"].';'."\r\n";

								break;
							case 12:
								$tmp = self::read12($value);
								$tmp["name"] = htmlspecialchars(substr(substr($tmp["name"],1),0,-1));
								$this->item[$cGroup][$tmp["index"]] = $tmp;
								$content.='$items['.$cGroup.']['.$tmp["index"].']["name"]="'.$tmp["name"].'";$items['.$cGroup.']['.$tmp["index"].']["x"]='.$tmp["x"].';$items['.$cGroup.']['.$tmp["index"].']["y"]='.$tmp["y"].';'."\r\n";
								break;
							case 13:
								$tmp = self::read13($value);
								if(!isset($tmp["index"]) or empty($tmp["index"]))
									$tmp["index"]=0;
								$tmp["name"] = htmlspecialchars(substr(substr($tmp["name"],1),0,-1));
								$this->item[$cGroup][$tmp["index"]] = $tmp;
								$content.='$items['.$cGroup.']['.$tmp["index"].']["name"]="'.$tmp["name"].'";$items['.$cGroup.']['.$tmp["index"].']["x"]='.$tmp["x"].';$items['.$cGroup.']['.$tmp["index"].']["y"]='.$tmp["y"].';'."\r\n";
								break;
							case 14:

								$tmp = self::read14($value);
								$tmp["name"] = htmlspecialchars(substr(substr($tmp["name"],1),0,-1));
								if(!isset($tmp["index"]) or empty($tmp["index"]))
									$tmp["index"]=0;
								$this->item[$cGroup][$tmp["index"]] = $tmp;
								$content.='$items['.$cGroup.']['.$tmp["index"].']["name"]="'.$tmp["name"].'";$items['.$cGroup.']['.$tmp["index"].']["x"]='.$tmp["x"].';$items['.$cGroup.']['.$tmp["index"].']["y"]='.$tmp["y"].';'."\r\n";
								break;
							case 15:
								$tmp = self::readSkiils($value);
								$tmp["name"] = htmlspecialchars(substr(substr($tmp["name"],1),0,-1));
								$this->item[$cGroup][$tmp["index"]] = $tmp;
								$content.='$items['.$cGroup.']['.$tmp["index"].']["name"]="'.$tmp["name"].'";$items['.$cGroup.']['.$tmp["index"].']["x"]='.$tmp["x"].';$items['.$cGroup.']['.$tmp["index"].']["y"]='.$tmp["y"].';'."\r\n";
								break;
							default:
								$this->item["error"] = "Unknown item Group";break;
						}
						$i++;
					}
				}
			}
		}
		else
			$this->item["error"]="Can't found $file";

		if(!empty($content) && $createphp)
		{
			$h = fopen("_sysvol/itemBase/items.php","w");
			fwrite($h,'<?php //itembaseArray(autogenered)'.@date("d.m.Y")."\r\n".$content);
			fclose($h);
		}

	}

	/**
	 * возвращает массив с полученными вещами
	 * @return mixed
	 */
	public function getItems()
	{
		return $this->item;
	}

	/**
     * чтение файла
     * @param array|string $file
     * @return array|bool
     */
    protected  function readFile($file)
    {
        if (!is_array($file))
        {
            if (file_exists($file))
            {
                $return = file($file);
                if (is_array($return))
                    return $return;
                return false;
            }
        }
        else
            return $file;
    }

	/**
	* возвращает массив с данными (циферками)
	* @itmar - строка о вещи из базы
	**/
	function getReq ($itmar,$num=3)
	{
		preg_match_all("/([-]?([0-9]{1,}))|([\"]{1}([A-Za-z0-9\&\(\)'\\\,.]{1,40}([\s]{0,})|[-]{0,}){1,5}[\"]{1})/", $itmar, $replaced);
		//preg_match_all("/([-]?([0-9]{1,}))|([\"]{1}([A-Za-z0-9\&\(\)']{1,20}([\s]{0,})|[-]?){1,5}[\"]{1})|([\"]{1}([A-Za-z0-9\&\(\)'\\\,.]{1,20}[\"]{1}))/", $itmar, $replaced);
		return $replaced[0];
	}

	public static function debug($Var)
	{
		print "<pre>";
		print_r($Var);
		print "</pre>";
	}
	
	/**
	* читаем оружие
	* возвращает название вещи
	* @itmar - строка о вещи из базы
	**/
	function readWeapons($itmar)
	{
		$get = self::getReq ($itmar);
		if ($this->firstS==-1) 
		{
			if (count($get)<30)
			$this->firstS = 0;
			else
			$this->firstS = 1;
		}
        $ar = array();
		
		if (count($get)<2)
		$ar["error"]="Can't read item info! $itmar";
		else
		{
			if ($this->firstS == 0)
			$j=0;
			else
                $j = abs(count($get)-30);

			foreach ($this->gW as $i=>$val)
			{
				$ar[$val] = $get[$j];
				$j++;
			}
		}
		return $ar;
	}
	
	/**
	* читаем амуницию
	* возвращает название вещи
	* @itmar - строка о вещи из базы
	**/
	function readArmors($itmar)
	{
		$get = self::getReq ($itmar);

        $ar = array();
		
		if (count($get)<2)
		$ar["error"]="Can't read item info! $itmar";
		else
		{
			if ($this->firstS == 0)
			$j=0;
			else
			$j = abs(count($get)-1-26);

			foreach ($this->gA as $i=>$val)
			{
				$ar[$val] = $get[$j];
				$j++;
			}
		}
		return $ar;
	}
	
	/**
	* читаем группу 12 (венги)
	* возвращает название вещи
	* @itmar - строка о вещи из базы
	**/
	function read12($itmar)
	{
		$get = self::getReq ($itmar,7);
		$ar = array();
		
		if (count($get)<2)
			$ar["error"]="Can't read item info! $itmar";
		else
		{
			if ($this->firstS == 0)
			$j=0;
			else
			$j = abs(count($get)-25);

			foreach ($this->gWn as $i=>$val)
			{
				$ar[$val] = $get[$j];
				$j++;
			}
		}

		return $ar;
	}
	
	/**
	* читаем группу 13
	* возвращает название вещи
	* @itmar - строка о вещи из базы
	**/
	function read13($itmar)
	{
		$get = self::getReq ($itmar);
		$ar = array();
		
		if (count($get)<2)
		$ar["error"]="Can't read item info! $itmar";
		else
		{
			if ($this->firstS == 0)
			$j=0;
			else
			$j = abs(count($get)-26);
			
			foreach ($this->gP as $i=>$val)
			{
				if(isset($get[$j]) && !empty($get[$j]))
					$ar[$val] = $get[$j];
				$j++;
			}
		}
		return $ar;
	}
	
	
	/**
	* читаем группу 14
	* возвращает название вещи
	* @itmar - строка о вещи из базы
	**/
	function read14($itmar)
	{
		$get = self::getReq ($itmar);
		$ar = array();
		
		
		if (count($get)<2)
		$ar["error"]="Can't read item info! $itmar";
		else
		{
			if ($this->firstS == 0)
			$j=0;
			else
			$j = abs(count($get)-11);
			
			foreach ($this->gJ as $i=>$val)
			{
				$ar[$val] = $get[$j];
				$j++;
			}
		}
		return $ar;
	}
	
	/**
	* читаем манию и скиллы
	* возвращает название вещи
	* @itmar - строка о вещи из базы
	**/
	function readSkiils($itmar)
	{
		$get = self::getReq ($itmar,6);
		$ar = array();

		if (count($get)<2)
			$ar["error"]="Can't read item info! $itmar";
		else
		{
			if ($this->firstS == 0)
			$j=0;
			else
			$j = abs(count($get)-20);

			
			foreach ($this->gS as $i=>$val)
			{
				$ar[$val] = $get[$j];
				$j++;
			}

		}
		return $ar;
	}
}
#endregion