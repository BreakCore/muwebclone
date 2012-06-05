<?php

/**
* ������� ����������� ������
* @name - �������� ��������
* @parameters array - [id][val]
* @cssclass - ��� ����� ��� ��������
* @selid - �������� ��������� �������
* @empty - true/false ���������� ��� �� ���������� ������ �������
* @onchange - ������ �� ����� "��� ���������"
* ���������� ������ � HTML �����
**/
function build_box($name,$parameters,$cssclass,$selid=-1,$empty=false,$onchange="")
{
 $output = "<select class='".$cssclass."' name='".$name."' id='".$name."' Onchange='".$onchange."'>";
 if ($empty==true)$output.= "<option value='-1'>None</option>";
 foreach ($parameters as $id=>$val)
 {
  $output.= "<option value='".$id."'";
  if ($id==$selid) $output.= " selected ";
  $output.= ">".$val."</option>";
 }
 
 $output.= "</select>";
 return $output;
}
