<?php

@set_time_limit(0);

if (!defined('E_DEPRECATED')) {
	@error_reporting(E_ALL ^ E_NOTICE);
	@ini_set('error_reporting', E_ALL ^ E_NOTICE);
} else {
	@error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
	@ini_set('error_reporting', E_ALL ^ E_DEPRECATED ^ E_NOTICE);
}

@ini_set('display_errors', true);
@ini_set('html_errors', false);

define('DATALIFEENGINE', true);
define('AUTOMODE', true);
define('LOGGED_IN', true);

define('ROOT_DIR', dirname (__FILE__));
define('ENGINE_DIR', ROOT_DIR . '/engine');

require_once ENGINE_DIR . '/data/config.php';

date_default_timezone_set($config['date_adjust']);

require_once ENGINE_DIR . '/classes/mysql.php';
require_once ENGINE_DIR . '/data/dbconfig.php';
require_once ENGINE_DIR . '/modules/functions.php';

dle_session();

//####################################################################################################################
//                    Определение категорий и их параметры
//####################################################################################################################
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

// Инициализация базового функционала модуля
require_once ENGINE_DIR . '/inc/hdlight/init.php';

if ($hdlight->config['cron_key'] && $hdlight->config['cron_key'] == $_GET['key']) {
	$cats = explode(',', $hdlight->config['cron_cats']);
	$black_list = explode(',', $hdlight->config['cron_black_list']);

	if ($hdlight->config['cron_type'] == 2)
		$approve = 'approve = 1';
	elseif ($hdlight->config['cron_type'] == 3)
		$approve = 'approve = 0';

	if ($hdlight->config['cron_white_list']) {
		$searchids = "id IN (" . $hdlight->config['cron_white_list'] . ")";
	}

	if ($cats) {
		$searchcategory = array();
		foreach ($cats as $cat_id) {
			$searchcategory[] = get_sub_cats($cat_id);
		}
		$searchcategory = implode('|', $searchcategory);

		if ($searchcategory)
			$searchcategory = "category" . ($hdlight->config['cron_cats_inverse'] ? ' not' : '') . " regexp '[[:<:]]($searchcategory)[[:>:]]'";
	}

	$threads = 0;
	$result_post = $hdlight->dle_db->query("SELECT * FROM " . PREFIX . "_post WHERE id > 0 " . ($searchcategory ? ' AND ' . $searchcategory : '') . ($approve ? ' AND ' . $approve : '') . ($searchids ? ' OR ' . $searchids : '') . " ORDER BY id DESC");
	while ($post = $result_post->fetch_assoc()) {
		$threads++;

		if (in_array($post['id'], $black_list))
			continue;

		// ----- BEGIN UPDATE POST -----

		// Получаем данные доп. полей
		$xfieldsdata = xfieldsdataload($post['xfields']);
		
		// Получаем ссылку
		$_field_output = explode('|', $hdlight->config['output']);
		if (count($_field_output) == 3) {
			$output = $xfieldsdata[$_field_output[0]];
		} elseif (count($_field_output) == 1) {
			$output = $post[$_field_output[0]];
		}

		// Получаем ID сторонней базы
		$pornodb = false;
		if ($hdlight->config['fields']) {
			$fields = explode('|', $hdlight->config['fields']);

			foreach ($fields as $field_name) {
				$_field = explode('|', $hdlight->config[$field_name]);
				$_value = $xfieldsdata[$_field[0]];

				if ($_value) {
					$out_base_id = $_value;
					$out_base_name = $field_name;

					if ($out_base_name == 'pornolab_id')
						$pornodb = true;
				}
			}
		}
		
		if ($out_base_id && $out_base_name) {
			$response = $hdlight->find($out_base_id, $out_base_name, null, $pornodb);
			if ($response[0] and $response[0]['iframe_url']) {
				foreach ($response as $key => $item) {
					$response[$key]['translator_id'] = intval($item['translator_id']);
				}

				$iframe_url = $response[0]['iframe_url'];

				if ($response[0]['type'] == 'movie')
					$voice_type = 'film';
				else
					$voice_type = 'serial';

				switch ($hdlight->config["{$voice_type}_quality"]) {
					case 1:
						foreach ($response as $item) {
							if (!$item['camrip']) {
								$iframe_url = $item['iframe_url'];
								break;
							}
						}
						break;

					case 2:
						$_result = $hdlight->dle_db->query("SELECT * FROM " . PREFIX . "_hdlight_voice WHERE {$voice_type}_priority > 0 ORDER BY {$voice_type}_priority ASC");
						while ($row = $_result->fetch_assoc()) {
							foreach ($response as $item) {
								if ($item['translator_id'] == $row['voice_id']) {
									$iframe_url = $item['iframe_url'];
									break 2;
								}
							}
						}
						break;

					case 3:
						$_result = $hdlight->dle_db->query("SELECT * FROM " . PREFIX . "_hdlight_voice WHERE {$voice_type}_priority > 0 ORDER BY {$voice_type}_priority ASC");
						while ($row = $_result->fetch_assoc()) {
							foreach ($response as $item) {
								if ($item['translator_id'] == $row['voice_id']) {
									$iframe_url = $item['iframe_url'];

									if ($item['camrip']) {
										foreach ($response as $_item) {
											if ($_item['translator_id'] == $row['voice_id'] && !$_item['camrip']) {
												$iframe_url = $_item['iframe_url'];
												break;
											}
										}
									}

									break 2;
								}
							}
						}
						break;

					case 4:
						foreach ($response as $item) {
							if (!$item['camrip']) {
								$iframe_url = $item['iframe_url'];

								$_result = $hdlight->dle_db->query("SELECT * FROM " . PREFIX . "_hdlight_voice WHERE {$voice_type}_priority > 0 ORDER BY {$voice_type}_priority ASC");
								while ($row = $_result->fetch_assoc()) {
									foreach ($response as $_item) {
										if (!$_item['camrip'] && $_item['translator_id'] == $row['voice_id']) {
											$iframe_url = $_item['iframe_url'];
											break 2;
										}
									}
								}

								break;
							}
						}
						break;
				}

				if ($iframe_url != $output) {
					$_field_output = explode('|', $hdlight->config['output']);
					if (count($_field_output) == 3) {
						$xfieldsdata[$_field_output[0]] = $iframe_url;
						
						$xfields = array();
						foreach ($xfieldsdata as $key => $value) {
							$value = str_replace('|', '&#124;', $value);
							$xfields[] = "$key|$value";
						}
						$xfields = implode('||', $xfields);

						$xfields = addslashes($xfields);
						
						$hdlight->dle_db->query("UPDATE " . PREFIX . "_post SET xfields = '$xfields' WHERE id = '{$post['id']}'");
					} elseif (count($_field_output) == 1) {
						$hdlight->dle_db->query("UPDATE " . PREFIX . "_post SET {$_field_output[0]} = '{$iframe_url}' WHERE id = '{$post['id']}'");
					}

					$result['post'] = $post;
					$result['kinopoisk_id'] = $kinopoisk_id;
					$result['out_base_id'] = $out_base_id;
					$result['out_base_name'] = $out_base_name;

					$added = date('d.m.Y H:i:s', time());

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

					$log_string = " $added [YES] $full_link" . ($result['out_base_id'] ? " ({$result['out_base_name']}: {$result['out_base_id']}) { {$iframe_url} }" : '') . "\r\n";
					file_put_contents(ENGINE_DIR . '/inc/hdlight/reports/cron.log', $log_string, FILE_APPEND | LOCK_EX);
				} else {
					$result['post'] = $post;
					$result['kinopoisk_id'] = $kinopoisk_id;
					$result['out_base_id'] = $out_base_id;
					$result['out_base_name'] = $out_base_name;

					$added = date('d.m.Y H:i:s', time());

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

					$log_string = " $added [NO] $full_link" . ($result['out_base_id'] ? " ({$result['out_base_name']}: {$result['out_base_id']})" : '') . "\r\n";
					file_put_contents(ENGINE_DIR . '/inc/hdlight/reports/cron.log', $log_string, FILE_APPEND | LOCK_EX);
				}
			}
		}

		// ----- END UPDATE POST -----

		if ($threads == $hdlight->config['cron_threads']) {
			$threads = 0;

			if ($hdlight->config['cron_interval'] > 0) {
				ob_flush();
				flush();

				sleep($hdlight->config['cron_interval']);
			}
		}
	}
}