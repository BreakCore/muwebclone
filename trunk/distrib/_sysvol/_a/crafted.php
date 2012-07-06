<?php if (!defined('inpanel')) die("no access"); 
ob_start();

global $config;
require "_sysvol/item.inc.php";
require "_sysvol/crafrer.inc.php";

if (file_exists("_dat/craft.db")) $base = file("_dat/craft.db");
else
{
 $handle = fopen("_dat/craft.db","w");
 fclose($handle);
 $base = file("_dat/craft.db");
}

if ( isset($_GET["del"]) && $_GET["del"]< count($base))
{
 $handle = fopen("_dat/craft.db","w");
  foreach ($base as $n=>$srt)
  {
   if ($n!=$_GET["del"])fwrite($handle,$srt);
  }
  fclose($handle);
   header("Location:".$config["siteaddress"]."/control.php?page=crafted");
}
echo "<div align='center' ><u>Уже имеющиеся комбинации:</u></div>";
foreach($base as $i=>$str)
{
 $tempz = explode(";",$str);

 echo "<div align='center' style='font-size:14px;font-style:italic;text-align:center'>".$tempz[1]." <a href='".$config["siteaddress"]."/control.php?page=crafted&del=".$i."'>[delete]</a></div>";
}
echo "<form method='POST' action='".$config["siteaddress"]."/control.php?page=crafted'>
 <div align='right' style='width:520px;'><br> Ингридиенты<span style='font-size:12px;font-style:italic'>(хекс-коды через запятую)</span>: <input type='text' name='ingreed'></div>
 <div align='right' style='width:520px;'> Конечный результат<span style='font-size:12px;font-style:italic'>(хекс-код)</span>: <input type='text' name='finish'></div>
 <div align='center'>  <input type='submit' class='button' value='Добавить' name='finish_bt'></div>";

 if ($_REQUEST["finish_bt"]) 
{
 require "imgs/items.php";
 $ingr = $_POST["ingreed"];
 $pr = $_POST["finish"];
 if (strlen($ingr)>=32 && strlen($pr)==32)
 {
   $prise = items::readitems($pr,$itembd,$anc);
   $obj = new crafter($itembd); 

   
   $tempz = explode(",",$ingr);
   $sum=0;
   foreach ($tempz as $hex)
   {
     $sum+=$obj->GetSum(items::readitems($hex,$itembd,$anc));
   }

   $handle = fopen("_dat/craft.db","a+");
   fwrite($handle,"\r\n".$sum.";".$obj->GenDbprize($prise));
   fclose($handle);
   header("Location:".$config["siteaddress"]."/control.php?page=crafted");
 }
 
}
 
$temp = ob_get_contents(); 
ob_end_clean();
