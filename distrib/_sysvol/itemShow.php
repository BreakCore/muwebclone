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

        if(isset($rItem["exc"]))
            $rItem["name"] = "Excellent ".$rItem["name"];
        if(isset($rItem["anc"]))
            $rItem["name"] = "Ancient ".$rItem["name"];
        if(isset($rItem["level"]) && $rItem["level"]>0)
            $rItem["name"].=" +".$rItem["level"];

        $output = "<p>{$rItem["name"]}</p>";

        $img = self::getImg($rItem);
        if($img)
            $output .= "<p>$img</p>";

        if(isset($rItem["equipment"]))
            $output .= "<p>{$rItem["equipment"]}</p>";


        echo $output;
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

}