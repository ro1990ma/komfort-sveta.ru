<?php
/**
 * PKinoPoisk for DLE
 * Подк. js
 * @version 2.x.x
 * @copyright Copyright (c) 2012, SpaitNet
 * @package pkinopoisk
 * @subpackage javascript
 */
if (!defined('DATALIFEENGINE')) {
	die("Hacking attempt!");
}

if (get_magic_quotes_runtime()) {
	set_magic_quotes_runtime(false);
}

$encoding = strtolower($config['charset']);
$encoding = ($encoding == 'utf-8' or $encoding == 'utf8') ? 'utf-8' : $encoding;

$config_mod = unserialize(file_get_contents(ENGINE_DIR.'/data/config_pkinopoisk.php'));

$news_id = isset($id) ? intval($id) : 0;
$keyEPS  = $config_mod['conf']['keyenter'] ? 'true' : 'false';

$btn_name = 'Найти на КиноПоиск';
if ($encoding != 'utf-8') {
	if (function_exists('mb_convert_encoding')) {
		$btn_name = mb_convert_encoding($btn_name, $encoding, 'UTF-8');
	} elseif (function_exists('iconv')) {
		$btn_name = iconv('UTF-8', $encoding.'//IGNORE', $btn_name);
	}
}

if (in_array($member_id['user_group'], $config_mod['conf']['group_ap']) !== false or $member_id['user_group'] == 1) {

	$not_status_proc = intval($config_mod['conf']['not_status_proc']);

	$script_pkp = <<<JavaScript
<script type="text/javascript">var config_pkp = {news_id:{$news_id},keyEPS:{$keyEPS},btn_name:'{$btn_name}',not_status_proc:{$not_status_proc}};</script>
<script type="text/javascript" charset="UTF-8" src="/engine/classes/js/pkinopoisk_sc.min.js?v{$config_mod['version']}"></script>
JavaScript;

} else {
	$script_pkp = '';
}

if (isset($is_loged_in) && $is_loged_in) {
	echo $script_pkp;
} elseif ($is_logged) {
	$tpl->copy_template .= $script_pkp;
}
