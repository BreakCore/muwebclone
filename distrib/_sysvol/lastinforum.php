<?php if (!defined('insite')) die("no access"); 
function lasttopicipb()
{
 $ipb_mysql_data_base_name="forum";  //��� ���� ������ ����� IPB
 $ipb_mysql_host="localhost"; //����� MySQL ��� ������ IPB
 $ipb_mysql_user="root"; //������������ ��� MySQL, �� ������� ����� ���� IPB
 $ipb_mysql_password="root"; //������ MySQL
 $ipb_mysql_db_prefix="ibf_"; //������� ������ IPB
 $ipb_site="http://forum.ru/"; //����� ����� ������ (�� /index.php)

 $koli4estvo_tem=5; //���-�� ��������� ���
 $dlina_temi=20; //���-�� �������� � ��������� ���������

 $forum_id="*"; //K���� ������� ������ ������������. ID �������� (�����, ������� ����� � ������ �� ������ ����� "showforum="). ����� ������� � ��� ��������. "*" - ������������ ��� �������.
 $forum_id_no="9999999999"; //����� ������� ������ �� ����� ������������. ��� ����, ����� �������������� ��� ������� ��������� $forum_id="*", � ���������� $forum_id_no ����� ���������� :) �����. �������� 1000000000. � �����, ����� ������ ���� ������ ��� ����� ���������� �������. ������� �������������� ���������� $forum_id: ���� �� ��������� "*", �� �������������� $forum_id_no. ���� ���������� $forum_id ��������� �������� �������� �� "*", �� ����� ��� �������������� $forum_id_no.
 //� ����� ����� ������ ��� "������������ ������ ������� � �������� ..." ��� "������������ ��� ����� �������..."

 /*������ ���������� ���������.
	{Full_title} - ������ �������� ����
	{URL_to_post} - ������ �� ��������� ���� ����
	{Short_title} - �������� ���� ���������� �� $dlina_temi ��������
	{User_name} - ��� ���������� ����������� ���� � ����
	{Date} - ���� � ����� ���������� �����

	{ReplyCount} - ���������� �������
	{Views} - ���������� ����������
	{ReplyCount} - ���������� �������
	{Views} - ���������� ����������
	*/
	$format_stroki=" <div><a title='{Full_title}' href='{URL_to_post}'>{Date}-{Short_title}</a></div>";
	//========== ����� �������������� ===========================
    
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
			$title2 = substr ($title1, 0, $dlina_temi)." ..."; 	//���� ����� ���� ������ $dlina_temi ��������, �� ��������
		else
			$title2 = $title1;									//���� ������ ��� �����, �� ���������

		//������ ���������� ���� � �������
		switch (date("d.m.Y",$row["last_post"])):
		case date("d.m.Y"):
			$day=date("������� � H:i",$row["last_post"]);	//���� ��� ������� �������
		break;
		case date("d.m.Y",time()-86400):
			$day=date("����� � H:i",$row["last_post"]);		//���� ��� ������� �����
			break;
		default:
			$day=date("d.m.Y H:i",$row["last_post"]);		//���� ��� ������� ����� ���� ���� �����
			endswitch;


			//����� URL �� ������� �����
			if ($row['last_poster_id'] != 0) 		//���� ������������������ ����
			{
				$format_stroki_user_profile='<a href='.$ipb_site.'/index.php?showuser='.$row['last_poster_id'].'>'.$row["last_poster_name"].'</a>';
			}
			else 													//���� �������������������� ����
			{
				$format_stroki_user_profile=$row["last_poster_name"];
			}

			$replycount=$row["posts"];
			$views=$row["views"];

			//������ ���������� ���������.
			$zagolovok=preg_replace('/{Full_title}/',$title1, $format_stroki);
			$zagolovok=preg_replace('/{URL_to_post}/',$ipb_site.'/index.php?showtopic='.$row["tid"].'&view=getlastpost', $zagolovok);
			$zagolovok=preg_replace('/{Short_title}/',$title2, $zagolovok);
			$zagolovok=preg_replace('/{User_name}/',$format_stroki_user_profile, $zagolovok);
			$zagolovok=preg_replace('/{Date}/',$day, $zagolovok);
			$zagolovok=preg_replace('/{ReplyCount}/',$replycount, $zagolovok);
			$zagolovok=preg_replace('/{Views}/',$views, $zagolovok);

			$asdaa.=$zagolovok;
	}
	mysql_free_result($sql_12354);
	return iconv("UTF-8", "Windows-1251" ,$asdaa);
}
$ntime =  @filemtime("_dat/cach/lastinforum");
if(!$ntime || (time()-$ntime >3600)) //��� � ��� ��������� ������ � ������
{
 ob_start();
 echo lasttopicipb();
 $temp = ob_get_contents();
 write_catch ("_dat/cach/lastinforum",$temp);
 ob_end_clean();
} else $temp = file_get_contents ('_dat/cach/lastinforum');
