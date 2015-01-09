<?php
/**
 * добавление/редактирование новостей
 */
error_reporting(E_ALL);
if(!file_exists("_dat/news/newslist"))
{
 $h=fopen("_dat/news/newslist",'w'); //если нету файла-оглавления новостей, создаем
 fclose($h);
}
require "configs/news_cfg.php";

$newsList = array();
$listCont = file_get_contents("_dat/news/newslist");
if(!empty($listCont))
{
 $newsList = unserialize($listCont);
 /*
  * если есть новости, ТО достаем список вида
  * array[номер_файла]
  * =>[title] тайтл новости
  * =>[link] линк на форум
  */
}
$nforumlink = "none";
if(!function_exists('get_magic_quotes_gpc')) //если нету этой функции, делаем заглушку
{
    function get_magic_quotes_gpc()
    {
        return false;
    }
}

$content->out("addnews_h.html");
if(isset($_GET["act"]))
{
    switch((int)$_GET["act"])
    {
         //форма добавления новости
         case 1:
             $content->set("|content_title|","");
             $content->set("|nforumlink|","");
             $content->set("|news_content|","");
             $content->set("|act|",2);
             $content->set("|new|",0);
             $content->set('|button_v|'," name='addnews'");
             $content->out("addnews_c.html");
             break;
         //нажата кнопка добавления новостей
         case 2:
             if(isset($_REQUEST["addnews"]))
             {
                 $title = $_POST["NewTitle"];
                 $news = $_POST["NewNews"];
                 $flink = (isset($_POST["flink"])) ? $_POST["flink"] : "none";
                 $cnt = count($newsList);
                 if($cnt>0)
                 {
                     $fname = count($newsList);
                 }
                 else
                 {
                     $fname = 0;
                 }

                 $newsList[$cnt] = array("title" => $title, "link" => $flink);
                 $h=fopen("_dat/news/newslist",'w');
                 fwrite($h,serialize($newsList));
                 fclose($h);

                 $h=fopen("_dat/news/$fname",'w');
                 fwrite($h,$news);
                 fclose($h);

                 header("Location:".$config["siteaddress"]."/control.php?page=addnews");
                 die();
             }
             break;
         //форма редактирования новости
         case 3:
             if(isset($_GET["new"]))
             {
                 $n = (int)$_GET["new"];
                 if(isset($newsList[$n]) && file_exists("_dat/news/$n"))
                 {
                     $content->set("|content_title|",valid::decode($newsList[$n]["title"]));
                     $link = valid::decode($newsList[$n]["link"]);
                     if($link !="none")
                         $content->set("|nforumlink|",$link);
                     else
                         $content->set("|nforumlink|","");

                     $content->set("|news_content|",valid::decode(file_get_contents("_dat/news/$n")));
                     $content->set("|act|",4);
                     $content->set("|new|",$n);
                     $content->set('|button_v|'," name='edited'");
                     $content->out("addnews_c.html");
                 }
                 else
                     header("Location:".$config["siteaddress"]."/control.php?page=addnews");
             }
            break;
         //редактируем и сохраняем новость
         case 4:
             if(isset($_REQUEST["edited"]) && (isset($_GET["new"]) || $_GET["new"] == "0") )
             {
                 $n = (int)$_GET["new"];
                 $title = $_POST["NewTitle"];
                 $news = $_POST["NewNews"];
                 $flink = (isset($_POST["flink"])) ? $_POST["flink"] : "none";
                 $newsList[$n] = array("title" => $title, "link" => $flink);
                 $h=fopen("_dat/news/newslist",'w');
                 fwrite($h,serialize($newsList));
                 fclose($h);

                 $h=fopen("_dat/news/$n",'w');
                 fwrite($h,$news);
                 fclose($h);

                 header("Location:".$config["siteaddress"]."/control.php?page=addnews");
                 die();
             }
            break;
            //удалить новость
            case 5:
                if(isset($_GET["new"]) || $_GET["new"] == "0")
                {
                    $n = (int)$_GET["new"];
                    unset($newsList[$n]);

                    $h=fopen("_dat/news/newslist",'w');
                    fwrite($h,serialize($newsList));
                    fclose($h);
                    unlink("_dat/news/$n");
                }
                break;
    }
}

//region список новостей
$content->out("addnews_newtitle.html");
foreach ($newsList as $id=>$news_title) {
 $content->set('|i|', $id);
 $content->set('|titleZ|', unhtmlentities($news_title["title"]));
 $content->out("addnews_list.html");
}
$content->out("addnews_f.html");
//endregion
