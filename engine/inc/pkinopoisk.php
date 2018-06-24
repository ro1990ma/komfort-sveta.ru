<?php
/**
 * PKinoPoisk for DLE
 * @version 3.x.x
 * @copyright Copyright (c) 2012, SpaitNet
 * @package pkinopoisk
 * @subpackage settion
 */
if (!defined('DATALIFEENGINE')) {
	die("Hacking attempt!");
}

$asag = $db->super_query("SELECT allow_groups FROM ".PREFIX."_admin_sections WHERE name = 'pkinopoisk'");
$asag = explode(',', $asag['allow_groups']);

if (!$is_loged_in or $member_id['user_id'] < 1 or in_array($member_id['user_group'], $asag) === false) {
	die('Hacking attempt!');
}

$encoding = strtolower($config['charset']);
$encoding = ($encoding == 'utf-8' or $encoding == 'utf8') ? 'utf-8' : $encoding;

$newdis = false;
if (isset($config['version_id']) and version_compare($config['version_id'], '10.2', '>=')) {
	$newdis = true;
}

$vmod = '3.3.0';
$json = array(
	'success' => '',
	'error'   => false
);

if (get_magic_quotes_runtime()) {
	set_magic_quotes_runtime(false);
}

function unti_mqq($data)
{
	if (is_array($data)) {
		foreach ($data as $key => $val) {
			$data[$key] = unti_mqq($val);
		}
	} else {
		$data = stripslashes($data);
	}

	return $data;
}

if ($_POST['mod_act'] == 'save') {

	if (isset($_POST['config'])) {
		$config_mod = $_POST['config'];

		if (get_magic_quotes_gpc()) {
			$config_mod = unti_mqq($config_mod);
		}

		foreach ($config_mod['conf'] as $key => $val) {
			if (is_numeric($val) and $key != 'pars_poster_film_size' and $key != 'pars_poster_film_big_size') {
				$val = intval($val);
			}
			$config_mod['conf'][$key] = $val;
		}

		$config_mod['template_person'] = array();

		if (isset($_POST['template_person_key']) and isset($_POST['config']['template_person'])) {
			foreach ($_POST['template_person_key'] as $key => $val) {
				$val = totranslit($val, false, true);

				if (!empty($val) and isset($_POST['config']['template_person'][$key])) {
					$config_mod['template_person'][$val] = $_POST['config']['template_person'][$key];
				}
			}
		}

		$config_mod['version'] = $vmod;

		file_put_contents(ENGINE_DIR.'/data/config_pkinopoisk.php', serialize($config_mod), LOCK_EX);
		@chmod(ENGINE_DIR.'/data/config_pkinopoisk.php', 0666);

		clear_cache('cookie_pk');
		clear_cache('pkinopoisk_');

		$json['success'] = 'Данные сохранены!';
	} else {

		$json['error'] = true;

	}

	echo json_encode($json);
	die();

} elseif ($_POST['mod_act'] == 'get_conf') {

	if (!function_exists('mb_convert_encoding') and !function_exists('iconv')) {

		$json['error'] = 'Необходимо установить libiconv или mbstring';

	} else {

		$config_mod = file_get_contents(ENGINE_DIR.'/data/config_pkinopoisk.php');
		$config_mod = unserialize($config_mod);

		$json['success'] = $config_mod;

	}

	echo json_encode($json);
	die();

} else {

	$config_mod = unserialize(file_get_contents(ENGINE_DIR.'/data/config_pkinopoisk.php'));

	$bufer = '';

	$xfields = xfieldsload();

	$group_ap_list = array();
	foreach ($user_group as $group) {

		if ($encoding != 'utf-8') {
			if (function_exists('mb_convert_encoding')) {
				$group['group_name'] = mb_convert_encoding($group['group_name'], 'UTF-8', $encoding);
			} elseif (function_exists('iconv')) {
				$group['group_name'] = iconv($encoding, 'UTF-8//IGNORE', $group['group_name']);
			}
		}

		$group_ap_list[$group['id']] = $group['group_name'];

	}

	echoheader("", "");

	$pkp_options = array(
		'main'  => array(
			array(
				'title' => 'Имя на КиноПоиск:',
				'desc'  => '',
				'name'  => 'config[conf][kp_user]',
				'type'  => 'text',
			),
			array(
				'title' => 'Пароль на КиноПоиск:',
				'desc'  => '',
				'name'  => 'config[conf][kp_pass]',
				'type'  => 'text',
			),
			'hr_line',
			array(
				'title' => 'Адрес сайта:',
				'desc'  => 'Укажите адрес сайта для формирования ссылок тегов и изображений. Адрес должен заканчиваться на "/". Если оставить пустым будет браться с "Настройка системы". Пример: "http://site.ru/" "/"',
				'name'  => 'config[conf][http_site]',
				'type'  => 'text',
			),
			array(
				'title'  => 'Включить режим "до оформить":',
				'desc'   => 'Если данный режим включен при оформлении будет предложено оформить пост полностью или заполнить только нужное поле.',
				'name'   => 'config[conf][to_make_form]',
				'type'   => 'select',
				'option' => array(
					'0' => 'Нет',
					'1' => 'Да'
				),
			),
			array(
				'title'  => 'Выводить дополнительную информацию при поиске:',
				'desc'   => 'Если данный режим включен при поиске будет выводится доп. информация (страна, режиссер, жанр).',
				'name'   => 'config[conf][search_print_addinfo]',
				'type'   => 'select',
				'option' => array(
					'0' => 'Нет',
					'1' => 'Да'
				),
			),
			array(
				'title'  => 'Не выводить статус при работе парсера:',
				'desc'   => 'Если "да", то при работе парсера не будет выводится состояния процесса парсинга.',
				'name'   => 'config[conf][not_status_proc]',
				'type'   => 'select',
				'option' => array(
					'0' => 'Нет',
					'1' => 'Да'
				),
			),
			'hr_line',
			array(
				'title'  => 'Загружать трейлер:',
				'desc'   => 'Трейлер будет загружаться на сервер или на YouTube. <span style="color:red;">ВНИМАНИЕ сервер должен позволять длительное выполнения скрипта.</span>',
				'name'   => 'config[conf][trailer_download]',
				'type'   => 'select',
				'option' => array(
					'0' => 'Нет',
					'1' => 'К себе на сервер',
					'2' => 'На YouTube'
				),
			),
			array(
				'title' => 'Логин на YouTube:',
				'desc'  => 'Пример test@gmail.com',
				'name'  => 'config[conf][trailer_yt_login]',
				'type'  => 'text',
			),
			array(
				'title' => 'Пароль на YouTube:',
				'desc'  => '',
				'name'  => 'config[conf][trailer_yt_pass]',
				'type'  => 'text',
			),
			array(
				'title' => 'API Key для YouTube:',
				'desc'  => 'Получить <a href="Получить http://code.google.com/apis/youtube/dashboard" target="_blank">http://code.google.com/apis/youtube/dashboard</a>',
				'name'  => 'config[conf][trailer_yt_key]',
				'type'  => 'text',
			),
			'hr_line',
			array(
				'title'  => 'Использовать proxy:',
				'desc'   => 'Если Вас забанили, одним из выходов использовать прокси',
				'name'   => 'config[conf][proxy_type]',
				'type'   => 'select',
				'option' => array(
					'0' => 'Нет',
					'1' => 'http прокси',
					'2' => 'socks 5 прокси'
				),
			),
			array(
				'title' => 'Proxy адрес:',
				'desc'  => 'Укажите один Proxy адрес, в виде XXX.XXX.XXX.XXX:port или login:password@XXX.XXX.XXX.XXX:port (если с авторизацией). Список можно взять тут http://spys.ru/proxylist/ (желательно брать HIA и Врм чем меньше тем лучше) для http прокси или http://spys.ru/socks/ для socks 5 прокси (Врм чем меньше тем лучше). Предпочтительней использовать socks 5 прокси',
				'name'  => 'config[conf][proxy]',
				'type'  => 'text',
			),
			'hr_line',
			array(
				'title'           => 'Модуль доступен группам:',
				'desc'            => 'Перечислите id групп, через запятую, которым доступен данный модуль',
				'name'            => 'config[conf][group_ap][]',
				'type'            => 'select',
				'select_multiple' => true,
				'option'          => $group_ap_list
			),
			array(
				'title' => 'Время задержки:',
				'desc'  => 'Промежуток времени между запросами, рекомендую 2-3 сек.. Запросом считается любое действие в том числе загрузка изображений, т.е. если Вы загружаете 10 кадров, основной постер и время выставлено на 3 сек., то процесс займет минимум 33 сек. (11*3=33)',
				'name'  => 'config[conf][sleep]',
				'type'  => 'text',
				'size'  => '12',
			),
			array(
				'title'  => 'Парсить список фильмов при нажатии "Enter":',
				'desc'   => 'Для парсинга списка фильмов достаточно будет нажать "enter" в поле "Заголовок статьи"',
				'name'   => 'config[conf][keyenter]',
				'type'   => 'select',
				'option' => array(
					'0' => 'Нет',
					'1' => 'Да'
				),
			)
		),
		'decor' => array(
			array(
				'title'  => 'Использовать данные "Год" как тег:',
				'desc'   => 'Если да, то данные будут заносится в поле тег и автоматически формировать ссылку на данный тег. Если "Да, без ссылки", будет добавляться только в поле "Облако тегов:", ссылка формироваться не будет. Если "Ссылка без добавления в тег" будет формироваться ссылка, но не будет попадать данные в теги.',
				'name'   => 'config[conf][t_year]',
				'type'   => 'select',
				'option' => array(
					'0' => 'Нет',
					'1' => 'Да',
					'2' => 'Да, без ссылки',
					'3' => 'Ссылка без добавления в тег'
				),
			),
			array(
				'title' => 'Префикс для тега "Год":',
				'desc'  => 'Если оставить пустым то ссылка будет формироваться с стандартным префиксом "tags/".',
				'name'  => 'config[conf][t_year_prefix]',
				'size'  => 12
			),
			array(
				'title'  => 'Использовать данные "Страна" как тег:',
				'desc'   => 'Если да, то данные будут заносится в поле тег и автоматически формировать ссылку на данный тег. Если "Да, без ссылки", будет добавляться только в поле "Облако тегов:", ссылка формироваться не будет. Если "Ссылка без добавления в тег" будет формироваться ссылка, но не будет попадать данные в теги.',
				'name'   => 'config[conf][t_country]',
				'type'   => 'select',
				'option' => array(
					'0' => 'Нет',
					'1' => 'Да',
					'2' => 'Да, без ссылки',
					'3' => 'Ссылка без добавления в тег'
				),
			),
			array(
				'title' => 'Префикс для тега "Страна":',
				'desc'  => 'Если оставить пустым то ссылка будет формироваться с стандартным префиксом "tags/".',
				'name'  => 'config[conf][t_country_prefix]',
				'size'  => 12
			),
			array(
				'title'  => 'Использовать данные "Жанр" как тег:',
				'desc'   => 'Если да, то данные будут заносится в поле тег и автоматически формировать ссылку на данный тег. Если "Да, без ссылки", будет добавляться только в поле "Облако тегов:", ссылка формироваться не будет. Если "Ссылка без добавления в тег" будет формироваться ссылка, но не будет попадать данные в теги.',
				'name'   => 'config[conf][t_genre]',
				'type'   => 'select',
				'option' => array(
					'0' => 'Нет',
					'1' => 'Да',
					'2' => 'Да, без ссылки',
					'3' => 'Ссылка без добавления в тег'
				),
			),
			array(
				'title' => 'Префикс для тега "Жанр":',
				'desc'  => 'Если оставить пустым то ссылка будет формироваться с стандартным префиксом "tags/".',
				'name'  => 'config[conf][t_genre_prefix]',
				'size'  => 12
			),
			array(
				'title'  => 'Использовать данные "В главных ролях" как тег:',
				'desc'   => 'Если да, то данные будут заносится в поле тег и автоматически формировать ссылку на данный тег. Если "Да, без ссылки", будет добавляться только в поле "Облако тегов:", ссылка формироваться не будет. Если "Ссылка без добавления в тег" будет формироваться ссылка, но не будет попадать данные в теги.',
				'name'   => 'config[conf][t_actors]',
				'type'   => 'select',
				'option' => array(
					'0' => 'Нет',
					'1' => 'Да',
					'2' => 'Да, без ссылки',
					'3' => 'Ссылка без добавления в тег'
				),
			),
			array(
				'title' => 'Префикс для тега "В главных ролях":',
				'desc'  => 'Если оставить пустым то ссылка будет формироваться с стандартным префиксом "tags/".',
				'name'  => 'config[conf][t_actors_prefix]',
				'size'  => 12
			),
			array(
				'title'  => 'Использовать данные "Режиссер" как тег:',
				'desc'   => 'Если да, то данные будут заносится в поле тег и автоматически формировать ссылку на данный тег. Если "Да, без ссылки", будет добавляться только в поле "Облако тегов:", ссылка формироваться не будет. Если "Ссылка без добавления в тег" будет формироваться ссылка, но не будет попадать данные в теги.',
				'name'   => 'config[conf][t_director]',
				'type'   => 'select',
				'option' => array(
					'0' => 'Нет',
					'1' => 'Да',
					'2' => 'Да, без ссылки',
					'3' => 'Ссылка без добавления в тег'
				)
			),
			array(
				'title' => 'Префикс для тега "Режиссер":',
				'desc'  => 'Если оставить пустым то ссылка будет формироваться с стандартным префиксом "tags/".',
				'name'  => 'config[conf][t_director_prefix]',
				'size'  => 12
			),
			array(
				'title'  => 'Использовать данные "Сценарист" как тег:',
				'desc'   => 'Если да, то данные будут заносится в поле тег и автоматически формировать ссылку на данный тег. Если "Да, без ссылки", будет добавляться только в поле "Облако тегов:", ссылка формироваться не будет. Если "Ссылка без добавления в тег" будет формироваться ссылка, но не будет попадать данные в теги.',
				'name'   => 'config[conf][t_screenwriter]',
				'type'   => 'select',
				'option' => array(
					'0' => 'Нет',
					'1' => 'Да',
					'2' => 'Да, без ссылки',
					'3' => 'Ссылка без добавления в тег'
				),
			),
			array(
				'title' => 'Префикс для тега "Сценарист":',
				'desc'  => 'Если оставить пустым то ссылка будет формироваться с стандартным префиксом "tags/".',
				'name'  => 'config[conf][t_screenwriter_prefix]',
				'size'  => 12
			),
			array(
				'title'  => 'Использовать данные "Производство" (студии) как тег:',
				'desc'   => 'Должно быть включено "Парсить студии". Если да, то данные будут заносится в поле тег и автоматически формировать ссылку на данный тег. Если "Да, без ссылки", будет добавляться только в поле "Облако тегов:", ссылка формироваться не будет. Если "Ссылка без добавления в тег" будет формироваться ссылка, но не будет попадать данные в теги.',
				'name'   => 'config[conf][t_studio]',
				'type'   => 'select',
				'option' => array(
					'0' => 'Нет',
					'1' => 'Да',
					'2' => 'Да, без ссылки',
					'3' => 'Ссылка без добавления в тег'
				),
			),
			array(
				'title' => 'Префикс для тега "Производство":',
				'desc'  => 'Если оставить пустым то ссылка будет формироваться с стандартным префиксом "tags/".',
				'name'  => 'config[conf][t_studio_prefix]',
				'size'  => 12
			),

			'hr_line',

			array(
				'title' => 'Сколько парсить "Знаете ли вы, что...":',
				'desc'  => 'Количество "Знаете ли вы, что...". 0 - не нужно; 999 - все данные;',
				'name'  => 'config[conf][trivia_count]',
				'size'  => 12
			),
			array(
				'title' => 'Разделитель между "Знаете ли вы, что...":',
				'desc'  => 'Укажите как нужно разделять данные "Знаете ли вы, что...". Например \'&lt;br/&gt;\' или \'&lt;/li&gt;&lt;li&gt;\' (в шаблоне &lt;ul&gt;[pkpgiven_trivia]&lt;/ul&gt;).',
				'name'  => 'config[conf][trivia_sep]',
				'size'  => 12
			),
			'hr_line',
			array(
				'title' => 'Сколько парсить "Ошибки в фильме":',
				'desc'  => 'Количество "Ошибки в фильме". 0 - не нужно; 999 - все данные;',
				'name'  => 'config[conf][trivia_blooper_count]',
				'size'  => 12
			),
			array(
				'title' => 'Разделитель между "Ошибки в фильме":',
				'desc'  => 'Укажите как нужно разделять данные "Ошибки в фильме". Например \'&lt;br/&gt;\' или \'&lt;/li&gt;&lt;li&gt;\' (в шаблоне &lt;ul&gt;[pkpgiven_trivia_blooper]&lt;/ul&gt;).',
				'name'  => 'config[conf][trivia_blooper_sep]',
				'size'  => 12
			),
			'hr_line',
			array(
				'title' => 'Количество загружаемых комментариев:',
				'desc'  => 'Будут парсится "Рецензии зрителей". Если не нужно поставить 0, немного ускоряет',
				'name'  => 'config[conf][comment_count]',
				'type'  => 'text',
				'size'  => '12',
			),
			array(
				'title' => 'Разделитель между комментариями:',
				'desc'  => 'Укажите как нужно разделять комментарии. Например \'&lt;br/&gt;--------&lt;br/&gt;\' или \'&lt;/li&gt;&lt;li&gt;\' (в шаблоне &lt;ul&gt;[pkpgiven_comment]&lt;/ul&gt;).',
				'name'  => 'config[conf][comment_sep]',
				'size'  => 12
			),
			array(
				'title'  => 'Сортировать комментарии:',
				'desc'   => 'Если "Да", то комментарии будут сортироваться по полезности комментария ( полезность комментария = (Полезная рецензия? Да) / (Полезная рецензия? Нет)) ',
				'name'   => 'config[conf][comment_sort]',
				'type'   => 'select',
				'option' => array(
					'0' => 'Нет',
					'1' => 'Да'
				),
			),
			'hr_line',
			array(
				'title'  => 'Парсить студии:',
				'desc'   => 'Будет парсить "Производство" из раздела Студии.',
				'name'   => 'config[conf][pars_studio]',
				'type'   => 'select',
				'option' => array(
					'0' => 'Нет',
					'1' => 'Да'
				),
			),
			array(
				'title' => 'Разделитель между "Студии":',
				'desc'  => 'Укажите как нужно разделять данные "Студии". Например \'&lt;br/&gt;\' или \'&lt;/li&gt;&lt;li&gt;\' (в шаблоне &lt;ul&gt;[pkpgiven_studio]&lt;/ul&gt;).',
				'name'  => 'config[conf][studio_sep]',
				'size'  => 12
			),
			'hr_line',
			array(
				'title'  => 'Данные "Зрители" отображать в виде картинки:',
				'desc'   => ' Если да, то вместо названия странны будет выводится флаг страны',
				'name'   => 'config[conf][aud_type]',
				'type'   => 'select',
				'option' => array(
					'0' => 'Нет',
					'1' => 'Да'
				),
			),
			array(
				'title' => 'Разделитель между "Зрители":',
				'desc'  => 'Укажите как нужно разделять данные "Зрители". Например \', \' или \'&lt;/li&gt;&lt;li&gt;\' (в шаблоне &lt;ul&gt;[pkpgiven_audience]&lt;/ul&gt;).',
				'name'  => 'config[conf][aud_type_sep]',
				'size'  => 12
			),
			'hr_line',
			array(
				'title'  => 'Данные "Рейтинг MPAA" отображать в виде картинки:',
				'desc'   => 'Если да, то вместо название будет выводится значок рейтинга',
				'name'   => 'config[conf][rate_pg_type]',
				'type'   => 'select',
				'option' => array(
					'0' => 'Нет',
					'1' => 'Да'
				),
			),
			array(
				'title'  => 'Убрать данные где значение "-":',
				'desc'   => 'Если в поле будет присутствовать только "-", то данное значение будет считаться пустым',
				'name'   => 'config[conf][del_tir]',
				'type'   => 'select',
				'option' => array(
					'0' => 'Нет',
					'1' => 'Да'
				),
			),
			array(
				'title'  => 'Убрать из названия доп. информацию:',
				'desc'   => 'При включенной опции из названия будет удалена дополнительная информация о фильме. Например (ТВ) (Сериал).',
				'name'   => 'config[conf][title_clean]',
				'type'   => 'select',
				'option' => array(
					'0' => 'Нет',
					'1' => 'Да'
				),
			),
			array(
				'title' => 'Формат времени для даты премьеры:',
				'desc'  => 'Формат вывода дат "премьера (мир)" и "премьера (РФ)". Помощь по оформлению <a href="#" onclick="Help(\'date\'); return false;">смотреть тут</a>. Пример "j M Y, D".',
				'name'  => 'config[conf][premier_format_date]',
				'type'  => 'text',
				'size'  => '12',
			),
			array(
				'title' => 'Формат времени для продолжительности трейлера:',
				'desc'  => 'Если поле пустое, будет выводиться полное количество секунд. Для оформления можно использовать следующие символы: m - полных мин. (0-59); M - полных мин. (00-59); s - сек. в последней минуте (0-59); S - сек. в последней минуте (00-59). Примеры: "PT00:M:S"; "m мин. s сек.".',
				'name'  => 'config[conf][trailer_duration_format]',
				'type'  => 'text',
				'size'  => '12',
			),
			array(
				'title'  => 'Парсить всех персон участвовавших в фильме:',
				'desc'   => '',
				'name'   => 'config[conf][pars_actor_all]',
				'type'   => 'select',
				'option' => array(
					'0' => 'Нет',
					'1' => 'Да'
				),
			),

		),
		'image' => array(
			array(
				'title'  => 'Парсить основной постер:',
				'desc'   => 'Если "нет", то основной постер (маленький) загружаться не будет',
				'name'   => 'config[conf][pars_poster_film]',
				'type'   => 'select',
				'option' => array(
					'0' => 'Нет',
					'1' => 'Да'
				),
			),
			array(
				'title' => 'Размер основного постера:',
				'desc'  => 'Укажите желаемый размер основного постра. Основной посте будет уменьшаться до заданного размера. Если задать <b>180x300 будет создан указанный размер, 180x0 - по ширине, 0x300 - по высоте</b>.',
				'name'  => 'config[conf][pars_poster_film_size]',
				'type'  => 'text',
				'size'  => '12',
			),
			array(
				'title'  => 'Парсить большой постер к фильму:',
				'desc'   => 'При включенной опции будет браться браться увеличенный постер с страницы фильма',
				'name'   => 'config[conf][pars_poster_film_big]',
				'type'   => 'select',
				'option' => array(
					'0' => 'Нет',
					'1' => 'Да'
				),
			),
			array(
				'title'  => 'Выбирать нужный постер вручную:',
				'desc'   => 'Прежде чем фильм будет загружен, будут показаны постеры с первой страницы постеров, для выбора нужного. В этом режиме изображения будут загружаться (на сервере файл не создается).',
				'name'   => 'config[conf][pars_poster_choose]',
				'type'   => 'select',
				'option' => array(
					'0' => 'Нет',
					'1' => 'Да'
				),
			),
			array(
				'title' => 'Размер большого постера:',
				'desc'  => 'Укажите желаемый размер большого постра. Большой посте будет уменьшаться до заданного размера. Если задать <b>180x300 будет создан указанный размер, 180x0 - по ширине, 0x300 - по высоте</b>.',
				'name'  => 'config[conf][pars_poster_film_big_size]',
				'type'  => 'text',
				'size'  => '12',
			),
			array(
				'title' => 'Размер тумба постера:',
				'desc'  => 'Укажите желаемую ширину тумба. Будет создаваться тумб для постера который грузится из раздела "постеры". Можно указать 800x600, тогда тумб будет создаваться заданного раздела. Если 0 то тумб создаваться не будет',
				'name'  => 'config[conf][tumb_poster]',
				'type'  => 'text',
				'size'  => '12',
			),
			array(
				'title'  => 'Тумб для постера создавать:',
				'desc'   => 'Если указа только одна сторона то тумб будет создаваться по данному критерию',
				'name'   => 'config[conf][tumb_poster_type]',
				'type'   => 'select',
				'option' => array(
					'0' => 'По наибольшей стороне',
					'1' => 'По ширине',
					'2' => 'По высоте'
				),
			),
			array(
				'title' => 'Альтернативное изображение постера:',
				'desc'  => 'Если постер не был загружен будет вставлять указанное изображение (указать путь к изображению, например http://test.ru/uploads/poster_no.jpg или /uploads/poster_no.jpg, не забудьте про тумб). Оставьте пустым, если альтернативное изображение не нужно',
				'name'  => 'config[conf][poster_film_alter]',
				'type'  => 'text',
			),
			'hr_line',
			array(
				'title' => 'Сколько загружать кадров:',
				'desc'  => 'Укажите желаемое число загружаемых изображений из раздела "кадры". Если загружать не нужно поставьте 0',
				'name'  => 'config[conf][kol_kadr]',
				'type'  => 'text',
				'size'  => '12',
			),
			array(
				'title'  => 'Выбирать кадры вручную:',
				'desc'   => 'Прежде чем фильм будет загружен, будут предложено выбрать изображения. В этом режиме будут загружены только выбранные изображения. Настройка количества будет игнорироваться.',
				'name'   => 'config[conf][pars_kadr_choose]',
				'type'   => 'select',
				'option' => array(
					'0' => 'Нет',
					'1' => 'Да'
				),
			),
			array(
				'title' => 'Сколько загружать скриншотов (не рекомендую):',
				'desc'  => 'Укажите желаемое число загружаемых изображений из раздела "скриншоты". Если загружать не нужно поставьте 0. Данный метод не отработан, если не нужно не вкл.',
				'name'  => 'config[conf][kol_scrin]',
				'type'  => 'text',
				'size'  => '12',
			),
			array(
				'title' => 'Размер тумба для кадров и скриншотов:',
				'desc'  => 'Укажите желаемую ширину тумба. Будет создаваться тумб для постера который грузится из раздела "постеры". Можно указать 800x600, тогда тумб будет создаваться заданного раздела. Если 0 то тумб создаваться не будет',
				'name'  => 'config[conf][tumb_kadr]',
				'type'  => 'text',
				'size'  => '12',
			),
			array(
				'title' => 'Качество сжатия изображения:',
				'desc'  => 'Качество сжатия JPEG картинки при копировании на сервер',
				'name'  => 'config[conf][jpeg_quality]',
				'type'  => 'text',
				'size'  => '12',
			),
			array(
				'title' => 'Максимально допустимые размеры изображения:',
				'desc'  => 'Существует две возможности использования данной настройки:<br><b>Первая:</b> Вы вводите допустимые размеры в пикселях любой из сторон оригинального изображения. Например: <b>800</b>.<br><b>Вторая:</b> Вы задаете ширину и высоту оригинального изображения в формате ширина x высота. Например: <b>800x600</b><br>Если размер будет больше, то оригинальное изображение будет автоматически уменьшено до указанного размера, иначе изображение будет пересжато без изменения размера. Вы можете указать <b>0</b>, если хотите чтобы изображение оставалось оригинальным.',
				'name'  => 'config[conf][max_up_side]',
				'type'  => 'text',
				'size'  => '12',
			),
			array(
				'title'  => 'Тумб для кадров и скриншотов создавать:',
				'desc'   => 'Если указа только одна сторона то тумб будет создаваться по данному критерию',
				'name'   => 'config[conf][tumb_kadr_type]',
				'type'   => 'select',
				'option' => array(
					'0' => 'По наибольшей стороне',
					'1' => 'По ширине',
					'2' => 'По высоте'
				),
			),
			array(
				'title'  => 'Обрезать загружаемые изображения:',
				'desc'   => 'Изображения с вотермарком кинопоиск.ru будут обрезаться',
				'name'   => 'config[conf][imag_cut]',
				'type'   => 'select',
				'option' => array(
					'0' => 'Нет',
					'1' => 'Справа',
					'2' => 'Снизу',
					'3' => 'По наибольшей стороне'
				),
			),
			array(
				'title'  => 'Разрешить наложение водяных знаков:',
				'desc'   => 'Водяные знаки будут накладываться на кадры и скриншоты средствами dle',
				'name'   => 'config[conf][watermark_on]',
				'type'   => 'select',
				'option' => array(
					'0' => 'Нет',
					'1' => 'Да'
				),
			),

		),
	);

	$config['admin_path'] = isset($config['admin_path']) ? $config['admin_path'] : 'admin.php';

	//Test lib
	$testlib = '';
	if (version_compare(PHP_VERSION, '5.2.0') < 0) {
		$testlib .= '<span style="color:#ff0000;">'.PHP_VERSION.' - рекомендуется использовать версию PHP не ниже 5.2</span><br />';
	}
	if (!extension_loaded('curl')) {
		$testlib .= '<span style="color:#ff0000;">curl - не найдено, обратитесь к администратору для установки данной библиотеке</span><br />';
	}
	if (!extension_loaded('iconv') and !extension_loaded('mbstring')) {
		$testlib .= '<span style="color:#ff0000;">iconv|mbstring - не найдено, обратитесь к администратору для установки данной библиотеке (нужно iconv или mbstring)</span><br />';
	}
	if (!extension_loaded('ionCube Loader')) {
		$testlib .= '<span style="color:#ff0000;">ionCube - не найдено, обратитесь к администратору для установки данной библиотеке</span><br />';
	}

	if (!empty($testlib)) {

		if ($newdis) {

			$testlib = <<<HTML
<div class="box">
	<div class="box-content">
		<div class="row box-section">{$testlib}</div>
	</div>
</div>
HTML;

		} else {

			$testlib = <<<HTML
<table width="100%">
    <tr>
        <td width="4"><img src="engine/skins/images/tl_lo.gif" width="4" height="4" border="0"></td>
        <td background="engine/skins/images/tl_oo.gif"><img src="engine/skins/images/tl_oo.gif" width="1" height="4" border="0"></td>
        <td width="6"><img src="engine/skins/images/tl_ro.gif" width="6" height="4" border="0"></td>
    </tr>
    <tr>
        <td background="engine/skins/images/tl_lb.gif"><img src="engine/skins/images/tl_lb.gif" width="4" height="1" border="0"></td>
        <td style="padding:5px;" bgcolor="#FFFFFF">
		{$testlib}
	</td>
        <td background="engine/skins/images/tl_rb.gif"><img src="engine/skins/images/tl_rb.gif" width="6" height="1" border="0"></td>
    </tr>
    <tr>
        <td><img src="engine/skins/images/tl_lu.gif" width="4" height="6" border="0"></td>
        <td background="engine/skins/images/tl_ub.gif"><img src="engine/skins/images/tl_ub.gif" width="1" height="6" border="0"></td>

        <td><img src="engine/skins/images/tl_ru.gif" width="6" height="6" border="0"></td>
    </tr>
</table>
HTML;

		}

	}
	//END Test lib

	$bufer .= <<<HTML
<style>
.box-section {
	padding: 0;
}
.box-section td {
	border: 1px solid #dddddd;
	padding: 12px;
    	vertical-align: middle;
    	line-height: 1.42857;
    	text-align: left;
}
.box-section tr:nth-child(2n) td {
	background: #f3f4f8;
}
.box-section .small {
	font-style: italic;
}
.tinfo {
	padding: 12px;
}
</style>
HTML;

	if ($newdis) {

		$bufer .= <<<HTML
<style>
.tab-pane {
	display: none;
}
.tab-pane.active {
	display: block;
}
.box #form_conf_pkp_submit {
	margin: 0 0 15px 15px;
}
</style>

<div class="box">
	<div class="box-header"><div class="title">Настройки модуля Парсер КиноПоиск (PKinoPoisk) {$vmod}</div></div>
	<div class="box-header">
		<ul class="nav nav-tabs nav-tabs-left">
			<li class="active"><a data-toggle="tab" href="#main">Основное</a></li>
			<li><a data-toggle="tab" href="#decor">Оформление</a></li>
			<li><a data-toggle="tab" href="#image">Изображения</a></li>
			<li><a data-toggle="tab" href="#template">Шаблоны</a></li>
			<li><a data-toggle="tab" href="#category">Категории</a></li>
		</ul>
	</div>
	<div class="box-content">
		<form id="form_conf_pkp" action="" method="">
HTML;

	} else {

		$bufer .= <<<HTML
<style>
.tab-pane {
	display: none;
}
</style>

<div style="padding-top:5px;padding-bottom:2px;">

{$testlib}

<table width="100%">
    <tr>
        <td width="4"><img src="engine/skins/images/tl_lo.gif" width="4" height="4" border="0"></td>
        <td background="engine/skins/images/tl_oo.gif"><img src="engine/skins/images/tl_oo.gif" width="1" height="4" border="0"></td>
        <td width="6"><img src="engine/skins/images/tl_ro.gif" width="6" height="4" border="0"></td>
    </tr>
    <tr>
        <td background="engine/skins/images/tl_lb.gif"><img src="engine/skins/images/tl_lb.gif" width="4" height="1" border="0"></td>
        <td style="padding:5px;" bgcolor="#FFFFFF">
	<table width="100%">
	    <tr>
	        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">Настройки модуля Парсер КиноПоиск (PKinoPoisk) {$vmod}</div></td>
	    </tr>
	</table>
	<div class="unterline"></div>

<form id="form_conf_pkp" action="" method="">

<div id="dle_tabView1">

HTML;

	}

	foreach ($pkp_options as $pkpokey => $pkp_option) {

		$tab_active = $pkpokey == 'main' ? 'active' : '';

		$bufer .= <<<HTML
<div id="{$pkpokey}" class="tab-pane {$tab_active}"><div class="row box-section">

<div class="dle_aTab">
<!-- Base {$pkpokey} -->
<table width="100%">
HTML;

		$i  = 0;
		$hl = false;

		foreach ($pkp_option as $val) {

			if ($val == 'hr_line') {
				$bufer .= '<tr><td colspan=2><div class="hr_line"></div></td></tr>';
				$hl = true;
				continue;
			}

			$i++;
			$hl = false;

			$value = isset($config_mod[$val['name']]) ? $config_mod[$val['name']] : '';

			$bufer .= '<tr><td class="option"><b>'.$val['title'].'</b><br /><span class=small>'.$val['desc'].'</span></td><td width=394 align=middle>';

			if ($val['type'] == 'select') {
				$option = '';

				foreach ($val['option'] as $sel_key => $sel_val) {
					$selected = $sel_key == $value ? ' selected="selected"' : '';
					$option .= '<option value="'.$sel_key.'"'.$selected.'>'.$sel_val.'</option>';
				}

				$multiple = isset($val['select_multiple']) && $val['select_multiple'] ? 'multiple' : '';
				$bufer .= '<select name="'.$val['name'].'" '.$multiple.'>'.$option.'</select>';
			} else {

				$size = isset($val['size']) ? $val['size'] : 40;

				$bufer .= '<input class="edit" type="text" name="'.$val['name'].'" value="'.htmlspecialchars($value, ENT_QUOTES, $encoding, false).'" size="'.$size.'">';
			}

			$bufer .= '</td></tr>';

		}

		$bufer .= <<<HTML
</table>
<!-- END Base {$pkpokey} -->
</div>

</div></div>
HTML;

	}

	$bufer .= <<<HTML
<div id="template" class="tab-pane"><div class="row box-section">

<div class="dle_aTab">
<!-- Template -->
<div class="tinfo">Что бы вставить какое либо значение вам нужно использовать следующую формат [pkpgiven_X], где X это имя нужных данных или [pkpgiven_X limit="Y"], где X это имя нужных данных, Y - количество символов выводимых данных. Также можно исключить не нужного появления текста, если дынных нет, это можно сделать используя связку [pkpvalue__poster_X]...[/pkpvalue__poster_film], где X это имя данных которые следует проверить на существование.</div>
<div class="tinfo">
<a href="#" id="dop_name_btn">Допустимые имена (в место X):</a><br>
<div id="dop_name" style="display:none">

<strong>name_f</strong> - название фильма<br>
<strong>name_orig</strong> - оригинальное название фильма<br>
<strong>year</strong> - год<br>
<strong>country</strong> - страна<br>
<strong>slogon</strong> - слоган<br>

<strong>actors</strong> - актеры в главных ролях (основная страница)<br>
<strong>director</strong> - режиссер<br>
<strong>screenwriter</strong> - сценарий<br>
<strong>producer</strong> - продюсер<br>
<strong>operator</strong> - оператор<br>
<strong>composer</strong> - композитор<br>

<strong>actor_all</strong> - актеры в главных ролях (из раздела всех "создатели фильма"), функция должна быть включена в настройка<br>
<strong>director_all</strong> - режиссеры (из раздела всех "создатели фильма"), функция должна быть включена в настройка<br>
<strong>producer_all</strong> - продюсеры (из раздела всех "создатели фильма"), функция должна быть включена в настройка<br>
<strong>voice_director_all</strong> - режиссеры дубляжа (из раздела всех "создатели фильма"), функция должна быть включена в настройка<br>
<strong>voice_all</strong> - актеры дубляжа (из раздела всех "создатели фильма"), функция должна быть включена в настройка<br>
<strong>writer_all</strong> - сценаристы (из раздела всех "создатели фильма"), функция должна быть включена в настройка<br>
<strong>operator_all</strong> - операторы (из раздела всех "создатели фильма"), функция должна быть включена в настройка<br>
<strong>composer_all</strong> - композиторы (из раздела всех "создатели фильма"), функция должна быть включена в настройка<br>
<strong>design_all</strong> - художники (из раздела всех "создатели фильма"), функция должна быть включена в настройка<br>
<strong>editor_all</strong> - монтажер (из раздела всех "создатели фильма"), функция должна быть включена в настройка<br>

<strong>genre</strong> - жанр<br>
<strong>budget</strong> - бюджет<br>
<strong>fees_use</strong> - сборы в США<br>
<strong>fees_world</strong> - сборы в мире<br>
<strong>fees_rus</strong> - сборы в России<br>
<strong>audience</strong> - зрители<br>
<strong>premier</strong> - мировая премьера<br>
<strong>premier_rus</strong> - премьера в России<br>
<strong>premier_date</strong> - мировая премьера в пользовательском формате<br>
<strong>premier_rus_date</strong> - премьера в Россиив в пользовательском формате<br>
<strong>reliz_dvd</strong> - релиз на dvd<br>
<strong>time_film</strong> - продолжительность фильма<br>
<strong>description</strong> - описание<br>
<strong>kp_rating</strong> - рейтинг КиноПоиск<br>
<strong>kp_rating_num</strong> - Число голосов рейтинга КиноПоиск<br>
<strong>imdb</strong> - рейтинг IMDB<br>
<strong>imdb_num</strong> - Число голосов рейтинга IMDB<br>
<strong>poster_film</strong> - ссылка на постер, формируется в виде "/uploads/posts/2010-03/1267793109_poster-403986.jpg"<br>
<strong>screen_film</strong> - кадры к фильму, формируются в виде "[thumb]/uploads/posts/2010-03/1267795190_1175803.jpg[/thumb]"<br>
<strong>reliz_bluray</strong> - релиз на Blu-Ray<br>
<strong>rate_pg</strong> - рейтинг MPAA<br>
<strong>rate_pg_text</strong> - рейтинг MPAA в виде текста, например "PG-13"<br>
<strong>rate_pg_img</strong> - рейтинг MPAA в виде ссылки на изображения, например "/uploads/pkinopoisk/mpaa/PG-13.gif"<br>
<strong>rate_pg_desc</strong> - рейтинг MPAA в виде описания, например "Детям до 13 лет просмотр не желателен"<br>
<strong>actors_dubl</strong> - роли дублировали<br>
<strong>trailer</strong> - трейлер, формируется в виде "[video=путь к трейлеру]"<br>
<strong>trailer_link</strong> - чистая ссылка на трейлер<br>
<strong>trailer_duration</strong> - продолжительность трейлера<br>
<strong>trailer_youtube_id</strong> - ID видео на YouTube, загруженного трейлера (если опция "Загружать трейлер:" включена "на YouTube")<br>
<strong>trivia</strong> - берет текст с "Знаете ли вы, что..."<br>
<strong>trivia_blooper</strong> - берет текст с "Ошибки в фильме"<br>
<strong>screen_film_scr</strong> - скриншоты к фильму (берутся с "скриншоты"), формируются в виде "[thumb]/uploads/posts/2010-03/1267795190_1175803.jpg[/thumb]"<br>
<strong>poster_film_big</strong> - Формируется в виде "/uploads/posts/2010-03/1267793109_poster-403986.jpg". Thumb создается по заданному размеру в настройках модуля. Можно использовать в [thumb]<br>
<strong>id_film</strong> - ID фильма на кинопоиск.ru<br>
<strong>comment</strong> - берет текст с "Рецензии зрителей"<br>
<strong>screen_film_НОМ</strong> - путь до загруженного кадра на вашем сервере, где НОМ порядковый номер за груженого изображения, формируется в виде "/uploads/posts/2010-03/1267793109_poster-403986.jpg"<br>
<strong>screen_film_scr_НОМ</strong> - путь до загруженного скриншота на вашем сервере, где НОМ порядковый номер за груженого изображения, формируется в виде "/uploads/posts/2010-03/1267793109_poster-403986.jpg"<br>
<strong>age_limit</strong> - возраст<br>
<strong>studio</strong> - студии<br>
<br>
<hr>
<h3>Пользовательские настройки постера</h3>
При включенной настройки "Выбирать нужный постер вручную", появляется возможность выбрать подходящие постеры и оформление задать с помощью тега<br>
<b>[pkpvalue_poster_film_big_NUM][pkpgiven_poster_film_big_NUM size=100x100 thumb=0 thumb_type=0 wmo=0][/pkpvalue_poster_film_big_NUM]</b><br>
<b>NUM</b> - Номер выбранного изображения<br>
<b>size</b> - Размер изображения. Если задать 180x300 будет создан указанный размер, 180x0 - по ширине, 0x300 - по высоте.<br>
<b>thumb</b> - Размер тумба. Если задана одна цифра будет учитываться параметр thumb_type. Можно значение 800x600, тогда тумб будет создаваться заданного раздела, параметр thumb_type игнорируется.<br>
<b>thumb_type</b> - Тип создаваемого тумба. Принимает следующие значения: 0 - По наибольшей стороне, 1 - ширине, 2 - высоте.<br>
<b>wmo</b> - Накладывание водяного знака. Принимает следующие значения: 0 - не накладывать, 1 - накладывать.<br>
Должен присутствовать хотя бы один параметр (size, thumb, thumb_type, wmo).<br>
<br>
Загружаются только те изображения для которых есть оформление, например если вы выбрали 5 постеров, а в шаблонах оформление есть только для первых двух, будет загружены только эти два изображения.<br>
<br>
Для одного выбранного изображения в шаблонах можно задать несколько выводов, например:<br>
[pkpvalue_poster_film_big_1][img=left][pkpgiven_poster_film_big_1 size=150x215 thumb=0][/img][/pkpvalue_poster_film_big_1]<br>
[pkpvalue_poster_film_big_1][thumb=left][pkpgiven_poster_film_big_1 thumb=250 thumb_type=1 wmo=1][/thumb][/pkpvalue_poster_film_big_1]<br>
Для каждого изображения, будет создаваться своя копия.<br>
<br>
</div>
</div>

<table width="100%">
    <tr>
        <td width="140">Шаблон для "Облако тегов":</td>
        <td><input name="config[template][tags]" type="text" size="95" value=""></td>
    </tr>
    <tr>
        <td width="140">Шаблон для "Метатег title":</td>
        <td><input name="config[template][meta_title]" type="text" size="95" value=""></td>
    </tr>
    <tr>
        <td width="140">Шаблон для "Описание для статьи":</td>
        <td><input name="config[template][descr]" type="text" size="95" value=""></td>
    </tr>
    <tr>
        <td width="140">Шаблон для "Ключевые слова":</td>
        <td><textarea name="config[template][keywords]" style="height:50px;width:590px;"></textarea></td>
    </tr>
    <tr>
        <td width="140">
        Шаблон для "ЧПУ URL статьи":<br>
        <span class="small">Будет преобразовано в латиницу</span>
        </td>
        <td><input name="config[template][alt_name]" type="text" size="95" value=""></td>
    </tr>

    <tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr>

    <tr>
        <td width="140">Шаблон для заголовка (оставьте пустым, если не хотите изменять):</td>
        <td><input name="config[template][title]" type="text" size="95" value=""></td>
    </tr>
    <tr>
        <td width="140">Шаблон для краткой новости:</td>
        <td><textarea name="config[template][short_story]" rows="13" style="width:98%;"></textarea></td>
    </tr>
    <tr>
        <td>Шаблон для полной новости:</td>
        <td><textarea name="config[template][full_story]" rows="16" style="width:98%;"></textarea></td>
    </tr>

    <tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr>

HTML;
	if (count($xfields)) {
		foreach ($xfields as $value) {

			if ($encoding != 'utf-8') {
				if (function_exists('mb_convert_encoding')) {
					$value[1] = mb_convert_encoding($value[1], 'UTF-8', $encoding);
				} elseif (function_exists('iconv')) {
					$value[1] = iconv($encoding, 'UTF-8//IGNORE', $value[1]);
				}
			}

			$bufer .= '<tr>
        <td width="140">Шаблон для доп. поля '.$value[1].':</td><td>';
			if ($value[3] == 'text') {
				$bufer .= '<input name="config[template_xfields]['.$value[0].']" type="text" size="95" value="">';
			} else {
				$bufer .= '<textarea name="config[template_xfields]['.$value[0].']" rows="13" style="width:98%;"></textarea>';
			}
			$bufer .= '</td></tr>';
		}
	}
	$bufer .= <<<HTML
</table>
<!-- END Template -->
<!-- Template person -->
<table width="100%">
    <tr>
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">Настройка своих полей</div></td>
    </tr>
</table>

<table width="100%">
<tbody id="tampls_pers_list">
HTML;

	foreach ($config_mod['template_person'] as $key => $val) {
		$bufer .= <<<HTML
<tr>
	<td><input name="template_person_key[{$key}]" type="text" value="{$key}" style="width: 250px;"></td>
	<td style="padding: 5px 0;"><textarea name="config[template_person][{$key}]" style="height:125px;width:550px;"></textarea></td>
</tr>
HTML;
	}

	$bufer .= <<<HTML
</tbody>

<thead style="font-weight: bolder;">
<tr>
	<td style="padding: 5px;width: 300px;">ID поля</td>
	<td>Шаблон оформления</td>
</tr>
<tr><td colspan="2"><div class="hr_line"></div></td></tr>
</thead>

<tfoot>
<tr><td colspan="2"><div class="hr_line"></div></td></tr>
<tr><td colspan="2"><button id="tampls_pers_btn" type="button">Добавить поле</button></td></tr>
</tfoot>

</table>
<!-- END Template person -->
</div>

</div></div>



<div id="category" class="tab-pane"><div class="row box-section">

<div class="dle_aTab">
<table width="100%" id="CatSoot_tr">
    <tr>
        <td width="150">Имя жанра на КП</td>
        <td align="left">Соответствует категория на сайте</td>
    </tr>
    <tr>
        <td colspan="2"><div class="unterline"></div></td>
    </tr>
HTML;

	$cat_kpa = array(
		'1750' => 'аниме',
		'22'   => 'биография',
		'3'    => 'боевик',
		'13'   => 'вестерн',
		'19'   => 'военный',
		'17'   => 'детектив',
		'456'  => 'детский',
		'20'   => 'для взрослых',
		'12'   => 'документальный',
		'8'    => 'драма',
		'27'   => 'игра',
		'23'   => 'история',
		'6'    => 'комедия',
		'1747' => 'концерт',
		'15'   => 'короткометражка',
		'16'   => 'криминал',
		'7'    => 'мелодрама',
		'21'   => 'музыка',
		'14'   => 'мультфильм',
		'9'    => 'мюзикл',
		'28'   => 'новости',
		'10'   => 'приключения',
		'25'   => 'реальное ТВ',
		'11'   => 'семейный',
		'999'  => 'сериал',
		'24'   => 'спорт',
		'26'   => 'ток-шоу',
		'4'    => 'триллер',
		'1'    => 'ужасы',
		'2'    => 'фантастика',
		'18'   => 'фильм-нуар',
		'5'    => 'фэнтези',
	);

	$cat_list = CategoryNewsSelection();
	if ($encoding != 'utf-8') {
		if (function_exists('mb_convert_encoding')) {
			$cat_list = mb_convert_encoding($cat_list, 'UTF-8', $encoding);
		} elseif (function_exists('iconv')) {
			$cat_list = iconv($encoding, 'UTF-8//IGNORE', $cat_list);
		}
	}

	foreach ($cat_kpa as $key => $val) {
		$bufer .= '<tr><td>'.$val.'</td><td><select name="config[cat_match]['.$key.']">'.$cat_list.'</select></td></tr>';
	}

	$bufer .= <<<HTML
</table>

</div>

</div></div>



</div>

<br>
<input class="buttons btn btn-success" value="Сохранить изменения" type="submit" id="form_conf_pkp_submit" style="display:none;">  <span id="mess" style="color:#ff0000;"></span>
<input type="hidden" name="mod_act" value="save">
</form>

<div id="info_mess" style="padding-bottom:15px;text-align:center">Настройки модуля загружаются, немного подождите!</div>
HTML;


	if ($newdis) {

		$bufer .= <<<HTML
</div></div>
HTML;

	} else {

		$bufer .= <<<HTML
</td>
        <td background="engine/skins/images/tl_rb.gif"><img src="engine/skins/images/tl_rb.gif" width="6" height="1" border="0"></td>
    </tr>
    <tr>
        <td><img src="engine/skins/images/tl_lu.gif" width="4" height="6" border="0"></td>
        <td background="engine/skins/images/tl_ub.gif"><img src="engine/skins/images/tl_ub.gif" width="1" height="6" border="0"></td>

        <td><img src="engine/skins/images/tl_ru.gif" width="6" height="6" border="0"></td>
    </tr>
</table>
</div>
HTML;
	}


	$bufer .= <<<HTML
<script type="text/javascript" src="/engine/skins/tabs.js"></script>
<script type="text/javascript">
	var initTabsList = Array('Основное', 'Оформление', 'Изображения', 'Шаблоны', 'Категории');
	var dle_admin_path = '{$config['admin_path']}';
</script>
<script type="text/javascript" charset="UTF-8" src="/engine/classes/js/pkinopoisk.min.js"></script>
HTML;

	if ($encoding != 'utf-8') {
		if (function_exists('mb_convert_encoding')) {
			$bufer = mb_convert_encoding($bufer, $encoding, 'UTF-8');
		} elseif (function_exists('iconv')) {
			$bufer = iconv('UTF-8', $encoding.'//IGNORE', $bufer);
		}
	}

	echo $bufer;

	echofooter();

}
