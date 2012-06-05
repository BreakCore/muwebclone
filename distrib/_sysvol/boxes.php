<?php

/**
* стройка выпадаюзего списка
* @name - название элемента
* @parameters array - [id][val]
* @cssclass - ксс класс для элемента
* @selid - отметить выбранный элемент
* @empty - true/false показывает или не показывает пустую строчку
* @onchange - скрипт жс эвент "при изменении"
* возвращает строку с HTML кодом
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
