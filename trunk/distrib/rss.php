<?php
header("content-type: application/rss+xml");
define ('insite', 1);
require "opt.php";
function bbcode($text) {
 $bbcode = array(
 "/\[url\=(.*?)\](.*?)\[\/url\]/is" => "<a target=\"_blank\" href=\"$1\">$2</a>",
 "/\[img\](.*?)\[\/img\]/is" => "",
 "/\[b\](.*?)\[\/b\]/is" => "<b>$1</b>",
 "/\[c\](.*?)\[\/c\]/is" => "$1",
 "/\[i\](.*?)\[\/i\]/is" => "<i>$1</i>",
 "/\[u\](.*?)\[\/u\]/is" => "<u>$1</u>",
 "/\[o\](.*?)\[\/o\]/is" => "$1",
 "/\[l\](.*?)\[\/l\]/is" => "$1",
 "/\[r\](.*?)\[\/r\]/is" => "$1",
 "/\[hr\]/is" => "<hr>",
 "/\[sup\](.*?)\[\/sup\]/is" => "$1",
 "/\[sub\](.*?)\[\/sub\]/is" => "$1",//подстрочный
 "/\[size\=(.*?)\](.*?)\[\/size\]/is" => "<span style=\"font-size:$1pt;\">$2</span>",
  "/\[color\=(.*?)\](.*?)\[\/color\]/is" => "<font color=\"#$1\">$2</font>",
  "/\[sml\](.*?)\[\/sml\]/is" => " "
 );
 
 $text = preg_replace(array_keys($bbcode), array_values($bbcode), $text);
 return $text;
}
function unhtmlentities ($str)
{
  $trans_tbl = get_html_translation_table (HTML_ENTITIES);
  $trans_tbl = array_flip ($trans_tbl);
  return strtr ($str, $trans_tbl);
}

echo '<?xml version="1.0" encoding="windows-1251"?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">
<channel>
<title>'.$config["server_name"].' news</title>
<link>'.$config["siteaddress"].'</link>
<description>news</description>
<language>ru-ru</language>';

$rss_lent = file("_dat/news.dat");
$i=0;
foreach ($rss_lent as $show)
{
list($title,$news,$linkz,$date)=split("::",unhtmlentities($show),3);
echo '
<item>
<title>'.bbcode($title).'</title>
<link>'.$config["siteaddress"].'/news'.$i.'.html</link>
<description>new news</description>
<category>news</category>
<dc:date>'.@date("r",$date).'</dc:date>
</item>';
$i++;
}
echo '
</channel>
</rss>';
