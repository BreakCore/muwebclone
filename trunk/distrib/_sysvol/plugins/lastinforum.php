<?php if (!defined('insite')) die("no access");
/**
 * Плагин "последнее с форума"
 * версия для IPB
 */
$ntime =  @filemtime("_dat/cach/lastinforum");
if(!$ntime || (time()-$ntime >3600)) //раз в час обновл¤ет данные с форума
{
	ob_start();
	$ipb_mysql_data_base_name="forum";  //им¤ базы данных форму IPB
	$ipb_mysql_host="localhost"; //адрес MySQL дл¤ форума IPB
	$ipb_mysql_user="root"; //пользователь дл¤ MySQL, на которой стоит база IPB
	$ipb_mysql_password="root"; //пароль MySQL
	$ipb_mysql_db_prefix="ibf_"; //префикс таблиц IPB
	$ipb_site="http://forum.ru/"; //адрес сайта форума (до /index.php)

	$koli4estvo_tem=5; //кол-во выводимых тем
	$dlina_temi=20; //кол-во символов в выводимом заголовке

	$forum_id="*"; //Kакие разделы форума обрабатывать. ID разделов (число, которое стоит в ссылке на раздел после "showforum="). „ерез зап¤тую и без пробелов. "*" - обрабатывать все разделы.
	$forum_id_no="9999999999"; // акие разделы форума не нужно обрабатывать. ƒл¤ того, чтобы обрабатывались ¬—≈ разделы присвойте $forum_id="*", а переменной $forum_id_no любое заоблочное :) число. Ќапример 1000000000. ¬ общем, число должно быть больше чем номер последнего раздела. —начала обрабатываетс¤ переменна¤ $forum_id: если ей присвоено "*", то обрабатываетс¤ $forum_id_no. если переменной $forum_id присвоино значение отличное от "*", то тогда уже обрабатываетс¤ $forum_id_no.
	//¬ общем можно делать так "ќбрабатывать только разделы с номерами ..." или "ќбрабатывать все кроме номеров..."

	/*формат выводимого заголовка.
        {Full_title} - полное название темы
        {URL_to_post} - ссылка на последний пост темы
        {Short_title} - название темы обрезанное до $dlina_temi символов
        {User_name} - им¤ последнего написавшего пост в теме
        {Date} - дата и врем¤ последнего поста

        {ReplyCount} - количество ответов
        {Views} - количество просмотров
        {ReplyCount} - количество ответов
        {Views} - количество просмотров
        */
	$format_stroki=" <div><a title='{Full_title}' href='{URL_to_post}'>{Date}-{Short_title}</a></div>";
	//==========  онец редактировани¤ ===========================

	$db=mysql_connect($ipb_mysql_host, $ipb_mysql_user, $ipb_mysql_password);
	mysql_select_db($ipb_mysql_data_base_name,$db);


	if ($forum_id=="*")
		$forum_id='WHERE `forum_id`!='.preg_replace('/,/'," AND `forum_id`!=",$forum_id_no);
	else
		$forum_id='WHERE `forum_id`='.preg_replace('/,/'," OR `forum_id`=",$forum_id);



	$query_str='SELECT posts,views,last_poster_id,last_post,title,tid,last_poster_name FROM `'.$ipb_mysql_db_prefix.'topics` '.$forum_id.' ORDER BY `last_post` DESC LIMIT 0 ,'.$koli4estvo_tem;

	$sql_12354=mysql_query($query_str);

	while ($row = mysql_fetch_array($sql_12354, MYSQL_ASSOC))
	{
		$title1=$row["title"];
		quoted_printable_decode($title1);

		if (strlen($title1) > $dlina_temi)
			$title2 = substr ($title1, 0, $dlina_temi)." ..."; 	//если длина темы больше $dlina_temi символов, то обрезаем
		else
			$title2 = $title1;									//если меньше или равна, то оставл¤ем

		//‘ормат выводимого даты и времени
		switch (date("d.m.Y",$row["last_post"])):
			case date("d.m.Y"):
				$day=date("—егодн¤ в H:i",$row["last_post"]);	//пост был написан сегодн¤
				break;
			case date("d.m.Y",time()-86400):
				$day=date("¬чера в H:i",$row["last_post"]);		//пост был написан вчера
				break;
			default:
				$day=date("d.m.Y H:i",$row["last_post"]);		//пост был написан более двух дней назад
		endswitch;


		//¬ывод URL на профиль юзера
		if ($row['last_poster_id'] != 0) 		//если зарегистрированный юзер
		{
			$format_stroki_user_profile='<a href='.$ipb_site.'/index.php?showuser='.$row['last_poster_id'].'>'.$row["last_poster_name"].'</a>';
		}
		else 													//если незарегистрированный юзер
		{
			$format_stroki_user_profile=$row["last_poster_name"];
		}

		$replycount=$row["posts"];
		$views=$row["views"];

		//формат выводимого заголовка.
		$zagolovok = preg_replace('/{Full_title}/',$title1, $format_stroki);
		$zagolovok = preg_replace('/{URL_to_post}/',$ipb_site.'/index.php?showtopic='.$row["tid"].'&view=getlastpost', $zagolovok);
		$zagolovok = preg_replace('/{Short_title}/',$title2, $zagolovok);
		$zagolovok = preg_replace('/{User_name}/',$format_stroki_user_profile, $zagolovok);
		$zagolovok = preg_replace('/{Date}/',$day, $zagolovok);
		$zagolovok = preg_replace('/{ReplyCount}/',$replycount, $zagolovok);
		$zagolovok = preg_replace('/{Views}/',$views, $zagolovok);
		echo $zagolovok;
	}
	mysql_free_result($sql_12354);
	$temp = ob_get_contents();
	write_catch ("_dat/cach/lastinforum",$temp);
	ob_end_clean();
}
else
	$temp = file_get_contents ('_dat/cach/lastinforum');
