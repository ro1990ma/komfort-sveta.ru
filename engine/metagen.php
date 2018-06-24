<?php
/*
=====================================================
 n0wheremany
-----------------------------------------------------
 http://nowheredev.ru/
-----------------------------------------------------
 Copyright (c) 2013 n0wheremany
=====================================================
 Данный код защищен авторскими правами
=====================================================
 Файл: metagen.php
-----------------------------------------------------
 Назначение: Скачивание файлов
=====================================================
*/

global $metaGen;

if(!is_array($metaGen)) $metaGen = array();

if($a='add' and isset($r) and isset($p) and isset($v)){
	$v = preg_replace('#[\\\\]+"#','"',$v);
	$metaGen[$r][$p] = $v;
} else
if($a='get' and isset($t)){
	$return = '';
	$t = preg_replace('#[\\\\]+"#','"',$t);
	foreach($metaGen as $rr => $dd)
		if((isset($r) and $r==$rr) or !isset($r))
		foreach($dd as $pp => $vv){
			$return .= str_replace(array(
				'{r}','{p}','{v}'
			),array(
				$rr,$pp,$vv
			),$t);
		}
	echo $return;
}

?>