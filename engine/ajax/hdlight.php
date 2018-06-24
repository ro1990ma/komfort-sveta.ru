<?php

@error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
@ini_set('error_reporting', E_ALL ^ E_WARNING ^ E_NOTICE);

define('DATALIFEENGINE', true);
define('ROOT_DIR', substr(dirname(__FILE__), 0, -12));
define('ENGINE_DIR', ROOT_DIR . '/engine');

include ENGINE_DIR.'/data/config.php';

if ($config['http_home_url'] == '') {
	$config['http_home_url'] = explode('engine/ajax/profile.php', $_SERVER['PHP_SELF']);
	$config['http_home_url'] = reset($config['http_home_url']);
	$config['http_home_url'] = 'http://' . $_SERVER['HTTP_HOST'] . $config['http_home_url'];
}

require_once ENGINE_DIR . '/classes/mysql.php';
require_once ENGINE_DIR . '/data/dbconfig.php';
require_once ENGINE_DIR . '/modules/functions.php';

if (function_exists("dle_session"))
	dle_session();

require_once ENGINE_DIR . '/modules/sitelogin.php';

// Инициализация базового функционала модуля
require_once ENGINE_DIR . '/inc/hdlight/init.php';

// Обрабатываем входящие параметры
$action = isset($_GET['action']) ? $_GET['action'] : null;
$out_base_id = isset($_GET['out_base_id']) ? intval($_GET['out_base_id']) : null;
$out_field_id = isset($_GET['out_field_id']) ? $_GET['out_field_id'] : null;
$title = isset($_GET['title']) ? urldecode(trim($_GET['title'])) : null;
$pornodb = isset($_GET['pornodb']) ? true : false;
$type = isset($_GET['type']) ? intval($_GET['type']) : null;
$threads = isset($_GET['threads']) ? intval($_GET['threads']) : null;
$last_post_id = isset($_GET['last_post_id']) ? intval($_GET['last_post_id']) : null;
$category = isset($_GET['category']) ? preg_replace("#[^0-9,]+#i", '', $_GET['category']) : '';
$category_inverse = isset($_GET['category_inverse']) ? intval($_GET['category_inverse']) : 0;
$post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : null;
$fields = isset($_GET['fields']) ? explode('|', $_GET['fields']) : null;
$rewrite = isset($_GET['rewrite']) ? intval($_GET['rewrite']) : null;

// Список категорий
$cat_info = get_vars("category");

if (!is_array($cat_info)) {
	$cat_info = array();
	
	$db->query("SELECT * FROM " . PREFIX . "_category ORDER BY posi ASC");

	while ($row = $db->get_row()) {
		$cat_info[$row['id']] = array();
		
		foreach ($row as $key => $value) {
			$cat_info[$row['id']][$key] = stripslashes($value);
		}
	}

	set_vars("category", $cat_info);

	$db->free();
}

// Обрабатываем AJAX запросы
switch ($action) {
	// Поиск фильма/сериала через API HD Light
	case 'find':
		// Проверяем включен ли модуль
		if (!$hdlight->config['on'])
			break;

		if (!in_array($member_id['user_group'], explode(',', $hdlight->config['allowgroups'])))
			break;
		
		if (!$out_base_id && !$out_field_id && !$title) {
			die(json_encode(array(
				'status' => 'error',
				'error' => '#01',
			)));
		} else {
			if ($out_base_id && $out_field_id)
				$response = $hdlight->find($out_base_id, $out_field_id, null, $pornodb);
			else
				$response = $hdlight->find(null, null, $title, $pornodb);
			
			if ($response['error'])
				die(json_encode(array(
					'status' => 'error',
					'error' => $response['error'],
				)));
			
			if ($response)
				die(json_encode(array(
					'status' => 'ok',
					'result' => $response,
				)));
			else
				die(json_encode(array(
					'status' => 'error',
					'error' => '#02',
				)));
		}
		break;
	
	// Получение id постов для потоков
	case 'get_replace_threads':
		if ($member_id['user_group'] != 1)
			break;

		if ($hdlight->config['on'] and $type and $threads) {
			$result = $hdlight->get_replace_threads($type, $threads, $last_post_id, $category, $category_inverse);
			if ($result)
				die(json_encode($result));
			else
				die(json_encode(array(
					'status' => 'end',
					'code' => '#02',
				)));
		} else
			die(json_encode(array(
				'status' => 'end',
				'code' => '#01',
			)));
		break;
	// Проставление ссылки по полю Кинопоиск ID
	case 'replace_thread':
		if ($member_id['user_group'] != 1)
			break;

		if ($hdlight->config['on'] and $post_id and $fields) {
			$result = $hdlight->replace_thread($post_id, $fields, $rewrite);
			if ($result) {
				if ($result['status'] == 'error') {
					if ($config['allow_alt_url']) {
						if ($config['seo_type'] == 1 || $config['seo_type'] == 2) {
							$result['post']['category'] = intval($result['post']['category']);
							if ($result['post']['category'] && $config['seo_type'] == 2) {
								$full_link = $config['http_home_url'] . get_url($result['post']['category']) . "/" . $result['post']['id'] . "-" . $result['post']['alt_name'] . ".html";
							} else {
								$full_link = $config['http_home_url'] . $result['post']['id'] . "-" . $result['post']['alt_name'] . ".html";
							}
						} else {
							$full_link = $config['http_home_url'] . date('Y/m/d/', $result['post']['date']) . $result['post']['alt_name'] . ".html";
						}
					} else {
						$full_link = $config['http_home_url'] . "index.php?newsid=" . $result['post']['id'];
					}

					$log_string = " $full_link" . ($result['out_base_id'] ? " ({$result['out_base_name']}: {$result['out_base_id']})" : '') . "\r\n";
					file_put_contents(ENGINE_DIR . '/inc/hdlight/reports/not_found.log', $log_string, FILE_APPEND | LOCK_EX);
				}

				die(json_encode($result));
			} else
				die(json_encode(array(
					'status' => 'end',
					'code' => '#02',
				)));
		} else
			die(json_encode(array(
				'status' => 'end',
				'code' => '#01',
			)));
		break;
}
