<?php if (!defined('insite')) die("no access");

/**
 * отображение "плагинов"
 * @param string $var название файла
 * @param $content
 * @param $config
 * @param $db
 * @return string
 */
function showst($var,$content,$config,$db)
{
    if (trim($var)=="baners")
    {
        $temp = file_get_contents("_dat/baners.dat");
    }
    else if (trim($var)=="lang_menu")
    {
 //change lang menu

    if (isset($_REQUEST["chsl"]))
    {
       $lan = substr($_POST["chsl"],0,3);
       if ($_SESSION["mwclang"]!=$lan)
           $_SESSION["mwclang"]=$lan;
       header('Refresh: 0;');
    }
     if (file_exists("_dat/cach/lang".substr($_SESSION["mwclang"],0,3)))
        $temp = file_get_contents("_dat/cach/lang".substr($_SESSION["mwclang"],0,3));
     else
     {
      ob_start();
      $content->out_content("theme/".$config["theme"]."/them/langmenu_h.html");
      $ld=opendir("lang");

      while (false !== ($file = readdir($ld)))
      {
       if (is_dir("lang/".$file) && $file!= "." && $file != "..")
       {
        $content->set('|value|', $file);
        $content->set('|caption|', $file);
        if ( $_SESSION["mwclang"]==$file)
         $content->set('|onsel|', "selected");
        else
         $content->set('|onsel|', "");
        $content->out_content("theme/".$config["theme"]."/them/langmenu_c.html");
       }
      }

      $content->out_content("theme/".$config["theme"]."/them/langmenu_f.html");
      $temp = ob_get_contents();
      write_catch("_dat/cach/lang".substr($_SESSION["mwclang"],0,3),$temp);
      ob_end_clean();
     }
 
 //
//end change menu
    }
    else
    {
        if (file_exists("_sysvol/plugins/".$var.".php"))
        {
            ob_start();
            require "_sysvol/plugins/".$var.".php";
            $temp_p = ob_get_contents();
            ob_end_clean();
            if (!isset($temp) || empty($temp))
                return $temp_p;
            return $temp;
        }
        else
            $temp = "no module ".htmlspecialchars($var);
    }
 return $temp;
}

function showpt($var)
{
 if (file_exists("pages/".$var.".php"))
  require "pages/".$var.".php";
 else
  $temp = "no module ".htmlspecialchars($var);
 return $temp;
}
