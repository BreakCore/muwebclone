<?php
/**
 * Created by epmak
 * Date: 22.01.2015
 * Time: 9:15
 * MuWebClone
 * класс чтения вещей
 */

class rItem {

    private $items = array();
    private $gW = array("index", "slot", "skill","x","y","serial","option","drop","name","level","dMin","dMax","speed","dur","mdur","mpow","lreq","sreq","areq","ereq","vreq","creq","sattr","dw","dk","elf","mg","dl","sum","rf");//weap
    private $gA = array("index","slot","skill","x","y","serial","option","drop","name","level","def","block","dur","lreq","sreq","areq","ereq","vreq","creq","sattr","dw","dk","elf","mg","dl","sum","rf"); //arm
    private $gWn = array("index","slot","skill","x","y","serial","option","drop","name","level","def","dur","lreq","ereq","sreq","areq","creq","buymoney","dw","dk","elf","mg","dl","sum","rf");//12 ICGN
    private $gP = array("index","slot","skill","x","y","serial","option","drop","name","level","dur","lreq","ereq","sreq","areq","vreq","creq","Res7","sattr","dw","dk","elf","mg","dl","sum","rf");//13
    private $gJ = array("index","slot","skill","x","y","serial","option","drop","name","value","level");//14
    private $gS = array("index","slot","skill","x","y","serial","option","drop","name","level","lreq","ereq","BuyMoney","dw","dk","elf","mg","dl","sum","rf");//15

    public  function  __construct()
    {

    }

    /**
     * чтение группы вешей из файловой бд
     * @param $group int Номер группы 0-15
     * @return bool|array в случае, если группа найдена, вернется массив с вещами группы, если нет, то false
     */
    public function readGroup($group)
    {
        if(file_exists("itemBase/itemGroup".$group))
        {
            $content = file("itemBase/itemGroup".$group);
            $it = new ArrayIterator($content);
            foreach($it as $id=>$value)
            {
                switch($group) //поддключаем названия столбцов (аналогичны с iWork
                {
                    case 0:
                    case 1:
                    case 2:
                    case 3:
                    case 4:
                    case 5: $dictionary = $this->gW; break;
                    case 6:
                    case 7:
                    case 8:
                    case 9:
                    case 10:
                    case 11:$dictionary = $this->gA; break;
                    case 12:$dictionary = $this->gWn; break;
                    case 13:$dictionary = $this->gP; break;
                    case 14:$dictionary = $this->gJ; break;
                    case 15:$dictionary = $this->gS; break;
                    default: die("range of groups 0 - 15");
                }
                $values = explode("|",$value);

                foreach($values as $id_=>$v)
                {
                    if(isset($dictionary[$id_]))
                    {
                        $this->items[$group][$values[0]][$dictionary[$id_]] = $v;
                    }
                }
            }
            return $this->items[$group];
        }
        else
            return false;
    }

    /**
     * чтение всей базы вещей
     * @return array массив с вещами
     */
    public function getItemBase()
    {
        for($i=0;$i<16;$i++)
        {
            if(!isset($this->items[$i]))
                self::readGroup($i);
        }
        return $this->items;
    }

    public function read($hex)
    {
        $size = strlen(trim($hex));

        switch($size)
        {
            case 16: //season 0-1
                $res = self::read16(strtoupper($hex));
                break;
            case 32: //season 2-6
                $res = self::read32(strtoupper($hex));
                break;
            case 64: //season 6-x?
                $res = self::read64(strtoupper($hex));
                break;
            default:
                die("item hex length could be 16,32,64");
        }
        return $res;
    }

    public function read16($hex)
    {
        return "under costruction!";
    }

    public function read32($hex)
    {
        $result["hex"] = $hex;//хекс код
        $result["id"] = self::dehex($hex,0,2);
        $result["intopt"] = self::dehex($hex,2,2); //лайф офпции, мана, скилл и т.п.
        $result["group"] = self::dehex($hex,18,1);
        $result["ispvp"] = self::dehex($hex,19,1);
        $result["serial1"] = substr($hex,6,8);
        $result["excnum"] = self::dehex($hex,14,2); //екселлентные опции циферкой
        $result["excnum_"] = $result["excnum"];
        $result["curDur"] = self::dehex($hex,4,2); //текущая прочность
        $result["harmonyOpt"] = self::dehex($hex,20,1); //опция хармони
        $result["harmonyLvl"] = self::dehex($hex,21,1); //уровень опции хармони
        $result["sockHex"] = strtoupper(substr($hex,22));//сокетовые опции
        $result["ancnum"] = self::dehex($hex,17,1); //эншент опция цифрой

        if(!isset($this->items[$result["group"]])) //нету вещей нужной группы? не впорос - узнаем
            self::readGroup($result["group"]);

        //до рассчета обязательно запускай сначала лайф адд, потом экселлент!
        $result = self::getOtions($result); //получение уровня, скилла, лака вещи

        switch($result["group"])
        {
            case 0:
            case 1:
            case 2:
            case 3:
            case 4:
            case 5: $result = self::readW($result);break;
            case 6:
            case 7:
            case 8:
            case 9:
            case 10:
            case 11: $result = self::readArmor($result); break;
            case 12: $result = self::read12($result); break;
            case 13: $result = self::read13($result); break;
            case 14: $result = self::read14($result); break;
            case 15: $result = self::readScrolls($result); break;
        }

        return $result;
    }

    public function read64($hex)
    {
        $result = self::read32(substr($hex,0,32));
        $result["hex"] = $hex;//хекс код
        $result["serial2"] = substr($hex,32,8);;//хекс код
        return $result;
    }


    /**
     * рассчет уровня, наличия скилла, лака на вещи
     * @param $itemInfo - массив с данными по вещи
     * @return array
     */
    private function getOtions($itemInfo)
    {
        if($itemInfo["intopt"]>=128)
        {
            $itemInfo["isSkill"] = 1;
            $itemInfo["intopt"]-=128;
        }

        $itemInfo["level"] = (int)($itemInfo["intopt"]/8);
        $itemInfo["intopt"] -= $itemInfo["level"]*8;

        if ($itemInfo["intopt"]>3)
        {
            $itemInfo["isLuck"] = 1;
            $itemInfo["intopt"] -= 4;
        }

        if (!isset($itemInfo["level"]))
            $itemInfo["level"]=0;

        return $itemInfo;
    }

    /**
     * функция чтения оружия
     * @param array $itemInfo
     * @return array|mixed
     */
    private function readW($itemInfo)
    {
        $itemInfo["name"]= $this->items[$itemInfo["group"]][$itemInfo["id"]]["name"];

        $itemInfo["x"] = (int)$this->items[$itemInfo["group"]][$itemInfo["id"]]["x"];
        $itemInfo["y"] = (int)$this->items[$itemInfo["group"]][$itemInfo["id"]]["y"];

        $itemInfo = self::getLifeOpt($itemInfo,4); //лайф опции
        $itemInfo = self::getStats($itemInfo); //требования силы, аги
        $itemInfo = self::getExcellent($itemInfo,1);

        $itemInfo["Dur"] = $this->items[$itemInfo["group"]][$itemInfo["id"]]["dur"];

        if(isset($itemInfo["exc"]))
        {
            $itemInfo["Dur"] += 15;
            $itemInfo["str"] = intval($itemInfo["str"] + $itemInfo["str"]*0.1);
            $itemInfo["agi"] = intval($itemInfo["agi"] + $itemInfo["agi"]*0.1);
        }

        $itemInfo["equipment"] = self::getEq($itemInfo);
        $itemInfo["equipmenta"] = self::getEqAr($itemInfo);
        $itemInfo["speed"] = (int)$this->items[$itemInfo["group"]][$itemInfo["id"]]["speed"];

        if ($itemInfo["ispvp"]>0) //pvp
            $itemInfo["pvp"] = "Has PvP options";


        if($itemInfo["ancnum"]>0) //ancient
        {
            $itemInfo["anc"] = "is ancient";
        }
        if (isset($itemInfo["isSkill"]))
        {
            $itemInfo["skillname"]="Have specific skill";
        }

        if ($itemInfo["harmonyOpt"]>0 && $itemInfo["harmonyLvl"]>=0)
        {
            $itemInfo["harmony"] = $itemInfo["harmonyOpt"]." +".$itemInfo["harmonyLvl"];
        }

        $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}0"; //group +id+lvl

        return $itemInfo;
    }

    /**
     * чтение арморов
     * @param $itemInfo
     * @return mixed
     */
    private function readArmor($itemInfo)
    {
        $itemInfo["x"] = (int)$this->items[$itemInfo["group"]][$itemInfo["id"]]["x"];
        $itemInfo["y"] = (int)$this->items[$itemInfo["group"]][$itemInfo["id"]]["y"];
        $itemInfo["name"]= $this->items[$itemInfo["group"]][$itemInfo["id"]]["name"];

        $itemInfo = self::getLifeOpt($itemInfo,4); //лайф опции
        $itemInfo = self::getStats($itemInfo); //требования силы, аги
        $itemInfo = self::getExcellent($itemInfo,2);


        if ($itemInfo["group"] !=6)//если не щиты
        {
            $itemInfo["defence"] = $this->items[$itemInfo["group"]][$itemInfo["id"]]["def"];
            $itemInfo["speed"] = $this->items[$itemInfo["group"]][$itemInfo["id"]]["block"];
        }
        else
        {
            $itemInfo["defence"] = $this->items[$itemInfo["group"]][$itemInfo["id"]]["def"];
        }

        $itemInfo["Dur"] = $itemInfo["defence"] = $this->items[$itemInfo["group"]][$itemInfo["id"]]["dur"];

        if(isset($itemInfo["exc"]))
        {
            $itemInfo["str"] = intval($itemInfo["str"] + $itemInfo["str"]*0.1);
            $itemInfo["agi"] = intval($itemInfo["agi"] + $itemInfo["agi"]*0.1);
        }
        $itemInfo["equipment"] = self::getEq($itemInfo);
        $itemInfo["equipmenta"] = self::getEqAr($itemInfo);


        if ($itemInfo["ispvp"]>0) //pvp
            $itemInfo["pvp"] = "Has PvP options";


        if($itemInfo["ancnum"]>0) //ancient
        {
            $itemInfo["anc"] = "is ancient";
        }

        if(isset($itemInfo["isSkill"])) //skill
        {
            $itemInfo["skillname"]="Have specific skill";
        }

        if ($itemInfo["harmonyOpt"]>0 && $itemInfo["harmonyLvl"]>=0)
        {
            $itemInfo["harmony"] = $itemInfo["harmonyOpt"]." +".$itemInfo["harmonyLvl"];
        }

        $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}0"; //group +id+lvl
        return $itemInfo;
    }

    /**
     * чтение 12 группы (винги и проч)
     * @param $itemInfo
     * @return mixed
     */
    private function read12($itemInfo)
    {
        $itemInfo["name"]= $this->items[$itemInfo["group"]][$itemInfo["id"]]["name"];

        $itemInfo["x"] = (int)$this->items[$itemInfo["group"]][$itemInfo["id"]]["x"];
        $itemInfo["y"] = (int)$this->items[$itemInfo["group"]][$itemInfo["id"]]["y"];

        $wings = array(0,1,2,3,4,5,6,6,36,37,38,39,40,41,42,43,49,50,130,131,132,133,134,135,262,263,264,265); //винги в 12 группе
        $seeds = array(60,61,62,63,64,65,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,121,122,123,124,125,126,127,128,129); //сокеты
        $elementalSys = array(5,200,201,221,231,241,251); //05 errtal of gale элементальные моменты
        $thirdWings = array(36,37,38,39,40,41,43,50);
        $firstwings = array(0,1,2);

        if($itemInfo["excnum"]>127) //подозрение на винги 2.5
        {
            $itemInfo["excnum"]-=128;
            if ($itemInfo["id"] == 6) // cloack of death
            {
                $itemInfo["name"] = "Cloak of Death";
                $itemInfo["img"]="cloakofdeath";
                $itemInfo["x"] = 2;
                $itemInfo["y"] = 3;
                $itemInfo = self::getLifeOpt($itemInfo,4,1);
                $itemInfo["defence"] = $this->items[$itemInfo["group"]][$itemInfo["id"]]["def"];
                $itemInfo["Dur"] = $this->items[$itemInfo["group"]][$itemInfo["id"]]["dur"];
                $itemInfo["equipment"] = "Can be equipment by Rage Fighter<br>Can be equipment by Dark Lord";
                $itemInfo["lvlreq"] = 230;
            }
            if ($itemInfo["id"] == 7)
            {

                $itemInfo["name"] = "Wings of Chaos";
                $itemInfo["img"]="wingsofchaos";
                $itemInfo["x"] = 3;
                $itemInfo["y"] = 2;
                $itemInfo = self::getLifeOpt($itemInfo,4,1);
                $itemInfo["defence"] = $this->items[$itemInfo["group"]][$itemInfo["id"]]["def"];
                $itemInfo["Dur"] = $this->items[$itemInfo["group"]][$itemInfo["id"]]["dur"];
                $itemInfo["equipment"] = "Can be equipment by Magic Gladiator<br>Can be equipment by Blade Knight";
                $itemInfo["lvlreq"] = 230;
            }
            if ($itemInfo["id"] == 8)
            {
                $itemInfo["name"] = "Wings of Magic";
                $itemInfo["img"]="wingsofmagic";
                $itemInfo["x"] = 3;
                $itemInfo["y"] = 2;
                $itemInfo = self::getLifeOpt($itemInfo,4,1);
                $itemInfo["defence"] = $this->items[$itemInfo["group"]][$itemInfo["id"]]["def"];
                $itemInfo["Dur"] = $this->items[$itemInfo["group"]][$itemInfo["id"]]["dur"];
                $itemInfo["equipment"] = "Can be equipment by Magic Gladiator<br>Can be equipment by Soul Master<br>Can be equipment by Bloody Summoner";
                $itemInfo["lvlreq"] = 230;
            }
            if ($itemInfo["id"] == 9)
            {
                $itemInfo["name"] = "Wings of Life";
                $itemInfo["img"]="wingsoflife";
                $itemInfo["x"] = 4;
                $itemInfo["y"] = 3;
                $itemInfo = self::getLifeOpt($itemInfo,4,1);
                $itemInfo["defence"] = $this->items[$itemInfo["group"]][$itemInfo["id"]]["def"];
                $itemInfo["Dur"] = $this->items[$itemInfo["group"]][$itemInfo["id"]]["dur"];
                $itemInfo["equipment"] = "Can be equipment by Muse Elf";
                $itemInfo["lvlreq"] = 230;
            }
            $itemInfo = self::getExcellent($itemInfo,6);
        }
        else if(in_array($itemInfo["id"], $wings) && !($itemInfo["id"] == 5 && $itemInfo["harmonyLvl"]>0)) //если это винги
        {
            $itemInfo = self::getLifeOpt($itemInfo,4,1);

            if(!in_array($itemInfo["id"],$thirdWings))
            {
                $itemInfo = self::getExcellent($itemInfo,3);
            }
            else
            {
                $itemInfo = self::getExcellent($itemInfo,5);
            }

            $itemInfo["defence"] = $this->items[$itemInfo["group"]][$itemInfo["id"]]["def"];
            $itemInfo["Dur"] = $this->items[$itemInfo["group"]][$itemInfo["id"]]["dur"];
            $itemInfo["equipment"] = self::getEq($itemInfo);
            $itemInfo["equipmenta"] = self::getEqAr($itemInfo);


            if(in_array($itemInfo["id"],$firstwings))
                $itemInfo["lvlreq"] = $this->items[$itemInfo["group"]][$itemInfo["id"]]["lreq"]+ ($itemInfo["level"]*4); //1st & 3rd wings
            elseif(in_array($itemInfo["id"],$thirdWings))
                $itemInfo["lvlreq"] = $this->items[$itemInfo["group"]][$itemInfo["id"]]["lreq"]; //1st & 3rd wings
            else
                $itemInfo["lvlreq"] = $this->items[$itemInfo["group"]][$itemInfo["id"]]["lreq"]+ ($itemInfo["level"]*3)+(10+$itemInfo["level"]* 3); //2nd wings

            if($itemInfo["lvlreq"]>400)
                $itemInfo["lvlreq"]=400;

            if ($itemInfo["ispvp"]>0) //pvp
                $itemInfo["pvp"] = "Has PvP options";


            if(isset($itemInfo["isSkill"])) //skill
            {
                $itemInfo["skillname"]="Have specific skill";
            }

            $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}0"; //group +id+lvl
        }
        elseif(in_array($itemInfo["id"],$seeds)) //если сокеты
        {
            $itemInfo = self::Seeds($itemInfo);
        }
        elseif(in_array($itemInfo["id"],$elementalSys))//books & errtels elemental
        {

        }
        else
        {
            $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}{$itemInfo['level']}"; //group +id+lvl
            switch ($itemInfo["id"])
            {
                case 11:
                    switch ($itemInfo["level"])
                    {
                        case 0 or "": $opt["name"]="Summoning Goblin";break;
                        case 1: $itemInfo["name"]="Summoning Stone Golim";break;
                        case 2: $itemInfo["name"]="Summoning Assasin";break;
                        case 3: $itemInfo["name"]="Summoning Bali";break;
                        case 4: $itemInfo["name"]="Summoning Soldier";break;
                        case 5: $itemInfo["name"]="Summoning Yeti";break;
                        case 6: $itemInfo["name"]="Summoning Dark Knight";break;
                    }

                    $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}{$itemInfo['level']}"; //group +id+lvl
                    unset($itemInfo["level"]);
                    break;
                case 30:
                    if($itemInfo["level"]==1)
                        $itemInfo["name"]="Jewel of Bless mix x20";
                    elseif($itemInfo["level"]==2)
                        $itemInfo["name"]="Jewel of Bless mix x30";
                    else
                        $itemInfo["name"]="Jewel of Bless mix x10";

                    $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}7"; //group +id+lvl
                    unset($itemInfo["level"]);
                    break;
                case 31:
                    if($itemInfo["level"]==1)
                        $itemInfo["name"]="Jewel of Soul mix x20";
                    elseif($itemInfo["level"]==2)
                        $itemInfo["name"]="Jewel of Soul mix x30";
                    else
                        $itemInfo["name"]="Jewel of Soul mix x10";
                    $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}7"; //group +id+lvl
                    unset($itemInfo["level"]);
                    break;
                case 136:
                    if($itemInfo["level"]==1)
                        $itemInfo["name"]="Jewel of Life mix x20";
                    elseif($itemInfo["level"]==2)
                        $itemInfo["name"]="Jewel of Life mix x30";
                    else
                        $itemInfo["name"]="Jewel of Life mix x10";
                    unset($itemInfo["level"]);
                    break;
                case 137:
                    if($itemInfo["level"]==1)
                        $itemInfo["name"]="Jewel of Creation mix x20";
                    elseif($itemInfo["level"]==2)
                        $itemInfo["name"]="Jewel of Creation mix x30";
                    else
                        $itemInfo["name"]="Jewel of Creation mix x10";
                    unset($itemInfo["level"]);
                    break;
                case 138:
                    if($itemInfo["level"]==1)
                        $itemInfo["name"]="Jewel of Guardian mix x20";
                    elseif($itemInfo["level"]==2)
                        $itemInfo["name"]="Jewel of Guardian mix x30";
                    else
                        $itemInfo["name"]="Jewel of Guardian mix x10";
                    unset($itemInfo["level"]);
                    break;
                case 140:
                    if($itemInfo["level"]==1)
                        $itemInfo["name"]="Jewel of Harmony mix x20";
                    elseif($itemInfo["level"]==2)
                        $itemInfo["name"]="Jewel of Harmony mix x30";
                    else
                        $itemInfo["name"]="Jewel of Harmony mix x10";
                    unset($itemInfo["level"]);
                    break;
                case 141:
                    if($itemInfo["level"]==1)
                        $itemInfo["name"]="Jewel of Chaos mix x20";
                    elseif($itemInfo["level"]==2)
                        $itemInfo["name"]="Jewel of Chaos mix x30";
                    else
                        $itemInfo["name"]="Jewel of Chaos mix x10";
                    unset($itemInfo["level"]);
                    break;
                case 142:
                    if($itemInfo["level"]==1)
                        $itemInfo["name"]="Jewel of Lower Refining Stone mix x20";
                    elseif($itemInfo["level"]==2)
                        $itemInfo["name"]="Jewel of Lower Refining Stone mix x30";
                    else
                        $itemInfo["name"]="Jewel of Lower Refining Stone mix x10";
                    unset($itemInfo["level"]);
                    break;
                case 143:
                    if($itemInfo["level"]==1)
                        $itemInfo["name"]="Jewel of Higher Refining Stone mix x20";
                    elseif($itemInfo["level"]==2)
                        $itemInfo["name"]="Jewel of Higher Refining Stone mix x30";
                    else
                        $itemInfo["name"]="Jewel of Higher Refining Stone mix x10";
                    unset($itemInfo["level"]);
                    break;
            }
            if(isset($itemInfo["lifeOpt"]))
            {
                $itemInfo["lifeOpt"] = "Automatic HP recovery +".$itemInfo["lifeOpt"];
            }

        }

        return $itemInfo;
    }

    /**
     * @param array $itemInfo
     * @return array
     * Чтение сидов
     */
    private function Seeds($itemInfo)
    {
        if (!isset($itemInfo["level"]))
            $itemInfo["level"] = 0;

        if (!isset($itemInfo["curDur"]))
            $itemInfo["curDur"] = 0;

        $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}{$itemInfo['level']}"; //group +id+lvl
        if($itemInfo["id"] == 60 || $itemInfo["id"] == 100 || $itemInfo["id"] == 106 || $itemInfo["id"] ==112 || $itemInfo["id"] ==118 || $itemInfo["id"] ==124) //fire
        {
            if($itemInfo["id"]>=100)
            {
                $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}{$itemInfo['level']}"; //group +id+lvl
            }
            else
            {
                $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}0";
            }
            switch($itemInfo["level"])
            {
                case 0: $itemInfo["lifeOpt"]="(level type) Attack/Wizardy increase"; break;
                case 1: $itemInfo["lifeOpt"]="Attack speed increas";break;
                case 2: $itemInfo["lifeOpt"]="Maximum attack/wizardy increase";break;
                case 3: $itemInfo["lifeOpt"]="Minimum attack/wizardy increase";break;
                case 4: $itemInfo["lifeOpt"]="Attack/wizardy increase";break;
                case 5: $itemInfo["lifeOpt"]="Increase AG cost decrease";break;
                default :$itemInfo["lifeOpt"]="Unknown item class {$itemInfo["curDur"]}-{$itemInfo["level"]}"; break;
            }
        }
        else if($itemInfo["id"] == 61 || $itemInfo["id"] == 101 || $itemInfo["id"] ==107 || $itemInfo["id"] ==113 || $itemInfo["id"] ==119 || $itemInfo["id"] ==125) //water
        {
            if($itemInfo["id"]>=100)
            {
                $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}{$itemInfo['level']}"; //group +id+lvl
            }
            else
            {
                $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}0";
            }
            switch($itemInfo["level"])
            {
                case 0: if( $itemInfo["curDur"] == 0)
                    $itemInfo["lifeOpt"]="Block rating increase";
                    break;
                case 1: $itemInfo["lifeOpt"]="Defense increase";break;
                case 2: $itemInfo["lifeOpt"]="Shield protection increase";break;
                case 3: $itemInfo["lifeOpt"]="Damage reduction";break;
                case 4: $itemInfo["lifeOpt"]="Damage reflection";break;
                default :$itemInfo["lifeOpt"]="Unknown item class {$itemInfo["curDur"]}-{$itemInfo["level"]}"; break;
            }
        }
        else if($itemInfo["id"] == 62 || $itemInfo["id"] == 102 || $itemInfo["id"] ==108 || $itemInfo["id"] ==114 || $itemInfo["id"] ==120 || $itemInfo["id"] ==126) //ice
        {
            if($itemInfo["id"]>=100)
            {
                $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}{$itemInfo['level']}"; //group +id+lvl
            }
            else
            {
                $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}0";
            }
            switch($itemInfo["level"])
            {
                case 0: $itemInfo["lifeOpt"]="Monster destruction for the life increase";break;
                case 1: $itemInfo["lifeOpt"]="Monster destruction for the mana increase";break;
                case 2: $itemInfo["lifeOpt"]="Skill attack increase";break;
                case 3: $itemInfo["lifeOpt"]="Attack rating increase";break;
                case 4: $itemInfo["lifeOpt"]="Item Durability increase";break;
                default :$itemInfo["lifeOpt"]="Unknown item class {$itemInfo["curDur"]}-{$itemInfo["level"]}"; break;
            }
        }
        else if($itemInfo["id"] == 63 || $itemInfo["id"] == 103 || $itemInfo["id"] ==109 || $itemInfo["id"] ==115 || $itemInfo["id"] ==121 || $itemInfo["id"] ==127) //wind
        {
            if($itemInfo["id"]>=100)
            {
                $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}{$itemInfo['level']}"; //group +id+lvl
            }
            else
            {
                $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}0";
            }

            switch($itemInfo["level"])
            {
                case 0: $itemInfo["lifeOpt"]="Automatic life recovery increase";break;
                case 1: $itemInfo["lifeOpt"]="Maximum life increase";break;
                case 2: $itemInfo["lifeOpt"]="Maximum mana increase";break;
                case 3: $itemInfo["lifeOpt"]="Automatic mana recovery increase";break;
                case 4: $itemInfo["lifeOpt"]="Maximum AG increase";break;
                case 5: $itemInfo["lifeOpt"]="Maximum AG value increase";break;
                default :$itemInfo["lifeOpt"]="Unknown item class {$itemInfo["level"]}"; break;
            }
        }
        else if($itemInfo["id"] == 64 || $itemInfo["id"] == 104 || $itemInfo["id"] ==110 || $itemInfo["id"] ==116 || $itemInfo["id"] ==122 || $itemInfo["id"] ==128) //lignhtning
        {
            if($itemInfo["id"]>=100)
            {
                $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}{$itemInfo['level']}"; //group +id+lvl
            }
            else
            {
                $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}0";
            }

            switch($itemInfo["level"])
            {
                case 0: $itemInfo["lifeOpt"]="Exellent damage increase";break;
                case 1: $itemInfo["lifeOpt"]="Exellent damage rate increase";break;
                case 2: $itemInfo["lifeOpt"]="Critical damage increase";break;
                case 3: $itemInfo["lifeOpt"]="Critical damage rate increase";break;
                default :$itemInfo["lifeOpt"]="Unknown item class {$itemInfo["level"]}"; break;
            }
        }
        else if($itemInfo["id"] == 65 || $itemInfo["id"] == 105 || $itemInfo["id"] ==111 || $itemInfo["id"] ==117 || $itemInfo["id"] ==123 || $itemInfo["id"] ==129) //earth
        {
            if($itemInfo["id"]>=100)
            {
                $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}{$itemInfo['level']}"; //group +id+lvl
            }
            else
            {
                $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}0";
            }
            switch($itemInfo["level"])
            {

                case 2: $itemInfo["lifeOpt"]="Increases Health"; break;
                default :$itemInfo["lifeOpt"]="Unknown item class {$itemInfo["level"]}"; break;
            }

        }
        return $itemInfo;
    }


    /**
     * чтение 13 группы (peng& rings)
     * @param $itemInfo
     * @return mixed
     */
    private function read13($itemInfo)
    {
        $itemInfo["name"]= $this->items[$itemInfo["group"]][$itemInfo["id"]]["name"];
        $itemInfo["x"] = (int)$this->items[$itemInfo["group"]][$itemInfo["id"]]["x"];
        $itemInfo["y"] = (int)$this->items[$itemInfo["group"]][$itemInfo["id"]]["y"];

        $biz  = array(8,9,21,22,23,24,12,13,25,26,27,28); //ринги
        $pend  = array(12,13,25,26,27,28); //пенданты
        ;
        switch($itemInfo["id"])
        {
            case 24: $addname = "Max mana increased +"; break;
            case 28: $addname = "Max AG increased +";break;
            case 30: $addname = "Additional damage +"; break;
            default: $addname = "Automatic HP recovery +";
        }

        if(in_array($itemInfo["id"], $biz)) //если это rings
        {

            $itemInfo = self::getLifeOpt($itemInfo,1);

            if(in_array($itemInfo["id"],$pend))
                $itemInfo = self::getExcellent($itemInfo,1);
            else
                $itemInfo = self::getExcellent($itemInfo,2);

            $itemInfo["Dur"] = $this->items[$itemInfo["group"]][$itemInfo["id"]]["dur"];
            $itemInfo["equipment"] = self::getEq($itemInfo);
            $itemInfo["equipmenta"] = self::getEqAr($itemInfo);

            if ($itemInfo["ispvp"]>0) //pvp
                $itemInfo["pvp"] = "Has PvP options";

            if($itemInfo["ancnum"]>0) //ancient
            {
                $itemInfo["anc"] = "is ancient";
            }

            if(isset($itemInfo["isSkill"])) //skill
            {
                $itemInfo["skillname"]="Have specific skill";
            }

            if ($itemInfo["harmonyOpt"]>0 && $itemInfo["harmonyLvl"]>=0)
            {
                $itemInfo["harmony"] = $itemInfo["harmonyOpt"]." +".$itemInfo["harmonyLvl"];
            }
            $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}0"; //group +id+lvl
        }
        else if ($itemInfo["id"] == 30) //Cape of Lord
        {
            $addname="";
            $itemInfo = self::getLifeOpt($itemInfo,4,1);
            $itemInfo = self::getExcellent($itemInfo,3);



            $itemInfo["defence"]=$this->items[$itemInfo["group"]][$itemInfo["id"]]["def"]+$itemInfo["level"]*2;


            $itemInfo["Dur"] = $this->items[$itemInfo["group"]][$itemInfo["id"]]["dur"];
            $itemInfo["equipment"] = self::getEq($itemInfo);
            $itemInfo["equipmenta"] = self::getEqAr($itemInfo);
            $itemInfo["lvlreq"] = 180+($itemInfo["level"]*4);


            if ($itemInfo["ispvp"]>0) //pvp
                $itemInfo["pvp"] = "Has PvP options";

            if(isset($itemInfo["isSkill"])) //skill
            {
                $itemInfo["skillname"]="Have specific skill";
            }

            $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}0"; //group +id+lvl
        }
        else
        {
            $itemInfo = self::getLifeOpt($itemInfo,1);
            switch($itemInfo["id"])
            {
                case 7:
                    if($itemInfo['level'] ==1 )
                    {
                        $itemInfo["name"]="Sperman";
                        unset($itemInfo["level"]);
                        $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}7"; //group +id+lvl
                    }
                    break;
                case 11:
                    if($itemInfo["level"]==1)
                    {
                        $itemInfo["name"]="Life Stone";
                        unset($itemInfo["level"]);
                        $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}7"; //group +id+lvl
                    }
                    else
                    {
                        $itemInfo["name"]="Guardian";
                        unset($itemInfo["level"]);
                        $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}7"; //group +id+lvl
                    }
                    break;
                //crest of monarch
                case 14:
                    if($itemInfo['level'] == 1)
                    {
                        $itemInfo["name"]="Crest of Monarch";
                        unset($itemInfo["level"]);
                        $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}1"; //group +id+lvl
                    }
                    else
                        $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}0"; //group +id+lvl
                    unset($itemInfo["curDur"]);
                    break;
                //warror's rings
                case 20:
                    if ($itemInfo['level'] ==1 || $itemInfo['level'] == 2)
                    {
                        $itemInfo["name"] = "Ring Of Warrior";
                        unset($itemInfo["level"]);
                        $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}7"; //group +id+lvl
                    }
                    break;
                //cape of lord
                case 30:
                    $itemInfo = self::getStats($itemInfo); //требования силы, аги
                    $itemInfo = self::getExcellent($itemInfo,3);
                    $itemInfo["defence"] = $this->items[$itemInfo["group"]][$itemInfo["id"]]["def"];
                    $itemInfo["Dur"] = $this->items[$itemInfo["group"]][$itemInfo["id"]]["dur"];

                    if(isset($itemInfo["exc"]))
                    {
                        $itemInfo["str"] = intval($itemInfo["str"] + $itemInfo["str"]*0.1);
                        $itemInfo["agi"] = intval($itemInfo["agi"] + $itemInfo["agi"]*0.1);
                    }

                    $itemInfo["equipment"] = self::getEq($itemInfo);
                    $itemInfo["equipmenta"] = self::getEqAr($itemInfo);


                    if ($itemInfo["ispvp"]>0) //pvp
                        $itemInfo["pvp"] = "Has PvP options";



                    if($itemInfo["ancnum"]>0) //ancient
                    {
                        $itemInfo["anc"] = "is ancient";
                    }

                    if(isset($itemInfo["isSkill"])) //skill
                    {
                        $itemInfo["skillname"]="Have specific skill";
                    }

                    if ($itemInfo["harmonyOpt"]>0 && $itemInfo["harmonyLvl"]>=0)
                    {
                        $itemInfo["harmony"] = $itemInfo["harmonyOpt"]." +".$itemInfo["harmonyLvl"];
                    }
                    $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}0"; //group +id+lvl
                    break;
                //dark raven or dark hourse
                case 31:
                    if($itemInfo['level'] == 1)
                    {
                        $itemInfo["name"]= "Spirit of Dark Raven";
                        $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}1"; //group +id+lvl

                        $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}0"; //group +id+lvl
                        unset($itemInfo['level']);
                    }
                    else
                    {
                        $itemInfo["name"]= "Spirit of Dark Horse";
                        $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}0"; //group +id+lvl
                        unset($itemInfo['level']);
                        $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}1"; //group +id+lvl
                    }
                    break;
                case 37:
                    switch ($itemInfo["excnum"])
                    {
                        case 0: $itemInfo["name"] = "Red ".$itemInfo["name"]; break;
                        case 1: $itemInfo["name"] = "Black ".$itemInfo["name"]; break;
                        case 2: $itemInfo["name"] = "Blue ".$itemInfo["name"]; break;
                        case 4: $itemInfo["name"] = "Gold ".$itemInfo["name"]; break;
                    }
                    $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}{$itemInfo['level']}"; //group +id+lvl
                    break;

                default:
                    $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}{$itemInfo['level']}"; //group +id+lvl
                    break;
            }
        }

        if(isset($itemInfo["lifeOpt"]))
            $itemInfo["lifeOpt"] = $addname.$itemInfo["lifeOpt"];

        return $itemInfo;
    }


    /**
     * чтение 14 группы
     * @param $itemInfo
     * @return mixed
     */
    private function read14($itemInfo)
    {
        $itemInfo["x"] = (int)$this->items[$itemInfo["group"]][$itemInfo["id"]]["x"];
        $itemInfo["y"] = (int)$this->items[$itemInfo["group"]][$itemInfo["id"]]["y"];

        switch($itemInfo["id"])
        {

            case 11:
                switch ($itemInfo["level"])
                {
                    case 1: $itemInfo["name"]="Star";$itemInfo["level"].="_";break;
                    case 2: $itemInfo["name"]="FireCracker";$itemInfo["level"].="_";break;
                    case 5: $itemInfo["name"]="Silver Medal";$itemInfo["level"].="_";break;
                    case 6: $itemInfo["name"]="Gold Medal";$itemInfo["level"].="_";break;
                    case 7: $itemInfo["name"]="Box of Heaven";$itemInfo["level"].="_";break;
                    case 8: $itemInfo["name"]="Box of Kundun +1";$itemInfo["level"].="_";break;
                    case 9: $itemInfo["name"]="Box of Kundun +2";$itemInfo["level"].="_";break;
                    case 10: $itemInfo["name"]="Box of Kundun +3";$itemInfo["level"].="_";break;
                    case 11: $itemInfo["name"]="Box of Kundun +4";$itemInfo["level"].="_";break;
                    case 12: $itemInfo["name"]="Box of Kundun +5";$itemInfo["level"].="_";break;
                    case 13: $itemInfo["name"]="Heart Of Lord";break;
                }

                $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}{$itemInfo["level"]}"; //group +id+lvl
                unset($itemInfo["level"]);

                break;
            case 7:
                if($itemInfo["level"]==1)
                {
                    $itemInfo["name"]="Potion of Soul";
                    $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}7"; //group +id+lvl
                }
                break;
            case 12:
                if($itemInfo["level"]==1)
                {
                    $itemInfo["name"]="Heart";
                    unset($itemInfo["level"]);
                    $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}0";
                }
                elseif($itemInfo["level"]==2)
                {
                    $itemInfo["name"]="Pergamin";
                    unset($itemInfo["level"]);
                    $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}7";
                }
                break;

            case 21:
                if($itemInfo["level"]==1)
                {
                    $itemInfo["name"]="Stone";
                    $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}{$itemInfo["level"]}";
                    unset($itemInfo["level"]);
                }
                elseif($itemInfo["level"]==3)
                {
                    $itemInfo["name"]="Sing of Lord";
                    $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}{$itemInfo["level"]}";
                    unset($itemInfo["level"]);
                }
                break;
            // lost map
            case 28:
                $itemInfo["name"]= $this->items[$itemInfo["group"]][$itemInfo["id"]]["name"];
                $itemInfo["img"] = "{$itemInfo["group"]}{$itemInfo["id"]}2";
                break;
            case 32:
                if($itemInfo["level"]==1)
                {
                    $itemInfo["name"]="Pink Candy Box";
                    $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}{$itemInfo["level"]}";
                    unset($itemInfo["level"]);
                }
                break;
            case 33:
                if($itemInfo["level"]==1)
                {
                    $itemInfo["name"]="Orange Candy Box";
                    $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}{$itemInfo["level"]}";
                    unset($itemInfo["level"]);
                }
                break;
            case 34:
                if($itemInfo["level"]==1)
                {
                    $itemInfo["name"]="Blue Candy Box";
                    $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}{$itemInfo["level"]}";
                    unset($itemInfo["level"]);
                }
                break;
            default:
                $itemInfo["name"]= $this->items[$itemInfo["group"]][$itemInfo["id"]]["name"];
                $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}{$itemInfo['level']}"; //group +id+lvl
                $itemInfo["equipment"] = self::getEq($itemInfo);
                $itemInfo["equipmenta"] = self::getEqAr($itemInfo);
                break;
        }


        return $itemInfo;
    }


    /**
     * чтение 15 группы  скруллы
     * @param $itemInfo
     * @return mixed
     */
    private function readScrolls($itemInfo)
    {
        $itemInfo["name"]= $this->items[$itemInfo["group"]][$itemInfo["id"]]["name"];
        $itemInfo["x"] = (int)$this->items[$itemInfo["group"]][$itemInfo["id"]]["x"];
        $itemInfo["y"] = (int)$this->items[$itemInfo["group"]][$itemInfo["id"]]["y"];

        $itemInfo["ene"]= $this->items[$itemInfo["group"]][$itemInfo["id"]]["ereq"];
        $itemInfo["img"]="{$itemInfo["group"]}{$itemInfo["id"]}0"; //group +id+lvl
        $itemInfo["equipment"] = self::getEq($itemInfo);
        $itemInfo["equipmenta"] = self::getEqAr($itemInfo);
        $eqc = count($itemInfo["equipment"]);
        return $itemInfo;
    }

    /**
     * узнаем кто сможет одеть вешь
     * @param $itemInfo
     * @return string
     */
    private function getEq($itemInfo)
    {
        $req = "";
        if($itemInfo["dw"]==3)
            $req.="Can be equipment by Grand Master<br>";
        else if($itemInfo["dw"]==2)
            $req.="Can be equipment by Soul Master<br>";
        else if($itemInfo["dw"]==1)
            $req.="Can be equipment by Dark Wizard<br>";


        if($itemInfo["dk"]==3)
            $req.="Can be equipment by Blade Master<br>";
        else if($itemInfo["dw"]==2)
            $req.="Can be equipment by Blade Knight<br>";
        else if($itemInfo["dw"]==1)
            $req.="Can be equipment by Dark Knight<br>";

        if($itemInfo["elf"]==3)
            $req.="Can be equipment by High Elf<br>";
        else if($itemInfo["elf"]==2)
            $req.="Can be equipment by Muse Elf<br>";
        else if($itemInfo["elf"]==1)
            $req.="Can be equipment by Fairy Elf<br>";

        if($itemInfo["mg"]==2)
            $req.="Can be equipment by Duel Master<br>";
        else if($itemInfo["mg"]==1)
            $req.="Can be equipment by Magic Gladiator<br>";

        if($itemInfo["dl"]==2)
            $req.="Can be equipment by Lord Emperor<br>";
        else if($itemInfo["dl"]==1)
            $req.="Can be equipment by Dark Lord<br>";

        if($itemInfo["sum"]==3)
            $req.="Can be equipment by Dimension Master<br>";
        else if($itemInfo["sum"]==2)
            $req.="Can be equipment by Bloody Summoner<br>";
        else if($itemInfo["sum"]==1)
            $req.="Can be equipment by Summoner<br>";

        if($itemInfo["rf"]==2)
            $req.="Can be equipment by Fist Master<br>";
        else if($itemInfo["rf"]==1)
            $req.="Can be equipment by Rage Fighter<br>";

        return $req;
    }

    /**
     * возвращает ассоциативный массив с пометками, какой класс может одевать вещь
     * @param $itemInfo
     * @return array
     */
    private function getEqAr($itemInfo)
    {
        $req = array();
        if($itemInfo["dw"]==3)
            $req["dwreq"] = 1;
        else if($itemInfo["dw"]==2)
            $req["dwreq"] = 1;
        else if($itemInfo["dw"]==1)
            $req["dwreq"] = 1;

        if($itemInfo["dk"]==3)
            $req["dkreq"] = 1;
        else if($itemInfo["dw"]==2)
            $req["dkreq"] = 1;
        else if($itemInfo["dw"]==1)
            $req["dkreq"] = 1;

        if($itemInfo["elf"]==3)
            $req["ereq"] = 1;
        else if($itemInfo["elf"]==2)
            $req["ereq"] = 1;
        else if($itemInfo["elf"]==1)
            $req["ereq"] = 1;

        if($itemInfo["mg"]==2)
            $req["mgreq"] = 1;
        else if($itemInfo["mg"]==1)
            $req["mgreq"] = 1;

        if($itemInfo["dl"]==2)
            $req["dlreq"] = 1;
        else if($itemInfo["dl"]==1)
            $req["dlreq"] = 1;

        if($itemInfo["sum"]==3)
            $req["sreq"] = 1;
        else if($itemInfo["sum"]==2)
            $req["sreq"] = 1;
        else if($itemInfo["sum"]==1)
            $req["sreq"] = 1;

        if($itemInfo["rf"]==2)
            $req["rfreq"] = 1;
        else if($itemInfo["rf"]==1)
            $req["rfreq"] = 1;

        return $req;
    }



    /**
     * Экселлентные опции
     * @param $itemInfo
     * @param integer $type
     * 1 оружие или пенданты
     * 2 щиты, сеты, кольца
     * 3 винги и плащи
     * 4 fenrir
     */
    private  function getExcellent($itemInfo,$type)
    {

        if($itemInfo["group"] == 12)
        {
            $wings = array(0,1,2); //винги в 12 группе
            if (in_array($itemInfo["id"],$wings))
            {
                return $itemInfo;
            }
        }
        $excoptar = array();
        $ex = $itemInfo["excnum"];
        switch($type)
        {
            case 1://weapons
                $excoptar[0]="Mana After Hunting Monsters +mana/8";
                $excoptar[1]="Life After Hunting Monsters +life/8";
                $excoptar[2]="Increase Attacking(Wizardy) speed +7";
                $excoptar[3]="Increase Damage +2%";
                $excoptar[4]="Increase Damage +Level/20";
                $excoptar[5]="Excellent Damage Rate +10%";
                break;
            case 2://armors
                $excoptar[0]="Increase Rate of Zen 30%";
                $excoptar[1]="Defense Success Rate +10%";
                $excoptar[2]="Reflect Damage +5%";
                $excoptar[3]="Damage Decrease +4%";
                $excoptar[4]="Increase Max Mana +4%";
                $excoptar[5]="Increase Max Hp +4%";
                break;
            case 3: //2nd wings
                $excoptar[0]="Increase Life +".(50+$itemInfo["level"] * 5);
                $excoptar[1]="Increase Mana +".(50+$itemInfo["level"] * 5);
                $excoptar[2]="Ignore Enemy&#39;s defense 3%";
                if($itemInfo["group"] == 13 && $itemInfo["id"]==30)
                {
                    $excoptar[3]="Increase comand +".(10+$itemInfo["level"] * 5);
                    $excoptar[4]="";
                }
                else
                {
                    $excoptar[3]="+50 Max Stamina";
                    $excoptar[4]="Wizardry Speed +5";
                }

                if($itemInfo["group"] == 12 && $itemInfo["id"]==49)
                    $excoptar[3]="";

                // $excoptar[5]="Not Used";
                break;
            case 4: //fenrir
                switch ($ex)
                {
                    case 1: $excoptar[0] = "Plazma Storm Skill<br>Increase final damage 10%"; break;
                    case 2: $excoptar[0] = "Plazma Storm Skill<br>Absorb final damage 10%"; break;
                    case 4: $excoptar[0] = "Plazma Storm Skill<br>Increase final damage 10%<br>Absorb final damage 10%"; break;
                }
                //$ex=0;
                break;
            //3rd wings
            case 5:

                $excoptar[0]="";
                $excoptar[1]="Ignor opponent&#39;s defensive power by 5%";
                $excoptar[2]="Return's the enemy&#39;s attack power in 5%";
                $excoptar[3]="Complete recovery of life in 5% rate";
                $excoptar[4]="Complete recover of Mana in 5% rate";

                break;
            //2.5 wings
            case 6:
                $excoptar[0]="Ignor oppinent's defensive power by 3%";
                $excoptar[1]="Complete recovery of life in 5% rate";
                $excoptar[2]="";
                $excoptar[3]="";
                $excoptar[4]="";
                $excoptar[5]="";
                break;
        }

        $isExc = 0;
        if($type == 5)
        {
            /*if (($ex-32)>=0)
            {
                $ex -=32;
                //$isExc++;
            }
            if (($ex-16)>=0)
            {
                $ex -=16;
            }*/
            if (($ex-8)>=0)
            {
                $ex -=8;
                $isExc++;
            }
            else
                unset($excoptar[4]);
            if (($ex-4)>=0)
            {
                $ex -=4;
                $isExc++;
            }
            else
                unset($excoptar[3]);
            if (($ex-2)>=0)
            {
                $ex -=2;
                $isExc++;
            }
            else
                unset($excoptar[2]);
            if (($ex-1)>=0)
            {
                $ex -=1;
                $isExc++;
            }
            else
                unset($excoptar[1]);
        }
        else
        {
            if (($ex-32)>=0)
            {
                $ex -=32;
                $isExc++;
            }
            else
                unset($excoptar[5]);
            if (($ex-16)>=0)
            {
                $ex -=16;
                $isExc++;
            }
            else
                unset($excoptar[4]);
            if (($ex-8)>=0)
            {
                $ex -=8;
                $isExc++;
            }
            else
                unset($excoptar[3]);
            if (($ex-4)>=0)
            {
                $ex -=4;
                $isExc++;
            }
            else
                unset($excoptar[2]);
            if (($ex-2)>=0)
            {
                $ex -=2;
                $isExc++;
            }
            else
                unset($excoptar[1]);
            if (($ex-1)>=0)
            {
                $ex -=1;
                $isExc++;
            }
            else
                unset($excoptar[0]);
        }
        /*if ($ex==0)
            unset( $excoptar[0]);*/

        if($isExc>0)
            $itemInfo["exc"] = $excoptar;

        $itemInfo["etype"]= $type;

        return $itemInfo;
    }

    /**
     * требования статов ОРИГИНАЛЬНО ЭТО ТРЕБОВАНИЯ ДЛЯ 0 УРОВНЯ!!!
     * @param $itemInfo
     * @return mixed
     */
    private function getStats($itemInfo)
    {

        if(isset($this->items[$itemInfo["group"]][$itemInfo["id"]]["sreq"]))
            $itemInfo["str"] = $this->items[$itemInfo["group"]][$itemInfo["id"]]["sreq"];
        if(isset($this->items[$itemInfo["group"]][$itemInfo["id"]]["areq"]))
            $itemInfo["agi"] = $this->items[$itemInfo["group"]][$itemInfo["id"]]["areq"];
        if(isset($this->items[$itemInfo["group"]][$itemInfo["id"]]["ereq"]))
            $itemInfo["ene"] = $this->items[$itemInfo["group"]][$itemInfo["id"]]["ereq"];
        if(isset($this->items[$itemInfo["group"]][$itemInfo["id"]]["vreq"]))
            $itemInfo["vit"] = $this->items[$itemInfo["group"]][$itemInfo["id"]]["vreq"];
        if(isset($this->items[$itemInfo["group"]][$itemInfo["id"]]["lreq"]))
            $itemInfo["cmd"] = $this->items[$itemInfo["group"]][$itemInfo["id"]]["lreq"];
        return $itemInfo;
    }

    /**
     * Рассчет лайф опций
     * @param $itemInfo оинформация о вещи
     * @param $mult множитель (для каждой группы свой)
     * @param int $isWings
     * @return mixed дополненный массив с информацией о вещи
     */
    private function getLifeOpt($itemInfo,$mult,$isWings = 0)
    {
        if($isWings == 0) // если это не винги
        {
            if ($itemInfo["excnum"]>63)
            {
                $itemInfo["lifeOpt"] = $mult * $itemInfo["intopt"] + $mult * 4;
                $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                $itemInfo["excnum"] -= 64;
            }
            else
            {
                if ($itemInfo["intopt"]>0)
                {
                    $itemInfo["lifeOpt"] = $mult * $itemInfo["intopt"];
                    $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                }
                else
                    $itemInfo["lifeLvl"] = 0;
            }
        }
        else
        {
            $wing_opt[0]="Additional wizardy damage ";
            $wing_opt[1]="Additional damage ";
            $wing_opt[2]="Automatic HP recovery ";
            $wing_opt[3]="Additional defence ";

            $optName="";

            $dw_wing = array(1,4); //wings dw
            $sum_wing = array(41,42);
            $dk_wing = array(2,5); // dk
            $elf_wing = array(0,3); // elf
            $thss = array(36,37,38,39,40,43,50);//винги для 3-го класса

            if ($itemInfo["group"]==12 or ($itemInfo["group"]==13 and $itemInfo["id"]==30))
            {

                if (in_array($itemInfo["id"],$dw_wing)) // опции на винги дв
                {
                    if(($itemInfo["excnum"]>=0 && $itemInfo["excnum"]<=31 && $itemInfo["intopt"]>0) or ($itemInfo["excnum"]>=64 && $itemInfo["excnum"]<=95 )) //HP rec
                    {
                        if ($itemInfo["excnum"]>=0 && $itemInfo["excnum"]<=31)
                        {
                            $optName= $wing_opt[0]."+".($itemInfo["intopt"]*4)."%";
                            $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                        }
                        else if($itemInfo["excnum"]>=64 && $itemInfo["excnum"]<=95)
                        {
                            $optName= $wing_opt[0]."+".($itemInfo["intopt"]*4 +16)."%";
                            $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                            $itemInfo["excnum"]-=64;
                        }
                        $itemInfo["lifeOpt"] = $optName;
                        return $itemInfo;
                    }
                    else //add dmg
                    {
                        if($itemInfo["excnum"]>=32 && $itemInfo["excnum"]<=63)
                        {
                            $optName= $wing_opt[0]."+".($itemInfo["intopt"]*4)."%";
                            $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                            $itemInfo["excnum"]-=32;
                        }
                        else if($itemInfo["excnum"]>=96)
                        {
                            $optName= $wing_opt[0]."+".($itemInfo["intopt"]*4+16)."%";
                            $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                            $itemInfo["excnum"]-=96;
                        }
                    }
                    if ($itemInfo["lifeLvl"]>0 && isset($optName))
                    {
                        $itemInfo["lifeOpt"] = $optName;
                        return $itemInfo;
                    }
                }
                if (in_array($itemInfo["id"],$sum_wing)) // опции на винги sum
                {
                    if(($itemInfo["excnum"]>=0  && $itemInfo["excnum"]<=31 ) or ($itemInfo["excnum"]>63 && $itemInfo["excnum"]<=95 )) //hp rec
                    {
                        if($itemInfo["id"] == 42)
                            $optName = "Additional Curse Spell ";
                        else
                            $optName = "Additional wizardy damage ";

                        if ($itemInfo["excnum"]>=0  && $itemInfo["excnum"]<=31)
                        {
                            $optName.= "+".($itemInfo["intopt"]*4)."%";
                            $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                            $itemInfo["excnum"]-=15;
                        }
                        else if($itemInfo["excnum"]>=64 && $itemInfo["excnum"]<=95 )
                        {
                            $optName.= "+".($itemInfo["intopt"]*4+16)."%";
                            $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                            $itemInfo["excnum"]-=64;
                        }
                        else
                            unset($optName);
                    }
                    else //add dmg
                    {
                        if($itemInfo["excnum"]>=32 && $itemInfo["excnum"]<=63)
                        {
                            $optName=$wing_opt[0]."+".($itemInfo["intopt"]*4)."%";
                            $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                            $itemInfo["excnum"]-=32;
                        }
                        else if($itemInfo["excnum"]>=96)
                        {
                            $optName=$wing_opt[0]."+".($itemInfo["intopt"]*4+16)."%";
                            $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                            $itemInfo["excnum"]-=96;
                        }
                    }
                    if ($itemInfo["lifeLvl"]>0 && isset($optName))
                    {
                        $itemInfo["lifeOpt"] = $optName;
                        return $itemInfo;
                    }
                }
                else if(in_array($itemInfo["id"],$dk_wing)) //опции на винги дк
                {
                    if($itemInfo["id"] == 5) //2е венги
                    {
                        if(($itemInfo["excnum"]>=0  && $itemInfo["excnum"]<=31 ) or ($itemInfo["excnum"]>63 && $itemInfo["excnum"]<=95 )) //HP rec
                        {
                            if ($itemInfo["excnum"]>=0  && $itemInfo["excnum"]<=31)
                            {
                                $optName = $wing_opt[2]. "+{$itemInfo["intopt"]}%";
                                $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                            }
                            else if($itemInfo["excnum"]>63 && $itemInfo["excnum"]<=95)
                            {
                                $optName = $wing_opt[2]. "+".($itemInfo["intopt"]+4)."%";
                                $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                                $itemInfo["excnum"]-=64;
                            }
                        }
                        else //add dmg
                        {


                            if($itemInfo["excnum"]>=32 && $itemInfo["excnum"]<=61)
                            {
                                $optName=$wing_opt[1]. "+".($itemInfo["intopt"]*4)."%";
                                $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                                $itemInfo["excnum"]-=32;
                            }
                            else if($itemInfo["excnum"]>=96)
                            {
                                $optName=$wing_opt[1]. "+".($itemInfo["intopt"]*4+16)."%";
                                $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                                $itemInfo["excnum"]-=96;
                            }
                        }
                    }
                    else // 1е
                    {
                        if(($itemInfo["excnum"]>=0  && $itemInfo["excnum"]<=31 ) or ($itemInfo["excnum"]>63 && $itemInfo["excnum"]<=95 )) //HP rec
                        {
                            if ($itemInfo["excnum"]>=0  && $itemInfo["excnum"]<=31)
                            {
                                $optName.=$wing_opt[1]. "+".($itemInfo["intopt"]*4)."%";
                                $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                            }
                            else if($itemInfo["excnum"]>63 && $itemInfo["excnum"]<=95)
                            {
                                $optName=$wing_opt[1]. "+".($itemInfo["intopt"]*4+16)."%";
                                $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                                $itemInfo["excnum"]-=64;
                            }
                        }
                        else //add dmg
                        {
                            if($itemInfo["excnum"]>=32 && $itemInfo["excnum"]<=61)
                            {
                                $optName =$wing_opt[2]. "+".($itemInfo["intopt"])."%";
                                $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                                $itemInfo["excnum"]-=32;
                            }
                            else if($itemInfo["excnum"]>=96)
                            {
                                $optName = $wing_opt[2]."+".($itemInfo["intopt"]+4)."%";
                                $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                                $itemInfo["excnum"]-=96;
                            }
                        }
                    }
                    if ($itemInfo["lifeLvl"]>0 && isset($optName))
                    {
                        $itemInfo["lifeOpt"] = $optName;
                        return $itemInfo;
                    }
                }
                else if(in_array($itemInfo["id"],$elf_wing)) // elf wings opt
                {
                    if(($itemInfo["excnum"]>=0  && $itemInfo["excnum"]<=31 ) or ($itemInfo["excnum"]>63 && $itemInfo["excnum"]<=95 )) // wiz dmg
                    {
                        if ($itemInfo["excnum"]>=0  && $itemInfo["excnum"]<=31)
                        {
                            $optName =$wing_opt[2]."+".($itemInfo["intopt"])."%";
                            $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                        }
                        else if($itemInfo["excnum"]>63 && $itemInfo["excnum"]<=95)
                        {
                            $optName.=$wing_opt[2]."+".($itemInfo["intopt"]+4)."%";
                            $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                            $itemInfo["excnum"]-=64;
                        }
                    }
                    else //HP rec
                    {
                        if($itemInfo["excnum"]>=32 && $itemInfo["excnum"]<=61)
                        {
                            $optName= $wing_opt[1]."+".($itemInfo["intopt"]*4)."%";
                            $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                            $itemInfo["excnum"]-=32;
                        }
                        else if($itemInfo["excnum"]>=96)
                        {
                            $optName= $wing_opt[1]."+".($itemInfo["intopt"]*4+16)."%";
                            $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                            $itemInfo["excnum"]-=96;
                        }

                    }
                    if ($itemInfo["lifeLvl"]>0 && isset($optName))
                    {
                        $itemInfo["lifeOpt"] = $optName;
                        return $itemInfo;
                    }
                }
                else if($itemInfo["group"]==13 && $itemInfo["id"]==30) //cape of lord
                {
                    if ($itemInfo["excnum"]<=31 && $itemInfo["intopt"]>0)
                    {
                        $optName= $wing_opt[1]."+".($itemInfo["intopt"]*4)."%";
                        $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                        $itemInfo["excnum"]-=16;
                    }
                    else if($itemInfo["excnum"]>=80)
                    {
                        $optName= $wing_opt[1]."+".($itemInfo["intopt"]*4+16)."%";
                        $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                        $itemInfo["excnum"]-=80;
                    }
                    if($itemInfo["lifeLvl"]>0 && isset($optName))
                    {
                        $itemInfo["lifeOpt"] = $optName;
                        return $itemInfo;
                    }
                }
                else if ($itemInfo["group"]==12 and $itemInfo["id"]==49) //Cape of Fighter
                {
                    if($itemInfo["excnum"]<=15 or ($itemInfo["excnum"]>=64 && $itemInfo["excnum"]<=79 )) //hp recovery
                    {
                        if ($itemInfo["excnum"]<=15 && $itemInfo["intopt"]>0)
                        {
                            $optName =$wing_opt[2]. "+".($itemInfo["intopt"])."%";
                            $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                        }
                        else if($itemInfo["excnum"]>=64 && $itemInfo["excnum"]<=79 )
                        {
                            $optName = $wing_opt[2]."+".($itemInfo["intopt"]+4)."%";
                            $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                            $itemInfo["excnum"]-=64;
                        }
                        else
                        {
                            unset($optName);
                        }
                        if($itemInfo["lifeLvl"]>0 && isset($optName))
                        {
                            $itemInfo["lifeOpt"] = $optName;
                            return $itemInfo;
                        }
                    }
                    else //wizardy dmg
                    {
                        if($itemInfo["excnum"]>=32 && $itemInfo["excnum"]<=47)
                        {
                            $optName= $wing_opt[1]."+".($itemInfo["intopt"]*4)."%";
                            $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                            $itemInfo["excnum"]-=32;
                        }
                        else if($itemInfo["excnum"]>=96)
                        {
                            $optName= $wing_opt[1]."+".($itemInfo["intopt"]*4+16)."%";
                            $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                            $itemInfo["excnum"]-=96;
                        }

                    }

                    if($itemInfo["lifeLvl"]>0 && isset($optName))
                    {
                        $itemInfo["lifeOpt"] = $optName;
                        return $itemInfo;
                    }

                }
                else if ($itemInfo["group"]==12 and $itemInfo["id"]==6) //2nd mg wings
                {
                    if($itemInfo["excnum"]<=15 or ($itemInfo["excnum"]>=64 && $itemInfo["excnum"]<=79 )) //add dmg
                    {

                        if ($itemInfo["excnum"]<=15 && $itemInfo["intopt"]>0)
                        {
                            $optName = $wing_opt[0]." +".($itemInfo["intopt"]*4)."%";
                            $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                        }
                        else if($itemInfo["excnum"]>=64 && $itemInfo["excnum"]<=79 )
                        {
                            $optName = $wing_opt[0]."+".($itemInfo["intopt"]*4+16)."%";
                            $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                            $itemInfo["excnum"]-=64;

                        }
                        if($itemInfo["lifeLvl"]>0 && isset($optName))
                        {
                            $itemInfo["lifeOpt"] = $optName;
                            return $itemInfo;
                        }
                    }
                    else //wizardy dmg
                    {
                        if($itemInfo["excnum"]>=32 && $itemInfo["excnum"]<=47 && $itemInfo["intopt"]>0)
                        {
                            $optName= $wing_opt[1]."+".($itemInfo["intopt"]*4)."%";
                            $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                            $itemInfo["excnum"]-=32;
                        }
                        else if($itemInfo["excnum"]>=96)
                        {
                            $optName= $wing_opt[1]."+".($itemInfo["intopt"]*4+16)."%";
                            $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                            $itemInfo["excnum"]-=96;
                        }
                        if($itemInfo["lifeLvl"]>0 && isset($optName))
                        {
                            $itemInfo["lifeOpt"] = $optName;
                            return $itemInfo;
                        }
                    }
                    return $itemInfo;
                }
                else if (in_array($itemInfo["id"],$thss)) //3thd class
                {
                    #region суммонерские 3и винги
                    if($itemInfo["group"]==12 && $itemInfo["id"]==43) // sumoner
                    {
                        if(($itemInfo["excnum"]>=32 && $itemInfo["excnum"]<=47) OR $itemInfo["excnum"]>=96)
                        {
                            if($itemInfo["excnum"]>=32 && $itemInfo["excnum"]<96)
                            {
                                if($itemInfo["excnum"]-32>=0)
                                {
                                    $optName="Additional Curse Spell +".($itemInfo["intopt"]*4);
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                                    $itemInfo["excnum"]-=32;
                                }
                            }
                            else
                            {
                                if($itemInfo["excnum"]-96>=0)
                                {
                                    $optName="Additional Curse Spell +".($itemInfo["intopt"]*4+16)."%";
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                                    $itemInfo["excnum"]-=96;
                                }
                            }
                            if($itemInfo["lifeLvl"]>0 && isset($optName))
                            {
                                $itemInfo["lifeOpt"] = $optName;
                                return $itemInfo;
                            }
                        }
                        else if (($itemInfo["excnum"]>=16 && $itemInfo["excnum"]<=31) or ($itemInfo["excnum"]>=80 && $itemInfo["excnum"]<=95))
                        {
                            if($itemInfo["excnum"]>=16 && $itemInfo["excnum"]<80)
                            {
                                if($itemInfo["excnum"]-16>=0)
                                {
                                    $optName="Additional Wizardry dmg + ".($itemInfo["intopt"]*4);
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                                    $itemInfo["excnum"]-=16;
                                }
                            }
                            else
                            {
                                if($itemInfo["excnum"]-80>=0)
                                {
                                    $optName="Additional Wizardry dmg +".($itemInfo["intopt"]*4+16)."%";
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                                    $itemInfo["excnum"]-=80;
                                }
                            }
                            if($itemInfo["lifeLvl"]>0 && isset($optName))
                            {
                                $itemInfo["lifeOpt"] = $optName;
                                return $itemInfo;
                            }
                        }
                        elseif (($itemInfo["excnum"]>=0 && $itemInfo["excnum"]<=47) OR($itemInfo["excnum"]>=64 && $itemInfo["excnum"]<=79))
                        {
                            if($itemInfo["excnum"]==0)
                            {
                                $optName=" HP Recovery +".$itemInfo["intopt"]."%".$optName;
                                $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                            }
                            else
                            {
                                if($itemInfo["excnum"]-64>=0)
                                {
                                    $optName=" HP Recovery +".($itemInfo["intopt"]+4)."%".$optName;
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                                    $itemInfo["excnum"]-=64;
                                }
                            }

                            if($itemInfo["lifeLvl"]>0 && isset($optName))
                            {
                                $itemInfo["lifeOpt"] = $optName;
                                return $itemInfo;
                            }
                        }

                        return $itemInfo;
                    }
                    #endregion

                    #region dl 3и винги
                    else if($itemInfo["group"]==12 && $itemInfo["id"]==40)
                    {
                        if(($itemInfo["excnum"]>=32 && $itemInfo["excnum"]<=47) OR $itemInfo["excnum"]>=96)
                        {

                            if($itemInfo["excnum"]>=32 && $itemInfo["excnum"]<96)
                            {
                                if($itemInfo["excnum"]-32>=0)
                                {
                                    $optName="Additional Defence +".($itemInfo["intopt"]*4);
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                                    $itemInfo["excnum"]-=32;
                                }
                            }
                            else
                            {
                                if($itemInfo["excnum"]-96>=0)
                                {
                                    $optName="Additional Defence  +".($itemInfo["intopt"]*4+16)."%";
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                                    $itemInfo["excnum"]-=96;
                                }
                            }
                            if($itemInfo["lifeLvl"]>0 && isset($optName))
                            {
                                $itemInfo["lifeOpt"] = $optName;
                                return $itemInfo;
                            }
                        }
                        else if (($itemInfo["excnum"]>=16 && $itemInfo["excnum"]<=31) or ($itemInfo["excnum"]>=80 && $itemInfo["excnum"]<=95))
                        {
                            if($itemInfo["excnum"]>=16 && $itemInfo["excnum"]<80)
                            {
                                if($itemInfo["excnum"]-16>=0)
                                {
                                    $optName.="Additional dmg +".($itemInfo["intopt"]*4);
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                                    $itemInfo["excnum"]-=16;
                                }
                            }
                            else
                            {
                                if($itemInfo["excnum"]-80>=0)
                                {
                                    $optName.="Additional dmg +".($itemInfo["intopt"]*4+16)."%";
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                                    $itemInfo["excnum"]-=80;
                                }
                            }
                            if($itemInfo["lifeLvl"]>0 && isset($optName))
                            {
                                $itemInfo["lifeOpt"] = $optName;
                                return $itemInfo;
                            }
                        }
                        elseif (($itemInfo["excnum"]>=0 && $itemInfo["excnum"]<=47 && $itemInfo["intopt"]>0) OR($itemInfo["excnum"]>=64 && $itemInfo["excnum"]<=79))
                        {
                            if($itemInfo["excnum"]==0)
                            {
                                $optName=" HP Recovery +".$itemInfo["intopt"]."%".$optName;
                                $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                            }
                            else
                            {
                                if($itemInfo["excnum"]-64>=0)
                                {
                                    $optName=" HP Recovery +".($itemInfo["intopt"]+4)."%".$optName;
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                                    $itemInfo["excnum"]-=64;
                                }
                            }

                            if($itemInfo["lifeLvl"]>0 && isset($optName))
                            {
                                $itemInfo["lifeOpt"] = $optName;
                                return $itemInfo;
                            }
                        }
                        return $itemInfo;
                    }
                    #endregion
                    #region mg 3и винги
                    else if($itemInfo["group"]==12 && $itemInfo["id"]==39)
                    {
                        if(($itemInfo["excnum"]>=32 && $itemInfo["excnum"]<=47) OR $itemInfo["excnum"]>=96)
                        {
                            if($itemInfo["excnum"]>=32 && $itemInfo["excnum"]<96)
                            {
                                if($itemInfo["excnum"]-32>=0)
                                {
                                    $optName="Wizardry dmg +".($itemInfo["intopt"]*4);
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                                    $itemInfo["excnum"]-=32;
                                }
                            }
                            else
                            {
                                if($itemInfo["excnum"]-96>=0)
                                {
                                    $optName="Wizardry dmg +".($itemInfo["intopt"]*4+16)."%";
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                                    $itemInfo["excnum"]-=96;
                                }
                            }
                            if($itemInfo["lifeLvl"]>0 && isset($optName))
                            {
                                $itemInfo["lifeOpt"] = $optName;
                                return $itemInfo;
                            }
                        }
                        else if (($itemInfo["excnum"]>=16 && $itemInfo["excnum"]<=31) or ($itemInfo["excnum"]>=80 && $itemInfo["excnum"]<=95))
                        {
                            if($itemInfo["excnum"]>=16 && $itemInfo["excnum"]<80)
                            {
                                if($itemInfo["excnum"]-16>=0)
                                {
                                    $optName="Additional dmg +".($itemInfo["intopt"]*4);
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                                    $itemInfo["excnum"]-=16;
                                }
                            }
                            else
                            {
                                if($itemInfo["excnum"]-80>=0)
                                {
                                    $optName="Additional dmg +".($itemInfo["intopt"]*4+16)."%";
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                                    $itemInfo["excnum"]-=80;
                                }
                            }
                            if($itemInfo["lifeLvl"]>0 && isset($optName))
                            {
                                $itemInfo["lifeOpt"] = $optName;
                                return $itemInfo;
                            }
                        }
                        elseif (($itemInfo["excnum"]>=0 && $itemInfo["excnum"]<=47) OR($itemInfo["excnum"]>=64 && $itemInfo["excnum"]<=79))
                        {
                            if($itemInfo["excnum"]==0 && $itemInfo["intopt"]>0)
                            {
                                $optName=" HP Recovery +".$itemInfo["intopt"]."%".$optName;
                                $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                            }
                            else
                            {
                                if($itemInfo["excnum"]-64>=0)
                                {
                                    $optName=" HP Recovery +".($itemInfo["intopt"]+4)."%".$optName;
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                                    $itemInfo["excnum"]-=64;
                                }
                            }

                            if($itemInfo["lifeLvl"]>0 && isset($optName))
                            {
                                $itemInfo["lifeOpt"] = $optName;
                                return $itemInfo;
                            }
                        }
                        return $itemInfo;
                    }
                    #endregion
                    #region elf 3и винги
                    else if($itemInfo["group"]==12 && $itemInfo["id"]==38)
                    {
                        if(($itemInfo["excnum"]>=32 && $itemInfo["excnum"]<=47) OR $itemInfo["excnum"]>=96)
                        {
                            if($itemInfo["excnum"]>=32 && $itemInfo["excnum"]<96)
                            {
                                if($itemInfo["excnum"]-32>=0)
                                {
                                    $optName="Additional Defence +".($itemInfo["intopt"]*4);
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                                    $itemInfo["excnum"]-=32;
                                }
                            }
                            else
                            {
                                if($itemInfo["excnum"]-96>=0)
                                {
                                    $optName="Additional Defence +".($itemInfo["intopt"]*4+16)."%";
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                                    $itemInfo["excnum"]-=96;
                                }
                            }
                            if($itemInfo["lifeLvl"]>0 && isset($optName))
                            {
                                $itemInfo["lifeOpt"] = $optName;
                                return $itemInfo;
                            }
                        }
                        else if (($itemInfo["excnum"]>=16 && $itemInfo["excnum"]<=31) or ($itemInfo["excnum"]>=80 && $itemInfo["excnum"]<=95))
                        {
                            if($itemInfo["excnum"]>=16 && $itemInfo["excnum"]<80)
                            {
                                if($itemInfo["excnum"]-16>=0)
                                {
                                    $optName.="Additional dmg +".($itemInfo["intopt"]*4);
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                                    $itemInfo["excnum"]-=16;
                                }
                            }
                            else
                            {
                                if($itemInfo["excnum"]-80>=0)
                                {
                                    $optName.="Additional dmg +".($itemInfo["intopt"]*4+16)."%";
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                                    $itemInfo["excnum"]-=80;
                                }
                            }
                            if($itemInfo["lifeLvl"]>0 && isset($optName))
                            {
                                $itemInfo["lifeOpt"] = $optName;
                                return $itemInfo;
                            }
                        }
                        elseif (($itemInfo["excnum"]>=0 && $itemInfo["excnum"]<=15) OR($itemInfo["excnum"]>=64 && $itemInfo["excnum"]<=79))
                        {
                            if($itemInfo["excnum"]>=0 && $itemInfo["excnum"]<=15)
                            {
                                $optName="+".$itemInfo["intopt"]."% HP Recovery";
                                $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                            }
                            else if(($itemInfo["excnum"]>=64 && $itemInfo["excnum"]<=79))
                            {
                                if($itemInfo["excnum"]-64>=0)
                                {
                                    $optName="+".($itemInfo["intopt"]+4)."% HP Recovery";
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                                    $itemInfo["excnum"]-=64;
                                }
                            }

                            if($itemInfo["lifeLvl"]>0 && isset($optName))
                            {
                                $itemInfo["lifeOpt"] = $optName;
                                return $itemInfo;
                            }
                        }
                        return $itemInfo;
                    }
                    #endregion
                    //region dw 3и винги
                    else if($itemInfo["group"]==12 && $itemInfo["id"]==37)
                    {
                        if(($itemInfo["excnum"]>=32 && $itemInfo["excnum"]<=47) OR $itemInfo["excnum"]>=96)
                        {
                            if($itemInfo["excnum"]>=32 && $itemInfo["excnum"]<96)
                            {
                                if($itemInfo["excnum"] - 32 >=0)
                                {
                                    $optName.="Additional Defence +".($itemInfo["intopt"]*4);
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                                    $itemInfo["excnum"]-=32;
                                }
                            }
                            else
                            {
                                if($itemInfo["excnum"] - 96 >=0)
                                {
                                    $optName.="Additional Defence +".($itemInfo["intopt"]*4+16)."%";
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                                    $itemInfo["excnum"]-=96;
                                }
                            }
                            if($itemInfo["lifeLvl"]>0 && isset($optName))
                            {
                                $itemInfo["lifeOpt"] = $optName;
                                return $itemInfo;
                            }
                        }
                        else if (($itemInfo["excnum"]>=16 && $itemInfo["excnum"]<=31) or ($itemInfo["excnum"]>=80 && $itemInfo["excnum"]<=95))
                        {
                            if($itemInfo["excnum"]>=16 && $itemInfo["excnum"]<=31)
                            {
                                if($itemInfo["excnum"]-16>=0)
                                {
                                    $optName.="Wizardry dmg +".($itemInfo["intopt"]*4);
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                                    $itemInfo["excnum"]-=16;
                                }
                            }
                            else
                            {
                                if($itemInfo["excnum"]-80>=0)
                                {
                                    $optName.="Wizardry dmg +".($itemInfo["intopt"]*4+16)."%";
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                                    $itemInfo["excnum"]-=80;
                                }
                            }
                            if($itemInfo["lifeLvl"]>0 && isset($optName))
                            {
                                $itemInfo["lifeOpt"] = $optName;
                                return $itemInfo;
                            }
                        }
                        elseif (($itemInfo["excnum"]>=0 && $itemInfo["excnum"]<=47) OR($itemInfo["excnum"]>=64 && $itemInfo["excnum"]<=79))
                        {

                            if($itemInfo["excnum"]==0)
                            {
                                $optName="+".$itemInfo["intopt"]."%  HP Recovery";
                                $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                            }
                            else
                            {
                                if($itemInfo["excnum"]-64>=0)
                                {
                                    $optName="+".($itemInfo["intopt"]+4)."%  HP Recovery";
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                                    $itemInfo["excnum"]-=64;
                                }
                            }

                            if($itemInfo["lifeLvl"]>0 && isset($optName))
                            {
                                $itemInfo["lifeOpt"] = $optName;
                                return $itemInfo;
                            }
                        }

                        if ( isset($itemInfo["lifeLvl"]) && $itemInfo["lifeLvl"]>0)
                            return $itemInfo;
                        else
                        {
                            unset($itemInfo["lifeLvl"],$itemInfo["lifeOpt"]);
                        }
                    }
                    //endregion
                    //region dk 3и винги
                    else if($itemInfo["group"]==12 && $itemInfo["id"]==36)
                    {
                        if(($itemInfo["excnum"]>=32 && $itemInfo["excnum"]<=47) OR $itemInfo["excnum"]>=96)
                        {
                            if($itemInfo["excnum"]>=32 && $itemInfo["excnum"]<96)
                            {
                                if($itemInfo["excnum"]-32>=0)
                                {
                                    $optName="Additional Defence +".($itemInfo["intopt"]*4);
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                                    $itemInfo["excnum"]-=32;
                                }
                            }
                            else
                            {
                                if($itemInfo["excnum"]-96>=0)
                                {
                                    $optName="Additional Defence +".($itemInfo["intopt"]*4+16)."%";
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                                    $itemInfo["excnum"]-=96;
                                }
                            }
                            if($itemInfo["lifeLvl"]>0 && isset($optName))
                            {
                                $itemInfo["lifeOpt"] = $optName;
                                return $itemInfo;
                            }
                        }
                        else if (($itemInfo["excnum"]>=16 && $itemInfo["excnum"]<=31) or ($itemInfo["excnum"]>=80 && $itemInfo["excnum"]<=95))
                        {
                            if($itemInfo["excnum"]>=16 && $itemInfo["excnum"]<80)
                            {
                                if($itemInfo["excnum"]-16>=0)
                                {
                                    $optName="Additional dmg +".($itemInfo["intopt"]*4);
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                                    $itemInfo["excnum"]-=16;
                                }
                            }
                            else
                            {
                                if($itemInfo["excnum"]-80>=0)
                                {
                                    $optName="Additional dmg +".($itemInfo["intopt"]*4+16)."%";
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                                    $itemInfo["excnum"]-=80;
                                }
                            }
                            if($itemInfo["lifeLvl"]>0 && isset($optName))
                            {
                                $itemInfo["lifeOpt"] = $optName;
                                return $itemInfo;
                            }
                        }
                        elseif (($itemInfo["excnum"]>=0 && $itemInfo["excnum"]<=47) OR($itemInfo["excnum"]>=64 && $itemInfo["excnum"]<=79))
                        {

                            if($itemInfo["excnum"]==0 && $itemInfo["intopt"]>0)
                            {
                                $optName="HP Recovery +".$itemInfo["intopt"]."%";
                                $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                            }
                            else if($itemInfo["excnum"]>=64)
                            {
                                if($itemInfo["excnum"]-64>=0)
                                {
                                    $optName="HP Recovery +".($itemInfo["intopt"]+4)."%";
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                                    $itemInfo["excnum"]-=64;
                                }
                            }

                            if($itemInfo["lifeLvl"]>0 && isset($optName))
                            {
                                $itemInfo["lifeOpt"] = $optName;
                                return $itemInfo;
                            }
                        }
                        return $itemInfo;
                    }
                    #endregion
                    #region rf 3и винги
                    else if($itemInfo["group"]==12 && $itemInfo["id"]==50)
                    {
                        if(($itemInfo["excnum"]>=32 && $itemInfo["excnum"]<=47) OR $itemInfo["excnum"]>=96)
                        {
                            if($itemInfo["excnum"]>=32 && $itemInfo["excnum"]<96)
                            {
                                if($itemInfo["excnum"]-32>=0)
                                {
                                    $optName.="Additional Defence +".($itemInfo["intopt"]*4);
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                                    $itemInfo["excnum"]-=32;
                                }
                            }
                            else
                            {
                                if($itemInfo["excnum"]-96>=0)
                                {
                                    $optName.="Additional Defence +".($itemInfo["intopt"]*4+16)."%";
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                                    $itemInfo["excnum"]-=96;
                                }
                            }
                            if($itemInfo["lifeLvl"]>0 && isset($optName))
                            {
                                $itemInfo["lifeOpt"] = $optName;
                                return $itemInfo;
                            }
                        }
                        else if (($itemInfo["excnum"]>=16 && $itemInfo["excnum"]<=31) or ($itemInfo["excnum"]>=80 && $itemInfo["excnum"]<=95))
                        {
                            if($itemInfo["excnum"]>=16 && $itemInfo["excnum"]<80)
                            {
                                if($itemInfo["excnum"]-16>=0)
                                {
                                    $optName.="Additional dmg +".($itemInfo["intopt"]*4);
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                                    $itemInfo["excnum"]-=16;
                                }
                            }
                            else
                            {
                                if($itemInfo["excnum"]-80>=0)
                                {
                                    $optName.="Additional dmg +".($itemInfo["intopt"]*4+16)."%";
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                                    $itemInfo["excnum"]-=80;
                                }
                            }
                            if($itemInfo["lifeLvl"]>0 && isset($optName))
                            {
                                $itemInfo["lifeOpt"] = $optName;
                                return $itemInfo;
                            }
                        }
                        elseif (($itemInfo["excnum"]>=0 && $itemInfo["excnum"]<=47) OR($itemInfo["excnum"]>=64 && $itemInfo["excnum"]<=79))
                        {

                            if(($itemInfo["excnum"]>=0 && $itemInfo["excnum"]<=47) && $itemInfo["intopt"]>0)
                            {
                                $optName="HP Recovery +".$itemInfo["intopt"]."%";
                                $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                            }
                            else if ($itemInfo["excnum"]>=64 && $itemInfo["excnum"]<=79)
                            {
                                if($itemInfo["excnum"]-64>=0)
                                {
                                    $optName="HP Recovery +".($itemInfo["intopt"]+4)."%";
                                    $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                                    $itemInfo["excnum"]-=64;
                                }
                            }
                            if($itemInfo["lifeLvl"]>0 && isset($optName))
                            {
                                $itemInfo["lifeOpt"] = $optName;
                                return $itemInfo;
                            }
                        }
                        return $itemInfo;
                    }
                    #endregion
                }
                else // все остальные винги
                {
                    if ($itemInfo["excnum"] <32 or ($itemInfo["excnum"]>=64 && $itemInfo["excnum"]<96) ) // wizardy dmg
                    {
                        if($itemInfo["excnum"]<64)
                        {
                            $optName=$wing_opt[0]."+".($itemInfo["intopt"]*4)."%";
                            $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                        }
                        else
                        {
                            $optName=$wing_opt[0]."+".($itemInfo["intopt"]*4+16)."%";
                            $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                            $itemInfo["excnum"]-=64;
                        }
                        if($itemInfo["lifeLvl"]>0 && isset($optName))
                        {
                            $itemInfo["lifeOpt"] = $optName;
                            return $itemInfo;
                        }
                    }
                    elseif($itemInfo["excnum"]>=32 or $itemInfo["excnum"] >=96) // add dmg
                    {
                        if($itemInfo["excnum"]>=32 && $itemInfo["excnum"]<96)
                        {
                            $optName =$wing_opt[1]."+".($itemInfo["intopt"]*4)."%";
                            $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                            $itemInfo["excnum"]-=32;
                        }
                        else
                        {
                            $optName=$wing_opt[1]."+".($itemInfo["intopt"]*4+16)."%";
                            $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                            $itemInfo["excnum"]-=96;
                        }
                        if($itemInfo["lifeLvl"]>0 && isset($optName))
                        {
                            $itemInfo["lifeOpt"] = $optName;
                            return $itemInfo;
                        }

                    }

                    if($itemInfo["excnum"]<=15 or ($itemInfo["excnum"]>=64 && $itemInfo["excnum"]<=79 )) //add dmg
                    {
                        if ($itemInfo["excnum"]<=15 && $itemInfo["intopt"]>0)
                        {
                            $optName= $wing_opt[1]."+".($itemInfo["intopt"]*4)."%";
                            $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                        }
                        else if($itemInfo["excnum"]>=64 && $itemInfo["excnum"]<=79 )
                        {
                            $optName= $wing_opt[1]."+".($itemInfo["intopt"]*4+16)."%";
                            $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                            $itemInfo["excnum"]-=64;
                        }
                        else
                        {
                            unset($optName);
                        }
                        if($itemInfo["lifeLvl"]>0 && isset($optName))
                        {
                            $itemInfo["lifeOpt"] = $optName;
                            return $itemInfo;
                        }
                    }
                    else //wizardy dmg
                    {
                        if($itemInfo["excnum"]>=32 && $itemInfo["excnum"]<=47)
                        {
                            $optName= $wing_opt[0]."+".($itemInfo["intopt"]*4)."%";
                            $itemInfo["lifeLvl"] = $itemInfo["intopt"];
                            $itemInfo["excnum"]-=32;
                        }
                        else if($itemInfo["excnum"]>=96)
                        {
                            $optName= $wing_opt[0]."+".($itemInfo["intopt"]*4+16)."%";
                            $itemInfo["lifeLvl"] = $itemInfo["intopt"] + 4;
                            $itemInfo["excnum"]-=96;
                        }
                        if($itemInfo["lifeLvl"]>0 && isset($optName))
                        {
                            $itemInfo["lifeOpt"] = $optName;
                            return $itemInfo;
                        }

                    }
                }
            }

            if(isset($optName) && $itemInfo["lifeLvl"]>0)
                $itemInfo["lifeOpt"] = $optName;
        }
        return $itemInfo;
    }

    /**
     * из 16 в 10 систему
     * @param $hex
     * @param $begin откуда начать
     * @param $length чем закончить
     * @return number 10чное число
     */
    static public function dehex($hex,$begin,$length)
    {
        return hexdec(substr($hex,$begin,$length));
    }
}