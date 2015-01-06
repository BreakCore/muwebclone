<? if (!defined('insite')) die("no access"); 

if (isset($_SESSION["mwclang"]))
{
	if(is_file("lang/".substr($_SESSION["mwclang"],0,3)."/".substr($_SESSION["mwclang"],0,3)."rules.txt"))  $rules = file_get_contents("lang/".substr($_SESSION["mwclang"],0,3)."/".substr($_SESSION["mwclang"],0,3)."rules.txt");
	else  $rules = file_get_contents("lang/".$config["def_lang"]."/".$config["def_lang"]."rules.txt");	
}
else  $rules = file_get_contents("lang/".$config["def_lang"]."rules.txt");
 $content->set('|rules|', $rules);
$temp=$content->out_content("theme/".$config["theme"]."/them/rules.html",1);