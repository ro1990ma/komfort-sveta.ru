<?php

if (!defined('DATALIFEENGINE'))
	die('Hacking attempt!');

// Инициализация базового функционала модуля
require_once ENGINE_DIR . '/inc/hdlight/init.php';

if ($member_id['user_group'] != 1)
	die('Hacking attempt!');

switch ($action) {
	// Настройки модуля
	case 'settings':
		$hdlight->doSettings();
		break;

	// Проставление кода по идентификатору кинопоиска всем новостям (если он зполнен)
	case 'replace':
		$hdlight->doReplace();
		break;

	// Удаление модуля
	case 'delete':
		$hdlight->doDelete();
		break;

	// Вывод главной страницы админпанели модуля
	default:
		$hdlight->doMain();
		break;
}