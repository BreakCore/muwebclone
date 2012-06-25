<?php
/**
* Класс для чтения опций вещей
**/
class items
{
 
  /**
  * добавление бинарного кода вещей в переменную в классе
  * @bighex - бинарный код, может быть с 0х.. но обязательно должен быть более 99 символов
  **/
  public function additmz($bighex)
  {
   if (strlen($bighex)>100)
   {
    if (substr($cachitems,0,2)=="0x") $cachitems = substr($cachitems,2);
    else $cachitems=strtoupper($cachitems);
	
	return $cachitems;
   }
   else echo "Wrong item format::additmz";
   return "";
  }
  
  /**
  * функция чтения вещи для вывода на экран
  * @hex - Бинарный код вещи
  * @itembd - массив с описанием вещей
  * @ancbd - массив с описанием анц названия вещей
  * @type - Тип вывода на экран
  **/
  public function readitems($hex,$itembd,$ancbd)
  {
   $is32=false;
      
    if (strlen($hex)!= 32)
       if (strlen($hex)!= 16) return "wrong item format!"; 
	   else $is32=false;
	else $is32=true;
     
	 
   if($is32) //если вещи хотя бы 2го сезона
   {
    
	$id = items::dehex($hex,0,2);
    $options = items::dehex($hex,2,2);
    $group = floor(items::dehex($hex,18,2)/16);
    $excopt = items::dehex($hex,14,2);

	
	$result = array();
	$result["intopt"]=$options;
	$result["group"] = $group;
	$result["id"] = $id;
	
	if ($options>0 || $excopt>0) $result += items::getOptions($group,$id,$options,$excopt);
	$result["equipment"] =items::showEq($group,$id,$itembd);
	$result = items::GetName($result,$itembd);
	
	if ($result["name"] && strlen($result["name"])<28)
	{
	 if (items::dehex($hex,19,1)==1) $lvl380 = "Additional Dmg+200<br>Pow Success Rate +10";
     else $lvl380 = "";
	 $harmony = items::dehex($hex,20,1);
	 $hlvl = items::dehex($hex,21,1);
	 $sockets =  strtoupper(substr($hex,22));
	 $ancient =  items::dehex($hex,17,1);
	 
	 
	 if ($ancient>0) 
	 {
	  $result += items::isAncient($group,$id,$ancient,$ancbd);
	  if (empty($result["ancient"])) unset($result["ancient"]);
	 }
	 if ($sockets!="FFFFFFFFFF" or  $sockets!="0000000000") 
	 {
	  $result +=items::Sockets($sockets);
	  if (empty($result["sokets"])) unset($result["sokets"]);
	 }
	 if ($harmony>0 && $hlvl>0)
     {
	  $result["harmony"] =items::GetHarmony($group,$harmony,$hlvl);
	  if (empty($result["harmony"])) unset($result["harmony"]);
	  
	 }
	 if (!empty($lvl380))$result["pvp"]=$lvl380;
	 
	}
   }
   
   return $result;
 }
  
  /**
  * возвращает хармони опции
  * @g группа вещи
  * @opt номер опции
  * @hlvl уровень опции
  **/
  
  private function GetHarmony($g,$opt,$hlvl)
  {
   if($g<5)
   {
   $hopt[1][0]="Min Attack Power Increase +2";
	$hopt[1][1]="Min Attack Power Increase +3";
	$hopt[1][2]="Min Attack Power Increase +4";
	$hopt[1][3]="Min Attack Power Increase +5";
	$hopt[1][4]="Min Attack Power Increase +6";
	$hopt[1][5]="Min Attack Power Increase +7";
	$hopt[1][6]="Min Attack Power Increase +9";
	$hopt[1][7]="Min Attack Power Increase +11";
	$hopt[1][8]="Min Attack Power Increase +12";
	$hopt[1][9]="Min Attack Power Increase +14";
	$hopt[1][10]="Min Attack Power Increase +15";
	$hopt[1][11]="Min Attack Power Increase +16";
	$hopt[1][12]="Min Attack Power Increase +17";
	$hopt[1][13]="Min Attack Power Increase +20";
	$hopt[1][14]="Min Attack Power Increase +100000";
	$hopt[1][15]="Min Attack Power Increase +110000";

	$hopt[2][0]="Max Attack Power Increase +3";
	$hopt[2][1]="Max Attack Power Increase +4";
	$hopt[2][2]="Max Attack Power Increase +5";
	$hopt[2][3]="Max Attack Power Increase +6";
	$hopt[2][4]="Max Attack Power Increase +7";
	$hopt[2][5]="Max Attack Power Increase +8";
	$hopt[2][6]="Max Attack Power Increase +10";
	$hopt[2][7]="Max Attack Power Increase +12";
	$hopt[2][8]="Max Attack Power Increase +14";
	$hopt[2][9]="Max Attack Power Increase +17";
	$hopt[2][10]="Max Attack Power Increase +20";
	$hopt[2][11]="Max Attack Power Increase +23";
	$hopt[2][12]="Max Attack Power Increase +26";
	$hopt[2][13]="Max Attack Power Increase +29";
	$hopt[2][14]="Max Attack Power Increase +100000";
	$hopt[2][15]="Max Attack Power Increase +110000";

	$hopt[3][0]="Need Strength Decrease -6";
	$hopt[3][1]="Need Strength Decrease -8";
	$hopt[3][2]="Need Strength Decrease -10";
	$hopt[3][3]="Need Strength Decrease -12";
	$hopt[3][4]="Need Strength Decrease -14";
	$hopt[3][5]="Need Strength Decrease -16";
	$hopt[3][6]="Need Strength Decrease -20";
	$hopt[3][7]="Need Strength Decrease -23";
	$hopt[3][8]="Need Strength Decrease -26";
	$hopt[3][9]="Need Strength Decrease -29";
	$hopt[3][10]="Need Strength Decrease -32";
	$hopt[3][11]="Need Strength Decrease -35";
	$hopt[3][12]="Need Strength Decrease -37";
	$hopt[3][13]="Need Strength Decrease -40";
	$hopt[3][14]="Need Strength Decrease +100000";
	$hopt[3][15]="Need Strength Decrease +110000";

	$hopt[4][0]="Need Agility Decrease -6";
	$hopt[4][1]="Need Agility Decrease -8";
	$hopt[4][2]="Need Agility Decrease -10";
	$hopt[4][3]="Need Agility Decrease -12";
	$hopt[4][4]="Need Agility Decrease -14";
	$hopt[4][5]="Need Agility Decrease -16";
	$hopt[4][6]="Need Agility Decrease -20";
	$hopt[4][7]="Need Agility Decrease -23";
	$hopt[4][8]="Need Agility Decrease -26";
	$hopt[4][9]="Need Agility Decrease -29";
	$hopt[4][10]="Need Agility Decrease -32";
	$hopt[4][11]="Need Agility Decrease -35";
	$hopt[4][12]="Need Agility Decrease -37";
	$hopt[4][13]="Need Agility Decrease -40";
	$hopt[4][14]="Need Agility Decrease +100000";
	$hopt[4][15]="Need Agility Decrease +110000";

	$hopt[5][6]="Attack (Min,Max) Increase +7";
	$hopt[5][7]="Attack (Min,Max) Increase +8";
	$hopt[5][8]="Attack (Min,Max) Increase +9";
	$hopt[5][9]="Attack (Min,Max) Increase +11";
	$hopt[5][10]="Attack (Min,Max) Increase +12";
	$hopt[5][11]="Attack (Min,Max) Increase +14";
	$hopt[5][12]="Attack (Min,Max) Increase +16";
	$hopt[5][13]="Attack (Min,Max) Increase +19";

	$hopt[6][6]="Critical Damage Increase +12";
	$hopt[6][7]="Critical Damage Increase +14";
	$hopt[6][8]="Critical Damage Increase +16";
	$hopt[6][9]="Critical Damage Increase +18";
	$hopt[6][10]="Critical Damage Increase +20";
	$hopt[6][11]="Critical Damage Increase +22";
	$hopt[6][12]="Critical Damage Increase +24";
	$hopt[6][13]="Critical Damage Increase +30";

	$hopt[7][9]="Skill Power Increase +12";
	$hopt[7][10]="Skill Power Increase +14";
	$hopt[7][11]="Skill Power Increase +16";
	$hopt[7][12]="Skill Power Increase +18";
	$hopt[7][13]="Skill Power Increase +22";

	$hopt[8][9]="Attack Rate Increase +5";
	$hopt[8][10]="Attack Rate Increase +7";
	$hopt[8][11]="Attack Rate Increase +9";
	$hopt[8][12]="Attack Rate Increase +11";
	$hopt[8][13]="Attack Rate Increase +14";

	$hopt[9][9]="SD Rate Increase +3";
	$hopt[9][10]="SD Rate Increase +5";
	$hopt[9][11]="SD Rate Increase +7";
	$hopt[9][12]="SD Rate Increase +9";
	$hopt[9][13]="SD Rate Increase +10";

	$hopt[10][13]="SD Ignore Rate Increase +10";
  }
   else if($g==5)
   {
    $hopt[1][0]="Magic Power Increase +6";
    $hopt[1][1]="Magic Power Increase +8";
    $hopt[1][2]="Magic Power Increase +10";
    $hopt[1][3]="Magic Power Increase +12";
	$hopt[1][4]="Magic Power Increase +14";
	$hopt[1][5]="Magic Power Increase +16";
	$hopt[1][6]="Magic Power Increase +17";
	$hopt[1][7]="Magic Power Increase +18";
	$hopt[1][8]="Magic Power Increase +19";
	$hopt[1][9]="Magic Power Increase +21";
	$hopt[1][10]="Magic Power Increase +23";
	$hopt[1][11]="Magic Power Increase +25";
	$hopt[1][12]="Magic Power Increase +27";
	$hopt[1][13]="Magic Power Increase +31";
	$hopt[1][14]="Magic Power Increase +100000";
	$hopt[1][15]="Magic Power Increase +110000";

	$hopt[2][0]="Need Strength Decrease -6";
	$hopt[2][1]="Need Strength Decrease -8";
	$hopt[2][2]="Need Strength Decrease -10";
	$hopt[2][3]="Need Strength Decrease -12";
	$hopt[2][4]="Need Strength Decrease -14";
	$hopt[2][5]="Need Strength Decrease -16";
	$hopt[2][6]="Need Strength Decrease -20";
	$hopt[2][7]="Need Strength Decrease -23";
	$hopt[2][8]="Need Strength Decrease -26";
	$hopt[2][9]="Need Strength Decrease -29";
	$hopt[2][10]="Need Strength Decrease -32";
	$hopt[2][11]="Need Strength Decrease -35";
	$hopt[2][12]="Need Strength Decrease -37";
	$hopt[2][13]="Need Strength Decrease -40";
	$hopt[2][14]="Need Strength Decrease +100000";
	$hopt[2][15]="Need Strength Decrease +110000";

	$hopt[3][0]="Need Agility Decrease -6";
	$hopt[3][1]="Need Agility Decrease -8";
	$hopt[3][2]="Need Agility Decrease -10";
	$hopt[3][3]="Need Agility Decrease -12";
	$hopt[3][4]="Need Agility Decrease -14";
	$hopt[3][5]="Need Agility Decrease -16";
	$hopt[3][6]="Need Agility Decrease -20";
	$hopt[3][7]="Need Agility Decrease -23";
	$hopt[3][8]="Need Agility Decrease -26";
	$hopt[3][9]="Need Agility Decrease -29";
	$hopt[3][10]="Need Agility Decrease -32";
	$hopt[3][11]="Need Agility Decrease -35";
	$hopt[3][12]="Need Agility Decrease -37";
	$hopt[3][13]="Need Agility Decrease -40";
	$hopt[3][14]="Need Agility Decrease +100000";
	$hopt[3][15]="Need Agility Decrease +110000";

	$hopt[4][6]="Skill Power Increase +7";
	$hopt[4][7]="Skill Power Increase +10";
	$hopt[4][8]="Skill Power Increase +13";
	$hopt[4][9]="Skill Power Increase +16";
	$hopt[4][10]="Skill Power Increase +19";
	$hopt[4][11]="Skill Power Increase +22";
	$hopt[4][12]="Skill Power Increase +25";
	$hopt[4][13]="Skill Power Increase +30";

	$hopt[5][6]="Critical Damage Increase +10";
	$hopt[5][7]="Critical Damage Increase +12";
	$hopt[5][8]="Critical Damage Increase +14";
	$hopt[5][9]="Critical Damage Increase +16";
	$hopt[5][10]="Critical Damage Increase +18";
	$hopt[5][11]="Critical Damage Increase +20";
	$hopt[5][12]="Critical Damage Increase +22";
	$hopt[5][13]="Critical Damage Increase +28";

	$hopt[6][9]="SD Rate Increase +4";
	$hopt[6][10]="SD Rate Increase +6";
	$hopt[6][11]="SD Rate Increase +8";
	$hopt[6][12]="SD Rate Increase +10";
	$hopt[6][13]="SD Rate Increase +13";

	//$hopt[7][13]="Attack Rate Increase";

	//$hopt[8][13]="SD Ignore Rate Increase";
   }
   else if($g>5 && $g<12)
   { 
    $hopt[1][0]="Def Power Increase +3";
	$hopt[1][1]="Def Power Increase +4";
	$hopt[1][2]="Def Power Increase +5";
	$hopt[1][3]="Def Power Increase +6";
	$hopt[1][4]="Def Power Increase +7";
	$hopt[1][5]="Def Power Increase +8";
	$hopt[1][6]="Def Power Increase +10";
	$hopt[1][7]="Def Power Increase +12";
	$hopt[1][8]="Def Power Increase +14";
	$hopt[1][9]="Def Power Increase +16";
	$hopt[1][10]="Def Power Increase +18";
	$hopt[1][11]="Def Power Increase +20";
	$hopt[1][12]="Def Power Increase +22";
	$hopt[1][13]="Def Power Increase +25";
	$hopt[1][14]="Def Power Increase +100000";
	$hopt[1][15]="Def Power Increase +110000";

	$hopt[2][3]="Max AG Increase +4";
	$hopt[2][4]="Max AG Increase +6";
	$hopt[2][5]="Max AG Increase +8";
	$hopt[2][6]="Max AG Increase +10";
	$hopt[2][7]="Max AG Increase +12";
	$hopt[2][8]="Max AG Increase +14";
	$hopt[2][9]="Max AG Increase +16";
	$hopt[2][10]="Max AG Increase +18";
	$hopt[2][11]="Max AG Increase +20";
	$hopt[2][12]="Max AG Increase +22";
	$hopt[2][13]="Max AG Increase +25";

	$hopt[3][3]="Max HP Increase +7";
	$hopt[3][4]="Max HP Increase +9";
	$hopt[3][5]="Max HP Increase +11";
	$hopt[3][6]="Max HP Increase +13";
	$hopt[3][7]="Max HP Increase +15";
	$hopt[3][8]="Max HP Increase +17";
	$hopt[3][9]="Max HP Increase +19";
	$hopt[3][10]="Max HP Increase +21";
	$hopt[3][11]="Max HP Increase +23";
	$hopt[3][12]="Max HP Increase +25";
	$hopt[3][13]="Max HP Increase +30";

	$hopt[4][6]="HP Auto Rate Increase +1";
	$hopt[4][7]="HP Auto Rate Increase +2";
	$hopt[4][8]="HP Auto Rate Increase +3";
	$hopt[4][9]="HP Auto Rate Increase +4";
	$hopt[4][10]="HP Auto Rate Increase +5";
	$hopt[4][11]="HP Auto Rate Increase +6";
	$hopt[4][12]="HP Auto Rate Increase +7";
	$hopt[4][13]="HP Auto Rate Increase +8";

	$hopt[5][9]="MP Auto Rate Increase +1";
	$hopt[5][10]="MP Auto Rate Increase +2";
	$hopt[5][11]="MP Auto Rate Increase +3";
	$hopt[5][12]="MP Auto Rate Increase +4";
	$hopt[5][13]="MP Auto Rate Increase +5";

	$hopt[6][9]="Def Success Rate Increase +3";
	$hopt[6][10]="Def Success Rate Increase +4";
	$hopt[6][11]="Def Success Rate Increase +5";
	$hopt[6][12]="Def Success Rate Increase +6";
	$hopt[6][13]="Def Success Rate Increase +8";

	$hopt[7][9]="Damage Rate Increase +3";
	$hopt[7][10]="Damage Rate Increase +4";
	$hopt[7][11]="Damage Rate Increase +5";
	$hopt[7][12]="Damage Rate Increase +6";
	$hopt[7][13]="Damage Rate Increase +7";

	//$hopt[8][0]="SD Rate Increase";
   }


   return $hopt[$opt][$hlvl];
  }
  
  
 /**
 * функция возвращает сокеты, если они есть на вещи
 **/
 private function Sockets($hex)
 {
  $sockets =array();
  if ($hex!="FFFFFFFFFF" or $hex!="0000000000") 
  {
   $socket = array(
					'FE' => 'Empty socket',
					'01' => 'Fire (Increase Damage/SkillPower (*lvl)) + 20',
					'33' => 'Fire (Increase Damage/SkillPower (*lvl)) + 400',
					'65' => 'Fire (Increase Damage/SkillPower (*lvl)) + 400',
					'97' => 'Fire (Increase Damage/SkillPower (*lvl)) + 400',
					'C9' => 'Fire (Increase Damage/SkillPower (*lvl)) + 400',
					'02' => 'Fire (Increase Attack Speed) + 7',
					'34' => 'Fire (Increase Attack Speed) + 1',
					'03' => 'Fire (Increase Maximum Damage/Skill Power) + 30',
					'35' => 'Fire (Increase Maximum Damage/Skill Power) + 1',
					'04' => 'Fire (Increase Minimum Damage/Skill Power) + 20',
					'36' => 'Fire (Increase Minimum Damage/Skill Power) + 1',
					'05' => 'Fire (Increase Damage/Skill Power) + 20',
					'37' => 'Fire (Increase Damage/Skill Power) + 1',
					'06' => 'Fire (Decrease AG Use) + 40',
					'38' => 'Fire (Decrease AG Use) + 1',
					'0B' => 'Water (Increase Defense Success Rate) + 10',
					'D3' => 'Water (Increase Defense Success Rate) + 1',
					'0C' => 'Water (Increase Defense) + 30',
					'3E' => 'Water (Increase Defense) + 1',
					'0D' => 'Water (Increase Defense Shield) + 7',
					'D5' => 'Water (Increase Defense Shield) + 1',
					'0E' => 'Water (Damage Reduction) + 4',
					'D6' => 'Water (Damage Reduction) + 1',
					'0F' => 'Water (Damage Reflections) + 5',
					'41' => 'Water (Damage Reflections) + 1',
					'11' => 'Ice (Increases + Rate of Life After Hunting) + 8',
					'43' => 'Ice (Increases + Rate of Life After Hunting) + 49',
					'75' => 'Ice (Increases + Rate of Life After Hunting) + 50',
					'A7' => 'Ice (Increases + Rate of Life After Hunting) + 51',
					'D9' => 'Ice (Increases + Rate of Life After Hunting) + 52',
					'12' => 'Ice (Increases + Rate of Mana After Hunting) + 8',
					'44' => 'Ice (Increases + Rate of Mana After Hunting) + 49',
					'76' => 'Ice (Increases + Rate of Mana After Hunting) + 50',
					'A8' => 'Ice (Increases + Rate of Mana After Hunting) + 51',
					'DA' => 'Ice (Increases + Rate of Mana After Hunting) + 52',
					'13' => 'Ice (Increase Skill Attack Power) + 37',
					'45' => 'Ice (Increase Skill Attack Power) + 1',
					'14' => 'Ice (Increase Attack Success Rate) + 25',
					'46' => 'Ice (Increase Attack Success Rate) + 1',
					'15' => 'Ice (Item Duarability Reinforcement) + 30',
					'47' => 'Ice (Item Duarability Reinforcement) + 1',
					'16' => 'Wind (Increase Life AutoRecovery) + 8',
					'48' => 'Wind (Increase Life AutoRecovery) + 1',
					'17' => 'Wind (Increase Maximum Life) + 4',
					'49' => 'Wind (Increase Maximum Life) + 1',
					'18' => 'Wind (Increase Maximum Mana) + 4',
					'4A' => 'Wind (Increase Maximum Mana) + 1',
					'19' => 'Wind (Increase Mana AutoRecovery) + 7',
					'4B' => 'Wind (Increase Mana AutoRecovery) + 1',
					'1A' => 'Wind (Increase Maximum AG) + 25',
					'4C' => 'Wind (Increase Maximum AG) + 1',
					'1B' => 'Wind (Increase AG Amount) + 3',
					'4D' => 'Wind (Increase AG Amount) + 1',
					'1E' => 'Lightning (Increase Excellent Damage) + 15',
					'50' => 'Lightning (Increase Excellent Damage) + 1',
					'1F' => 'Lightning (Increase Excellent Damage Success Rate) + 10',
					'51' => 'Lightning (Increase Excellent Damage Success Rate) + 1',
					'20' => 'Lightning (Increase Critical Damage) + 30',
					'52' => 'Lightning (Increase Critical Damage) + 1',
					'21' => 'Lightning (Increase Critical Damage Success Rate) + 8',
					'53' => 'Lightning (Increase Critical Damage Success Rate) + 1',
					'85' => 'Lightning (Increase Critical Damage Success Rate) + 1',
					'B7' => 'Lightning (Increase Critical Damage Success Rate) + 1',
					'E9' => 'Lightning (Increase Critical Damage Success Rate) + 1',
					'25' => 'Ground (Increase Stamina) + 30',
					'57' => 'Ground (Increase Stamina) + 1',
					'89' => 'Ground (Increase Stamina) + 1',
					'BB' => 'Ground (Increase Stamina) + 1',
					'ED' => 'Ground (Increase Stamina) + 1');
		$i=0;
		while ($i<10)
		{
		 if ($socket[substr($hex,$i,2)]!="") $sockets[].="<br><span style=color:#9400D3;font-size:8px;font-weight:bold;>".$socket[substr($hex,$i,2)]."</span>";
			$i+=2;
		}
	}
	$ss["sokets"] = $sockets;
	return $ss;
 }
 
 /**
 *фозвращает массив с опциями, вписав имя и исправив в некоторых случаях уровень
 *@g - группа @id - номер @opt - массив с уже известными опциями @itembd - база имен вещей
 **/
 public function GetName($opt,$itembd)
 {

  if ($opt["group"]==13 && $opt["id"]==31) 
	{
	 if ($opt["level"]==1)
	 {
	  $opt["name"] = "Spirit of Dark Raven"; 
	  unset($opt["level"]);
	  
     }
	 else $opt["name"] = "Spirit of Dark Horse";
	}
	elseif($opt["group"]==12 && $opt["id"]==26)//boxes
	{
	  switch ($opt["level"])
	  {
	   case 1: $opt["name"]="Red Crystal";break;
	   case 2: $opt["name"]="Blue Crystal";break;
	   case 3: $opt["name"]="Dark Crystal";break;
	   case 4: $opt["name"]="Box of Treasure";break;
	   case 5: $opt["name"]="Box of Surprice";break;
	  }
	  unset($opt["level"],$opt["equipment"]);
	  $opt["llevel"]=7;
	}
	elseif($opt["group"]==13 && $opt["id"]==20)	//warror's rings
	{
	 if ($opt["level"]==1 || $opt["level"]==2)
	 {
	  $opt["name"]="Ring Of Warrior"; 
	  unset($opt["level"]);
	  $opt["llevel"]=7;
	 }							
	}
	elseif($opt["group"]==14 && $opt["id"]==11)
	{
	 switch ($opt["level"])
	 {
	  case 1: $opt["name"]="Star";break;
	  case 2: $opt["name"]="FireCracker";break;
	  case 5: $opt["name"]="Silver Medal";break;
	  case 6: $opt["name"]="Gold Medal";break;
	  case 7: $opt["name"]="Box of Heaven";break;
	  case 8: $opt["name"]="Box of Kundun +1";break;
	  case 9: $opt["name"]="Box of Kundun +2";break;
	  case 10: $opt["name"]="Box of Kundun +3";break;
	  case 11: $opt["name"]="Box of Kundun +4";break;
	  case 12: $opt["name"]="Box of Kundun +5";break;
	  case 13: $opt["name"]="Heart Of Lord";break;
	 }
	  unset($opt["level"],$opt["equipment"]);
	  $opt["llevel"]=7;
	}
	elseif($opt["group"]==12 && $opt["id"]==11)
	{
	  switch ($opt["level"])
	  {
	   case 0 or "": $opt["name"]="Summoning Goblin";break;
	   case 1: $opt["name"]="Summoning Stone Golim";break;
	   case 2: $opt["name"]="Summoning Assasin";break;
	   case 3: $opt["name"]="Summoning Bali";break;
	   case 4: $opt["name"]="Summoning Soldier";break;
	   case 5: $opt["name"]="Summoning Yeti";break;
	   case 6: $opt["name"]="Summoning Dark Knight";break;
	  }
	  unset($opt["level"],$opt["equipment"]);
	}
	elseif($opt["group"]==12)
	{
	  switch($opt["id"])
	  {
	    case 30: if($opt["level"]==1)    $opt["name"]="Jewel of Bless mix x20";
		         elseif($opt["level"]==2)$opt["name"]="Jewel of Bless mix x30";
				 else $opt["name"]="Jewel of Bless mix x10";
				 $opt["llevel"]=7;
				 unset($opt["level"],$opt["equipment"]);
				 break;
	    case 31: if($opt["level"]==1)    $opt["name"]="Jewel of Soul mix x20";
		         elseif($opt["level"]==2) $opt["name"]="Jewel of Soul mix x30";
				 else {$opt["name"]="Jewel of Soul mix x10";unset($opt["equipment"]);}
				 unset($opt["level"],$opt["equipment"]);
				 $opt["llevel"]=7;
				 break;    
	  }
	  
	}
	elseif($opt["group"]==13)
	{
	  switch($opt["id"])
	  {
	    case 7: if($opt["level"]==1){ $opt["name"]="Sperman";unset($opt["level"],$opt["equipment"]);$opt["llevel"]=7;} break;
	    case 11: if($opt["level"]==1){ $opt["name"]="Life Stone";unset($opt["level"],$opt["equipment"]);$opt["llevel"]=7;}
		         else { $opt["name"]="Guardian";unset($opt["level"],$opt["equipment"]);$opt["llevel"]=7;} break;
	    case 14: if($opt["level"]==1){ $opt["name"]="Crest of Monarch";unset($opt["level"],$opt["equipment"]);$opt["llevel"]=7;} break;
	  }
	}
	elseif($opt["group"]==14)
	{
	  switch($opt["id"])
	  {
	    case 7: if($opt["level"]==1) { $opt["name"]="Potion of Soul";unset($opt["level"],$opt["equipment"]);$opt["llevel"]=7;} break;
	    case 12: if($opt["level"]==1){ $opt["name"]="Heart";unset($opt["level"],$opt["equipment"]);}elseif($opt["level"]==2){ $opt["name"]="Pergamin";$opt["llevel"]=7;unset($opt["level"],$opt["equipment"]);} break;
	    case 21: if($opt["level"]==1){ $opt["name"]="Stone";$opt["llevel"]=7;unset($opt["level"],$opt["equipment"]);}elseif($opt["level"]==3){ $opt["name"]="Sing of Lord";$opt["llevel"]=7;unset($opt["level"],$opt["equipment"]);} break;
	    case 32: if($opt["level"]==1){ $opt["name"]="Pink Candy Box";unset($opt["level"],$opt["equipment"]);} break;
	    case 33: if($opt["level"]==1){ $opt["name"]="Orange Candy Box";unset($opt["level"],$opt["equipment"]);} break;
	    case 34: if($opt["level"]==1){ $opt["name"]="Blue Candy Box";unset($opt["level"],$opt["equipment"]);} break;
	  }
	}
	if (empty($opt["name"]))
	{
	  $opt["name"] = $itembd[$opt["group"]][$opt["id"]][0];
	  if (empty($opt["name"])) $opt["name"]="Unknown item group:".$opt["group"].", id ".$opt["id"];
	}

	return $opt;
 }
 
 /**
 * показывает, на кого вещь
 **/
 private function showEq($g,$id,$itembd)
 {
  $group12 = array(12,15,26,31); // база вещей, в группе, на которые нужно подцеплять надпись эквипмента 12 группа
  $group13 = array(11,14,15,29,31,31,33,34,35,36,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61); //13
  if ($g==12 and in_array($id,$group12) or $g==13 and in_array($id,$group13)  or $g==14 ) unset($array["equip"]);
  else if (can_equip( substr($itembd[$g][$id][1],2))!="")	$show= "Can be equipment by  ".items::can_equip( substr($itembd[$g][$id][1],2));
  return $show;
 }
 
 /**
 * функсия определения классов
 */
 public function can_equip($array)
 {
  $ch_name[0][1] = "DW";    $ch_name[1][1] = "DK";   $ch_name[2][1] = "FE";
  $ch_name[0][2] = "SM";    $ch_name[1][2] = "BK";   $ch_name[2][2] = "ME";
  $ch_name[0][3] = "GrM";   $ch_name[1][3] = "BM";   $ch_name[2][3] = "HE";

  $ch_name[3][1] = "MG";    $ch_name[4][1] = "DL";   $ch_name[5][1] = "Sum";
  $ch_name[3][2] = "DM";    $ch_name[4][2] = "LE";   $ch_name[5][2] = "BS";
  $ch_name[3][3] = "DM";    $ch_name[4][3] = "LE";   $ch_name[5][3] = "DimM";

  $ch_name[6][1] = "RF";
  $ch_name[6][2] = "FM";
  $ch_name[6][3] = "Unknown class";

  $display="";
  
  for ($i=0;$i<strlen(trim($array));$i++)
  {
	if ($array{$i}!=0)
	{
		if ($display!="") $display .="/";
			
		$display .= $ch_name[$i][$array{$i}];
	}
  }
  return $display;
}
 
 /**
 * определяет anc опции
 * @g group
 * @id
 * @anc - цифра опций
 * @ancnamez массив с названиями опций
 **/
 private function isAncient($g,$id,$anc,$ancnamez)
 {
  $ancient = array();
  $temp = array();
  
  if ($anc>0)
  { 
	if ($anc==10) 
	{ 
	 $ancient["name"] = $ancnamez["na"][$g][$id]; 
	 $ancient["opt"] = "+10 ancient options";
	}
	else $ancient["name"] = $ancnamez["a"][$g][$id];
	if ($anc==5 || $anc==6) $ancient["opt"] = "+5 ancient options";
	else if ($anc==9)$ancient["opt"] = "+10 ancient options";

  }
  if (empty($ancient["name"]))unset($ancient["name"]);
  $temp["ancient"] = $ancient;
  return $temp;
 }
 
 
 /**
 * функция для работы с опциями вещей: узнает все опции
 * @g group
 * @id
 * @option цифра опций
 * @ex цифра экс опций
 **/
 private function getOptions($g,$id,$option,$ex)
 {
  $itemsopt[0] = "Additional dmg";
  $itemsopt[1] = "Additional dmg";
  $itemsopt[2] = "Additional dmg";
  $itemsopt[3] = "Additional dmg";
  $itemsopt[4] = "Additional dmg";
  $itemsopt[5] = "Additional wizardy dmg";
  $itemsopt[6] = "Additional defence rate";
  $itemsopt[7] = "Additional defence";
  $itemsopt[8] = "Additional defence";
  $itemsopt[9] = "Additional defence";
  $itemsopt[10] = "Additional defence";
  $itemsopt[11] = "Additional defence";
  $itemsopt[12] = "options";
  $it_opt = array();
  
  if($option>=128)
  {
   $it_opt["skill"] = "+ Skill";  
   $option-=128;
  }
  $it_opt["level"] = (integer)($option/8);
  $option-=$it_opt["level"]*8;

  if ($option>3) 
  {
   $it_opt["luck"] = "Luck (success rate of Jewel of Soul + 25%)<br>Luck (critical damage rate +5%)";	
   $option-=4;
  }

  $m_array = items::getMn($g,$id);  // получаем множители для вычисления опций

	
  if ($g==13 && $id!=30)
  {
	switch($id)
	{
	 case 24: $itemsopt[13] = "Max mana increased"; break;	
	 case 28: $itemsopt[13] = "Max AG increased";break;	
	 default: $itemsopt[13] = "Automatic HP recovery";
	}
  }else $itemsopt[13] = "Additional dmg";
	
	if ($option>=0) 
	{
	 /*wings options add*/
	 $wing_opt[0]="Additional wizardy dmg";
	 $wing_opt[1]="Additional dmg";
	 $wing_opt[2]="Automatic HP recovery";
	 $wing_opt[3]="Additional defence";
 
	 $dw_wing = array(1,4,41,42); //wings sum & dw
	 $dk_wing = array(2,5); // dk
	 $elf_wing = array(3,0); // elf
	 $thss = array(36,37,38,39,40,43);//винги для 3-го класса
	
	 if ($g==12 or ($g==13 and $id==30))
	 {
	  if (in_array($id,$dw_wing)) // опции на винги дв
	  {
	   if (($ex>=31 and $ex<=64 and $ex!=32)  or $ex==0)
	   {
	     $itemsopt[12]=$wing_opt[2];
	     if ($ex<=31) $it_opt["options"] = $option;
	     else 
	     {
		  $it_opt["options"] = ($option+4);
		  $ex-=64;
	     }
	   }
	   elseif($ex>=96 or $ex==32) // add wiz dmg
	   {
	     $itemsopt[12]=$wing_opt[0];
	     if ($ex>=96)
	     {
		  $it_opt["options"] =($option*$m_array["mnoz"])+$m_array["sum"];
		  $ex-=96;
	     }
	     else
	     {
		  $it_opt["options"] =($option*$m_array["mnoz"]);
		  $ex-=32;
	     }
	   }
	  }
	  else if(in_array($id,$dk_wing)) //опции на винги дк
	  {
	   if ($ex>=32 or $ex>=96)//add dmg
	   {
	    $itemsopt[12]=$wing_opt[1];
	    if ($ex>=32 && $ex<96)
	    {
		$it_opt["options"] = $option*4;
		$ex-=32;
	    }
	    else 
	    {
	     $it_opt["options"] = ($option+4)+16;
	     $ex-=96;
	    }
	   }
	   elseif($ex>=0 or ($ex>=64 && $ex<96)) //HP rec
	   {
	    if ($ex>=0)
	    {
	     $it_opt["options"] =$option;			
	    }
	    else
	    {
	     $it_opt["options"] =$option+4;
	     $ex-=64;
	    }
	     $itemsopt[12]=$wing_opt[2];
	   }
	  }
	  else if(in_array($item["id"],$elf_wing)) // elf wings opt
	  {
	    if ($ex>=32 or $ex>=96)//HP rec
	    {
		 $itemsopt[12]=$wing_opt[2];
		 if ($ex>=32 && $ex<96)
		 {
		  $it_opt["options"] = $option;
		  $ex-=32;
		 }
		 else 
		 {
		  $it_opt["options"] = $option+4;
		  $ex-=96;
		 }
	    }
	    elseif($ex>=0 or ($ex>=64 && $ex<96)) //add dmg
	    {
		 if ($ex>=0) $it_opt["options"] =$option*4;
		 else
		 {
		  $it_opt["options"] =($option*4)+16;
		  $ex-=64;
		 }
		 $itemsopt[12]=$wing_opt[1];
	    }
	  }
	  else if (in_array($id,$thss))//3thd class
	  {
	   if (($ex>=31 and $ex<=64 and $ex!=32)  or $ex==0)
	   {
	    $itemsopt[12]=$wing_opt[2];
	    if ($ex<=31) $it_opt["options"] = $option;
	    else
        {
         $it_opt["options"] = ($option+4);
		 $ex-=64;
		}
	   }
	   elseif($ex>=96 or $ex==32) // add def
	   {
	    if ($ex>=96)
	    {
	     $it_opt["options"] =($option*$m_array["mnoz"])+$m_array["sum"];
	     $ex-=96;
	    }
	    else
	    {
	     $it_opt["options"] =($option*$m_array["mnoz"]);
	     $ex-=32;
	    }
	    $itemsopt[12]=$wing_opt[3];
	   }
	  }
	  else // все остальные винги
	  {
	   if ($ex <32 or ($ex>=64 && $ex<96) ) // wizardy dmg
	   {
	    if($ex<64) $it_opt["options"] =($option*4);
	    else
        {
	     $it_opt["options"] =($option*4)+16;
	     $ex=-64;
	    }							   
	    $itemsopt[12]=$wing_opt[0];
	   }
	   elseif($ex>=32 or $ex >=96) // add dmg
	   {
	    if($ex>=32 && $ex<96)
	    {
	     $it_opt["options"] =($option*4);
		 $ex-=32;
	    }
	    else
        {
	     $it_opt["options"] =($option*4)+16;
	     $ex=-96;
	    }							   
	    $itemsopt[12]=$wing_opt[1];
	   }
	  }
	 }
	 else // обычные вещи
	 {
	  if ($ex>63)
	  {
	   $it_opt["options"] = (($option*$m_array["mnoz"])+$m_array["sum"]);
	   $ex -=64;
	  }
	  elseif($ex<=63) $it_opt["options"] = ($option*$m_array["mnoz"]);						 
	 }

	 if($it_opt["options"]>0) $it_opt["options"] = $itemsopt[$g]." +".$it_opt["options"]."%";
	 else unset($it_opt["options"]);
	}
	else unset($it_opt["options"]);
	
	if ($ex>0) $it_opt["excellent"] = items::isExcelent($g,$id,$ex); 
	
	if (empty($it_opt["excellent"]))unset($it_opt["excellent"]);
	
	return $it_opt;
 }
 
 /**
  * возврашает список экселлентных опций(если есть)
  * @group - группа вещи
  * @id - номер вещи в группе
  * @ex - цифра экс опций
  **/
 private function isExcelent ($group,$id,$ex=0)
 {
  $Excellent = array();
  if($ex>=0)
  {				
	 if (($group<=5)||($group==13 && ($id==12 || $id==13 || $id>=25 && $id<=28)))//если это оружие или пенданты
	 {
 	  $excoptar[0]="<div class=\"excellent\">Mana After Hunting Monsters +mana/8</div>";
	  $excoptar[1]="<div class=\"excellent\">Life After Hunting Monsters +life/8</div>";
	  $excoptar[2]="<div class=\"excellent\">Increase Attacking(Wizardy) speed +7</div>";
	  $excoptar[3]="<div class=\"excellent\">Increase Damage +2%</div>";
	  $excoptar[4]="<div class=\"excellent\">Increase Damage +Level/20</div>";
	  $excoptar[5]="<div class=\"excellent\">Excellent Damage Rate +10%</div>";
	 }
	 elseif(($group>=6 && $group<=11)||(($group==13 && $id!=30)&&(($id>=8 && $id<=10) || ($id>=20 && $id<=24)||($id>=38 && $id<=41))))	// щиты, сеты, кольца
	 {
	  $excoptar[0]="<div class=\"excellent\">Increase Rate of Zen 40%</div>";
	  $excoptar[1]="<div class=\"excellent\">Defense Success Rate +10%</div>";
	  $excoptar[2]="<div class=\"excellent\">Reflect Damage +5%</div>";
	  $excoptar[3]="<div class=\"excellent\">Damage Decrease +4%</div>";
	  $excoptar[4]="<div class=\"excellent\">Increase Max Mana +4%</div>";
	  $excoptar[5]="<div class=\"excellent\" >Increase Max Hp +4%</div>";
	 }
	 elseif($group==12 || ($group==13 && $id==30))// винги и плащи
	 {
	  $excoptar[0]="<div class=\"excellent\" >+ 115 HP</div>";
	  $excoptar[1]="<div class=\"excellent\">+ 115 MP</div>";
	  $excoptar[2]="<div class=\"excellent\">Ignore Enemy&#39;s defense 3%</div>";
	  $excoptar[3]="<div class=\"excellent\">+50 Max Stamina</div>";
	  $excoptar[4]="<div class=\"excellent\">Wizardry Speed +7</div>";
	  $excoptar[5]="<div class=\"excellent\"></div>";					
     }

	 if (($ex-32)>=0) $ex -=32; else $excoptar[5]="false";
	 if (($ex-16)>=0) $ex -=16; else $excoptar[4]="false";
	 if (($ex-8)>=0) $ex -=8; else $excoptar[3]="false"; 
	 if (($ex-4)>=0) $ex -=4; else $excoptar[2]="false";
	 if (($ex-2)>=0) $ex -=2; else $excoptar[1]="false";
	 if ($ex==0)$excoptar[0]="false";
	 
	 
	 foreach ($excoptar as $k) 
	 {
       if ($k!="false") $Excellent[]=$k;
	 }
  }
  if(count($Excellent)>0)return $Excellent;
 }
  /**
  * переводит хекс в цифры
  * @hex - бинарный код, требующий расшифровки
  * @begin - с какого сомвола начинать(от 0)
  * @length - на сколько символов
  * возвращает цифры
  **/
  private static function dehex($hex,$begin,$length)
  {
   return hexdec(substr($hex,$begin,$length));
  }

  /**
  * Возвращает множители для рассчета опций с лайфов
  * @group - шруппа вещи
  * @id - номер вещи в группе
  * возвращает массив mnoz - множитель sum- сумма
  **/
  private function getMn ($group,$id)
  {
   $bezuter = array(8,9,10,20,21,22,23,24,38,39,40,41,42,12,13,25,26,27,28);//номера колец и пендантов в 13 группе
   $mn = array();
   if ( $group < 6 or ($group==12)||($group==13 && $group==30))
   {
	$mn["mnoz"] = 4; $mn["sum"]= 16;
   }
   elseif($group==6)
   {
	$mn["mnoz"] = 5; $mn["sum"] = 20;
   }
   elseif($group==13 && in_array($id,$bezuter))//бижутерия
   {
    $mn["mnoz"] = 1; $mn["sum"] = 4;
   }
   else //wings
   {
    $mn["mnoz"] = 4; $mn["sum"] = 16;
   }
   return $mn;
  }
  
 public function smartsearch($item_hex,$x,$y,$itembd,$tt=0)
 {
  if ($tt==0)
  {
	if (substr($item_hex,0,2)=='0x') $item_hex=substr($item_hex,2);
	else $item_hex=strtoupper(urlencode(bin2hex($item_hex)));
  }
  
  $col_i = strlen($item_hex)/32;

  for ($i=0;$i<$col_i;$i++) 
  {
   if (!$itemarr[$i] || strlen($itemarr[$i]==32))$itemarr[$i] = substr($item_hex,$i*32, 32);
   if ($itemarr[$i]!="FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF" && strlen($itemarr[$i])==32)
   {
    $it["id"] = hexdec(substr($itemarr[$i],0,2)); // ID
	$it["group"] = hexdec(substr($itemarr[$i],18,2))/16; // group
	if ($it["group"] == 7 || ($it["group"]>=9 && $it["group"]<=11 && $it["id"]!=128) || $it["group"]==15)
	{
     $xin = substr($itembd[$it["group"]][0][1],0,1);
	 $yin = substr($itembd[$it["group"]][0][1],1,1);
	}
	else
	{
	 $xin = substr($itembd[$it["group"]][$it["id"]][1],0,1);
	 $yin = substr($itembd[$it["group"]][$it["id"]][1],1,1);
	}
	$j=$xin*$yin;
	$str=$i;
	$x1=0;
	while($j>0)
	{
	 if($x1<=$xin)
	 {	
	  $itemarr[$str]="not_empty";
	  $str++;
	  $x1++;
	  if($x1==$xin)
	  {
	   $str +=(8-$xin);
	   $x1=0;
	  }
	 }
	 $j--;	
	}	
   }
  }
  $c=0;
 
  for ($i=0;$i<$col_i;$i++) 
  {
   $j = $x * $y;
   $str = $i;
   $x1=0;
   $found=0;		
   $ind = ((floor($i/8)+1)*8)-1; // правый конец строки
   $raz = $i+($x-1);
   
   while($j>0)
   {
	if($x1<=$x)
	{	
	 if (strlen($itemarr[$str])==32 && $str<$col_i && $raz<=$ind) $found++; else {$j=0;$found=0;}
	 $str++;
	 $x1++;
     if($x1==$x)
	 {
	  $str +=(8-$x);
	  $ind+=8;
	  $x1=0;
     }
	}
	$j--;	
   }	
   if ($found == $x*$y){unset($itemarr); return $i;}
  }
  unset($itemarr);
  return -1;
 }
}

/**
* клас, описывающий возвращаемые значения
**/
class Iitems
{
 /**
  * добавление бинарного кода вещей в переменную в классе
  * @bighex - бинарный код, может быть с 0х.. но обязательно должен быть более 99 символов
  **/
  public function additmz($bighex)
  {
   if (strlen($bighex)>100)
   {
    if (substr($bighex,0,2)=="0x") $cachitems = substr($bighex,2);
    else $cachitems=strtoupper($bighex);
	
	return $cachitems;
   }
   else echo "Wrong item format::additmz";
   return "";
  }
  
  public function  Iitems (){} 
  
  /**
  * показывает изображение инвентаря
  * @hex бинарный код вещи @link линк при нажатии на вещь
  **/
  public function showWH($hex,$link="")
  { 
    require 'imgs/items.php';
    $hex =$this->additmz($hex);

	echo "<table border='0' style='border-spacing: 0px;empty-cells: hide;' cellPadding='0' cellSpacing='0'><tbody>";
	
	$col_i = strlen($hex)/32;
	for ($i=0;$i<$col_i;$i++) 
    {
     if (!$itemarr[$i] || strlen($itemarr[$i]==32))$itemarr[$i] = substr($hex,$i*32, 32); //вещь

     if ($itemarr[$i]!="FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF" && strlen($itemarr[$i])==32) //если не пустая ячейка
     {
	  $items = items::readitems($itemarr[$i],$itembd,$anc);
	  $this->getOptions($items);
	  
	  if ($items["group"] == 7 || ($items["group"]>=9 && $items["group"]<=11 && $items["id"]!=128) || $items["group"]==15)
	  {
	   $x = substr($itembd[$items["group"]][0][1],0,1);
	   $y = substr($itembd[$items["group"]][0][1],1,1);
	  }
	  else
	  {
	   $x = substr($itembd[$items["group"]][$items["id"]][1],0,1);
	   $y = substr($itembd[$items["group"]][$items["id"]][1],1,1);
	  }
	  if (!$x || !$y){$x=1;$y=1;}//если нет в базе вещи
	  
	  $j=$x*$y;
      $str=$i;
	  $x1=0;
	  $title=$this->getOptions(items::readitems($itemarr[$i],$itembd,$anc));
	 // echo $this->getOptions(items::readitems($itemarr[$i],$itembd,$anc),3);
	 }
	 
	 if($itemarr[$i]=="FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF") $itemarr[$i]="<td style='color:white;background:url(imgs/1.png) no-repeat;' width='32' height='32' align='center'>&nbsp;</td>";
	 if ($items["intopt"]=='' || !$items["intopt"]) $items["intopt"]=0;
	 if ($items["intopt"]>128) {$items["intopt"] -=128; $items["lopt"]=(int)($items["intopt"]/8);} 
	 $items["intopt"] = knowop($items["id"],$items["group"],$items["intopt"]);
	 $img = "imgs/items/".$items["group"].$items["id"].$items["intopt"].".gif"; // looking for item img
	  
	   if (!file_exists($img))$img="<span style='font-size:8px;'>".$items["group"].".".$items["id"].".".$items["intopt"]."</span>"; //если нет изображения
	   else $img = "<img title = '".$title."' src='".$img."' id='tooltiper' alt='".$items["name"]."' align='center' style='width:".($x*32)."px; height:".($y*32)."px;'>";			

	 while($j>0)
	   {
		if($x1<=$x)
		{
		 if (!empty($link)) $link1="<a href='".$link."=".$i."'>$img</a>";
		 else $link1=$img;
		 
	     if ($str==$i)$itemarr[$str]="<td align='center' colspan='".$x."' rowspan='".$y."' style='color:white;background:url(imgs/2.png);width:32px;height:32px;'>".$link1."</td>";
		 else $itemarr[$str]="none";
		 $str++;
         $x1++;
		 if($x1==$x)
		 {
		  $str +=(8-$x);
		  $x1=0;
		 }
		}
		else $itemarr[$str]="<td style='color:white;background:url(imgs/1.png) no-repeat;width:32px;height:32px;' width='32' height='32' align='center'>&nbsp;</td>";
		$j--;
	   }
	   
	 if($i==0) echo "<tr>";
	 if($itemarr[$i]!="none" )echo $itemarr[$i];		
	 if ($i%8==7 && $i+1!=$col_i)echo "</tr><tr>";
	 if($i+1==$col_i) echo"</tr>";
	}
	echo "</table>";
  }
  
  /**
  * возвращает свормированную таблицу опций
  * @item - бинарный код вещи
  * @type = 0 - показывает название вещи и список опций
  * 1 = то же что и 0, только с изображением вещи
  * 2 = показывает название вещи, при наведении отображает полную информацию
  * 3 = краткая информация о вещи, при навиедении на изобрадение полная информация
  * 4 = показывает вещь в развертке
  **/
  public function getOptions($item,$type=0)
  {
    
	if ($item["level"]>=0 && $item["level"]<4) $csstyle="name0";
	else if ($item["level"]<7 && $item["level"]>=4) $csstyle="name4";
	else if($item["level"]>=7) $csstyle="name7";
	if($item["level"] && $item["level"]>0)$item["level"]=" +".$item["level"];
	if ($item["llevel"]==7)$csstyle = "name7";
	if ($item["level"]==0)unset($item["level"]);
	$excl = $item["excellent"];
	if (is_array($excl) && $item["group"]<12)
	{
	 $csstyle="name_ex";
	 $item["name"] = "Excellent ".$item["name"];
	 $display_e="";
	 foreach($excl as $v) $display_e.=$v;
	} 
	$anc = $item["ancient"];
	if (is_array($anc))
	{
	 $csstyle="ancient";
	 $item["name"] = $anc["name"]." ".$item["name"];
	 $item["aopt"] = $anc["opt"];
	} 
	$sok =  $item["sokets"];
	if (is_array($sok))
	{
	 $s_opt="";
	 foreach($sok as $v) $s_opt.= $v;
	 $csstyle = "name_s";
	}
	
	$eqtcl="bcanbe";
	Switch($type)
	{
	 case 0:
	  $eqtcl="wcanbe";
 	  $display = "<table width=\"250\">";
	  $display.= "<tr><td align=\"center\" valign=\"center\" class=\"".$csstyle." \" >".$item["name"].$item["level"]."</td></tr>";
	  $display.="<tr><td align=\"center\" valign=\"center\" class=\"".$eqtcl."\" >".$item["equipment"]."</td></tr>";
	  if ($item["skill"]) $display.="<tr><td align=\"center\" valign=\"center\" class=\"cskill\">".$item["skill"]."</td></tr>";
	  if ($item["luck"]) $display.="<tr><td align=\"center\" valign=\"center\" class=\"cluck\">".$item["luck"]."</td></tr>";
	  if ($item["options"]) $display.="<tr><td align=\"center\" valign=\"center\" class=\"iopt\">".$item["options"]."</td></tr>";
	  if ($display_e) $display.="<tr><td align=\"center\" valign=\"center\">".$display_e."</td></tr>";
	  if ($item["aopt"]) $display.="<tr><td align=\"center\" valign=\"center\" class=\"anc_opt\">".$item["aopt"]."</td></tr>";
	  if ($item["pvp"]) $display.="<tr><td align=\"center\" valign=\"center\" class=\"refinery\">".$item["pvp"]."</td></tr>";
	  if ($item["harmony"]) $display.="<tr><td align=\"center\" valign=\"center\" class=\"harmony\">".$item["harmony"]."</td></tr>";
	  if ($s_opt) $display.="<tr><td align=\"center\" valign=\"center\" class=\"socket\">".$s_opt."</td></tr>";
	  $display.="</table>"; 
	 break;
	 case 1:
	  $eqtcl="wcanbe";
 	  $display = "<table width=\"250\">";
	  $display.= "<tr><td align=\"center\" valign=\"center\" class=\"".$csstyle." \" >".$item["name"].$item["level"]."</td></tr>";
	  
	  if ($item["intopt"]=='' || !$item["intopt"]) $item["intopt"]=0;
	  if ($item["intopt"]>128) {$item["intopt"] -=128; $item["lopt"]=(int)($item["intopt"]/8);} 
	  $item["intopt"] = $this->knowop($item["id"],$item["group"],$item["intopt"]);
	  $img = "imgs/items/".$item["group"].$item["id"].$item["intopt"].".gif"; // looking for item img
	  if (!file_exists($img))$img="<span style=\"font-size:8px;\">".$items["group"].".".$items["id"].".".$items["intopt"]."</span>"; //если нет изображения
	  else $img = "<img src=\"".$img."\" align=\"center\" >";			
	  $display.="<tr><td align=\"center\" valign=\"center\" >".$img."</td></tr>";

	  $display.="<tr><td align=\"center\" valign=\"center\" class=\"".$eqtcl."\" >".$item["equipment"]."</td></tr>";
	  if ($item["skill"]) $display.="<tr><td align=\"center\" valign=\"center\" class=\"cskill\">".$item["skill"]."</td></tr>";
	  if ($item["luck"]) $display.="<tr><td align=\"center\" valign=\"center\" class=\"cluck\">".$item["luck"]."</td></tr>";
	  if ($item["options"]) $display.="<tr><td align=\"center\" valign=\"center\" class=\"iopt\">".$item["options"]."</td></tr>";
	  if ($display_e) $display.="<tr><td align=\"center\" valign=\"center\">".$display_e."</td></tr>";
	  if ($item["aopt"]) $display.="<tr><td align=\"center\" valign=\"center\" class=\"anc_opt\">".$item["aopt"]."</td></tr>";
	  if ($item["pvp"]) $display.="<tr><td align=\"center\" valign=\"center\" class=\"refinery\">".$item["pvp"]."</td></tr>";
	  if ($item["harmony"]) $display.="<tr><td align=\"center\" valign=\"center\" class=\"harmony\">".$item["harmony"]."</td></tr>";
	  if ($s_opt) $display.="<tr><td align=\"center\" valign=\"center\" class=\"socket\">".$s_opt."</td></tr>";
	  $display.="</table>"; 
	 break;
	 
	 case 2:
	 if ($item["level"]==0)unset($item["level"]);
	  $eqtcl="wcanbe";
 	  $display = "<table width=\"250\">";
	  $display.= "<tr><td align=\"center\" valign=\"center\" class=\"".$csstyle." \" >".$item["name"].$item["level"]."</td></tr>";
	  
	  
	  $n = $item["name"].$item["level"];
	  
	  if ($item["intopt"]=='' || !$item["intopt"]) $item["intopt"]=0;
	  if ($item["intopt"]>128) {$item["intopt"] -=128; $item["lopt"]=(int)($item["intopt"]/8);} 
	  $item["intopt"] = $this->knowop($item["id"],$item["group"],$item["intopt"]);
	  $img = "imgs/items/".$item["group"].$item["id"].$item["intopt"].".gif"; // looking for item img
	  if (!file_exists($img))$img="<span style=\"font-size:8px;\">".$items["group"].".".$items["id"].".".$items["intopt"]."</span>"; //если нет изображения
	  else $img = "<img src=\"".$img."\" align=\"center\" >";			
	  $display.="<tr><td align=\"center\" valign=\"center\" >".$img."</td></tr>";

	  $display.="<tr><td align=\"center\" valign=\"center\" class=\"".$eqtcl."\" >".$item["equipment"]."</td></tr>";
	  if ($item["skill"])
      {
       $display.="<tr><td align=\"center\" valign=\"center\" class=\"cskill\">".$item["skill"]."</td></tr>";
	   $n.=" + skill";
	  }
	  if ($item["luck"]) 
	  {
	    $display.="<tr><td align=\"center\" valign=\"center\" class=\"cluck\">".$item["luck"]."</td></tr>";
		$n.=" + luck";
	  }
	  if ($item["options"])
      {
       $display.="<tr><td align=\"center\" valign=\"center\" class=\"iopt\">".$item["options"]."</td></tr>";
	   $n.=" + options";
	  }
	  if ($display_e) $display.="<tr><td align=\"center\" valign=\"center\">".$display_e."</td></tr>";
	  if ($item["aopt"]) $display.="<tr><td align=\"center\" valign=\"center\" class=\"anc_opt\">".$item["aopt"]."</td></tr>";
	  if ($item["pvp"]) $display.="<tr><td align=\"center\" valign=\"center\" class=\"refinery\">".$item["pvp"]."</td></tr>";
	  if ($item["harmony"]) $display.="<tr><td align=\"center\" valign=\"center\" class=\"harmony\">".$item["harmony"]."</td></tr>";
	  if ($s_opt) $display.="<tr><td align=\"center\" valign=\"center\" class=\"socket\">".$s_opt."</td></tr>";
	  $display.="</table>"; 

	  $display = "<div align=\"center\" class=\"".$csstyle."\" id=\"tooltiper\" title='$display'>$n</div>";
	 break;
	
	case 3:
	 $eqtcl="wcanbe";
	 if ($item["level"]==0)unset($item["level"]);
	 $n = $item["name"].$item["level"];

	  $display1="<table width=\"250\"><tr><td align=\"center\" valign=\"center\" class=\"".$csstyle." \" >".$item["name"].$item["level"]."</td></tr>";
	  $display1.="<tr><td align=\"center\" valign=\"center\" class=\"".$eqtcl."\" >".$item["equipment"]."</td></tr>";
	  if ($item["skill"])
      {
       $display1.="<tr><td align=\"center\" valign=\"center\" class=\"cskill\">".$item["skill"]."</td></tr>";
	   $n.=" + skill";
	  }
	  if ($item["luck"]) 
	  {
	    $display1.="<tr><td align=\"center\" valign=\"center\" class=\"cluck\">".$item["luck"]."</td></tr>";
		$n.=" + luck";
	  }
	  if ($item["options"])
      {
       $display1.="<tr><td align=\"center\" valign=\"center\" class=\"iopt\">".$item["options"]."</td></tr>";
	   $n.=" + options";
	  }
	  if ($display_e) $display1.="<tr><td align=\"center\" valign=\"center\">".$display_e."</td></tr>";
	  if ($item["aopt"]) $display1.="<tr><td align=\"center\" valign=\"center\" class=\"anc_opt\">".$item["aopt"]."</td></tr>";
	  if ($item["pvp"]) $display1.="<tr><td align=\"center\" valign=\"center\" class=\"refinery\">".$item["pvp"]."</td></tr>";
	  if ($item["harmony"]) $display1.="<tr><td align=\"center\" valign=\"center\" class=\"harmony\">".$item["harmony"]."</td></tr>";
	  if ($s_opt) $display1.="<tr><td align=\"center\" valign=\"center\" class=\"socket\">".$s_opt."</td></tr>";
	 


	  if ($item["intopt"]=='' || !$item["intopt"]) $item["intopt"]=0;
	  if ($item["intopt"]>128) {$item["intopt"] -=128; $item["lopt"]=(int)($item["intopt"]/8);} 
	  $item["intopt"] = $this->knowop($item["id"],$item["group"],$item["intopt"]);
	  $img = "imgs/items/".$item["group"].$item["id"].$item["intopt"].".gif"; // looking for item img
	  if (!file_exists($img))$img="<span style=\"font-size:8px;\">".$items["group"].".".$items["id"].".".$items["intopt"]."</span>"; //если нет изображения
	  //title='$display1'
	  else $img = "<img id=\"tooltiper\"  src=\"".$img."\" align=\"center\" >";		

      $display = "<table width=\"100%\">
	  <tr><td align=\"center\" valign=\"center\" class=\"".$csstyle." \" >".$n."</td></tr>	  
	  <tr><td align=\"center\" id='tooltiper' title='$display1' valign=\"center\" height=\"128\">".$img."</td></tr>
      </table>"; 
	 break;
	 case 4:
 	  $display = "<table width=\"250\">";
	  $display.= "<tr><td align=\"center\" valign=\"center\" class=\"".$csstyle." \" >".$item["name"].$item["level"]."</td></tr>";
	  
	  if ($item["intopt"]=='' || !$item["intopt"]) $item["intopt"]=0;
	  if ($item["intopt"]>128) {$item["intopt"] -=128; $item["lopt"]=(int)($item["intopt"]/8);} 
	  $item["intopt"] = $this->knowop($item["id"],$item["group"],$item["intopt"]);
	  $img = "imgs/items/".$item["group"].$item["id"].$item["intopt"].".gif"; // looking for item img
	  if (!file_exists($img))$img="<span style=\"font-size:8px;\">".$items["group"].".".$items["id"].".".$items["intopt"]."</span>"; //если нет изображения
	  else $img = "<img src=\"".$img."\" align=\"center\" >";			
	  $display.="<tr><td align=\"center\" valign=\"center\" >".$img."</td></tr>";

	  $display.="<tr><td align=\"center\" valign=\"center\" class=\"".$eqtcl."\" >".$item["equipment"]."</td></tr>";
	  if ($item["skill"]) $display.="<tr><td align=\"center\" valign=\"center\" class=\"cskill\">".$item["skill"]."</td></tr>";
	  if ($item["luck"]) $display.="<tr><td align=\"center\" valign=\"center\" class=\"cluck\">".$item["luck"]."</td></tr>";
	  if ($item["options"]) $display.="<tr><td align=\"center\" valign=\"center\" class=\"iopt\">".$item["options"]."</td></tr>";
	  if ($display_e) $display.="<tr><td align=\"center\" valign=\"center\">".$display_e."</td></tr>";
	  if ($item["aopt"]) $display.="<tr><td align=\"center\" valign=\"center\" class=\"anc_opt\">".$item["aopt"]."</td></tr>";
	  if ($item["pvp"]) $display.="<tr><td align=\"center\" valign=\"center\" class=\"refinery\">".$item["pvp"]."</td></tr>";
	  if ($item["harmony"]) $display.="<tr><td align=\"center\" valign=\"center\" class=\"harmony\">".$item["harmony"]."</td></tr>";
	  if ($s_opt) $display.="<tr><td align=\"center\" valign=\"center\" class=\"socket\">".$s_opt."</td></tr>";
	  $display.="</table>"; 
	 break;
	}
	
    	
		

	return $display;
  }
  /*
  * узнать опции на вингах
  * @exnum - цифра эксел
  * @n - номер опции в массиве wing_opt
  * @n1 - номер опции в массиве wing_opt
  * @show_info - остатки опции на вывод
  */
 function knowWopt($exnum,$n,$n1,$show_info, $sum,$mnoz)
 {
  $ret_arr = array();
  /*wings options add*/
  $wing_opt[0]="Additional wizardy dmg";
  $wing_opt[1]="Additional dmg";
  $wing_opt[2]="Automatic HP recovery";
  $wing_opt[3]="Additional defence";

  if (($exnum>=31 and $exnum<=64 and $exnum!=32)  or $exnum==0)
  {
   $ret_arr[1] = $wing_opt[$n];
   if ($exnum<=31) $ret_arr[2] = $show_info; 
   else 
   {
    $ret_arr[2] = ($show_info+4);
    $exnum-=64;
   }
  }
  elseif($exnum>=96 or $exnum==32)
  {
   if ($exnum>=96)
   {
    $ret_arr[2] =($show_info*$mnoz)+$sum;
    $exnum-=96;
   }
   else
   {
    $ret_arr[2] =($show_info*$mnoz);
    $exnum-=32;
   }
   $ret_arr[1] = $wing_opt[$n1];
  }
  $ret_arr[0] = $exnum;
  return $ret_arr;
 }
 
 /**
 * функция возвращает цифру, учавтсвующую в генерации имени изображения(опции) 
 * @num - id вещи
 * @group - группа вещи
 * @opt - опции что есть
 **/
 function knowop($num,$group,$opt)
 {
  if($group>14 || $group<12) return 0;
		
  $groupp12=array(0,1,2,3,4,5,6,15,36,37,38,39,40,41,42,43);
  $groupp13=array(0,1,2,3,4,5,8,9,10,12,13,21,22,23,24,25,26,27,28,30,37,39,40,41,42,64,65,66,67,80);
  $groupp14=array(0,1,2,3,4,5,6,8,13,14,16,35,36,37,38,39,40);
  switch($group)
  {
   case 12: if(in_array($num,$groupp12)) return 0; break;
   case 13: if(in_array($num,$groupp13)) return 0; break;
   case 14: if(in_array($num,$groupp14)) return 0; break;
  }
  return $opt;
 }
}