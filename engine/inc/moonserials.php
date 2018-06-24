<?php
/**
 * MoonSerials
 * =======================================================
 * Автор:	kild
 * =======================================================
 * Файл:  moonserials.php
 * -------------------------------------------------------
 * Версия: 1.4.5(8.05.2016)
 * =======================================================
 */
if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
  die("Hacking attempt!");
}

if($member_id['user_group'] != 1) {

	msg("error", $lang['index_denied'], $lang['index_denied']);

}

require_once (ENGINE_DIR . '/data/moonserials_options.php');

	// Мыссивы доступных полей
	$fields = array(
		'kinopoisk' => array(
			'' => '----',
		),
		'status' => array(
			'' => '----',
		),
		'season' => array(
			'' => '----',
		),
		'series' => array(
			'' => '----',
		),
		'studios' => array(
			'' => '----',
		),
		'studios_all' => array(
			'' => '----',
		),
		'season_in' => array(
			'' => '----',
		),
		'series_in' => array(
			'' => '----',
		),
		'title_year' => array(
			'' => '----',
		),
		'title_in' => array(
			'' => '----',
		),
		'title2_year' => array(
			'' => '----',
		),
		'title2_in' => array(
			'' => '----',
		),
		'cpu_year' => array(
			'' => '----',
		),
		'cpu_in' => array(
			'' => '----',
		),
		'title_ru' => array(
			'' => '----',
		),
		'field_series-max' => array(
			'' => '----',
		),
		'season_mod' => array(
			'' => '----',
		),
		'series_mod' => array(
			'' => '----',
		),
	);

		$xfields = xfieldsload();
		if ($xfields) foreach ($xfields as $key => $value) {
			$fields['kinopoisk']["{$value[0]}"] = " {$value[1]} ";
			$fields['status']["{$value[0]}"] = " {$value[1]} ";
            $fields['season']["{$value[0]}"] = " {$value[1]} ";
            $fields['series']["{$value[0]}"] = " {$value[1]} ";
            $fields['studios']["{$value[0]}"] = " {$value[1]} ";
            $fields['studios_all']["{$value[0]}"] = " {$value[1]} ";
            $fields['season_in']["{$value[0]}"] = " {$value[1]} ";
            $fields['series_in']["{$value[0]}"] = " {$value[1]} ";
            $fields['title_year']["{$value[0]}"] = " {$value[1]} ";
            $fields['title_in']["{$value[0]}"] = " {$value[1]} ";
            $fields['title2_year']["{$value[0]}"] = " {$value[1]} ";
            $fields['title2_in']["{$value[0]}"] = " {$value[1]} ";
            $fields['cpu_year']["{$value[0]}"] = " {$value[1]} ";
            $fields['cpu_in']["{$value[0]}"] = " {$value[1]} ";
            $fields['title_ru']["{$value[0]}"] = " {$value[1]} ";
            $fields['field_series-max']["{$value[0]}"] = " {$value[1]} ";
            $fields['season_mod']["{$value[0]}"] = " {$value[1]} ";
            $fields['series_mod']["{$value[0]}"] = " {$value[1]} ";
		}




	function showRow($title = "", $description = "", $field = "", $class = "") {


		echo "<tr>
        <td class=\"col-xs-6 col-sm-6 col-md-7\"><h6 class=\"media-heading text-semibold\">{$title}</h6><span class=\"text-muted text-size-small hidden-xs\">{$description}</span></td>
        <td class=\"col-xs-6 col-sm-6 col-md-5\">{$field}</td>
        </tr>";
	}

	function makeDropDown($options, $name, $selected) {
		$output = "<select class=\"uniform\" name=\"$name\">\r\n";
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

		return "<input class=\"switch\" type=\"checkbox\" name=\"{$name}\" value=\"1\" {$selected}>";

	}

if( $action == "save" ) {

	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {

		die( "Hacking attempt! User not found" );

	}

	$save_con = $_POST['save_con'];
	$save_con['allow_news_update'] = intval($save_con['allow_news_update']);
	$save_con['allow_module_on'] = intval($save_con['allow_module_on']);
    $save_con['allow_module_new'] = intval($save_con['allow_module_new']);
    $save_con['allow_module_new_season'] = intval($save_con['allow_module_new_season']);
    $save_con['allow_module_new_series'] = intval($save_con['allow_module_new_series']);
    $save_con['allow_fields_spy'] = intval($save_con['allow_fields_spy']);
    $save_con['disable_sub'] = intval($save_con['disable_sub']);
    $save_con['add_series_one'] = intval($save_con['add_series_one']);
    $save_con['add_series_one_tpl'] = intval($save_con['add_series_one_tpl']);
    $save_con['allow_news_title_update'] = intval($save_con['allow_news_title_update']);
    $save_con['ms_title_season'] = intval($save_con['ms_title_season']);
    $save_con['ms_title_season_one'] = intval($save_con['ms_title_season_one']);
    $save_con['ms_title_series'] = intval($save_con['ms_title_series']);
    $save_con['ms_title_series_add'] = intval($save_con['ms_title_series_add']);
    $save_con['allow_news_title2_update'] = intval($save_con['allow_news_title2_update']);
    $save_con['ms_title2_season'] = intval($save_con['ms_title2_season']);
    $save_con['ms_title2_season_one'] = intval($save_con['ms_title2_season_one']);
    $save_con['ms_title2_series'] = intval($save_con['ms_title2_series']);
    $save_con['ms_title2_series_add'] = intval($save_con['ms_title2_series_add']);
    $save_con['allow_news_cpu_update'] = intval($save_con['allow_news_cpu_update']);
    $save_con['ms_cpu_season'] = intval($save_con['ms_cpu_season']);
    $save_con['ms_cpu_season_one'] = intval($save_con['ms_cpu_season_one']);
    $save_con['ms_cpu_series'] = intval($save_con['ms_cpu_series']);
    $save_con['ms_cpu_series_add'] = intval($save_con['ms_cpu_series_add']);
    $save_con['allow_module_new_series_max'] = intval($save_con['allow_module_new_series_max']);
    $save_con['sendpm'] = intval($save_con['sendpm']);
    $save_con['sendpm'] = intval($save_con['sendpm']);
	$find = array();
	$replace = array();

	$find[] = "'\r'";
	$replace[] = "";
	$find[] = "'\n'";
	$replace[] = "";

	$save_con = $save_con + $moonserials_options;

	$handler = fopen( ENGINE_DIR . '/data/moonserials_options.php', "w+" );

	fwrite( $handler, "<?PHP \n\n//Moonserials Configurations\n\n\$moonserials_options = array (\n\n" );
	foreach ( $save_con as $name => $value ) {

		$value = trim(strip_tags(stripslashes( $value )));
		$value = htmlspecialchars( $value, ENT_QUOTES, $config['charset']);
		$value = preg_replace( $find, $replace, $value );

		$name = trim(strip_tags(stripslashes( $name )));
		$name = htmlspecialchars( $name, ENT_QUOTES, $config['charset'] );
		$name = preg_replace( $find, $replace, $name );

		$value = str_replace( "$", "&#036;", $value );
		$value = str_replace( "{", "&#123;", $value );
		$value = str_replace( "}", "&#125;", $value );
		$value = str_replace( chr(92), "", $value );
		$value = str_replace( chr(0), "", $value );
		$value = str_replace( '(', "", $value );
		$value = str_replace( ')', "", $value );
		$value = str_ireplace( "base64_decode", "base64_dec&#111;de", $value );

		$name = str_replace( "$", "&#036;", $name );
		$name = str_replace( "{", "&#123;", $name );
		$name = str_replace( "}", "&#125;", $name );
		$name = str_replace( ".", "", $name );
		$name = str_replace( '/', "", $name );
		$name = str_replace( chr(92), "", $name );
		$name = str_replace( chr(0), "", $name );
		$name = str_replace( '(', "", $name );
		$name = str_replace( ')', "", $name );
		$name = str_ireplace( "base64_decode", "base64_dec&#111;de", $name );

		fwrite( $handler, "'{$name}' => '{$value}',\n\n" );

	}
	fwrite( $handler, ");\n\n?>" );
	fclose( $handler );

	clear_cache();
	msg( "info", $lang['opt_sysok'], $lang['opt_sysok_1'], "$PHP_SELF?mod=moonserials" );
}

    echoheader( "<i class=\"fa fa-play position-left\"></i><span class=\"text-semibold\">MoonSerials v. 1.4.5 от 08.05.2016</span>", "Автор: <a href=\"http://kild.me/\">kild</a>" );
echo <<<HTML
<script type="text/javascript">
	$(function(){
		$('[data-toggle="tab"]').on('shown.bs.tab', function(e) {
		  var id;
		  id = $(e.target).attr("href");
		  $(id).find(".cat_select").chosen({allow_single_deselect:true, no_results_text: 'Ничего не найдено'});
		});
	});
</script>
 <script language='JavaScript' type="text/javascript">
        function ChangeOption(selectedOption) {
                document.getElementById('general').style.display = "none";
                document.getElementById('title').style.display = "none";
                document.getElementById('title2').style.display = "none";
                document.getElementById('chpu').style.display = "none";
                document.getElementById('block').style.display = "none";
                document.getElementById(selectedOption).style.display = "";
       }
$(document).ready(function(){
           $("#showHideContent").click(function () {
                       if ($("#content_help").is(":hidden")) {
                               $("#content_help").show("slow");
                       } else {
                               $("#content_help").hide("slow");
                       }
 return false;
});
});
</script>


<!-- Toolbar -->
<div class="navbar navbar-default navbar-component navbar-xs systemsettings">
	<ul class="nav navbar-nav visible-xs-block">
		<li class="full-width text-center"><a data-toggle="collapse" data-target="#navbar-filter"><i class="fa fa-bars"></i></a></li>
	</ul>
	<div class="navbar-collapse collapse" id="navbar-filter">
		<ul class="nav navbar-nav">
		 <li style="min-width:90px;"><a href="javascript:ChangeOption('general');" class="tip" title="Общие настройки модуля"><i class="fa fa-cog"></i><span> Общие</span></a></li>
		 <li style="min-width:90px;"><a href="javascript:ChangeOption('title');" class="tip" title="Настройки тайтлов"><i class="fa fa-retweet"></i><span> Тайтл</span></a></li>
         <li style="min-width:90px;"><a href="javascript:ChangeOption('title2');" class="tip" title="Настройки заголовков"><i class="fa fa-text-width"></i><span> Заголовок</span></a></li>
		 <li style="min-width:90px;"><a href="javascript:ChangeOption('chpu');" class="tip" title="Настройки ЧПУ"><i class="fa fa-link"></i><span> ЧПУ</span></a></li>
		 <li style="min-width:90px;"><a href="javascript:ChangeOption('block');" class="tip" title="Настройки вывода блока на главной"><i class="fa fa-th-large"></i><span> Блок</span></a></li>
		</ul>
	</div>
</div>
<!-- /toolbar -->






<form action="$PHP_SELF?mod=moonserials&action=save" name="conf" id="conf" method="post" class="systemsettings">
HTML;

	if ( $moonserials_options['field_season'] == $moonserials_options['field_season_iframe'] OR $moonserials_options['field_season'] == $moonserials_options['season_mod'] )
		echo "<div class=\"alert alert-danger alert-styled-left alert-arrow-left alert-component\">Поля Куда выводить сезон и Откуда брать сезон не должны совпадать!</div>";
	if ( $moonserials_options['field_series'] == $moonserials_options['field_series_iframe'] OR $moonserials_options['field_series'] == $moonserials_options['series_mod'] )
		echo "<div class=\"alert alert-danger alert-styled-left alert-arrow-left alert-component\">Поля Куда выводить серию и Откуда брать серию не должны совпадать!</div>";
    if ( !$moonserials_options['field_studios'] )
        echo "<div class=\"alert alert-danger alert-styled-left alert-arrow-left alert-component\">Заполните поле Куда выводить студию!</div>";


echo <<<HTML
<div id="general" class="panel panel-flat">
  <div class="panel-body">
    MoonSerials - общие настройки
  </div>
  <div class="table-responsive">
  <table class="table table-striped">
HTML;
	showRow( 'Включить модуль?', 'Бывает полезно, когда мунвалк лежит', makeCheckBox( "save_con[allow_module_on]", "{$moonserials_options['allow_module_on']}" ) );
    showRow( 'Выводить плееры?', 'Если включено, модуль надо подключать вместо плеера, и он будет выводить плееры в вкладках с озвучками<br>Если выключено, модуль будет выводить только текстовую информацию - номер сезона и серии, и подключать его надо там, где хотите эту информацию вывести', makeCheckBox( "save_con[allow_module_new]", "{$moonserials_options['allow_module_new']}" ) );
    showRow( 'Отключить вывод перевода "Субтитры"?', 'Если включено, этот вид перевода во вкладках показывать не будет', makeCheckBox( "save_con[disable_sub]", "{$moonserials_options['disable_sub']}" ) );
    showRow( 'Выводить сезон?', 'Если включено, модуль будет в полной новости выводить сезон<br><b>Работает, только если выключен вывод плееров</b>', makeCheckBox( "save_con[allow_module_new_season]", "{$moonserials_options['allow_module_new_season']}" ) );
    showRow( 'Выводить серию?', 'Если включено, модуль будет в полной новости выводить серию<br><b>Работает, только если выключен вывод плееров</b>', makeCheckBox( "save_con[allow_module_new_series]", "{$moonserials_options['allow_module_new_series']}" ) );
    showRow( 'Определять количество серий в сезоне?', 'Если включено, модуль будет делать запрос к АПИ Кинопоиск и определять количество серий в сезоне<br><b>Работает, только если идет вывод по сезонам или по сериям</b><br><b>Запрос делается только один раз, потом результат записывается в поле и больше запросов не будет</b>', makeCheckBox( "save_con[allow_module_new_series_max]", "{$moonserials_options['allow_module_new_series_max']}" ) );
	showRow( 'API token', 'Ваш API token, можно посмотреть в настройках личного кабинета, http://moonwalk.cc/partners/edit_profile', "<input class=\"form-control\" type=text style=\"width:100%;\" name=\"save_con[api_token]\" value=\"{$moonserials_options['api_token']}\" >");
    showRow( 'Название дополнительного поля ID фильма кинопоиск:', 'Название дополнительного поля, из которого модуль будет брать ID кинопоиска', makeDropDown( ( $fields['kinopoisk'] ), "save_con[field_kpid]", "{$moonserials_options['field_kpid']}" ) );
    showRow( 'Название дополнительного поля Статус сериала:', 'Название дополнительного поля, из которого модуль будет брать статус сериала', makeDropDown( ( $fields['status'] ), "save_con[field_status_name]", "{$moonserials_options['field_status_name']}" ) );
	showRow( 'Куда выводить сезон', 'Название дополнительного поля, в которое будет выводится номер сезона', makeDropDown( ( $fields['season'] ), "save_con[field_season]", "{$moonserials_options['field_season']}" ) );
	showRow( 'Куда выводить форматированный сезон', 'Название дополнительного поля, в которое будет выводится форматированный номер сезона', makeDropDown( ( $fields['season_mod'] ), "save_con[season_mod]", "{$moonserials_options['season_mod']}" ) );
	showRow( 'Формат вывода сезона', 'Выберите в каком именно формате выводить сезон в поле', makeDropDown( array ("type1" => '1 сезон, 2 сезон, 3 сезон', "type2" => '1 сезон, 1-2 сезон, 1-3 сезон', "type3" => '1 сезон, 1,2 сезон, 1,2,3 сезон'), "save_con[field_season_form]", "{$moonserials_options['field_season_form']}" ) );
    showRow( 'Куда выводить серию', 'Название дополнительного поля, в которое будет выводится номер серии', makeDropDown( ( $fields['series'] ), "save_con[field_series]", "{$moonserials_options['field_series']}" ) );
    showRow( 'Куда выводить форматированную серию', 'Название дополнительного поля, в которое будет выводится форматированный номер серии', makeDropDown( ( $fields['series_mod'] ), "save_con[series_mod]", "{$moonserials_options['series_mod']}" ) );
	showRow( 'Формат вывода серии', 'Выберите в каком именно формате выводить серию в поле', makeDropDown( array ("type1" => '1 серия, 2 серия, 3 серия', "type2" => '1 серия, 1-2 серия, 1-3 серия, 1-4 серия', "type3" => '1 серия, 1,2 серия, 1,2,3 серия, 1,2,3,4 серия', "type4" => '1 серия, 1,2 серия, 1,2,3 серия, 2,3,4 серия', "type5" => '1,2 серия, 1,2,3 серия, 1,2,3,4,5 серия, 1-5,6,7 серия'), "save_con[field_series_form]", "{$moonserials_options['field_series_form']}" ) );
    showRow( 'Куда выводить количество серий в сезоне', 'Название дополнительного поля, в которое будет выводится количество серий в сезоне', makeDropDown( ( $fields['field_series-max'] ), "save_con[field_series-max]", "{$moonserials_options['field_series-max']}" ) );
    showRow( 'Добавлять к серии +1 в поле?', 'Если включено, значение серии в дополнительном поле будет увеличено на 1', makeCheckBox( "save_con[add_series_one]", "{$moonserials_options['add_series_one']}" ) );
    showRow( 'Добавлять к серии +1 в шаблоне?', 'Если включено, значение серии в шаблоне в полной новости будет увеличено на 1', makeCheckBox( "save_con[add_series_one_tpl]", "{$moonserials_options['add_series_one_tpl']}" ) );
    showRow( 'Куда выводить студию', 'Название дополнительного поля, в которое будет выводится студия, которая озвучила сериал', makeDropDown( ( $fields['studios'] ), "save_con[field_studios]", "{$moonserials_options['field_studios']}" ) );
    showRow( 'Куда выводить все студии', 'Название дополнительного поля, в которое будет выводится список доступных озвучек', makeDropDown( ( $fields['studios_all'] ), "save_con[field_studios_sp]", "{$moonserials_options['field_studios_sp']}" ) );
    showRow( 'Следить за полями если сериал закончен?', 'Если включено, модуль будет чистить поле серия и заполнять поле сезон в случае, если сериал закончен. <br>Если выключено, в случае, если сериал закончен, вы сможете в эти поля писать что угодно и модуль их трогать не будет', makeCheckBox( "save_con[allow_fields_spy]", "{$moonserials_options['allow_fields_spy']}" ) );
	showRow( 'Откуда брать сезон', 'Название дополнительного поля, из которого модуль будет брать номер сезона, для случаев, когда в новости надо вывести только конкретный сезон<br><b>Можно оставить пустым</b><br><span style="color: #CC0000"><b>НЕ ДОЛЖНО СОВПАДАТЬ С ПОЛЕМ КУДА ВЫВОДИТЬ СЕЗОН</b></span>', makeDropDown( ( $fields['season_in'] ), "save_con[field_season_iframe]", "{$moonserials_options['field_season_iframe']}" ) );
    showRow( 'Откуда брать серию', 'Название дополнительного поля, из которого модуль будет брать номер серии, для случаев, когда в новости надо вывести только конкретную серию<br><b>Можно оставить пустым</b><br><span style="color: #CC0000"><b>НЕ ДОЛЖНО СОВПАДАТЬ С ПОЛЕМ КУДА ВЫВОДИТЬ СЕРИЮ</b></span>', makeDropDown( ( $fields['series_in'] ), "save_con[field_series_iframe]", "{$moonserials_options['field_series_iframe']}" ) );
	showRow( 'Статус сериала', 'Значение дополнительного поля "Статус сериала" в случае, если сериал закончен', "<input class=\"form-control\" type=text style=\"width:100%;\" name=\"save_con[field_status]\" value=\"{$moonserials_options['field_status']}\" >");
	showRow( 'Если сериал закончен', 'Текст, который модуль будет выводить, если сериал закончен', "<input class=\"form-control\" type=text style=\"width:100%;\" name=\"save_con[if_series_ower]\" value=\"{$moonserials_options['if_series_ower']}\" >");
	showRow( 'Если есть пилотная', 'Текст, который модуль будет выводить, если в сезоне есть пилотная серия', "<input class=\"form-control\" type=text style=\"width:100%;\" name=\"save_con[if_pilot_series]\" value=\"{$moonserials_options['if_pilot_series']}\" >");
	showRow( 'Поднимать новость?', 'Если включено, модуль будет обновлять дату новости при выходе новых серий сериалов', makeCheckBox( "save_con[allow_news_update]", "{$moonserials_options['allow_news_update']}" ) );
	showRow( 'Уведомлять о выходе новой серии?', 'Если включено, модуль будет отправлять ЛС пользователю с ID 1 о выходе новой серии', makeCheckBox( "save_con[sendpm]", "{$moonserials_options['sendpm']}" ) );
	showRow( 'Префикс кеша', 'Выберите значение префикса кеша, от этого зависит, когда будет чиститься кеш модуля.<br><b>news, rss, comm</b> - при добавлении новости или комментария.<br><b>news, related, tagscloud, archives, calendar, topnews, rss</b> - при добавлении новости.<br><b>comm</b> - при редактировании комментария.<br><b>news, rss</b> - при редактировании новости, при выcтавлении рейтинга<br><b>news, full, comm, rss</b> - при массовом удалении комментариев<br><b>news, full, comm, tagscloud, archives, calendar, rss</b> - при удалении новости', makeDropDown( array ("archives" => archives, "news" => news, "rss" => rss, "comm" => comm, "related" => related, "tagscloud" => tagscloud, "calendar" => calendar, "topnews" => topnews, "full" => full ), "save_con[cashe_prefix_dle]", "{$moonserials_options['cashe_prefix_dle']}" ) );
echo <<<HTML
</table></div></div>
HTML;

echo <<<HTML
<div id="title" class="panel panel-flat" style="display:none">
  <div class="panel-body">
    MoonSerials - настройки title
  </div>
  <div class="table-responsive">
  <table class="table table-striped">
HTML;
	showRow( 'Менять тайтлы?', 'Если включено, модуль будет обновлять тайтл новости при выходе новых серий сериалов<br><br><b>Структура тайтла:</b><br>1. Префикс тайтла (текст перед названием сериала)<br>2. Название сериала (берется из ответа мунвалка, т.к. у каждого по разному)<br>3. Год (из дополнительного поля)<br>4. Текст после года<br>5. Сезон<br>6. Серия<br>7. Произвольный текст<br>8. Дата<br><br>Например, для сериала "Сонная Лощина" можно слепить такую конструкцию:<br>(1)Сериал (2)Сонная Лощина (3)2013 (4)смотреть онлайн (5)3 сезон (6)7 серия (7)все серии подряд на русском языке - (8)16 ноября 2015<br>и если, например, 20 ноября выйдет 8 серия, тайтл автоматически изменится на:<br>Сериал Сонная Лощина 2013 смотреть онлайн 3 сезон 8 серия все серии подряд на русском языке - 20 ноября 2015<br><br><b><span style="color: #4C73CC">Внимание! Эта функция добавлена по просьбе отдельных людей, и я, как автор модуля, не хочу и не буду отвечать за результаты ее использования.</span></b>', makeCheckBox( "save_con[allow_news_title_update]", "{$moonserials_options['allow_news_title_update']}" ) );
	showRow( 'Добавлять сезон?', 'Если включено, модуль будет добавлять в тайтл сезон', makeCheckBox( "save_con[ms_title_season]", "{$moonserials_options['ms_title_season']}" ) );
	showRow( 'Не добавлять сезон, если сезон первый', 'Если включено, модуль будет добавлять сезон только если сезон не первый', makeCheckBox( "save_con[ms_title_season_one]", "{$moonserials_options['ms_title_season_one']}" ) );
	showRow( 'Формат вывода сезона', 'Выберите в каком именно формате выводить сезон в тайтл', makeDropDown( array ("type1" => '1 сезон, 2 сезон, 3 сезон', "type2" => '1 сезон, 1-2 сезон, 1-3 сезон', "type3" => '1 сезон, 1,2 сезон, 1,2,3 сезон'), "save_con[title_season_form]", "{$moonserials_options['title_season_form']}" ) );
	showRow( 'Добавлять серию?', 'Если включено, модуль будет добавлять в тайтл серию', makeCheckBox( "save_con[ms_title_series]", "{$moonserials_options['ms_title_series']}" ) );
	showRow( 'Добавлять к серии +1?', 'Если включено, модуль будет добавлять в тайтл +1 к серии', makeCheckBox( "save_con[ms_title_series_add]", "{$moonserials_options['ms_title_series_add']}" ) );
	showRow( 'Формат вывода серии', 'Выберите в каком именно формате выводить серию в тайтл', makeDropDown( array ("type1" => '1 серия, 2 серия, 3 серия', "type2" => '1 серия, 1-2 серия, 1-3 серия, 1-4 серия', "type3" => '1 серия, 1,2 серия, 1,2,3 серия, 1,2,3,4 серия', "type4" => '1 серия, 1,2 серия, 1,2,3 серия, 2,3,4 серия', "type5" => '1,2 серия, 1,2,3 серия, 1,2,3,4,5 серия, 1-5,6,7 серия'), "save_con[title_series_form]", "{$moonserials_options['title_series_form']}" ) );
	showRow( 'Префикс тайтла', 'Текст, который модуль будет вставлять в начало тайтла<br>Если оставить пустым, будет игнорироваться', "<input class=\"form-control\" type=text style=\"width:100%;\" name=\"save_con[ms_title_preffix]\" value=\"{$moonserials_options['ms_title_preffix']}\" >");
	showRow( 'Название поля год', 'Название дополнительного поля год<br>Если оставить пустым, будет игнорироваться', makeDropDown( ( $fields['title_year'] ), "save_con[ms_title_year]", "{$moonserials_options['ms_title_year']}" ) );
	showRow( 'Текст после года', 'Текст, который модуль будет вставлять в тайтл после года<br>Если оставить пустым, будет игнорироваться', "<input class=\"form-control\" type=text style=\"width:100%;\" name=\"save_con[ms_title_t1]\" value=\"{$moonserials_options['ms_title_t1']}\" >");
	showRow( 'Произвольный текст', 'Текст, который модуль будет вставлять после серии и перед датой<br>Если оставить пустым, будет игнорироваться', "<input class=\"form-control\" type=text style=\"width:100%;\" name=\"save_con[ms_title_t2]\" value=\"{$moonserials_options['ms_title_t2']}\" >");
	showRow( 'Название поля, которое добавлять в тайтл', 'Название дополнительного поля, значение которого добавлять в тайтл<br>Если оставить пустым, будет игнорироваться', makeDropDown( ( $fields['title_in'] ), "save_con[ms_title_field]", "{$moonserials_options['ms_title_field']}" ) );
	showRow( 'Формат даты', 'Если оставить пустым, дату вставлять не будет.<br>Распознаются такие символы:<br>d - день (число) месяца, 2 цифры с ведущим нулём, если необходимо; т. е. от "01" до "31"<br>D - день недели, буквенный, 3 буквы; например, "Fri"<br>F - месяц, буквенный, long; например, "January"<br>g - час, 12-часовой формат без ведущих нулей; т.е. от "1" до "12"<br>G - час, 24-часовой формат без ведущих нулей; т.е. от "0" до "23"<br>h - час, 12-часовой формат; т.е. от "01" до "12"<br>H - час, 24-часовой формат; т.е. от "00" до "23"<br>i - минуты; т.е. от "00" до "59"<br>j - день (число) месяца без ведущих нулей; т.е. от "1" до "31"<br>l ("L" в нижнем регистре) - день недели, буквенный, long; например, "Friday"<br>m - месяц; т.е. от "01" до "12"<br>M - месяц, буквенный, 3 буквы; например, "Jan"<br>n - месяц без ведущих нулей; т.е. от "1" до "12"<br>s - секунды; т.е. от "00" до "59"<br>Y - год, 4 цифры; например, "1999"<br>y - год, 2 цифры; например, "99"', "<input type=text style=\"width:100%;\" name=\"save_con[ms_title_date]\" value=\"{$moonserials_options['ms_title_date']}\" >");
echo <<<HTML
</table></div></div>
HTML;

echo <<<HTML
<div id="title2" class="panel panel-flat" style="display:none">
  <div class="panel-body">
    MoonSerials - настройки заголовков
  </div>
  <div class="table-responsive">
  <table class="table table-striped">
HTML;
	showRow( 'Менять заголовки?', 'Если включено, модуль будет обновлять заголовок новости при выходе новых серий сериалов<br><br><b>Структура заголовка:</b><br>1. Префикс заголовка (текст перед названием сериала)<br>2. Название сериала (берется из ответа мунвалка, т.к. у каждого по разному)<br>3. Год (из дополнительного поля)<br>4. Текст после года<br>5. Сезон<br>6. Серия<br>7. Произвольный текст<br>8. Дата<br><br>Например, для сериала "Сонная Лощина" можно слепить такую конструкцию:<br>(1)Сериал (2)Сонная Лощина (3)2013 (4)смотреть онлайн (5)3 сезон (6)7 серия (7)все серии подряд на русском языке - (8)16 ноября 2015<br>и если, например, 20 ноября выйдет 8 серия, заголовок автоматически изменится на:<br>Сериал Сонная Лощина 2013 смотреть онлайн 3 сезон 8 серия все серии подряд на русском языке - 20 ноября 2015<br><br><b><span style="color: #4C73CC">Внимание! Эта функция добавлена по просьбе отдельных людей, и я, как автор модуля, не хочу и не буду отвечать за результаты ее использования.</span></b>', makeCheckBox( "save_con[allow_news_title2_update]", "{$moonserials_options['allow_news_title2_update']}" ) );
	showRow( 'Добавлять сезон?', 'Если включено, модуль будет добавлять в заголовок сезон', makeCheckBox( "save_con[ms_title2_season]", "{$moonserials_options['ms_title2_season']}" ) );
	showRow( 'Не добавлять сезон, если сезон первый', 'Если включено, модуль будет добавлять сезон только если сезон не первый', makeCheckBox( "save_con[ms_title2_season_one]", "{$moonserials_options['ms_title2_season_one']}" ) );
	showRow( 'Формат вывода сезона', 'Выберите в каком именно формате выводить сезон в заголовок', makeDropDown( array ("type1" => '1 сезон, 2 сезон, 3 сезон', "type2" => '1 сезон, 1-2 сезон, 1-3 сезон', "type3" => '1 сезон, 1,2 сезон, 1,2,3 сезон'), "save_con[title2_season_form]", "{$moonserials_options['title2_season_form']}" ) );
	showRow( 'Добавлять серию?', 'Если включено, модуль будет добавлять в заголовок серию', makeCheckBox( "save_con[ms_title2_series]", "{$moonserials_options['ms_title2_series']}" ) );
	showRow( 'Добавлять к серии +1?', 'Если включено, модуль будет добавлять в заголовок к серии +1', makeCheckBox( "save_con[ms_title2_series_add]", "{$moonserials_options['ms_title2_series_add']}" ) );
	showRow( 'Формат вывода серии', 'Выберите в каком именно формате выводить серию в заголовок', makeDropDown( array ("type1" => '1 серия, 2 серия, 3 серия', "type2" => '1 серия, 1-2 серия, 1-3 серия, 1-4 серия', "type3" => '1 серия, 1,2 серия, 1,2,3 серия, 1,2,3,4 серия', "type4" => '1 серия, 1,2 серия, 1,2,3 серия, 2,3,4 серия', "type5" => '1,2 серия, 1,2,3 серия, 1,2,3,4,5 серия, 1-5,6,7 серия'), "save_con[title2_series_form]", "{$moonserials_options['title2_series_form']}" ) );
	showRow( 'Префикс заголовка', 'Текст, который модуль будет вставлять в начало заголовка<br>Если оставить пустым, будет игнорироваться', "<input class=\"form-control\" type=text style=\"width:100%;\" name=\"save_con[ms_title2_preffix]\" value=\"{$moonserials_options['ms_title2_preffix']}\" >");
	showRow( 'Название поля год', 'Название дополнительного поля год<br>Если оставить пустым, будет игнорироваться', makeDropDown( ( $fields['title2_year'] ), "save_con[ms_title2_year]", "{$moonserials_options['ms_title2_year']}" ) );
	showRow( 'Текст после года', 'Текст, который модуль будет вставлять в заголовок после года<br>Если оставить пустым, будет игнорироваться', "<input class=\"form-control\" type=text style=\"width:100%;\" name=\"save_con[ms_title2_t1]\" value=\"{$moonserials_options['ms_title2_t1']}\" >");
	showRow( 'Произвольный текст', 'Текст, который модуль будет вставлять после серии и перед датой<br>Если оставить пустым, будет игнорироваться', "<input class=\"form-control\" type=text style=\"width:100%;\" name=\"save_con[ms_title2_t2]\" value=\"{$moonserials_options['ms_title2_t2']}\" >");
	showRow( 'Название поля, которое добавлять в заголовок', 'Название дополнительного поля, значение которого добавлять в заголовок<br>Если оставить пустым, будет игнорироваться', makeDropDown( ( $fields['title2_in'] ), "save_con[ms_title2_field]", "{$moonserials_options['ms_title2_field']}" ) );
	showRow( 'Формат даты', 'Если оставить пустым, дату вставлять не будет.<br>Распознаются такие символы:<br>d - день (число) месяца, 2 цифры с ведущим нулём, если необходимо; т. е. от "01" до "31"<br>D - день недели, буквенный, 3 буквы; например, "Fri"<br>F - месяц, буквенный, long; например, "January"<br>g - час, 12-часовой формат без ведущих нулей; т.е. от "1" до "12"<br>G - час, 24-часовой формат без ведущих нулей; т.е. от "0" до "23"<br>h - час, 12-часовой формат; т.е. от "01" до "12"<br>H - час, 24-часовой формат; т.е. от "00" до "23"<br>i - минуты; т.е. от "00" до "59"<br>j - день (число) месяца без ведущих нулей; т.е. от "1" до "31"<br>l ("L" в нижнем регистре) - день недели, буквенный, long; например, "Friday"<br>m - месяц; т.е. от "01" до "12"<br>M - месяц, буквенный, 3 буквы; например, "Jan"<br>n - месяц без ведущих нулей; т.е. от "1" до "12"<br>s - секунды; т.е. от "00" до "59"<br>Y - год, 4 цифры; например, "1999"<br>y - год, 2 цифры; например, "99"', "<input type=text style=\"width:100%;\" name=\"save_con[ms_title2_date]\" value=\"{$moonserials_options['ms_title2_date']}\" >");
echo <<<HTML
</table></div></div>
HTML;

echo <<<HTML
<div id="chpu" class="panel panel-flat" style="display:none">
  <div class="panel-body">
    MoonSerials - настройки ЧПУ
  </div>
  <div class="table-responsive">
  <table class="table table-striped">
HTML;
	showRow( 'Менять ЧПУ?', 'Если включено, модуль будет обновлять ЧПУ новости при выходе новых серий сериалов<br><br><b>Структура ЧПУ:</b><br>1. Префикс ЧПУ (текст перед названием сериала)<br>2. Название сериала (берется из ответа мунвалка, т.к. у каждого по разному)<br>3. Год (из дополнительного поля)<br>4. Текст после года<br>5. Сезон<br>6. Серия<br>7. Произвольный текст<br>8. Дата<br><br>Например, для сериала "Сонная Лощина" можно слепить такую конструкцию:<br>(1)serial-(2)sonnaya-loschina-(3)2013-(4)smotret-onlayn-(5)3-sezon-(6)7-seriya-(7)besplatno-(8)16-11-2015<br>и если, например, 20 ноября выйдет 8 серия, ЧПУ автоматически изменится на:<br>serial-sonnaya-loschina-2013-smotret-onlayn-3-sezon-8-seriya-besplatno-20-11-2015<br><br><b><span style="color: #4C73CC">Внимание! Эта функция добавлена по просьбе отдельных людей, и я, как автор модуля, не хочу и не буду отвечать за результаты ее использования.</span></b>', makeCheckBox( "save_con[allow_news_cpu_update]", "{$moonserials_options['allow_news_cpu_update']}" ) );
	showRow( 'Добавлять сезон?', 'Если включено, модуль будет добавлять в ЧПУ сезон', makeCheckBox( "save_con[ms_cpu_season]", "{$moonserials_options['ms_cpu_season']}" ) );
	showRow( 'Не добавлять сезон, если сезон первый', 'Если включено, модуль будет добавлять сезон только если сезон не первый', makeCheckBox( "save_con[ms_cpu_season_one]", "{$moonserials_options['ms_cpu_season_one']}" ) );
	showRow( 'Формат вывода сезона', 'Выберите в каком именно формате выводить сезон в ЧПУ', makeDropDown( array ("type1" => '1 сезон, 2 сезон, 3 сезон', "type2" => '1 сезон, 1-2 сезон, 1-3 сезон', "type3" => '1 сезон, 1,2 сезон, 1,2,3 сезон'), "save_con[cpu_season_form]", "{$moonserials_options['cpu_season_form']}" ) );
	showRow( 'Добавлять серию?', 'Если включено, модуль будет добавлять в ЧПУ серию', makeCheckBox( "save_con[ms_cpu_series]", "{$moonserials_options['ms_cpu_series']}" ) );
	showRow( 'Добавлять к серии +1?', 'Если включено, модуль будет добавлять в ЧПУ к серии +1', makeCheckBox( "save_con[ms_cpu_series_add]", "{$moonserials_options['ms_cpu_series_add']}" ) );
	showRow( 'Формат вывода серии', 'Выберите в каком именно формате выводить серию в ЧПУ', makeDropDown( array ("type1" => '1 серия, 2 серия, 3 серия', "type2" => '1 серия, 1-2 серия, 1-3 серия, 1-4 серия', "type3" => '1 серия, 1,2 серия, 1,2,3 серия, 1,2,3,4 серия', "type4" => '1 серия, 1,2 серия, 1,2,3 серия, 2,3,4 серия', "type5" => '1,2 серия, 1,2,3 серия, 1,2,3,4,5 серия, 1-5,6,7 серия'), "save_con[cpu_series_form]", "{$moonserials_options['cpu_series_form']}" ) );
	showRow( 'Префикс ЧПУ', 'Текст, который модуль будет вставлять в начало ЧПУ<br>Если оставить пустым, будет игнорироваться<br><b>Писать на русском, будет преобразовано в транслит</b>', "<input class=\"form-control\" type=text style=\"width:100%;\" name=\"save_con[ms_cpu_preffix]\" value=\"{$moonserials_options['ms_cpu_preffix']}\" >");
	showRow( 'Название поля год', 'Название дополнительного поля год<br>Если оставить пустым, будет игнорироваться', makeDropDown( ( $fields['cpu_year'] ), "save_con[ms_cpu_year]", "{$moonserials_options['ms_cpu_year']}" ) );
	showRow( 'Текст после года', 'Текст, который модуль будет вставлять в тайтл после года<br>Если оставить пустым, будет игнорироваться<br><b>Писать на русском, будет преобразовано в транслит</b>', "<input class=\"form-control\" type=text style=\"width:100%;\" name=\"save_con[ms_cpu_t1]\" value=\"{$moonserials_options['ms_cpu_t1']}\" >");
	showRow( 'Произвольный текст', 'Текст, который модуль будет вставлять после серии и перед датой<br>Если оставить пустым, будет игнорироваться<br><b>Писать на русском, будет преобразовано в транслит</b>', "<input class=\"form-control\" type=text style=\"width:100%;\" name=\"save_con[ms_cpu_t2]\" value=\"{$moonserials_options['ms_cpu_t2']}\" >");
	showRow( 'Название поля, которое добавлять в ЧПУ', 'Название дополнительного поля, значение которого добавлять в ЧПУ<br>Если оставить пустым, будет игнорироваться<br><b>Писать на русском, будет преобразовано в транслит</b>', makeDropDown( ( $fields['cpu_in'] ), "save_con[ms_cpu_field]", "{$moonserials_options['ms_cpu_field']}" ) );
	showRow( 'Формат даты', 'Если оставить пустым, дату вставлять не будет.<br>Распознаются такие символы:<br>d - день (число) месяца, 2 цифры с ведущим нулём, если необходимо; т. е. от "01" до "31"<br>D - день недели, буквенный, 3 буквы; например, "Fri"<br>F - месяц, буквенный, long; например, "January"<br>g - час, 12-часовой формат без ведущих нулей; т.е. от "1" до "12"<br>G - час, 24-часовой формат без ведущих нулей; т.е. от "0" до "23"<br>h - час, 12-часовой формат; т.е. от "01" до "12"<br>H - час, 24-часовой формат; т.е. от "00" до "23"<br>i - минуты; т.е. от "00" до "59"<br>j - день (число) месяца без ведущих нулей; т.е. от "1" до "31"<br>l ("L" в нижнем регистре) - день недели, буквенный, long; например, "Friday"<br>m - месяц; т.е. от "01" до "12"<br>M - месяц, буквенный, 3 буквы; например, "Jan"<br>n - месяц без ведущих нулей; т.е. от "1" до "12"<br>s - секунды; т.е. от "00" до "59"<br>Y - год, 4 цифры; например, "1999"<br>y - год, 2 цифры; например, "99"', "<input type=text style=\"width:100%;\" name=\"save_con[ms_cpu_date]\" value=\"{$moonserials_options['ms_cpu_date']}\" >");
echo <<<HTML
</table></div></div>
HTML;

echo <<<HTML
<div id="block" class="panel panel-flat" style="display:none">
  <div class="panel-body">
    MoonSerials - настройки блока
  </div>
  <div class="table-responsive">
  <table class="table table-striped">
HTML;
	showRow( 'Категории, которые выводить в блоке', 'ID категорий, которые будут выведены в блоке, можно несколько, через запятую', "<input class=\"form-control\" type=text style=\"width:100%;\" name=\"save_con[ms_block_cat]\" value=\"{$moonserials_options['ms_block_cat']}\" >");
	showRow( 'Категории, которые не выводить в блоке', 'ID категорий, которые не будут выведены в блоке, можно несколько, через запятую', "<input class=\"form-control\" type=text style=\"width:100%;\" name=\"save_con[ms_block_not_cat]\" value=\"{$moonserials_options['ms_block_not_cat']}\" >");
	showRow( 'Выводить законченные?', 'Если включено, в блоке будут выводиться сериалы, у которых стоит статус Закончен', makeCheckBox( "save_con[ms_block_ower]", "{$moonserials_options['ms_block_ower']}" ) );
    showRow( 'Куда выводить русское название', 'Название дополнительного поля, в которое будет выводится русское название, для ситуаций, когда у вас в заголовке есть лишние слова, а в блоке вам надо только название<br><b>Будет пусто - будет игнорироваться</b>', makeDropDown( ( $fields['title_ru'] ), "save_con[field_title_ru]", "{$moonserials_options['field_title_ru']}" ) );
	showRow( 'За сколько дней выводить новости?', 'Введите число дней, которые выводить в блоке, кроме сегодня, то есть если введете 2 то будет сегодня, вчера и позавчера', "<input class=\"form-control\" type=text style=\"width:100%;\" name=\"save_con[ms_block_day]\" value=\"{$moonserials_options['ms_block_day']}\" >");
	showRow( 'Формат времени для блока', 'Введите, в каком формате выводить время. Обязательно к заполнению', "<input class=\"form-control\" type=text style=\"width:100%;\" name=\"save_con[ms_block_time]\" value=\"{$moonserials_options['ms_block_time']}\" >");
    showRow( 'Общее количество новостей в блоке', 'Обязательно к заполнению', "<input class=\"form-control\" type=text style=\"width:100%;\" name=\"save_con[ms_block_limit]\" value=\"{$moonserials_options['ms_block_limit']}\" >");

echo <<<HTML
</table></div></div>
HTML;


echo <<<HTML
<div style="margin-bottom:30px;">
<input type="hidden" name="user_hash" value="{$dle_login_hash}" />
<input type="submit" class="btn btn-green" value="{$lang['user_save']}">
</div>
</form>
HTML;


	if(!is_writable(ENGINE_DIR . '/data/moonserials_options.php')) {

		$lang['stat_system'] = str_replace ("{file}", "engine/data/moonserials_options.php", $lang['stat_system']);

		echo "<div class=\"alert alert-danger alert-styled-left alert-arrow-left alert-component\">{$lang['stat_system']}</div>";

	}

echofooter();
?>