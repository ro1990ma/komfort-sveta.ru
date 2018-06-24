<?php

function remove_hdlight_files() {
	@unlink(ROOT_DIR . '/hdlight_install.php');
	@unlink(ROOT_DIR . '/hdlight_admin_install.php');
	@unlink(ROOT_DIR . '/hdlight_cron.php');
	@unlink(ENGINE_DIR . '/ajax/hdlight.php');
	@unlink(ENGINE_DIR . '/classes/hdlight.class.php');
	@unlink(ENGINE_DIR . '/data/hdlightconfig.php');
	@unlink(ENGINE_DIR . '/inc/hdlight.php');
	@unlink(ENGINE_DIR . '/skins/images/hdlight.png');
	@listdir(ENGINE_DIR . '/inc/hdlight');
	@listdir(ENGINE_DIR . '/skins/hdlight');
}

class hdlight {
	public $config;
	public $dle_config;
	public $dle_db;
	public $dle_tpl;

	public $copyright = 'HD Light <b>0.9.7.4</b>';

	// Массив с типами видео
	public $video_type = array(
		'movie' => 'Фильм',
		'serial' => 'Сериал',
	);

	// Мыссивы доступных полей
	public $fields = array(
		'title' => array(
			'' => '----',
			'title' => 'Заголовок статьи',
		),
		'kinopoisk' => array(
			'' => '----',
		),
		'output' => array(
			'' => '----',
			'short_story' => 'Краткое описание',
			'full_story' => 'Полное описание',
		),
	);

	public function __construct($dle_config, $dle_db, $dle_tpl = null) {
		// Подключаем конфигурацию модуля
		$this->config = require_once ENGINE_DIR . '/data/hdlightconfig.php';

		// Подключаем конфигурацию скрипта
		$this->dle_config = $dle_config;
		
		// Подключение к базе
		$this->dle_db = $dle_db;
		
		// Шаблонизатор скрипта
		$this->dle_tpl = $dle_tpl;

		// Загружаем доп. поля
		$xfields = xfieldsload();
		if ($xfields) foreach ($xfields as $key => $value) {
			$this->fields['title']["{$value[0]}|xfield|{$value[3]}"] = "Доп. поле &raquo; {$value[1]}";
			$this->fields['kinopoisk']["{$value[0]}|xfield|{$value[3]}"] = "Доп. поле &raquo; {$value[1]}";
			$this->fields['output']["{$value[0]}|xfield|{$value[3]}"] = "Доп. поле &raquo; {$value[1]}";
		}
	}

	public function doMain() {
		// Подключаем шаблон главной страницы админпанели модуля
		include_once ENGINE_DIR . '/inc/hdlight/templates/main.php';
	}
	public function doSettings() {
		// Сохранение настроек модуля
		if (isset($_POST['settings'])) {
			// Сохраняем приоритеты озвучек для фильмов
			if (isset($_POST['film_voice_save'])) {
				$arr = explode(',', $_POST['film_voice_save']);
				if ($arr)  {
					$this->dle_db->query("UPDATE " . PREFIX . "_hdlight_voice SET film_priority = '0'");
					
					foreach ($arr as $key => $value) {
						$val = explode('|', $value);

						$val[0] = intval($val[0]);
						$val[1] = intval($val[1]);

						$this->dle_db->query("UPDATE " . PREFIX . "_hdlight_voice SET film_priority = '{$val[0]}' WHERE id = '{$val[1]}'");
					}
				}
			}

			// Сохраняем приоритеты озвучек для сериалов
			if (isset($_POST['serial_voice_save'])) {
				$arr = explode(',', $_POST['serial_voice_save']);
				if ($arr) {
					$this->dle_db->query("UPDATE " . PREFIX . "_hdlight_voice SET serial_priority = '0'");

					foreach ($arr as $key => $value) {
						$val = explode('|', $value);

						$val[0] = intval($val[0]);
						$val[1] = intval($val[1]);

						$this->dle_db->query("UPDATE " . PREFIX . "_hdlight_voice SET serial_priority = '{$val[0]}' WHERE id = '{$val[1]}'");
					}
				}
			}

			// Собираем настройки
			$data = "<?php\r\n\r\nreturn array(";
			foreach ($_POST['settings'] as $key => $value) {
				if ($key == 'allowgroups' || $key == 'cron_cats')
					$value = implode(',', $value);

				if ($key == 'fields')
					$value = implode('|', $value);

				$key = addslashes(trim($key));
				$value = addslashes(trim($value));

				$data .= "\r\n\t'{$key}' => '{$value}',";
			}
			$data .= "\r\n);";
			
			// Записываем настройки в файл
			$fh = fopen(ENGINE_DIR . '/data/hdlightconfig.php', 'w');
			fwrite($fh, $data);
			fclose($fh);

			header('Location: ?mod=hdlight&action=settings&success=1');
		}

		$source = file_get_contents('http://moonwalk.cc/api/translators.json?api_token=' . $this->config['api_token']);
		if ($source) {
			$voice = json_decode($source, true);

			if ($voice) {
				foreach ($voice as $item) {
					$_voice = $this->dle_db->super_query("SELECT * FROM " . PREFIX . "_hdlight_voice WHERE voice_id = '{$item['id']}'");

					if (!$_voice) {
						if ($this->dle_config['charset'] == 'windows-1251')
							$item['name'] = iconv('utf-8', 'windows-1251', $item['name']);

						$voice_id = intval($item['id']);
						$voice_name = $this->dle_db->safesql($item['name']);

						$this->dle_db->query("INSERT INTO " . PREFIX . "_hdlight_voice (id, voice_id, voice_name) VALUES (null, '$voice_id', '$voice_name')");
					}
				}

				$film_voice_use = array();
				$voice_use_result = $this->dle_db->query("SELECT * FROM " . PREFIX . "_hdlight_voice WHERE film_priority > 0 ORDER BY film_priority ASC");
				while ($voice_use_row = $voice_use_result->fetch_assoc()) {
					$film_voice_use[] = $voice_use_row;
				}

				$film_voice_off = array();
				$voice_off_result = $this->dle_db->query("SELECT * FROM " . PREFIX . "_hdlight_voice WHERE film_priority = 0 ORDER BY voice_id ASC");
				while ($voice_off_row = $voice_off_result->fetch_assoc()) {
					$film_voice_off[] = $voice_off_row;
				}

				$serial_voice_use = array();
				$voice_use_result = $this->dle_db->query("SELECT * FROM " . PREFIX . "_hdlight_voice WHERE serial_priority > 0 ORDER BY serial_priority ASC");
				while ($voice_use_row = $voice_use_result->fetch_assoc()) {
					$serial_voice_use[] = $voice_use_row;
				}

				$serial_voice_off = array();
				$voice_off_result = $this->dle_db->query("SELECT * FROM " . PREFIX . "_hdlight_voice WHERE serial_priority = 0 ORDER BY voice_id ASC");
				while ($voice_off_row = $voice_off_result->fetch_assoc()) {
					$serial_voice_off[] = $voice_off_row;
				}

				$voice_settings = true;
			}
		}

		// Подключаем шаблон страницы настроек модуля
		include_once ENGINE_DIR . '/inc/hdlight/templates/settings.php';
	}
	public function doReplace() {
		// Получаем количество записей в базе
		$post = $this->dle_db->super_query("SELECT COUNT(*) as count FROM " . PREFIX . "_post");
		
		// Подключаем шаблон станицы массового проставления ссылок на видео/сериал
		include_once ENGINE_DIR . '/inc/hdlight/templates/replace.php';
	}
	public function doDelete() {
		global $db;

		// Код подключения кнопки поиска на HD Light
$hdlight_code_button = "

/* HD Light Initialization Begin */

// Инициализация базового функционала модуля
require_once ENGINE_DIR . '/inc/hdlight/init.php';

// Присоединяем к выводу доп. полей кнопку поиска на HD Light
\$output .= \$hdlight->get_button();

/* HD Light Initialization End */

";

		// Код подключения вывода плеера HD Light
$hdlight_code_player = "

/* HD Light Add Player Begin */

// Инициализация базового функционала модуля
require_once ENGINE_DIR . '/inc/hdlight/init.php';

// Добавляем плеер
\$hdlight->add_player(\$row);

/* HD Light Add Player Begin */

";

		$addnews_source = file_get_contents(ENGINE_DIR . '/inc/addnews.php');
		$editnews_source = file_get_contents(ENGINE_DIR . '/inc/editnews.php');
		$showfull_source = file_get_contents(ENGINE_DIR . '/modules/show.full.php');

		$addnews_source = str_replace($hdlight_code_button, '', $addnews_source);
		$editnews_source = str_replace($hdlight_code_button, '', $editnews_source);
		$showfull_source = str_replace($hdlight_code_player, '', $showfull_source);

		file_put_contents(ENGINE_DIR . '/inc/addnews.php', $addnews_source);
		file_put_contents(ENGINE_DIR . '/inc/editnews.php', $editnews_source);
		file_put_contents(ENGINE_DIR . '/modules/show.full.php', $showfull_source);

		$db->query("DROP TABLE IF EXISTS " . PREFIX . "_hdlight_voice");

		require_once ENGINE_DIR . '/api/api.class.php';

		$dle_api->uninstall_admin_module('hdlight');

		register_shutdown_function('remove_hdlight_files');

		die("<p>Модуль <b>HD Light</b> успешно удалён из вашего сайта!</p>");
	}

	/**
	 * Поиск через API HDLight
	 *
	 * @param int $out_base_id id видео в сторонней базе
	 * @param string $out_field_id имя поля для id видео в сторонней базе
	 * @param string $title оригинальное название фильма/сериала
	 * @param boolean $pornodb искать в pornodb
	 * @return array список найденых фильмов/сериалов
	 */
	public function find($out_base_id = 0, $out_field_id = '', $title = '', $pornodb = false) {
		// Проверяем включен ли модуль
		if (!$this->config['on'])
			return array('error' => 'module_is_disabled');

		if ($title && $this->dle_config['charset'] == 'windows-1251')
			$title = iconv('windows-1251', 'utf-8', $title);

		// Формируем запрос
		if ($pornodb)
			$api_url = "http://pornodb.co/api/videos.json?api_token={$this->config['api_token']}";
		else
			$api_url = "http://moonwalk.cc/api/videos.json?api_token={$this->config['api_token']}";

		if ($out_base_id && $out_field_id)
			$api_url .= "&{$out_field_id}={$out_base_id}";
		else
			$api_url .= "&title=" . urlencode($title);
		
		// Инициализируем cURL
		if ($ch = curl_init($api_url)) {
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$output = curl_exec($ch);
			curl_close($ch);
			
			if ($output) {
				$response = json_decode($output, true);

				if ($this->config['domain']) {
					foreach ($response as $key => $item) {
						if ($item['iframe_url'])
							$response[$key]['iframe_url'] = preg_replace("#https?://[^/]+#i", $this->config['domain'], $item['iframe_url']);
					}
				}

				return $response;
			} else
				die('Error! cURL result empty');
		} else
			die('Error! cURL not found');
	}
	
	public function get_replace_threads($type, $threads, $last_post_id = array(), $category = '', $category_inverse = false) {
		if ($type == 2)
			$approve = 'approve = 1';
		elseif ($type == 3)
			$approve = 'approve = 0';

		if ($category) {
			$searchcategory = array();
			$category = explode(',', $category);
			foreach ($category as $category_id) {
				$searchcategory[] = get_sub_cats($category_id);
			}
			$searchcategory = implode('|', $searchcategory);

			if ($searchcategory)
				$searchcategory = "category" . ($category_inverse ? ' not' : '') . " regexp '[[:<:]]($searchcategory)[[:>:]]'";
		}
		
		if ($last_post_id) {
			$next_posts_id = array();
			$post_result = $this->dle_db->query("SELECT id FROM " . PREFIX . "_post WHERE id < {$last_post_id}" . ($searchcategory ? ' AND ' . $searchcategory : '') . ($approve ? ' AND ' . $approve : '') . " ORDER BY id DESC LIMIT {$threads}");
			while ($post_row = $post_result->fetch_assoc()) {
				$next_posts_id[] = $post_row['id'];
			}
			
			if ($next_posts_id) {
				$result = array(
					'status' => 'ok',
					'next_posts_id' => $next_posts_id,
				);
			} else
				$result = array(
					'status' => 'end',
				);
		} else {
			$post = $this->dle_db->super_query("SELECT COUNT(*) as count FROM " . PREFIX . "_post WHERE id <> 0 " . ($searchcategory ? ' AND ' . $searchcategory : '') . ($approve ? ' AND ' . $approve : ''));
			if ($post['count']) {
				$next_posts_id = array();
				$post_result = $this->dle_db->query("SELECT id FROM " . PREFIX . "_post WHERE id <> 0 " . ($searchcategory ? ' AND ' . $searchcategory : '') . ($approve ? ' AND ' . $approve : '') . " ORDER BY id DESC LIMIT {$threads}");
				while ($post_row = $post_result->fetch_assoc()) {
					$next_posts_id[] = $post_row['id'];
				}
				
				if ($next_posts_id) {
					$result = array(
						'status' => 'ok',
						'next_posts_id' => $next_posts_id,
						'count' => $post['count'],
					);
				} else
					$result = array(
						'status' => 'end',
					);
			} else
				$result = array(
					'status' => 'end',
					'code' => '#00',
				);
		}
		
		return $result;
	}
	public function replace_thread($post_id, $fields, $rewrite = 0) {
		$post = $this->dle_db->super_query("SELECT * FROM " . PREFIX . "_post WHERE id = {$post_id}");
		
		// Получаем данные доп. полей
		$xfieldsdata = xfieldsdataload($post['xfields']);
		
		// Получаем ссылку
		$_field_output = explode('|', $this->config['output']);
		if (count($_field_output) == 3) {
			$output = $xfieldsdata[$_field_output[0]];
		} elseif (count($_field_output) == 1) {
			$output = $post[$_field_output[0]];
		}
		
		// Получаем ID сторонней базы
		foreach ($fields as $field_name) {
			$_field = explode('|', $this->config[$field_name]);
			$_value = $xfieldsdata[$_field[0]];

			if ($_value) {
				$out_base_id = $_value;
				$out_base_name = $field_name;

				if ($out_base_name == 'pornolab_id')
					$pornodb = true;
			}
		}
		
		if ($out_base_id && $out_base_name) {
			if ($output) {
				if ($rewrite) {
					$response = $this->find($out_base_id, $out_base_name, null, $pornodb);
					if ($response[0] and $response[0]['iframe_url']) {
						foreach ($response as $key => $item) {
							$response[$key]['translator_id'] = intval($item['translator_id']);
						}

						$iframe_url = $response[0]['iframe_url'];

						if ($response[0]['type'] == 'movie')
							$voice_type = 'film';
						else
							$voice_type = 'serial';

						switch ($this->config["{$voice_type}_quality"]) {
							case 1:
								foreach ($response as $item) {
									if (!$item['camrip']) {
										$iframe_url = $item['iframe_url'];
										break;
									}
								}
								break;

							case 2:
								$result = $this->dle_db->query("SELECT * FROM " . PREFIX . "_hdlight_voice WHERE {$voice_type}_priority > 0 ORDER BY {$voice_type}_priority ASC");
								while ($row = $result->fetch_assoc()) {
									foreach ($response as $item) {
										if ($item['translator_id'] == $row['voice_id']) {
											$iframe_url = $item['iframe_url'];
											break 2;
										}
									}
								}
								break;

							case 3:
								$result = $this->dle_db->query("SELECT * FROM " . PREFIX . "_hdlight_voice WHERE {$voice_type}_priority > 0 ORDER BY {$voice_type}_priority ASC");
								while ($row = $result->fetch_assoc()) {
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

										$result = $this->dle_db->query("SELECT * FROM " . PREFIX . "_hdlight_voice WHERE {$voice_type}_priority > 0 ORDER BY {$voice_type}_priority ASC");
										while ($row = $result->fetch_assoc()) {
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

						$_field_output = explode('|', $this->config['output']);
						if (count($_field_output) == 3) {
							$xfieldsdata[$_field_output[0]] = $iframe_url;
							
							$xfields = array();
							foreach ($xfieldsdata as $key => $value) {
								$value = str_replace('|', '&#124;', $value);
								$xfields[] = "$key|$value";
							}
							$xfields = implode('||', $xfields);

							$xfields = addslashes($xfields);
							
							$this->dle_db->query("UPDATE " . PREFIX . "_post SET xfields = '$xfields' WHERE id = {$post_id}");
						} elseif (count($_field_output) == 1) {
							$this->dle_db->query("UPDATE " . PREFIX . "_post SET {$_field_output[0]} = '{$iframe_url}' WHERE id = {$post_id}");
						}
						
						$result = array(
							'status' => 'ok',
						);
					} else
						$result = array(
							'status' => 'error',
							'code' => '#02',
						);
				} else
					$result = array(
						'status' => 'exist',
					);
			} else {
				$response = $this->find($out_base_id, $out_base_name, null, $pornodb);
				if ($response[0] and $response[0]['iframe_url']) {
					foreach ($response as $key => $item) {
						$response[$key]['translator_id'] = intval($item['translator_id']);
					}

					$iframe_url = $response[0]['iframe_url'];

					if ($response[0]['type'] == 'movie')
						$voice_type = 'film';
					else
						$voice_type = 'serial';

					switch ($this->config["{$voice_type}_quality"]) {
						case 1:
							foreach ($response as $item) {
								if (!$item['camrip']) {
									$iframe_url = $item['iframe_url'];
									break;
								}
							}
							break;

						case 2:
							$result = $this->dle_db->query("SELECT * FROM " . PREFIX . "_hdlight_voice WHERE {$voice_type}_priority > 0 ORDER BY {$voice_type}_priority ASC");
							while ($row = $result->fetch_assoc()) {
								foreach ($response as $item) {
									if ($item['translator_id'] == $row['voice_id']) {
										$iframe_url = $item['iframe_url'];
										break 2;
									}
								}
							}
							break;

						case 3:
							$result = $this->dle_db->query("SELECT * FROM " . PREFIX . "_hdlight_voice WHERE {$voice_type}_priority > 0 ORDER BY {$voice_type}_priority ASC");
							while ($row = $result->fetch_assoc()) {
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

									$result = $this->dle_db->query("SELECT * FROM " . PREFIX . "_hdlight_voice WHERE {$voice_type}_priority > 0 ORDER BY {$voice_type}_priority ASC");
									while ($row = $result->fetch_assoc()) {
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

					$_field_output = explode('|', $this->config['output']);
					if (count($_field_output) == 3) {
						$xfieldsdata[$_field_output[0]] = $iframe_url;
						
						$xfields = array();
						foreach ($xfieldsdata as $key => $value) {
							$value = str_replace('|', '&#124;', $value);
							$xfields[] = "$key|$value";
						}
						$xfields = implode('||', $xfields);

						$xfields = addslashes($xfields);
						
						$this->dle_db->query("UPDATE " . PREFIX . "_post SET xfields = '$xfields' WHERE id = {$post_id}");
					} elseif (count($_field_output) == 1) {
						$this->dle_db->query("UPDATE " . PREFIX . "_post SET {$_field_output[0]} = '{$iframe_url}' WHERE id = {$post_id}");
					}
					
					$result = array(
						'status' => 'ok',
					);
				} else
					$result = array(
						'status' => 'error',
						'code' => '#03',
					);
			}
		} else
			$result = array(
				'status' => 'error',
				'code' => '#01',
			);

		$result['post'] = $post;
		$result['kinopoisk_id'] = $kinopoisk_id;
		$result['out_base_id'] = $out_base_id;
		$result['out_base_name'] = $out_base_name;

		if ($result['status'] == 'ok') {
			if ($this->dle_config['allow_alt_url']) {
				if ($this->dle_config['seo_type'] == 1 || $this->dle_config['seo_type'] == 2) {
					$result['post']['category'] = intval($result['post']['category']);
					if ($result['post']['category'] && $this->dle_config['seo_type'] == 2) {
						$full_link = $this->dle_config['http_home_url'] . get_url($result['post']['category']) . "/" . $result['post']['id'] . "-" . $result['post']['alt_name'] . ".html";
					} else {
						$full_link = $this->dle_config['http_home_url'] . $result['post']['id'] . "-" . $result['post']['alt_name'] . ".html";
					}
				} else {
					$full_link = $this->dle_config['http_home_url'] . date('Y/m/d/', $result['post']['date']) . $result['post']['alt_name'] . ".html";
				}
			} else {
				$full_link = $this->dle_config['http_home_url'] . "index.php?newsid=" . $result['post']['id'];
			}

			$log_string = " $full_link" . ($result['out_base_id'] ? " ({$result['out_base_name']}: {$result['out_base_id']})" : '') . "\r\n";
			file_put_contents(ENGINE_DIR . '/inc/hdlight/reports/success.log', $log_string, FILE_APPEND | LOCK_EX);
		}

		if ($result['status'] == 'exist') {
			if ($this->dle_config['allow_alt_url']) {
				if ($this->dle_config['seo_type'] == 1 || $this->dle_config['seo_type'] == 2) {
					$result['post']['category'] = intval($result['post']['category']);
					if ($result['post']['category'] && $this->dle_config['seo_type'] == 2) {
						$full_link = $this->dle_config['http_home_url'] . get_url($result['post']['category']) . "/" . $result['post']['id'] . "-" . $result['post']['alt_name'] . ".html";
					} else {
						$full_link = $this->dle_config['http_home_url'] . $result['post']['id'] . "-" . $result['post']['alt_name'] . ".html";
					}
				} else {
					$full_link = $this->dle_config['http_home_url'] . date('Y/m/d/', $result['post']['date']) . $result['post']['alt_name'] . ".html";
				}
			} else {
				$full_link = $this->dle_config['http_home_url'] . "index.php?newsid=" . $result['post']['id'];
			}

			$log_string = " $full_link" . ($result['out_base_id'] ? " ({$result['out_base_name']}: {$result['out_base_id']})" : '') . "\r\n";
			file_put_contents(ENGINE_DIR . '/inc/hdlight/reports/found.log', $log_string, FILE_APPEND | LOCK_EX);
		}
		
		return $result;
	}

	/**
	 * Вывод кнопки поиска на HDLight
	 *
	 * @return string обработанный в зависимости от версии скрипта шаблон кнопки поиска
	 */
	public function get_button() {
		global $member_id;

		// Проверяем включен ли модуль
		if (!$this->config['on'])
			return '';

		if (!in_array($member_id['user_group'], explode(',', $this->config['allowgroups'])))
			return '';
		
		// Загружаем шаблон кнопки поиска
		ob_start();
		include_once ENGINE_DIR . '/inc/hdlight/templates/button.php';
		$button_template = ob_get_clean();

		// Подключаем вывод кнопки с обработчиком в зависимости от версии DLE
		if ($this->dle_config['version_id'] < 10.2) {
			// для версий ниже 10.2
			$button_template = "<tr>
				<td width=\"140\" height=\"29\" style=\"padding-left:5px;\">HD Light:</td>
				<td>
					{$button_template}
				</td>
			</tr>";
		} else {
			// для версий 10.2 и выше
			$button_template = "<div class=\"form-group\">
				<label class=\"control-label col-lg-2\">HD Light:</label>
				<div class=\"col-lg-10\">
					{$button_template}
				</div>
			</div>";
		}
		
		return $button_template;
	}

	/**
	 * Вставка в основной шаблон кода плеера HDLight
	 *
	 * @param object $tpl шаблонизатор
	 * @param array $row текущая новость
	 */
	public function add_player($row) {
		// Проверяем включен ли модуль
		if (!$this->config['on'])
			return false;
		
		if ($this->config['on'] and $row and strpos($this->dle_tpl->copy_template, '{hdlight-player}') !== false) {
			// Получаем данные доп. полей
			$xfieldsdata = xfieldsdataload($row['xfields']);
			
			// Получаем ссылку
			$_field = explode('|', $this->config['output']);
			if (count($_field) == 3) {
				$hdlight_video_url = $xfieldsdata[$_field[0]];
			} elseif (count($_field) == 1) {
				$hdlight_video_url = $row[$_field[0]];
			}
			
			// Если заполнена ссылка и в настройках задана ширина и высота видео
			if ($hdlight_video_url && $this->config['video_width'] && $this->config['video_height']) {
				// Строим код видео плеера
				$hdlight_player = "<iframe src=\"{$hdlight_video_url}\" width=\"{$this->config['video_width']}\" height=\"{$this->config['video_height']}\" frameborder=\"0\" allowfullscreen></iframe>";
				
				// Заменяем {hdlight-player} на код плеера
				$this->dle_tpl->copy_template = str_replace('{hdlight-player}', $hdlight_player, $this->dle_tpl->copy_template);
				
				// Всё что будет в тегах [hdlight-player]...[/hdlight-player] будет отображаться только если выводится код плеера
				$this->dle_tpl->copy_template = str_replace('[hdlight-player]', '', $this->dle_tpl->copy_template);
				$this->dle_tpl->copy_template = str_replace('[/hdlight-player]', '', $this->dle_tpl->copy_template);
			// Если заполнены не все необходимые параметры
			} else {
				// Удаляем тег плеера из шаблона
				$this->dle_tpl->copy_template = str_replace('{hdlight-player}', '', $this->dle_tpl->copy_template);
				
				// Так же удаляем блок кода плеера, то есть всё что будет в эти тегах [hdlight-player]...[/hdlight-player]
				$this->dle_tpl->copy_template = preg_replace("'\\[hdlight-player\\](.*?)\\[/hdlight-player\\]'is", '', $this->dle_tpl->copy_template);
			}
		}
	}
}