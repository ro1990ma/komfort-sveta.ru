<?php

if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
  die("Hacking attempt!");
}

if($member_id['user_group'] != 1) {

	msg("error", $lang['index_denied'], $lang['index_denied']);

}

require_once (ENGINE_DIR . '/data/ufmoon_options.php');

function showRow($title = "", $description = "", $field = "", $class = "") {
	echo "<tr>
       <td class=\"col-xs-6 col-sm-6 col-md-7 {$class}\"><h6 class=\"media-heading text-semibold\">{$title}</h6><span class=\"text-muted text-size-small hidden-xs\">{$description}</span></td>
       <td class=\"col-xs-6 col-sm-6 col-md-5 settingstd {$class}\">{$field}</td>
       </tr>";
}
	
function makeDropDown($options, $name, $selected) {
	$output = "<select class=\"uniform\" style=\"min-width:100px;\" name=\"$name\">\r\n";
	foreach ( $options as $value => $description ) {
		$output .= "<option value=\"$value\"";
		if( $selected == $value ) {
			$output .= " selected ";
		}
		$output .= ">$description</option>\n";
	}
	$output .= "</select>";
	return $output;
}

function makeCheckBox($name, $selected) {
	$selected = $selected ? "checked" : "";

	return "<input class=\"switch\" type=\"checkbox\" name=\"$name\" value=\"1\" {$selected}>";
}

if( $action == "save" ) {

	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}

	$save_con = array();
	$save_con = $_POST['save_con'];

	$save_con['up_date'] = intval($save_con['up_date']);
	$save_con['cam_pars'] = intval($save_con['cam_pars']);
	$save_con['allow_module_on'] = intval($save_con['allow_module_on']);
	$save_con['big_qual'] = intval($save_con['big_qual']);
	$save_con['ufm_size'] = intval($save_con['ufm_size']);
	$find = array();
	$replace = array();
	
	$find[] = "'\r'";
	$replace[] = "";
	$find[] = "'\n'";
	$replace[] = "";
	
	$save_con = $save_con + $ufMoonOptions;
	
	$handler = fopen( ENGINE_DIR . '/data/ufmoon_options.php', "w+" );

	fwrite( $handler, "<?PHP \n\n//ufMoon Configurations\n\n\$ufMoonOptions = array (\n\n" );
	foreach ( $save_con as $name => $value ) {

		fwrite( $handler, "'{$name}' => '{$value}',\n\n" );
	
	}
	fwrite( $handler, ");\n\n?>" );
	fclose( $handler );
	
	clear_cache();
	msg( "info", $lang['opt_sysok'], $lang['opt_sysok_1'], "$PHP_SELF?mod=ufmoon" );


}

	echoheader( "<i class=\"fa fa-play\"></i>"."<span class=\"text-semibold\">ufMoon v.1.9.2.12 от 13.09.2017</span>", "Автор: <b>Sistemos.ru</b>. E-mail <b>sistemos-art@yandex.ru</b>. Telegram: <b>@Sistemos</b>" );

echo <<<HTML
<form action="$PHP_SELF?mod=ufmoon&action=save" name="conf" id="conf" method="post">
<div class="panel panel-default">
  <div class="panel-body">
    <div class="title">ufMoon</div>
  </div>
  <div class="table-responsive">
  <table class="table table-striped">
HTML;

$cat_treiler = CategoryNewsSelection($ufMoonOptions['cat_treiler']);
$cat_cam = CategoryNewsSelection($ufMoonOptions['cat_cam']);
$cat_hd = CategoryNewsSelection($ufMoonOptions['cat_hd']);

	showRow( 'Включить модуль?', 'Удобно выключить модуль, если мунвалк недоступен (просто не будут выполняться запросы по api к мунвалку, и это никак не повляет на работоспособность сайта)', makeCheckBox( "save_con[allow_module_on]", "{$ufMoonOptions['allow_module_on']}" ) );

	showRow( 'API token', 'Ваш API token, можно посмотреть в настройках личного кабинета, http://moonwalk.cc/partners/edit_profile', "<input type=text class=\"form-control width-550 position-left\" name=\"save_con[api_token]\" value=\"{$ufMoonOptions['api_token']}\" >");
	showRow( 'Куда выводить url плеера?', 'Название дополнительного поля для ссылки плеера moonwalk, только само название, без xfvalue, например если это поле [xfvalue_hdlight], то прописать надо hdlight', "<input type=text class=\"form-control width-550 position-left\" name=\"save_con[dp_player]\" value=\"{$ufMoonOptions['dp_player']}\" >");
	showRow( 'Куда выводить значение качества?', 'Название дополнительного поля, в которое будет выводиться информация о качестве', "<input type=text class=\"form-control width-550 position-left\" name=\"save_con[dp_kach]\" value=\"{$ufMoonOptions['dp_kach']}\" >");
	showRow( 'Обозначение Трейлера', 'Обозначение Трейлера в дополнительном поле (регистрозависимое).<br>У фильма с таким Значением в дальнейшем будет заменено качество на CAMrip или HDrip.', "<input type=text class=\"form-control width-550 position-left\" name=\"save_con[kash_treiler]\" value=\"{$ufMoonOptions['kash_treiler']}\" >");
	showRow( 'Обозначение HD качества', 'Обозначение HD качества в дополнительном поле (регистрозависимое)', "<input type=text class=\"form-control width-550 position-left\" name=\"save_con[kash_hd]\" value=\"{$ufMoonOptions['kash_hd']}\" >");
	showRow( 'Обозначение CAM качества', 'Обозначение CAMrip качества в дополнительном поле (регистрозависимое)', "<input type=text class=\"form-control width-550 position-left\" name=\"save_con[kash_cam]\" value=\"{$ufMoonOptions['kash_cam']}\" >");
	showRow( 'Обозначение TS качества', 'Обозначение TS качества в дополнительном поле (регистрозависимое)', "<input type=text class=\"form-control width-550 position-left\" name=\"save_con[kash_ts]\" value=\"{$ufMoonOptions['kash_ts']}\" >");	
	showRow( 'Обозначение DVD качества', 'Обозначение DVD качества в дополнительном поле (регистрозависимое)', "<input type=text class=\"form-control width-550 position-left\" name=\"save_con[kash_dvd]\" value=\"{$ufMoonOptions['kash_dvd']}\" >");
	
	showRow( 'Как часто проверять', 'Периодичность проверки изменения качества фильмов на moonwalk (в секундах; 21600 - 6 часов)', "<input type=text class=\"form-control width-550 position-left\" name=\"save_con[time_cash]\" value=\"{$ufMoonOptions['time_cash']}\" >");	
	showRow( 'Поднимать фильм?', 'Если включено, модуль будет обновлять дату фильма на текущую, если обновилось качество', makeCheckBox( "save_con[up_date]", "{$ufMoonOptions['up_date']}" ) );
	showRow( 'Какие Значения в доп.поле игнорировать?', 'Если перечислить здесь Значения, <b>через запятую</b>, используемые в доп.поле для качества, то модуль будет игнорировать новости с выбранными Значениями.<br>Оставьте поле пустым, если не требуется.', "<input type=text class=\"form-control width-550 position-left\" name=\"save_con[dop_ignore]\" value=\"{$ufMoonOptions['dop_ignore']}\" >");			
	showRow( 'Категория с Трейлерами', 'Если выбрать, то <b>фильм будет удален и этой категории</b>, когда появится CAMrip или HD качество. <br>Если не надо, то оставить пустым.', "<select class=\"uniform\" name=\"save_con[cat_treiler]\" >{$cat_treiler}</select>");
	showRow( 'Категория с CAM-TS-DVD-новинками', 'Если выбрать, то фильм будет добавлен в эту категорию, когда появится CAMrip качество, <br>и будет удален из неё, когда появится HD качество. <br>Если не надо, то оставить пустым.', "<select class=\"uniform\" name=\"save_con[cat_cam]\" >{$cat_cam}</select>" );
	showRow( 'Категория с HD-новинками', 'Если выбрать, то фильм будет добавлен в эту категорию, когда появится HD качество. <br>Если не надо, то оставить пустым.', "<select class=\"uniform\" name=\"save_con[cat_hd]\" >{$cat_hd}</select>" );	
	showRow( 'Куда выводить информации о переводе?', 'Название дополнительного поля для информации о переводе. <br>Если это поле [xfvalue_sound], то прописать надо sound. <br>Если не используется на сайте, то оставить пустым.', "<input type=text class=\"form-control width-550 position-left\" name=\"save_con[dp_audio]\" value=\"{$ufMoonOptions['dp_audio']}\" >");
	showRow( 'Что показывать, если перевод n/a ?', 'Значение по-умолчанию, если нет информации о переводе. <br>Можно оставить пустым, в таком случае доп.поле для перевода заполняться не будет.', "<input type=text class=\"form-control width-550 position-left\" name=\"save_con[na_audio]\" value=\"{$ufMoonOptions['na_audio']}\" >");
	showRow( 'Автозамена стандартных названий озвучек', 'Если вам не нравятся некоторые стандартные названия озвучек, то можно здесь задать автозамену.<br> Если в поле вписать <b>den904=Одноголосый,Дубляж=Дублированный</b>, <br>то вместо <b>den904</b> будет вставляться <b>Одноголосый</b>, а вместо <b>Дубляж</b> вставится <b>Дублированный</b>. <br>Если вас устраивают стандартные названия озвучек, то оставьте поле пустым.', "<input type=text class=\"form-control width-550 position-left\" name=\"save_con[audio_replace]\" value=\"{$ufMoonOptions['audio_replace']}\" >");
	showRow( 'Включить дополнительную перепроверку CAMrip и TS по названию файлов?', 'Для значений CAMrip и TS будет дополнительно запускаться проверка (парсинг) названий файлов на Moonwalk-e, что улучшит проставновку значения HD качества для российских фильмов и значений TS и DVDRip качества для зарубежных. Эффективна не на 100%, а лишь улучшит ситуацию.', makeCheckBox( "save_con[cam_pars]", "{$ufMoonOptions['cam_pars']}" ) );
	showRow( 'Включить простановку BDRip, WEB-DL и т.д.?', '<b>Перед тем как включать</b>, нужно добавить в доп.поле качество, в список, эти значения: <br>WEB-DL<br>BDRip<br>BluRay<br>HDTV<br>DVDScr<br>WEBRip<br>DVDRip (если не было).', makeCheckBox( "save_con[big_qual]", "{$ufMoonOptions['big_qual']}" ) );
	showRow( 'Включить простановку 720 и 1080 в поле <b>ufm_size</b>?', '<b>Перед тем как включать</b>, нужно создать доп.поле <b>ufm_size</b>. В него модуль будет проставлять 720 или 1080. Инфа берется из названия файла и может отличаться от того, что доступно в плеере.', makeCheckBox( "save_con[ufm_size]", "{$ufMoonOptions['ufm_size']}" ) );
	showRow( 'Доп.настройка для HD фильмов и озвучек', 'Продвинутая настройка доп.обновления для HD фильмов и озвучек. Оставьте это поле пустым, если сомневаетесь. Как её использвать написано в отдельной инструкции. <br>Пример: <b>BDRip,WEB-DL|Дубляж|1|2<b>', "<input type=text class=\"form-control width-550 position-left\" name=\"save_con[long_conf]\" value=\"{$ufMoonOptions['long_conf']}\" >");
	
	echo <<<HTML
</table></div></div>
<div style="margin-bottom:30px;">
<input type="hidden" name="user_hash" value="{$dle_login_hash}" />
<button type="submit" class="btn bg-teal btn-raised position-left legitRipple"><i class="fa fa-floppy-o position-left"></i>{$lang['user_save']}</button>
</div>
</form>
HTML;


	if(!is_writable(ENGINE_DIR . '/data/ufmoon_options.php')) {

		$lang['stat_system'] = str_replace ("{file}", "engine/data/ufmoon_options.php", $lang['stat_system']);

		echo "<div class=\"alert alert-error\">{$lang['stat_system']}</div>";

	}

echofooter();
?>