<?php
/**
 * Date: 07.02.2015
 * Time: 10:02
 */

class itemShow
{
    private static $luck = "Luck(succes rate of Jewel of soul +25%)<br>Luck(critical damage rate +5%)";

    public static function show($rItem)
    {
        //debug($rItem);
        $class= "item";
        if(isset($rItem["exc"]))
        {
            $rItem["name"] = "Excellent ".$rItem["name"];
            $class = "exc";
        }
        if(isset($rItem["anc"]))
        {
            $rItem["name"] = "Ancient ".$rItem["name"];
            if($class=="exc")
                $class = "excanc";
            else
                $class = "anc";
        }

        if(isset($rItem["level"]) && $rItem["level"]>0)
        {
            $rItem["name"].=" +".$rItem["level"];
            if($class == "item")
                $class = "item".$rItem["level"];
        }

        if(isset($rItem["isSkill"]) || isset($rItem["lifeOpt"]))
        {
            $class = "itemwithoption";
        }
        $output[$class] = $rItem["name"];

        $img = self::getImg($rItem);
        if($img)
            $output["image"]= "$img";

        if(isset($rItem["speed"]))
            $output["speed"] = $rItem["speed"];

        if(isset($rItem["str"]))
        {
            $output["str"]= "Strength Requirement: ".$rItem["str"];
        }
        if(isset($rItem["str"]))
        {
            $output["agi"]= "Agility Requirement: ".$rItem["agi"];
        }
        if(isset($rItem["ene"]))
        {
            $output["ene"]= "Energy Requirement: ".$rItem["ene"];
        }
        if(isset($rItem["vit"]))
        {
            $output["vit"]= "Vitality Requirement: ".$rItem["vit"];
        }
        if(isset($rItem["cmd"]))
        {
            $output["cmd"]= "Command Requirement: ".$rItem["cmd"];
        }

        if(isset($rItem["exc"]) && is_array($rItem["exc"]))
        {
            $output["exc"] = $rItem["exc"];
        }
        if(isset($rItem["pvp"]))
        {
            $output["pvp"] = $rItem["pvp"];
        }

        if(isset($rItem["anc"]))
        {
            $output["anc"] = $rItem["anc"];
        }


        if(isset($rItem["equipment"]))
            $output["equipment"]= $rItem["equipment"];
        if(isset($rItem["isLuck"]))
            $output["luck"] = self::$luck;
        if(isset($rItem["skillname"]))
            $output["skillname"] = $rItem["skillname"];

        if(isset($rItem["harmony"]))
            $output["harmony"] = $rItem["harmony"];

        return $output;
    }

    static public function getImg($rItem)
    {
        if(isset($rItem["img"]))
        {
            if(file_exists("imgs/items/".$rItem["img"].".gif"))
                return "<img src='/imgs/items/{$rItem["img"]}.gif'>";
            else if(file_exists("imgs/items/".$rItem["img"].".png"))
                return "<img src='/imgs/items/{$rItem["img"]}.png'>";
            else
                return "no such image {$rItem["img"]}";
        }
        return false;
    }

    /**
     * вывод вещи
     * @param $rItem array
     * @param $type integer
     * 1 - просто изображение
     * 2 - просто с информацией
     * @return string
     */
    public static function showImg($rItem,$type)
    {
        $result = self::show($rItem);
        if($type == 1)
        {
            if(isset($result["image"]))
                return $result["image"];
            return "no image";
        }
        else
        {

        }


    }

}