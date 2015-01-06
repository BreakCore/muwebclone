<?php if (!defined('insite')) die("no access"); 

require "configs/top100_cfg.php";
require "configs/online_cfg.php";
$gettime = time();
$ntime = load_cache("_dat/cach/".$_SESSION["mwclang"]."_toponline",true);

if($gettime-$ntime>300)
{
 ob_start();
 $i=0;
 $acc_online = $db->query("SELECT
 ac.GameIDC,
 ch.Class,
 ch.cLevel,
 ch.{$top100["t100res_colum"]}
FROM
 MEMB_STAT ms,
 AccountCharacter ac,
 [Character] ch
WHERE
 ac.Id COLLATE DATABASE_DEFAULT = ms.memb___id COLLATE DATABASE_DEFAULT
 AND ac.GameIDC COLLATE DATABASE_DEFAULT = ch.Name COLLATE DATABASE_DEFAULT
 AND ms.Connectstat=1
 order by  ms.ConnectTM desc");

 $content->out("online_h.html");

 while ($racc_on = $acc_online->FetchRow())
 {
  ($i % 2 == 0) ? $sh_pl = "class='lighter1'" : $sh_pl="";

  $content->set('|style|', $sh_pl);
  $content->set('|name|',  $racc_on["GameIDC"]);
  $content->set('|class|', classname($racc_on["Class"]));
  $content->set('|level|', $racc_on["cLevel"]);
  $content->set('|reset|', $racc_on[$top100["t100res_colum"]]);
  $content->out("online_c.html");
  $i++;
 }
 $content->out("online_f.html");
 timing(300,$content);
 $temp = ob_get_contents();
 ob_clean();
 write_catch("_dat/cach/".$_SESSION["mwclang"]."_toponline",$temp);
}
else
 $temp = load_cache( "_dat/cach/".$_SESSION["mwclang"]."_toponline");
