<?php
/**
 * PKinoPoisk for DLE
 * @version 3.x.x
 * @copyright Copyright (c) 2012, SpaitNet
 * @package pkinopoisk
 * @subpackage parser
 */
define('DEBUG', false);

$debug = array();

if (DEBUG) {
	@error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
	@ini_set('error_reporting', E_ALL ^ E_WARNING ^ E_NOTICE);

	@ini_set('display_errors', true);
	@ini_set('html_errors', true);
} else {
	@error_reporting(E_ERROR);
	@ini_set('error_reporting', E_ERROR);

	@ini_set('display_errors', true);
	@ini_set('html_errors', false);
}

@ini_set('max_execution_time', '1800');
@set_time_limit(1800);

define('DATALIFEENGINE', true);
define('ROOT_DIR', realpath(dirname(__FILE__).'/../..'));
define('ENGINE_DIR', ROOT_DIR.'/engine');

if (get_magic_quotes_runtime()) {
	set_magic_quotes_runtime(false);
}

$tio = isset($_POST['tio']) ? intval($_POST['tio']) : false;

/**
 * Pring data
 * @param string $info
 * @param bool $error
 */
function printData($json, $error = false)
{
	global $debug, $parser;

	@header('Content-Type: text/html; charset=UTF-8');

	if (!$error) {

		if (is_string($json)) {
			$json = array('text' => $json);
		}

		if (DEBUG) {
			$json['debug'] = $debug;

			if (isset($parser->debug_list)) {
				$json['debug_parser'] = $parser->debug_list;
			}
		}

		$json = json_encode($json);

		if (DEBUG and function_exists('json_last_error') and json_last_error()) {
			$json = array(
				'debug_json' => json_last_error()
			);

			$json = json_encode($json);
		}

		echo $json;

	} else {

		$json = array('text' => '<span style="color: red;">'.$json.'</span>');
		echo json_encode($json);
		set_file_info('delete');
		exit;

	}
}

/**
 * Set status info
 * @param string $info
 * @return mix
 */
function set_file_info($info)
{
	global $tio;

	if (!$tio) {
		return false;
	}

	if ($info == 'delete') {
		@unlink(ENGINE_DIR.'/cache/pkinopoisk_tio_'.$tio.'.tmp');
	} elseif ($info) {
		if (file_exists(ENGINE_DIR.'/cache/pkinopoisk_tio_'.$tio.'.tmp')) {
			$info = '<br />'.$info;
		}
		$fh = fopen(ENGINE_DIR.'/cache/pkinopoisk_tio_'.$tio.'.tmp', 'a+');
		fwrite($fh, $info);
		fclose($fh);
	}
}

/**
 * Convert charset to utf-8
 * @param string $data
 * @param string $from
 * @param string $to
 * @return string
 */
function convert_charset_pkp($data, $from = 'cp1251', $to = 'utf-8')
{
	if ($from == $to) {
		return $data;
	} elseif (function_exists('mb_convert_encoding')) {
		$data = mb_convert_encoding($data, $to, $from);
	} elseif (function_exists('iconv')) {
		$data = iconv($from, $to.'//IGNORE', $data);
	}

	return $data;
}

/**
 * Заменяем ASCII код в соответствующий html спец. код
 * @param string $text
 * @return string
 */
function convert_charset_ascii_to_code($text)
{
	//http://easywebscripts.net/html/ascii.php
	$sc = array('&#034;', '&#038;', '&#126;', '&#130;', '&#132;', '&#134;', '&#135;', '&#137;', '&#139;', '&#145;', '&#146;', '&#147;', '&#148;', '&#150;', '&#151;', '&#152;', '&#153;', '&#155;', '&#159;', '&#160;', '&#161;', '&#162;', '&#163;', '&#164;', '&#165;', '&#166;', '&#167;', '&#168;', '&#169;', '&#170;', '&#171;', '&#172;', '&#173;', '&#174;', '&#175;', '&#176;', '&#177;', '&#178;', '&#179;', '&#180;', '&#181;', '&#182;', '&#183;', '&#184;', '&#185;', '&#186;', '&#187;', '&#188;', '&#189;', '&#190;', '&#191;', '&#192;', '&#193;', '&#194;', '&#195;', '&#196;', '&#197;', '&#198;', '&#199;', '&#200;', '&#201;', '&#202;', '&#203;', '&#204;', '&#205;', '&#206;', '&#207;', '&#208;', '&#209;', '&#210;', '&#211;', '&#212;', '&#213;', '&#214;', '&#215;', '&#216;', '&#217;', '&#218;', '&#219;', '&#220;', '&#221;', '&#222;', '&#223;', '&#224;', '&#225;', '&#226;', '&#227;', '&#228;', '&#229;', '&#230;', '&#231;', '&#232;', '&#233;', '&#234;', '&#235;', '&#236;', '&#237;', '&#238;', '&#239;', '&#240;', '&#241;', '&#242;', '&#243;', '&#244;', '&#245;', '&#246;', '&#247;', '&#248;', '&#249;', '&#250;', '&#251;', '&#252;', '&#253;', '&#254;', '&#255;', '&#133;');

	$rp = array('&quot;', '&amp;', '&tilde;', '&sbquo;', '&dbquo;', '&dagger;', '&Dagger;', '&permil;', '&lsaquo;', '&lsquo;', '&rsquo;', '&ldquo;', '&rdquo;', '&ndash;', '&mdash;', '&tilde', '&trade;', '&rsaquo;', '&Yuml;', '&nbsp;', '&ixcl;', '&cent;', '&pound;', '&curren;', '&yen;', '&brvbar;', '&sect;', '&uml;', '&copy;', '&ordf;', '&laquo;', '&not;', '&shy;', '&reg;', '&macr;', '&deg;', '&plusmn;', '&sup2;', '&sup3;', '&acuate;', '&micro;', '&para;', '&middot;', '&cedil;', '&sup1;', '&ordm;', '&raquo;', '&frac14;', '&frac12;', '&frac34;', '&iquest;', '&Agrave;', '&Aacute;', '&Acirc;', '&Atilde;', '&Auml;', '&Aring;', '&AElig;', '&Ccedil;', '&Egrave;', '&Eacute;', '&Ecirc;', '&Euml;', '&Igrave;', '&Iacute;', '&Icirс;', '&Iuml;', '&ETH;', '&Ntilde;', '&Ograve;', '&Oacute;', '&Ocirc;', '&Otilde;', '&Ouml;', '&times;', '&Oslash;', '&Ugrave;', '&Uacute;', '&Ucirc;', '&Uuml;', '&Yacute;', '&THORN;', '&szlig;', '&agrave;', '&aacute;', '&acirc;', '&atilde;', '&auml;', '&aring;', '&aelig;', '&ccedil;', '&egrave;', '&eacute;', '&ecirc;', '&euml;', '&igrave;', '&iacute;', '&icirс;', '&eth;', '&eth;', '&ntilde;', '&ograve;', '&oacute;', '&ocirc;', '&otilde;', '&ouml;', '&divide;', '&oslash;', '&ugrave;', '&uacute;', '&ucirc;', '&uuml;', '&yacute;', '&thorn;', '&yuml;', '...',);

	$text = str_replace($sc, $rp, $text);

	return $text;
}

/**
 * Отключаемся от БД, если работы парсера слишлоком долгая
 */
function db_disconnect()
{
	global $db;

	if ($db->db_id !== false) {
		$db->close();
		$db->db_id = false;
	}
}

function db_check_existence_column($table, $column_name)
{
	global $db;

	$existence = false;

	//"SHOW COLUMNS FROM ".PREFIX."_post WHERE Field = 'kp_id_movie'"
	if ($db->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".DBNAME."' AND TABLE_NAME = '".$table."' AND COLUMN_NAME = '".$column_name."'", false) and ($row = $db->get_row()) and isset($row['COLUMN_NAME']) and $row['COLUMN_NAME'] = $column_name) {
		$existence = true;
	}

	db_disconnect();

	return $existence;
}

if (isset($_POST['id']) && $_POST['id'] == 'tio' && $tio) {
	if (file_exists(ENGINE_DIR.'/cache/pkinopoisk_tio_'.$tio.'.tmp')) {
		$time_file = @filemtime(ENGINE_DIR.'/cache/pkinopoisk_tio_'.$tio.'.tmp');
		$time_file = $time_file ? ' ('.(time() - $time_file).' сек.)' : '';
		printData('<span style="color: green;">'.file_get_contents(ENGINE_DIR.'/cache/pkinopoisk_tio_'.$tio.'.tmp').$time_file.'</span>');
	} else {
		printData('finish');
	}
	die();
}

//DLE function
@session_start();
require_once ENGINE_DIR.'/data/config.php';
require_once ENGINE_DIR.'/classes/mysql.php';
require_once ENGINE_DIR.'/data/dbconfig.php';
require_once ENGINE_DIR.'/modules/functions.php';

include ROOT_DIR.'/language/'.$config['langs'].'/website.lng';

$config['charset'] = strtolower($config['charset']);
$config['charset'] = ($config['charset'] == 'utf8' || $config['charset'] == 'utf-8') ? 'utf-8' : $config['charset'];

$prefix_link_tags = 'tags/';

$_TIME = time() + ($config['date_adjust'] * 60);

$user_group = get_vars("usergroup");
if (!$user_group) {
	$user_group = array();
	$db->query("SELECT * FROM ".USERPREFIX."_usergroups ORDER BY id ASC");
	while ($row = $db->get_row()) {
		$user_group[$row['id']] = array();
		foreach ($row as $key => $value) {
			$user_group[$row['id']][$key] = stripslashes($value);
		}
	}
	set_vars("usergroup", $user_group);
	$db->free();
}
require_once ENGINE_DIR.'/modules/sitelogin.php';

db_disconnect();

//Mod main
$config_mod = unserialize(file_get_contents(ENGINE_DIR.'/data/config_pkinopoisk.php'));

if (!$is_logged or (in_array($member_id['user_group'], $config_mod['conf']['group_ap']) === false and $member_id['user_group'] != 1)) {
	printData('Вам не разрешено пользоваться данным модулем!', true);
}

@session_write_close();

require_once ENGINE_DIR.'/classes/thumb.class.php';
define('FOLDER_PREFIX', date("Y-m"));

$news_id = isset($_POST['news_id']) ? intval($_POST['news_id']) : 0;
$author  = $member_id['name'];
$time_ex = time();

if (get_magic_quotes_gpc()) {
	$_POST['film_name'] = stripslashes($_POST['film_name']);
}

$title = trim($_POST['title']);
$sact  = isset($_POST['sact']) ? trim(strip_tags($_POST['sact'])) : false;

if (empty($title)) {
	printData('Для поиска введите название фильма в заголовок статьи!', true);
}

$config_mod['conf']['sleep'] = intval($config_mod['conf']['sleep']);

if ($config_mod['conf']['jpeg_quality'] <= 0) {
	$config_mod['conf']['jpeg_quality'] = $config['jpeg_quality'];
}

if (!empty($config_mod['conf']['http_site'])) {
	$config['http_home_url'] = $config_mod['conf']['http_site'];
}


/**
 * Class Parser
 * Класс парсера. Работает через curl, поддерживает post.
 */
class Parser
{
	const PROXY_TYPE_HTML = 1;
	const PROXY_TYPE_SOCKS5 = 2;

	public $debug = false;
	public $debug_list = array();

	private $_curl_timeout = 120;

	private $_user_agent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:24.0) Gecko/20100101 Firefox/24.0';
	private $_proxy_type = false;
	private $_proxy = '';
	private $_cookie_file = '';

	private $_dir_cache = false;
	public $cached = false;

	/**
	 * Максимальное количество редиректов, актуально если не работает опция CURLOPT_FOLLOWLOCATION
	 * @var int
	 */
	private $_redirect_max = 5;
	private $_redirect_current = 0;

	private $_sleep = 2;
	private $_last_active_time = 0;

	/**
	 * Ссылка на обработчик cUrl
	 * @var bool|resource
	 */
	private $_ch = false;

	public function initClose()
	{
		curl_close($this->_ch);
		$this->_ch = false;
	}

	/**
	 * Устанавливает файл cookie, если файла нет создает
	 * @param $cookie_file
	 * @param bool $new Если true, старый файл будет очищен
	 */
	public function setCoolieFile($cookie_file, $new = false)
	{
		if (!$new and file_exists($cookie_file)) {

			$this->_cookie_file = $cookie_file;

		} elseif (file_put_contents($cookie_file, ' ', LOCK_EX)) {

			chmod($cookie_file, 0666);
			$this->_cookie_file = $cookie_file;

		} else {

			$this->_cookie_file = null;

		}

		if ($this->_ch) {
			curl_setopt($this->_ch, CURLOPT_COOKIEJAR, $this->_cookie_file);
			curl_setopt($this->_ch, CURLOPT_COOKIEFILE, $this->_cookie_file);
		}

	}

	/**
	 * Устанавливает время задержки между запросами
	 * @param int $sleep Время задержки в сек.
	 */
	public function setSleep($sleep)
	{
		$sleep        = intval($sleep);
		$this->_sleep = $sleep > 0 ? $sleep : 0;
	}

	/**
	 * Устанавливает user agent для запроса.
	 * @param string $user_agent
	 */
	public function setUserAgent($user_agent)
	{
		$this->_user_agent = $user_agent;

		if ($this->_ch) {
			curl_setopt($this->_ch, CURLOPT_USERAGENT, $this->_user_agent);
		}
	}

	/**
	 * Устанавливает путь до кеша для запроса.
	 * @param string $dir_cache
	 */
	public function setDirCache($dir_cache)
	{
		if (is_dir($dir_cache)) {
			$this->_dir_cache = $dir_cache;
		}
	}

	/**
	 * Устанавливает прокси
	 * @param $proxy
	 * @param $proxy_type Может принимать self::PROXY_TYPE_HTML или self::PROXY_TYPE_SOCKS5
	 */
	public function setProxy($proxy, $proxy_type)
	{
		if (!empty($proxy)) {
			if ($proxy_type == self::PROXY_TYPE_HTML) {

				$this->_proxy_type = self::PROXY_TYPE_HTML;
				$this->_proxy      = $proxy;

			} elseif ($proxy_type == self::PROXY_TYPE_SOCKS5) {

				$this->_proxy_type = self::PROXY_TYPE_SOCKS5;
				$this->_proxy      = $proxy;
			}
		}
	}

	/**
	 * Инициализирует curl, создает обработчик curl
	 */
	private function _initCurl()
	{
		if (!$this->_ch) {

			$this->_ch = curl_init();

			curl_setopt($this->_ch, CURLOPT_USERAGENT, $this->_user_agent);
			@curl_setopt($this->_ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($this->_ch, CURLOPT_TIMEOUT, $this->_curl_timeout);
			curl_setopt($this->_ch, CURLOPT_COOKIEJAR, $this->_cookie_file);
			curl_setopt($this->_ch, CURLOPT_COOKIEFILE, $this->_cookie_file);

			if ($this->_proxy_type == self::PROXY_TYPE_HTML) {
				curl_setopt($this->_ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC | CURLAUTH_NTLM);
				curl_setopt($this->_ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
				curl_setopt($this->_ch, CURLOPT_PROXY, $this->_proxy);
			} elseif ($this->_proxy_type == self::PROXY_TYPE_SOCKS5) {
				curl_setopt($this->_ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
				curl_setopt($this->_ch, CURLOPT_PROXY, $this->_proxy);
			}

			$curl_version = curl_version();
			if (preg_match('#^([0-9]+)\.([0-9]+)#is', $curl_version['version'], $match) and $match[1] >= 7 and $match[2] >= 10) {
				if (function_exists('gzdecode')) {
					curl_setopt($this->_ch, CURLOPT_ENCODING, 'gzip, deflate');
				} else {
					curl_setopt($this->_ch, CURLOPT_ENCODING, '');
				}
			}

			curl_setopt($this->_ch, CURLOPT_HTTPHEADER, array(
				'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
				'Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
				'Cache-Control:	max-age=0',
				'DNT: 0',
				'Connection: keep-alive',
				'Proxy-Connection:'
			));

			curl_setopt($this->_ch, CURLOPT_HEADER, 1);

		}
	}

	/**
	 * Загружает страницу. Публичный
	 * @param $url
	 * @param bool $refer
	 * @param bool $post
	 * @return bool|mixed|string
	 */
	public function loadPage($url, $refer = false, $post = false)
	{
		if (($page = $this->_getCache($url.http_build_query($post))) and !$this->debug) {
			return $page;
		}

		$this->_redirect_current = 0;

		$this->_initCurl();

		if ($refer) {
			curl_setopt($this->_ch, CURLOPT_REFERER, $refer);
		} else {
			curl_setopt($this->_ch, CURLOPT_REFERER, null);
		}

		if ($post) {
			curl_setopt($this->_ch, CURLOPT_POST, true);
			curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $post);
		} else {
			curl_setopt($this->_ch, CURLOPT_POST, false);
			curl_setopt($this->_ch, CURLOPT_HTTPGET, true);
		}

		$page = $this->_load($url);

		$this->_setCache($url.http_build_query($post), $page);

		return $page;
	}

	private function _load($url)
	{
		if ($this->_redirect_current >= $this->_redirect_max) {
			return false;
		}

		if ($this->_sleep > 0) {
			if (($this->_last_active_time + $this->_sleep) > time()) {
				sleep($this->_sleep);
			}
			$this->_last_active_time = time();
		}

		if ($this->debug) {
			curl_setopt($this->_ch, CURLINFO_HEADER_OUT, true);
		}

		curl_setopt($this->_ch, CURLOPT_URL, $url);

		$page = curl_exec($this->_ch);

		$curl_info = curl_getinfo($this->_ch);

		if ($this->debug) {
			$this->debug_list[] = array(
				'url'            => $url,
				'request_header' => $curl_info['request_header'],
				'header'         => substr($page, 0, $curl_info['header_size']),
				'body'           => convert_charset_pkp(substr($page, $curl_info['header_size']), $from = 'cp1251', $to = 'utf-8'),
				'curl_errno'     => curl_errno($this->_ch),
				'curl_error'     => curl_error($this->_ch)
			);
		}

		if ($curl_info['http_code'] == 301 or $curl_info['http_code'] == 302) {

			$header = substr($page, 0, $curl_info['header_size']);

			if (preg_match('/Location:(.*?)(\n|$)/is', $header, $matches) and ($url = parse_url(trim($matches[1])))) {
				$url['scheme'] = $url['scheme'] ? $url['scheme'] : 'http';
				$url['host']   = $url['host'] ? $url['host'] : 'www.kinopoisk.ru';
				$url['path']   = $url['path'] ? $url['path'] : '';
				$url['query']  = $url['query'] ? '?'.$url['query'] : '';

				$url = $url['scheme'].'://'.$url['host'].$url['path'].$url['query'];

				$this->_redirect_current++;

				return $this->_load($url);
			} else {
				return false;
			}
		}

		$page = substr($page, $curl_info['header_size']);

		return $page;

	}

	/**
	 * Записывает данные в кеш
	 * @param $url
	 * @param $page
	 * @return bool|int
	 */
	private function _setCache($url, $page)
	{
		if ($this->cached and $this->_dir_cache) {
			return file_put_contents($this->_dir_cache.'/parser_cache_page_'.md5($url).'.tmp', $page, LOCK_EX);
		}

		return false;
	}

	/**
	 * Берет данные из кеша
	 * @param $url
	 * @return bool|string
	 */
	private function _getCache($url)
	{
		if ($this->cached and $this->_dir_cache) {
			$file = $this->_dir_cache.'/parser_cache_page_'.md5($url).'.tmp';

			if (file_exists($file)) {
				return file_get_contents($file);
			}
		}

		return false;
	}
}

$parser        = new Parser();
$parser->debug = DEBUG;
$parser->setDirCache(ENGINE_DIR.'/cache');
$parser->cached = true;

$proxy_type = 0;
if ($config_mod['conf']['proxy_type'] == 1) {
	$proxy_type = Parser::PROXY_TYPE_HTML;
} elseif ($config_mod['conf']['proxy_type'] == 2) {
	$proxy_type = Parser::PROXY_TYPE_SOCKS5;
}
$parser->setProxy($config_mod['conf']['proxy'], $proxy_type);

if (isset($_SERVER['HTTP_USER_AGENT']) && strlen($_SERVER['HTTP_USER_AGENT']) > 25) {
	$parser->setUserAgent($_SERVER['HTTP_USER_AGENT']);
}

define('MOD_COOKIE_FILE', ENGINE_DIR.'/cache/system/cookie_www.kinopoisk.ru.tmp');

/**
 * Удаляем файл cookie и создаем пустой новый файл
 */
function clear_cookie_file()
{
	@unlink(MOD_COOKIE_FILE);
	$fh = fopen(MOD_COOKIE_FILE, 'w');
	fwrite($fh, '');
	fclose($fh);
	chmod(MOD_COOKIE_FILE, 0666);
}

if (!is_file(MOD_COOKIE_FILE) or filemtime(MOD_COOKIE_FILE) < (time() - 43200)) {
	clear_cookie_file();
}

$parser->setCoolieFile(MOD_COOKIE_FILE);

function uploads_pic($urlcopy, $name, $refer, $tumb = '', $tumb_t = '', $wmo = '', $ico = '', $img_size = false)
{
	global $author, $news_id, $db, $config_mod, $config, $parser;

	if (!is_dir(ROOT_DIR."/uploads/posts/".FOLDER_PREFIX)) {
		@mkdir(ROOT_DIR."/uploads/posts/".FOLDER_PREFIX, 0777);
		@chmod(ROOT_DIR."/uploads/posts/".FOLDER_PREFIX, 0777);
		@mkdir(ROOT_DIR."/uploads/posts/".FOLDER_PREFIX."/thumbs", 0777);
		@chmod(ROOT_DIR."/uploads/posts/".FOLDER_PREFIX."/thumbs", 0777);
	}

	$image_name_full  = FOLDER_PREFIX."/".$name;
	$image_name_thumb = FOLDER_PREFIX."/thumbs/".$name;

	//Загружаем
	if (!($page = $parser->loadPage($urlcopy, $refer)) or !file_put_contents(ROOT_DIR."/uploads/posts/".$image_name_full, $page)) {
		unset($page);

		return false;
	}
	unset($page);

	$imag_info = @getimagesize(ROOT_DIR."/uploads/posts/".$image_name_full);

	//Проверяем размер изображения
	if ($imag_info[0] < 10 or $imag_info[1] < 10 or ($ico == '1' and ($imag_info[0] - 155) < 10) or ($ico == '2' and ($imag_info[1] - 37) < 10) or ($ico == '3' and $imag_info[0] <= $imag_info[1] and ($imag_info[1] - 37) < 10) or ($ico == '3' and $imag_info[0] > $imag_info[1] and ($imag_info[0] - 155) < 10)) {
		@unlink(ROOT_DIR."/uploads/posts/".$image_name_full);

		return false;
	}

	if ($ico == '1' || $ico == '2' || $ico == '3') {
		if ($imag_info[2] == 2) {
			$imag_src = @imagecreatefromjpeg(ROOT_DIR."/uploads/posts/".$image_name_full);
		} elseif ($imag_info[2] == 1) {
			$imag_src = @imagecreatefromgif(ROOT_DIR."/uploads/posts/".$image_name_full);
		} else {
			$imag_src = @imagecreate($imag_info[0], $imag_info[1]);
		}

		if ($ico == '1') {
			$imag_des = @imagecreatetruecolor($imag_info[0] - 155, $imag_info[1]);
			@imagecopy($imag_des, $imag_src, 0, 0, 0, 0, $imag_info[0] - 155, $imag_info[1]);
		} elseif ($ico == '2') {
			$imag_des = @imagecreatetruecolor($imag_info[0], $imag_info[1] - 37);
			@imagecopy($imag_des, $imag_src, 0, 0, 0, 0, $imag_info[0], $imag_info[1] - 37);
		} elseif ($ico == '3') {
			if ($imag_info[0] <= $imag_info[1]) {
				$imag_des = @imagecreatetruecolor($imag_info[0], $imag_info[1] - 37);
				@imagecopy($imag_des, $imag_src, 0, 0, 0, 0, $imag_info[0], $imag_info[1] - 37);
			} else {
				$imag_des = @imagecreatetruecolor($imag_info[0] - 155, $imag_info[1]);
				@imagecopy($imag_des, $imag_src, 0, 0, 0, 0, $imag_info[0] - 155, $imag_info[1]);
			}
		}

		@imagejpeg($imag_des, ROOT_DIR."/uploads/posts/".$image_name_full, 100);
		@imagedestroy($imag_des);
		@imagedestroy($imag_src);
	}

	if ($config_mod['conf']['max_up_side'] != 0) {
		$imag = new thumbnail(ROOT_DIR."/uploads/posts/".$image_name_full);
		$imag->size_auto($config_mod['conf']['max_up_side'], 0);
		$imag->jpeg_quality($config_mod['conf']['jpeg_quality']);
		$imag->save(ROOT_DIR."/uploads/posts/".$image_name_full);
	}

	if ($img_size) {
		$img_size = explode('x', $img_size);

		if (count($img_size) == 2 and ($img_size[0] > 0 or $img_size[1] > 0)) {

			if ($img_size[0] > 0 and $img_size[1] > 0) {
				$img_size = $img_size[0].'x'.$img_size[1];
				$site     = 0;
			} elseif ($img_size[0] > 0) {
				$img_size = $img_size[0];
				$site     = 1;
			} else {
				$img_size = $img_size[1];
				$site     = 2;
			}

			$imag = new thumbnail(ROOT_DIR."/uploads/posts/".$image_name_full);
			$imag->size_auto($img_size, $site);
			$imag->jpeg_quality($config_mod['conf']['jpeg_quality']);
			$imag->save(ROOT_DIR."/uploads/posts/".$image_name_full);

		}
	}

	if ($tumb != 0) {
		if (!isset($imag)) {
			$imag = new thumbnail(ROOT_DIR."/uploads/posts/".$image_name_full);
		} else {
			$imag->thumbnail(ROOT_DIR."/uploads/posts/".$image_name_full);
		}
		$imag->size_auto($tumb, $tumb_t);
		$imag->jpeg_quality($config_mod['conf']['jpeg_quality']);

		if ($wmo) {
			$imag->insert_watermark($config['max_watermark']);
		}

		$imag->save(ROOT_DIR."/uploads/posts/".$image_name_thumb);
	}

	if ($wmo) {
		if (!isset($imag)) {
			$imag = new thumbnail(ROOT_DIR."/uploads/posts/".$image_name_full);
		} else {
			$imag->thumbnail(ROOT_DIR."/uploads/posts/".$image_name_full);
		}
		$imag->insert_watermark($config['max_watermark']);
		$imag->save(ROOT_DIR."/uploads/posts/".$image_name_full);
	}

	@chmod(ROOT_DIR."/uploads/posts/".$image_name_full, 0666);

	$row = $db->super_query("SELECT COUNT(*) as count FROM ".PREFIX."_images where author='$author' AND news_id='$news_id'");
	if (!$row['count']) {
		$added_time = time() + ($config['date_adjust'] * 60);
		$inserts    = $image_name_full;
		$db->query("INSERT INTO ".PREFIX."_images (images, author, news_id, date) values ('$inserts', '$author', '$news_id', '$added_time')");
	} else {
		$row = $db->super_query("SELECT images  FROM ".PREFIX."_images where author = '$author' AND news_id = '$news_id'");
		if ($row['images'] == "") {
			$listimages = array();
		} else {
			$listimages = explode("|||", $row['images']);
		}
		$listimages[]  = $image_name_full;
		$row['images'] = implode("|||", $listimages);
		$db->query("UPDATE ".PREFIX."_images set images='{$row['images']}' where author = '$author' AND news_id = '$news_id'");
		$db->free();
	}

	db_disconnect();

	return $config['http_home_url'].'uploads/posts/'.$image_name_full;
}

function uploads_trailer($url, $name, $refer)
{
	global $author, $news_id, $db, $_TIME, $config_mod;

	$onserver = time().'_'.$name;

	$stoption = array(
		'HTTP' => array(
			'timeout' => 600
		)
	);
	if ($config_mod['conf']['proxy_type'] > 0 && !empty($config_mod['conf']['proxy'])) {
		$stoption['HTTP']['proxy']           = $config_mod['conf']['proxy'];
		$stoption['HTTP']['request_fulluri'] = true;
	}
	$scc = stream_context_create($stoption);

	if ($file = file_get_contents($url, false, $scc)) {
		$fh = fopen(ROOT_DIR.'/uploads/files/'.$onserver, 'w+');
		fwrite($fh, $file);
		fclose($fh);
		unset($file);

		$db->query("INSERT INTO ".PREFIX."_files (news_id, name, onserver, author, date) values ('".$news_id."', '".$name."', '".$onserver."', '".$author."', '".$_TIME."')");

		db_disconnect();

		return '/uploads/files/'.$onserver;
	} else {
		return false;
	}
}

function trailer_to_youtube($url, $name, $title)
{
	global $author, $news_id, $db, $_TIME, $config_mod, $config;

	$stoption = array(
		'HTTP' => array(
			'timeout' => 600
		)
	);
	if ($config_mod['conf']['proxy_type'] > 0 && !empty($config_mod['conf']['proxy'])) {
		$stoption['HTTP']['proxy']           = $config_mod['conf']['proxy'];
		$stoption['HTTP']['request_fulluri'] = true;
	}
	$scc = stream_context_create($stoption);

	if ($file = file_get_contents($url, false, $scc)) {
		$ftmp = ENGINE_DIR.'/cache/pkinopoisk_trailer_'.md5($url).'.tmp';
		$fh   = fopen($ftmp, 'w+');
		fwrite($fh, $file);
		fclose($fh);
		chmod($ftmp, 0666);
		unset($file);

		$mime = false;
		if (function_exists('finfo_open')) {
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mime  = finfo_file($finfo, $ftmp);
			finfo_close($finfo);

			if (stripos($mime, 'video') === false) {
				return false;
			}
		} else {
			$rs = strtolower(substr($name, strrpos($name, '.') + 1));
			switch ($rs) {
				case 'flv':
					$mime = 'video/x-flv';
					break;
				case 'mp4':
					$mime = 'video/quicktime';
					break;
				default:
					$mime = 'video/quicktime';
					break;
			}

		}

		$fs = round((filesize($ftmp) / 1048576), 2).'Мб';
		set_file_info('Размер трейлера '.$fs);
		set_file_info('Загружаем трейлер на YouTube');

		require_once ENGINE_DIR.'/classes/Zend/Loader.php';
		Zend_Loader::loadClass('Zend_Gdata_YouTube');
		Zend_Loader::loadClass('Zend_Gdata_ClientLogin');

		$httpClient = Zend_Gdata_ClientLogin::getHttpClient($config_mod['conf']['trailer_yt_login'], $config_mod['conf']['trailer_yt_pass'], 'youtube', null, $config['home_title'], null, null, 'https://www.google.com/youtube/accounts/ClientLogin');
		$httpClient->setHeaders('X-GData-Key', 'key='.$config_mod['conf']['trailer_yt_key']);

		$yt           = new Zend_Gdata_YouTube($httpClient);
		$myVideoEntry = new Zend_Gdata_YouTube_VideoEntry();

		$filesource = $yt->newMediaFileSource($ftmp);
		if ($mime) {
			$filesource->setContentType($mime);
		}

		$filesource->setSlug($name);

		$myVideoEntry->setMediaSource($filesource);

		$mediaGroup        = $yt->newMediaGroup();
		$mediaGroup->title = $yt->newMediaTitle()->setText($title);
		//$mediaGroup->description = $yt->newMediaDescription()->setText('My description');

		$mediaGroup->category = array(
			$yt->newMediaCategory()->setText('Film')->setScheme('http://gdata.youtube.com/schemas/2007/categories.cat'),
			$yt->newMediaCategory()->setText('mydevelopertag')->setScheme('http://gdata.youtube.com/schemas/2007/developertags.cat'),
			$yt->newMediaCategory()->setText('anotherdevelopertag')->setScheme('http://gdata.youtube.com/schemas/2007/developertags.cat')
		);

		$mediaGroup->keywords = $yt->newMediaKeywords()->setText('трейлер');

		$myVideoEntry->mediaGroup = $mediaGroup;

		try {
			$newEntry = $yt->insertEntry($myVideoEntry, 'http://uploads.gdata.youtube.com/feeds/users/default/uploads', 'Zend_Gdata_YouTube_VideoEntry');
			$yt_id    = $newEntry->getId()->getText('_text');
			$yt_id    = substr($yt_id, strrpos($yt_id, '/') + 1);
			unlink($ftmp);

			return $yt_id;
		} catch (Zend_Gdata_App_Exception $e) {
			unlink($ftmp);
			printData($e->getMessage(), true);

			return false;
		}
	} else {
		return false;
	}
}

function login_kp()
{
	global $config_mod, $parser;

	$config_mod['conf']['kp_user'] = convert_charset_pkp($config_mod['conf']['kp_user'], 'UTF-8', 'cp1251');
	$config_mod['conf']['kp_pass'] = convert_charset_pkp($config_mod['conf']['kp_pass'], 'UTF-8', 'cp1251');

	$time      = time();
	$filemtime = @filemtime(MOD_COOKIE_FILE) + (60 * 60 * 24 * 7);
	$coockie   = @file_get_contents(MOD_COOKIE_FILE);
	$chsstrpos = strpos($coockie, $config_mod['conf']['kp_user']);

	if ($config_mod['conf']['kp_user'] != '' and $config_mod['conf']['kp_pass'] != '' and ($time >= $filemtime or !($coockie) or $chsstrpos === false)) {
		set_file_info('Логинимся на КП');

		clear_cookie_file();

		$parser->cached = false;

		$parser->loadPage('http://www.kinopoisk.ru/login/', 'http://www.kinopoisk.ru/');
		$parser->loadPage('http://www.kinopoisk.ru/login/', 'http://www.kinopoisk.ru/login/', 'shop_user[login]='.$config_mod['conf']['kp_user'].'&shop_user[pass]='.$config_mod['conf']['kp_pass'].'&shop_user[mem]=on&auth=%E2%EE%E9%F2%E8+%ED%E0+%F1%E0%E9%F2');

		$parser->cached = true;
	}
}


/**
 * Формирует список изображений
 * @param $kp_id
 * @param $type
 * @param int $page_num
 * @return array
 */
function getListImageKP($kp_id, $type, $page_num = 1)
{
	global $parser;

	$page_num = $page_num > 1 ? 'page/'.$page_num.'/' : '';
	$page     = $parser->loadPage('http://www.kinopoisk.ru/film/'.$kp_id.'/'.$type.'/'.$page_num, 'http://www.kinopoisk.ru/film/'.$kp_id.'/');

	$page = convert_charset_pkp($page, 'cp1251', 'utf-8');

	$list = array();

	if (preg_match_all('#<a href="/picture/([0-9]+?)/"><img[^>]{1,5}?src="([^"]+?)"[^>]+?></a>.{1,5}?<b><i>(.*?)</i><a[^>]+?></a>(.*?)</b>#is', $page, $match, PREG_SET_ORDER)) {
		foreach ($match as $val) {
			$list[] = array(
				'id'           => $val[1],
				'preview_link' => $val[2],
				'side'         => $val[3],
				'size'         => $val[4]
			);
		}
	}

	return $list;
}

/**
 * Поиск постера/трейлера к фильму
 * @param int $kp_id
 * @param string $type
 * @param int $page_num
 * @return string
 */
function buildChooseImage($kp_id, $type, $page_num = 1)
{
	global $parser;

	$buf = '';

	$list = getListImageKP($kp_id, $type, $page_num);

	/*
	 * Если постеры, добавляем постер с страницм фильма
	 */
	if ($type == 'posters') {
		$page = $parser->loadPage('http://www.kinopoisk.ru/film/'.$kp_id.'/', 'http://www.kinopoisk.ru/');

		if (preg_match('#openImgPopup\(\'(.*?)\'\);#si', $page, $match)) {

			if (substr($match[1], 0, 1) == '/') {
				$match[1] = 'http://www.kinopoisk.ru'.$match[1];
			}

			array_unshift($list, array(
				'id'           => $match[1],
				'preview_link' => $match[1],
				'side'         => '509x755',
				'size'         => ''
			));
		}
	}

	$page_num = $page_num > 1 ? 'page/'.$page_num.'/' : '';

	foreach ($list as $img) {
		$pars_img = 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAIBAQIBAQICAgICAgICAwUDAwMDAwYEBAMFBwYHBwcGBwcICQsJCAgKCAcHCg0KCgsMDAwMBwkODw0MDgsMDAz/2wBDAQICAgMDAwYDAwYMCAcIDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAz/wAARCAABAAEDASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD9/KKKKAP/2Q==';

//		if (stristr($img['preview_link'], 'kp.yandex.') !== false) {
		$buf .= '<li data-image_id="'.$img['id'].'" data-choosed="0"><img src="'.$img['preview_link'].'" data-loaded="1"><br>'.$img['side'].'  '.$img['size'].'<div class="num"></div></li>';
//		} else {
//			$buf .= '<li data-image_id="'.$img['id'].'" data-choosed="0"><img src="'.$pars_img.'" data-kp_id="'.$kp_id.'" data-type="'.$type.'" data-page_num="'.$page_num.'" data-link="'.$img['preview_link'].'" data-loaded="0"><br>'.$img['side'].'  '.$img['size'].'<div class="num"></div></li>';
//		}

	}

	return $buf;
}

if ($sact == 'load_image') {

	$json['text'] = '';

	$link  = isset($_POST['link']) ? filter_var($_POST['link'], FILTER_SANITIZE_URL) : fales;
	$kp_id = isset($_POST['kp_id']) ? intval($_POST['kp_id']) : 0;
	$type  = isset($_POST['type']) ? filter_var($_POST['type'], FILTER_SANITIZE_STRING) : false;

	if ($link and $kp_id > 0 and $type) {

		$page_num = isset($_POST['page_num']) ? intval($_POST['page_num']) : 0;
		$page_num = $page_num > 1 ? 'page/'.$page_num.'/' : '';

		$pars_img = $parser->loadPage($link, 'http://www.kinopoisk.ru/film/'.$kp_id.'/'.$type.'/'.$page_num);
		$pars_img = 'data:image/jpeg;base64,'.chunk_split(base64_encode($pars_img));

		$json['text'] = $pars_img;

	}

	printData($json);

}

if ($_POST['id'] == 'list' and (preg_match('#^\*([0-9]+)$#', $title, $match) or preg_match('#kinopoisk.ru/film/([0-9]+)(/|$)#', $title, $match))) {
	/**
	 * Проверяем название является прямой ссылкой/id фильма
	 */
	$_POST['id'] = $match[1];

	//"SHOW COLUMNS FROM ".PREFIX."_post WHERE Field = 'kp_id_movie'"
	if ($news_id == 0 and db_check_existence_column(PREFIX.'_post', 'kp_id_movie') and ($row = $db->super_query("SELECT id FROM ".PREFIX."_post WHERE kp_id_movie = '".intval($_POST['id'])."'"))) {
		$onclick = 'PKP.getUp(\''.$news_id.'\')';

		$json['text'] = 'ID KP '.$_POST['id'].' в базе уже есть:[ <a href="/'.$config['admin_path'].'?mod=editnews&action=editnews&id='.$row['id'].'" target="_blank">админ</a> ] [ <a href="/index.php?newsid='.$row['id'].'" target="_blank">сайт</a> ] [ <a href="#" onClick="'.$onclick.'; return false;">парсить</a> ] ';

		$_POST['id'] = false;
		$sact        = false;

		printData($json);

	} else {

		$sact = 'get_up';

	}

}

if ($sact == 'get_up' and ($id = intval($_POST['id'])) > 0) {
	/**
	 * Проверяем влючена или нет функция выбора перед парсингом
	 */
	$buf = '';

	if ($config_mod['conf']['pars_poster_choose'] or $config_mod['conf']['pars_kadr_choose']) {
		$page = $parser->loadPage('http://www.kinopoisk.ru/film/'.$id.'/', 'http://www.kinopoisk.ru/');
	}

	if ($config_mod['conf']['pars_poster_choose'] and (strpos($page, '/film/'.$id.'/posters/') != false or preg_match('#openImgPopup\(\'(.*?)\'\);#si', $page, $match))) {
		$buf .= <<<HTML
<div id="poster_choose" data-load='0'>
<a href="#" onclick="PKP.choosePoster({$id});return false;"><b>Выберите основной постер</b></a> выбрано <span class="num_all">0</span>  <a href="#" class="stop_load_image">остановить загрузку предпросмотра</a>
<ul id="poster_choose_list" class="list"></ul>
</div>
HTML;
	}

	if ($config_mod['conf']['pars_kadr_choose'] and strpos($page, '/film/'.$id.'/stills/') != false) {
		$buf .= <<<HTML
<div id="kadr_choose" data-load='0'>
<a href="#" onclick="PKP.chooseKadr({$id});return false;"><b>Выберите кадры для загрузки</b></a> выбрано <span class="num_all">0</span>  <a href="#" class="stop_load_image">остановить загрузку предпросмотра</a>
<ul id="kadr_choose_list" class="list"></ul>
</div>
HTML;
	}

	if (!empty($buf)) {
		$json['text'] = <<<HTML
<style>
#pkinopoisk_result .list {
	list-style: none outside none;
	margin: 0;
	padding: 0;
}
#pkinopoisk_result .list li img {
	border: none;
	opacity: 0.7;
	width: 170px;
}
#pkinopoisk_result .list li.choosed img {
	border: 1px solid #870007;
	opacity: 1;
}
#pkinopoisk_result .list li {
	position: relative;
	cursor: pointer;
	display: inline-block;
	padding: 3px;
	text-align: center;
}
#pkinopoisk_result .conbtm {
}

#pkinopoisk_result .num {
	background-color: #000000;
	border: 1px solid #870007;
	color: #FFFFFF;
	display: none;
	font-weight: bolder;
	opacity: 0.75;
	padding: 5px;
	position: absolute;
	right: 3px;
	top: 3px;
}

#pkinopoisk_result .choosed .num {
	display: block;
}

#pkinopoisk_result .stop_load_image {
	display: none;
	color: #e1705a;
}
</style>
{$buf}
<div class="conbtm">
<input class="edit btn btn-mini btn-sm btn-black" type="button" onClick="PKP.getUpMovei('{$id}'); return false;" style="width:160px;" value="Парсить">
</div>
HTML;

		$json['container'] = '#pkinopoisk_result';

		printData($json);

		$_POST['id'] = false;
		$sact        = false;
	}

}

if ($_POST['id'] == 'list' or $_POST['id'] == 'list_all') {
	/**
	 * Поиск фильма по названию. Тянем и выводим список
	 */
	set_file_info('delete');
	login_kp();

	if (!($refer = dle_cache('pkp_ref_'.$author))) {
		$refer = 'http://www.kinopoisk.ru/';
	}

	set_file_info('Парсим список фильмов');

	$kp_query = urlencode(convert_charset_pkp($title, 'utf-8', 'cp1251'));

	if ($_POST['id'] == 'list_all') {
		$url       = 'http://www.kinopoisk.ru/s/type/film/list/1/find/'.$kp_query.'/order/relevant/perpage/200/';
		$pars_list = $parser->loadPage($url, 'http://www.kinopoisk.ru/index.php?first=no&what=&kp_query='.$kp_query);
	} else {
		$url       = 'http://www.kinopoisk.ru/index.php?first=no&what=&kp_query='.$kp_query;
		$pars_list = $parser->loadPage($url, $refer);
	}

	$pars_list = convert_charset_pkp($pars_list, 'cp1251', 'utf-8');

	create_cache('pkp_ref_'.$author, $url);

	//Проверяем естил столбец для проверки
	$kp_id_movie_list  = array();
	$kp_id_movie_check = false;

	if ($news_id == 0 and db_check_existence_column(PREFIX.'_post', 'kp_id_movie')) {
		$kp_id_movie_check = true;
	}

	$list_film = array();

	if (preg_match_all('#<p class="name"><a href=".*?/level/1/film/([0-9]+)/sr/1/".*?>([^<]+)</a>[^<]+<span class="year">(.*?)</span></p>(.{5,100}<span class="gray">(.*?)</span>)?#is', $pars_list, $link_film, PREG_SET_ORDER)) {
		foreach ($link_film as $value) {

			$onclick = 'PKP.getUp(\''.$value[1].'\')';

			if ($config_mod['conf']['search_print_addinfo']) {
				$value[5] = trim(strip_tags($value[5]));
				$value[5] = !empty($value[5]) ? ' <span class="navigation">'.$value[5].'</span>' : '';
			} else {
				$value[5] = '';
			}

			$list_film[$value[1]] = '<a href="#" onClick="'.$onclick.'; return false;">'.$value[2].' ('.$value[3].')</a> <a href="http://www.kinopoisk.ru/film/'.$value[1].'/" target="_blank"><img src="uploads/pkinopoisk/kinopoisk.ru.png" alt="Посмотреть на КиноПоиск" width="16" height="16" border="0"></a>'.$value[5];

			if ($kp_id_movie_check) {
				$kp_id_movie_list[$value[1]] = $value[1];
			}
		}

		if (preg_match('#<p class="show_all"><a href="http://www.kinopoisk.ru/s/type/film/list/1/find/[^"]+?/">показать все(.*?)</a>#is', $pars_list, $match)) {
			$list_film['more'] = '<br><b><i><a href="#" onclick="PKP.getList(true);return false;">Найти все результаты</a></i></b>';
		}
	}

	if (count($list_film) == 0) {
		if (preg_match('#<title>(.*?)</title>.*?<script type="text/javascript"> id_film = ([0-9]*?); </script>#si', $pars_list, $mach) and $mach[2] != '') {
			$onclick = 'PKP.getUp(\''.$mach[2].'\')';

			$list_film[$mach[2]] = '<a href="#" onClick="'.$onclick.'; return false;">'.$mach[1].'</a> <a href="http://www.kinopoisk.ru/film/'.$mach[2].'/" target="_blank"><img src="uploads/pkinopoisk/kinopoisk.ru.png" alt="Посмотреть на КиноПоиск" width="16" height="16" border="0"></a>';

			if ($kp_id_movie_check) {
				$kp_id_movie_list[$mach[2]] = $mach[2];
			}
		} else {
			$list_film[] = 'По Вашему запросу фильмы не найдены';

			$kp_id_movie_check = false;
		}
	}

	//Проверяем есть ли фильм в бд
	if ($kp_id_movie_check and count($kp_id_movie_list) > 0) {
		$db->query("SELECT id, kp_id_movie FROM ".PREFIX."_post WHERE kp_id_movie IN ('".implode("','", $kp_id_movie_list)."')");

		while ($row = $db->get_row()) {
			if (isset($list_film[$row['kp_id_movie']])) {
				$onclick = 'PKP.getUp(\''.$row['kp_id_movie'].'\')';

				$list_film[$row['kp_id_movie']] = strip_tags($list_film[$row['kp_id_movie']]).' [ <a href="/'.$config['admin_path'].'?mod=editnews&action=editnews&id='.$row['id'].'" target="_blank">админ</a> ] [ <a href="/index.php?newsid='.$row['id'].'" target="_blank">сайт</a> ] [ <a href="#" onClick="'.$onclick.'; return false;">парсить</a> ] <a href="http://www.kinopoisk.ru/film/'.$row['kp_id_movie'].'/" target="_blank"><img src="uploads/pkinopoisk/kinopoisk.ru.png" alt="Посмотреть на КиноПоиск" width="16" height="16" border="0"></a>';
			}
		}
	}

	$json = array('text' => implode('<br>', $list_film));

	printData($json);

} elseif ($sact == 'poster_choose' and ($id = intval($_POST['id'])) > 0) {
	/**
	 * Тянем и выводим список постеров
	 */
	set_file_info('Загрузка списка постеров');

	$json = array(
		'container' => '#poster_choose_list',
		'append'    => true
	);

	$json['text'] = buildChooseImage($id, 'posters', 1);

	printData($json);

} elseif ($sact == 'kadr_choose' and ($id = intval($_POST['id'])) > 0) {
	/**
	 * Тянем и выводим список кадров
	 */
	set_file_info('Загрузка списка кадров');

	$json = array(
		'container' => '#kadr_choose_list',
		'append'    => true
	);

	$json['text'] = buildChooseImage($id, 'stills', 1);

	printData($json);

} elseif (is_numeric($_POST['id']) && $_POST['id'] > 0) {
	/**
	 * Парсим и оформляем фильм
	 */
	set_file_info('delete');
	login_kp();

	$kp_id = $_POST['id'];

	if (!($refer = dle_cache('pkp_ref_'.$author))) {
		$refer = 'http://www.kinopoisk.ru/';
	}
	$url_p = 'http://www.kinopoisk.ru/level/1/film/'.$_POST['id'].'/';

	set_file_info('Парсим основную информацию');

	$pars_film = $parser->loadPage($url_p, $refer);
	$pars_film = convert_charset_pkp($pars_film, 'cp1251', 'utf-8');
	create_cache('pkp_ref_'.$author, $url_p);

	$pars_data = array(
		'name_f'           => '#class="moviename-big" itemprop="name">(.*?)</h1>#si',
		'name_orig'        => '#itemprop="alternativeHeadline">(.*?)</span>#si',
		'year'             => '#год</td>.{10,150}?m_act%5Byear%5D/[0-9]+/" title="">([0-9]+)</a>#is',
		'country'          => '#страна</td>.{0,30}?<td>(.*?)</td>#si',
		'slogon'           => '#слоган</td>.*?<td style="color: \#555">(.*?)</td>#si',

		'director'         => '#режиссер</td><td itemprop="director">(.*?)</td></tr>#si',
		'screenwriter'     => '#сценарий</td><td>(.*?)</td>#si',
		'producer'         => '#продюсер</td><td itemprop="producer">(.*?)</td></tr>#si',
		'operator'         => '#оператор</td><td>(.*?)</td></tr>#si',
		'design'           => '#художник</td><td>(.*?)</td>#si',
		'editor'           => '#монтаж</td><td>(.*?)</td>#si',
		'composer'         => '#композитор</td><td itemprop="musicBy">(.*?)</td>#si',

		'fees_use'         => '#сборы в США</td>.{0,150}?<div[^>]+?>(.*?)</div>#si',
		'fees_world'       => '#сборы в мире</td>.{0,150}?<div[^>]+?>(.*?)</div>#si',
		'fees_rus'         => '#сборы в России</td>.{0,150}?<div[^>]+?>(.*?)</div>#si',

		'genre'            => '#жанр</td><td>(.*?)</td></tr>#si',
		'budget'           => '#бюджет</td>(.*?)</tr>[^<]{0,}?<tr#si',
		'premier'          => '#премьера \(мир\)</td>.*?<td[^>]+class="calendar"[^>]{0,}>(.*?)</td>#si',
		'premier_rus'      => '#премьера \(РФ\)</td>.*?<td[^>]+class="calendar"[^>]{0,}>(.*?)</td>#si',
		'reliz_dvd'        => '#релиз на DVD</td>(.*?)</tr>#si',
		'reliz_bluray'     => '#релиз на Blu-Ray</td>(.*?)</tr>#si',
		'time_film'        => '#время</td><td class="time" id="runtime">(.*?)</td>#si',
		'poster_film'      => '#<div id="photoBlock".{70,700}?<img[^>]+?src="([^>]+?)"#si',
		'link_kadr'        => '#<a.*?href="/film/([0-9]+)/stills/">#si',
		'description'      => '#<span class="_reachbanner_">(.*?)</span>#si',
		'kp_rating'        => '#rating:([0-9\.]+),#si',
		'kp_rating_num'    => '#itemprop="ratingCount">(.*?)</span>#si',
		'imdb'             => '#IMDB:\s(.*?)\s.*?</div>#si',
		'imdb_num'         => '#IMDB:.*?\((.*?)\).*?</div>#si',

		'trailer'          => '#"trailerFile":([^"]{0,3})?"([^"]+)",#si',
		'trailer_duration' => '#trailerinfo(.{500,1000})?>([0-9: ]{2,7})<#si',

		'trivia'           => '#<div class="triviaBlock fact">.{0,25}?<ul class="trivia[^>]+?>(.*?)</ul>#si',
		'trivia_blooper'   => '#<div class="triviaBlock blooper">.{0,25}?<ul class="trivia[^>]+?>(.*?)</ul>#si',
		'poster_film_big'  => '#openImgPopup\(\'(.*?)\'\);#si',
		'poster_link'      => '#<a.*?href="/film/([0-9]+)/posters/">#si',
		'actors'           => '#<h4>В главных ролях:</h4>.{0,}?<ul>(.*?)</ul>#is',
		'actors_dubl'      => '#<h4>Роли дублировали:</h4>.{0,}?<ul>(.*?)</ul>#is',
		'rate_pg'          => '#MPAA</td>.*?images/mpaa/(.*?)\.gif#si',
		'audience'         => '#зрители.{10,100}?<div style="margin-left: -20px">(.*?)</div>#si',
		'age_limit'        => '#<div class="ageLimit age([0-9]+)"></div>#is',

		'person_link'      => '#/film/('.$kp_id.')/cast/#is',

		'studio_link'      => '#film/('.$kp_id.')/studio/#is',
	);

	$rate_pg = array(
		'G'     => 'Нет возрастных ограничений',
		'PG'    => 'Рекомендуется присутствие родителей',
		'PG-13' => 'Детям до 13 лет просмотр не желателен',
		'R'     => 'Лицам до 17 лет обязательно присутствие взрослого',
		'NC-17' => 'Лицам до 17 лет просмотр запрещен',
	);

	$cat_sel = array();

	$post_data = array(
		'id_film'            => $_POST['id'],
		'trailer_link'       => '',
		'trailer_youtube_id' => '',

		'rate_pg_text'       => '',
		'rate_pg_img'        => '',
		'rate_pg_desc'       => '',

		'premier_date'       => '',
		'premier_rus_date'   => '',

		'actor_all'          => '',
		'director_all'       => '',
		'producer_all'       => '',
		'voice_director_all' => '',
		'voice_all'          => '',
		'writer_all'         => '',
		'operator_all'       => '',
		'composer_all'       => '',
		'design_all'         => '',
		'editor_all'         => '',

		'studio'             => '',
	);

	$cpm_person_type = array(
		'director'       => '1',
		'actor'          => '2',
		'producer'       => '3',
		'voice_director' => '4',
		'voice'          => '5',
		'writer'         => '6',
		'operator'       => '7',
		'composer'       => '8',
		'design'         => '9',
		'editor'         => '10',
	);
	$cpm             = array();

	foreach ($pars_data as $name => $value) {
		$post_data[$name] = '';

		if (preg_match($value, $pars_film, $matches)) {

			if ($name == 'rate_pg') {

				$matches[1] = strtoupper($matches[1]);

				if (isset($rate_pg[$matches[1]])) {

					if ($config_mod['conf']['rate_pg_type']) {
						$post_data[$name] = '[img=none]/uploads/pkinopoisk/mpaa/'.$matches[1].'.gif[/img]';
					} else {
						$post_data[$name] = $matches[1];
					}

					$post_data['rate_pg_text'] = $matches[1];
					$post_data['rate_pg_img']  = '/uploads/pkinopoisk/mpaa/'.$matches[1].'.gif';
					$post_data['rate_pg_desc'] = $rate_pg[$matches[1]];

				}

			} elseif ($name == 'audience') {

				if ($config_mod['conf']['aud_type']) {
					$post_data[$name] = preg_replace('#<img src="([^">]+)?/images/flags/(.*?)" width=.16[^>]+?>#i', '[img=none]/uploads/pkinopoisk/flags/$2[/img]', $matches[1]);
				} else {
					$post_data[$name] = preg_replace('#<img src="([^">]+)?/images/flags/[^>]+?title="(.*?)"[^>]+?>#i', ' $2 - ', str_replace('&nbsp;', '', $matches[1]));
				}

				$post_data[$name] = explode(',', $post_data[$name]);
				$audience_list    = array();

				foreach ($post_data[$name] as $aval) {
					$aval = trim(strip_tags($aval), "\n\r\t .,");

					if (!empty($aval)) {
						$audience_list[] = $aval;
					}
				}

				$post_data[$name] = implode($config_mod['conf']['aud_type_sep'], $audience_list);

			} elseif ($name == 'genre') {

				$post_data[$name] = trim($matches[1]);
				$genre            = array();

				$matchCat = array();

				if (preg_match_all('#/(lists|level/10)/m_act%5Bgenre%5D/([0-9]+)/([^>]{1,25})?>(.*?)</a>#is', $post_data[$name], $matchCat, PREG_SET_ORDER)) {
					foreach ($matchCat as $valMC) {
						if (isset($config_mod['cat_match'][$valMC[2]])) {
							$cat_sel[] = $config_mod['cat_match'][$valMC[2]];
						}

						$valMC[4] = trim(strip_tags($valMC[4]));
						if (!empty($valMC[4])) {
							$genre[] = $valMC[4];
						}
					}
				}

				if (count($genre) > 0) {
					$post_data[$name] = implode(', ', $genre);
				} else {
					$post_data[$name] = str_replace('слова', '', $post_data[$name]);
					$post_data[$name] = trim(strip_tags($post_data[$name]), "\n\r\t., ");
				}

			} elseif ($name == 'kp_rating_num') {

				$post_data[$name] = preg_replace('#[^\d]+#', '', $matches[1]);

			} elseif ($name == 'trailer') {

				$trailer_match = $matches[2];

				if (preg_match('#getTrailersDomain\(\)\{return [\'"]([^\'"]+?)[\'"];\}#is', $pars_film, $tr_match)) {
					$trailer_match = 'http://'.$tr_match[1].'/'.trim($trailer_match);
				} else {
					$trailer_match = 'http://kp.cdn.yandex.net/'.trim($trailer_match);
				}

				$post_data[$name] = '';
				if (filter_var($trailer_match, FILTER_VALIDATE_URL)) {
					$post_data['trailer_link'] = $trailer_match;
					$post_data[$name]          = '[video='.$trailer_match.']';
				}

			} elseif ($name == 'poster_film' || $name == 'poster_film_big') {

				if (stripos($matches[1], 'film_image_none.jpg') !== false) {
					$post_data[$name] = '';
				} elseif (substr($matches[1], 0, 1) == '/') {
					$post_data[$name] = 'http://www.kinopoisk.ru'.$matches[1];
				} else {
					$post_data[$name] = $matches[1];
				}

			} elseif ($name == 'name_f' and $config_mod['conf']['title_clean']) {

				$post_data[$name] = trim(strip_tags(preg_replace('#^([^<]+)(<.*?)?$#is', '$1', $matches[1])));

			} elseif ($name == 'actors' || $name == 'actors_dubl') {

				$post_data[$name] = array();
				if (preg_match_all('#<a href="/name/([0-9]+)/">(.*?)</a>#is', $matches[1], $act_match, PREG_SET_ORDER)) {
					foreach ($act_match as $act_val) {
						$post_data[$name][$act_val[1]] = trim(strip_tags($act_val[2]));

						if ($name == 'actors') {
							$cpm['actor'][] = array(
								'name_person'  => $act_val[2],
								'kp_id_person' => $act_val[1],
								'person_type'  => $cpm_person_type['actor'],
							);
						} elseif ($name == 'actors_dubl') {
							$cpm['voice'][] = array(
								'name_person'  => $act_val[2],
								'kp_id_person' => $act_val[1],
								'person_type'  => $cpm_person_type['voice'],
							);
						}
					}
				}
				$post_data[$name] = implode(', ', $post_data[$name]);

			} elseif ($name == 'trailer_duration') {

				$trailer_duration_match = explode(':', trim($matches[2]));

				if (count($trailer_duration_match) == 2) {
					if ($config_mod['conf']['trailer_duration_format']) {
						$td = $config_mod['conf']['trailer_duration_format'];
						$td = str_replace('m', intval($trailer_duration_match[0]), $td);
						$td = str_replace('M', $trailer_duration_match[0], $td);
						$td = str_replace('s', intval($trailer_duration_match[1]), $td);
						$td = str_replace('S', $trailer_duration_match[1], $td);

						$post_data[$name] = $td;
					} else {
						$post_data[$name] = intval($trailer_duration_match[0]) * 60 + intval($trailer_duration_match[1]);
					}
				}

			} elseif ($name == 'trivia' or $name == 'trivia_blooper') {

				if (preg_match_all('#<li class="trivia[^>]+?>(.*?)</li>#is', $matches[1], $trivia_match, PREG_SET_ORDER)) {

					if ($config_mod['conf'][$name.'_count'] > 0) {

						$matches[1] = array();

						$it = 0;

						foreach ($trivia_match as $trivia_val) {

							if (strpos($trivia_match[0], 'trivia_text') === false) {
								continue;
							}

							$matches[1][] = trim(strip_tags($trivia_val[1]));

							$it++;

							if ($it >= $config_mod['conf'][$name.'_count']) {
								break;
							}
						}

						$post_data[$name] = implode($config_mod['conf'][$name.'_sep'], $matches[1]);

					}

				}

			} else {

				if ($name == 'premier' || $name == 'premier_rus') {

					//Формируем дату в пользовательском виде
					if (!empty($config_mod['conf']['premier_format_date']) and preg_match('#data-date-premier-start-link="([0-9]{4})([0-9]{2})([0-9]{2})"#is', $matches[1], $pr_match)) {
						$date_format = strtotime($pr_match[3].'-'.$pr_match[2].'-'.$pr_match[1]);

						if ($date_format) {
							$post_data[$name.'_date'] = langdate($config_mod['conf']['premier_format_date'], $date_format);

							$post_data[$name.'_date'] = convert_charset_pkp($post_data[$name.'_date'], $config['charset'], 'utf-8');
						}
					}

					$matches[1] = preg_replace('#([\n]*|[\r]*|[\t]*)#is', '', $matches[1]);
					$matches[1] = preg_replace('#([\s]{2,})#is', ' ', $matches[1]);

				} elseif ($name == 'director' or $name == 'screenwriter' or $name == 'producer' or $name == 'operator' or $name == 'composer' or $name == 'design' or $name == 'editor') {

					$llp = array(
						'director'     => 'director',
						'producer'     => 'producer',
						'operator'     => 'operator',
						'composer'     => 'composer',
						'screenwriter' => 'writer',
						'design'       => 'design',
						'editor'       => 'editor'
					);

					if (isset($llp[$name]) and preg_match_all('#<a href="/name/([0-9]+)/"[^>]{0,}?>(.*?)</a>#is', $matches[1], $act_match, PREG_SET_ORDER)) {
						foreach ($act_match as $act_val) {

							$cpm[$llp[$name]][] = array(
								'name_person'  => $act_val[2],
								'kp_id_person' => $act_val[1],
								'person_type'  => $cpm_person_type[$llp[$name]],
							);

						}
					}

				} elseif ($name == 'fees_world') {

					$matches[1] = trim(str_replace('сборы', '', $matches[1]), ' .,');

				}

				$matches[1] = strip_tags($matches[1], '<br><br/><br />');
				$matches[1] = trim($matches[1]);

				if ($name == 'director' || $name == 'screenwriter' || $name == 'producer' || $name == 'operator' || $name == 'composer' || $name == 'design' || $name == 'editor' || $name == 'reliz_dvd' || $name == 'reliz_bluray' || $name == 'premier' || $name == 'premier_rus') {

					$matches[1] = trim(str_replace('  ', '', $matches[1]), ' .,');

				}

				$post_data[$name] = $matches[1];

			}

			unset($matches);

			$post_data[$name] = convert_charset_ascii_to_code($post_data[$name]);
			$post_data[$name] = html_entity_decode($post_data[$name], ENT_QUOTES, 'utf-8');
		}
	}

	function commentSortRating($a, $b)
	{
		if ($a['rating'] == $b['rating']) {
			return 0;
		}

		return ($a['rating'] < $b['rating']) ? -1 : 1;
	}

	/**
	 * Comments
	 */
	$post_data['comment'] = '';
	if ($config_mod['conf']['comment_count'] > 0) {

		$i        = 0;
		$data_com = array();

		if (preg_match_all('#<p id="ext_text_[0-9]+"><span[^>]+>(.*?)</span></p>.{500,1200}?<li id="comment_num_vote_[0-9]+">([0-9]+) / ([0-9]+)</li>#is', $pars_film, $matcCom, PREG_SET_ORDER)) {
			foreach ($matcCom as $valCom) {
				$patt      = array(
					'/<b>(.*?)<\/b>/is',
					'/<i>(.*?)<\/i>/is',
					'/[0-9]+(&nbsp;| )из(&nbsp;| )[0-9]+/is'
				);
				$replac    = array(
					'[b]$1[/b]',
					'[i]$1[/i]',
					''
				);
				$valCom[1] = preg_replace($patt, $replac, $valCom[1]);
				$valCom[1] = trim(strip_tags($valCom[1], '<br><br/><br />'));

				$data_com[] = array(
					'text'   => $valCom[1],
					'rating' => ($valCom[2] / ($valCom[3] < 1 ? 1 : $valCom[3]))
				);

				$i++;
				if ($i >= $config_mod['conf']['comment_count']) {
					break;
				}
			}

			if ($config_mod['conf']['comment_sort']) {
				usort($data_com, 'commentSortRating');
			}

			if (isset($data_com)) {
				$post_data['comment'] = array();
				foreach ($data_com as $valCom) {
					$valCom['text'] = convert_charset_ascii_to_code($valCom['text']);
					$valCom['text'] = html_entity_decode($valCom['text'], ENT_QUOTES, 'utf-8');

					$post_data['comment'][] = $valCom['text'];
				}
				$post_data['comment'] = implode($config_mod['conf']['comment_sep'], $post_data['comment']);
			}
		}
	}


	/**
	 * Studio
	 */
	if ($post_data['studio_link'] and $config_mod['conf']['pars_studio']) {
		$page_studio = $parser->loadPage('http://www.kinopoisk.ru/film/'.$kp_id.'/studio/', 'http://www.kinopoisk.ru/film/'.$kp_id.'/');
		$page_studio = convert_charset_pkp($page_studio, 'cp1251', 'utf-8');

		$studio = array();

		if (preg_match('#Производство:(.*?)</table>#is', $page_studio, $match_studio_main) and preg_match_all('#<a href="/lists/m_act%5Bstudio%5D/[0-9]+/" class="all">(.*?)</a>#is', $match_studio_main[1], $match_studo, PREG_SET_ORDER)) {

			foreach ($match_studo as $studo) {
				$studo[1] = trim(strip_tags($studo[1]));

				if (!empty($studo[1])) {
					$studio[] = $studo[1];
				}
			}

		}

		$post_data['studio'] = implode($config_mod['conf']['studio_sep'], $studio);

		unset($page_studio, $studio, $match_studio_main, $match_studo);
	}


	/**
	 * Основной постер
	 */
	if (!empty($post_data['poster_film']) && $config_mod['conf']['pars_poster_film']) {
		$file_name = 'poster-'.$_POST['id'].'.jpg';
		$file_name = time()."_".$file_name;

		set_file_info('Парсим основной постер');

		if (!($post_data['poster_film'] = uploads_pic($post_data['poster_film'], $file_name, $url_p, '', '', '', '', $config_mod['conf']['pars_poster_film_size']))) {
			$post_data['poster_film'] = '';
		}
	} else {
		$post_data['poster_film'] = '';
	}


	/**
	 * Загружает изображения по ImageID КиноПоиска
	 * @param $img_id
	 * @param $refer
	 * @param $tumb
	 * @param $tumb_t
	 * @param $wmo
	 * @param $ico
	 * @param $img_size
	 * @return bool|string
	 */
	function downloadImageById($img_id, $refer, $tumb, $tumb_t, $wmo, $ico, $img_size = false)
	{
		global $parser;

		$url_img  = 'http://www.kinopoisk.ru/picture/'.$img_id.'/';
		$pars_img = $parser->loadPage($url_img, $refer);

		if (preg_match('|<tr>.*?<td valign="top">.*?<img.*?src="(.*?)"[^>]+>.*?</td>.*?</tr>|is', $pars_img, $matches_l)) {
			$file_name = time()."_".substr((md5($img_id).microtime()), 0, 10).'.jpg';

			$matches_l[1] = stripos($matches_l[1], 'kinopoisk.ru/') === false ? 'http://www.kinopoisk.ru'.$matches_l[1] : $matches_l[1];

			if (($ff = uploads_pic($matches_l[1], $file_name, $url_img, $tumb, $tumb_t, $wmo, $ico, $img_size))) {
				return $ff;
			}
		}

		return false;
	}

	/**
	 * Функция парсит из раздела изображений
	 * @param $idfilm
	 * @param $type
	 * @param string $col_imag
	 * @param string $tumb
	 * @param string $tumb_t
	 * @param string $wmo
	 * @param string $ico
	 * @param bool $img_size
	 * @return array|bool
	 */
	function pars_imag($idfilm, $type, $col_imag = '', $tumb = '', $tumb_t = '', $wmo = '', $ico = '', $img_size = false)
	{
		global $url_p, $parser;

		static $url_p_i = '';

		if ($url_p_i == '') {
			$url_p_i = $url_p;
		}

		if ($type == 'screen') {
			$size_l_oboi = '/';
			$url_link    = 'http://www.kinopoisk.ru/level/13/film/'.$idfilm.'/adv_type/still/';
			$sear_f      = '<img[^>]+>';
		} elseif ($type == 'poster') {
			$size_l_oboi = '/';
			$url_link    = 'http://www.kinopoisk.ru/film/'.$idfilm.'/posters/';
			$sear_f      = '<img[^>]+>';
		} elseif ($type == 'kadr') {
			$size_l_oboi = '/';
			$url_link    = 'http://www.kinopoisk.ru/film/'.$idfilm.'/stills/';
			$sear_f      = '<img[^>]+>';
		}

		$film_data = array();

		if (is_numeric($idfilm) && $col_imag > 0) {
			$pars_oboi = $parser->loadPage($url_link, $url_p_i);
			$pars_oboi = convert_charset_pkp($pars_oboi, 'cp1251', 'utf-8');
			$url_p_i   = $url_link;
			if (preg_match('|<a href="/picture/([0-9]+)'.$size_l_oboi.'">'.$sear_f.'</a>|is', $pars_oboi, $matches_if)) {

				$pars_img = $parser->loadPage('http://www.kinopoisk.ru/picture/'.$matches_if[1].$size_l_oboi, $url_p_i);
				$pars_img = convert_charset_pkp($pars_img, 'cp1251', 'utf-8');
				$url_p_fi = 'http://www.kinopoisk.ru/picture/'.$matches_if[1].$size_l_oboi;

				preg_match('|<tr>.*?<td valign="top">.*?<img.*?src="(.*?)"[^>]+>.*?</td>.*?</tr>|is', $pars_img, $matches_l);
				$file_name    = time()."_".$matches_if[1].'.jpg';
				$matches_l[1] = stripos($matches_l[1], 'kinopoisk.ru/') === false ? 'http://www.kinopoisk.ru'.$matches_l[1] : $matches_l[1];
				if (($ff = uploads_pic($matches_l[1], $file_name, $url_p_fi, $tumb, $tumb_t, $wmo, $ico))) {
					$film_data[$matches_if[1]] = $ff;
				}

				if (preg_match_all('|<a href="/picture/([0-9]+)'.$size_l_oboi.'">[0-9]+</a>|is', $pars_img, $matches_ar, PREG_SET_ORDER)) {

					if ($col_imag > 1) {
						$i = 2;
						foreach ($matches_ar as $link_n_img) {
							unset($matches_l);

							if (($ff = downloadImageById($link_n_img[1], $url_p_fi, $tumb, $tumb_t, $wmo, $ico))) {
								$film_data[$link_n_img[1]] = $ff;
							}

							if ($i >= $col_imag) {
								break;
							}

							$i++;
						}
					}
				}
			}
		} else {
			return false;
		}

		return $film_data;
	}


	/**
	 * Списко выбранных постеров
	 */
	$poster_choose_list = array();
	$i                  = 0;
	if (isset($_POST['poster_choose_list']) and is_array($_POST['poster_choose_list'])) {
		foreach ($_POST['poster_choose_list'] as $val) {
			if (filter_var($val, FILTER_VALIDATE_INT) or filter_var($val, FILTER_VALIDATE_URL)) {
				$i++;
				$poster_choose_list[$i] = $val;
			}
		}
	}

	/**
	 * Большой постер
	 */
	if ($config_mod['conf']['pars_poster_film_big']) {

		$poster_id = false;
		if (count($poster_choose_list)) {
			$pcl_templ = $poster_choose_list;
			$poster_id = array_shift($pcl_templ);
		}

		if ($poster_id) {

			$file_name = time()."_".substr(md5($post_data['poster_film_big']), 0, 7).'.jpg';

			set_file_info('Парсим большой постер');

			if (filter_var($poster_id, FILTER_VALIDATE_URL)) {

				if (($ff = uploads_pic($poster_id, $file_name, 'http://www.kinopoisk.ru/film/'.$poster_id.'/', $config_mod['conf']['tumb_poster'], $config_mod['conf']['tumb_poster_type'], $config_mod['conf']['watermark_on'], '', $config_mod['conf']['pars_poster_film_big_size']))) {
					$post_data['poster_film_big'] = $ff;
				}

			} elseif ($poster_id > 0) {

				if (($ff = downloadImageById($poster_id, 'http://www.kinopoisk.ru/picture/'.$poster_id.'/', $config_mod['conf']['tumb_poster'], $config_mod['conf']['tumb_poster_type'], $config_mod['conf']['watermark_on'], '', $config_mod['conf']['pars_poster_film_big_size']))) {
					$post_data['poster_film_big'] = $ff;
				}

			}

		} elseif (!empty($post_data['poster_film_big'])) {

			$file_name = time()."_".substr(md5($post_data['poster_film_big']), 0, 7).'.jpg';

			set_file_info('Парсим большой постер');

			if (!($post_data['poster_film_big'] = uploads_pic($post_data['poster_film_big'], $file_name, $url_p, $config_mod['conf']['tumb_poster'], $config_mod['conf']['tumb_poster_type'], $config_mod['conf']['watermark_on'], '', $config_mod['conf']['pars_poster_film_big_size']))) {
				$post_data['poster_film_big'] = '';
			}

		} elseif ($post_data['poster_link'] > 0) {

			set_file_info('Парсим большой постер');

			$post_data['poster_film_big'] = $config_mod['conf']['poster_film_alter'];
			$data_bposter                 = pars_imag($post_data['poster_link'], 'poster', 1, $config_mod['conf']['tumb_poster'], $config_mod['conf']['tumb_poster_type'], $config_mod['conf']['watermark_on'], '', $config_mod['conf']['pars_poster_film_big_size']);

			if (count($data_bposter) > 0) {
				$post_data['poster_film_big'] = array_shift($data_bposter);
			}

		} else {

			$post_data['poster_film_big'] = $config_mod['conf']['poster_film_alter'];

		}

	} else {

		$post_data['poster_film_big'] = '';

	}


	/**
	 * Кадры
	 */
	$post_data['screen_film'] = '';
	$data_kadr                = array();

	if (isset($_POST['kadr_choose_list']) and is_array($_POST['kadr_choose_list'])) {
		set_file_info('Парсим кадры');

		$url_p_fi = 'http://www.kinopoisk.ru/film/'.$kp_id.'/stills/';

		foreach ($_POST['kadr_choose_list'] as $kval) {
			$kval = intval($kval);

			if ($kval > 0 and ($ff = downloadImageById($kval, $url_p_fi, $config_mod['conf']['tumb_kadr'], $config_mod['conf']['tumb_kadr_type'], $config_mod['conf']['watermark_on'], $config_mod['conf']['imag_cut']))) {
				$data_kadr[$kval] = $ff;
				$post_data['screen_film'] .= '[thumb]'.$ff.'[/thumb] ';
			}
		}

	} elseif (!empty($post_data['link_kadr']) && $config_mod['conf']['kol_kadr'] > 0) {
		set_file_info('Парсим кадры');

		$data_kadr = pars_imag($_POST['id'], 'kadr', $config_mod['conf']['kol_kadr'], $config_mod['conf']['tumb_kadr'], $config_mod['conf']['tumb_kadr_type'], $config_mod['conf']['watermark_on'], $config_mod['conf']['imag_cut']);

		if (count($data_kadr) > 0) {
			foreach ($data_kadr as $value) {
				$post_data['screen_film'] .= '[thumb]'.$value.'[/thumb] ';
			}
		}
	}

	/**
	 * Скриншоты
	 */
	$post_data['screen_film_scr'] = '';
	$data_scrin                   = array();
	if (!empty($post_data['link_kadr']) && $config_mod['conf']['kol_scrin'] > 0) {
		set_file_info('Парсим скриншоты');

		$data_scrin = pars_imag($_POST['id'], 'screen', $config_mod['conf']['kol_scrin'], $config_mod['conf']['tumb_kadr'], $config_mod['conf']['tumb_kadr_type'], $config_mod['conf']['watermark_on'], $config_mod['conf']['imag_cut']);

		if (count($data_scrin) > 0) {
			foreach ($data_scrin as $value) {
				$post_data['screen_film_scr'] .= '[thumb]'.$value.'[/thumb] ';
			}
		}
	}

	/**
	 * Трейлер
	 */
	if (($config_mod['conf']['trailer_download'] == 1 || $config_mod['conf']['trailer_download'] == 2) && !empty($post_data['trailer_link'])) {
		set_file_info('Загружаем трейлер');

		$tl   = explode('/', $post_data['trailer_link']);
		$name = array_pop($tl);
		$name = strtolower(ltrim(str_replace('kinopoisk.ru', '', $name), '-'));

		if ($config_mod['conf']['trailer_download'] == 2) {
			$incp = get_include_path().PATH_SEPARATOR.ENGINE_DIR.DIRECTORY_SEPARATOR.'classes';
			//$incp = preg_replace('#[\\/]#s', DIRECTORY_SEPARATOR, $incp);
			set_include_path($incp);
			ini_set('include_path', $incp);
		}

		if ($config_mod['conf']['trailer_download'] == 1 && ($trailer = uploads_trailer($post_data['trailer_link'], $name, $url_p))) {
			$post_data['trailer_link'] = $trailer;
			$post_data['trailer']      = '[video='.$trailer.']';
		} elseif ($config_mod['conf']['trailer_download'] == 2 && ($trailer = trailer_to_youtube($post_data['trailer_link'], $name, $post_data['name_f']))) {
			$post_data['trailer_youtube_id'] = $trailer;
			$post_data['trailer_link']       = 'http://youtu.be/'.$trailer;
			$post_data['trailer']            = '[video=http://youtu.be/'.$trailer.']';
		}
	}

	/**
	 * Все персоны
	 */
	if ($config_mod['conf']['pars_actor_all'] == 1 and $post_data['person_link'] > 0) {
		set_file_info('Загружаем всех актеров');

		$person_list_link = array(
			'director'       => 'режиссер',
			'actor'          => 'актеры',
			'producer'       => 'продюсеры',
			'voice_director' => 'режиссер дубляжа',
			'voice'          => 'актеры дубляжа',
			'writer'         => 'сценарист',
			'operator'       => 'оператор',
			'composer'       => 'композитор',
			'design'         => 'художники',
			'editor'         => 'монтажер',
//			'translator' => 'переводчик',
		);

		$page_cast = $parser->loadPage('http://www.kinopoisk.ru/film/'.$kp_id.'/cast/', 'http://www.kinopoisk.ru/film/'.$kp_id.'/');
		$page_cast = convert_charset_pkp($page_cast, 'cp1251', 'utf-8');

		foreach ($person_list_link as $pll_key => $pll_val) {
			$post_data[$pll_key.'_all'] = '';

			if (preg_match('#<a name="'.$pll_key.'"></a>[^<]{0,10}?<div style="[^"]+?">[^<]+?</div>(.*?)(<a name="[^"]+?"></a>|<table cellspacing=0 cellpadding=0 width=100% border=0>)#is', $page_cast, $pll_match)) {

				if (preg_match_all('#<div class="dub.{1,30}?">.{100,500}?<div class="name"><a href="/name/([0-9]+)/">([^<]+?)</a>.{10,200}?MyKP_Folder_Select#is', $pll_match[1], $match, PREG_SET_ORDER)) {
					$plist = array();

					$cpm[$pll_key] = array();

					foreach ($match as $val) {
						$val[2] = trim(strip_tags($val[2]));

						$cpm[$pll_key][] = array(
							'name_person'  => $val[2],
							'kp_id_person' => $val[1],
							'person_type'  => $cpm_person_type[$pll_key],
						);

						$plist[] = $val[2];
					}

					$post_data[$pll_key.'_all'] = implode(', ', $plist);
				}

			}
		}

	}

	/**
	 * Формирует теги.
	 * @param string $text
	 * @param int $typelink
	 * @param string $prefix_link
	 * @return array
	 */
	function pkp_get_tags($text, $typelink, $prefix_link)
	{
		global $config, $prefix_link_tags;

		$tags_l = array();
		$tags   = array();

		if (empty($prefix_link)) {
			$prefix_link = $prefix_link_tags;
		}

		if ($text != '') {
			$tags_array = explode(',', $text);

			foreach ($tags_array as $value) {

				$value = trim($value);
				$tag   = trim(preg_replace('/‘|’|&#(1(45|46)|39)[;]?|\'|&#x27;/', ' ', $value));

				if (preg_match("/[\||\'|\<|\>|\"|\!|\?|\$|\@|\/|\\\|\&\~\*\+]/", $value)) {
					$tags_l[] = $value;
				} else {
					if ($config['charset'] != 'utf-8' or $config['charset'] != 'utf8') {
						$url_tag = convert_charset_pkp($tag, 'utf-8', $config['charset']);
					} else {
						$url_tag = $tag;
					}

					if ($typelink == 2) {
						//Тег, без ссылки
						$tags_l[] = $value;
						$tags[]   = $tag;
					} elseif ($typelink == 3) {
						//Ссылка без добавления в тег
						$tags_l[] = '[url='.$config["http_home_url"].$prefix_link.urlencode($url_tag).'/]'.$value.'[/url]';
					} elseif ($typelink == 1) {
						//Тег и ссылка
						$tags_l[] = '[url='.$config["http_home_url"].$prefix_link.urlencode($url_tag).'/]'.$value.'[/url]';
						$tags[]   = $tag;
					}
				}

			}

			$tags_l = implode(', ', $tags_l);
			$tags   = implode(', ', $tags);
		}

		return array(
			$tags_l,
			$tags
		);
	}


	$templ_main = array(
		'short_story' => $config_mod['template']['short_story'],
		'full_story'  => $config_mod['template']['full_story'],
	);
	//Not tag link
	$templ_main_nt = array(
		'title'      => $config_mod['template']['title'],
		'meta_title' => $config_mod['template']['meta_title'],
		'descr'      => $config_mod['template']['descr'],
		'keywords'   => $config_mod['template']['keywords'],
		'alt_name'   => $config_mod['template']['alt_name'],
	);
	$templ_tags    = $config_mod['template']['tags'];
	$templ_xfields = $config_mod['template_xfields'];
	$templ_person  = $config_mod['template_person'];

	$templ_person['kp_id_movie'] = $post_data['id_film'];

	//Image separate
	$image_type = array(
		'screen_film'     => $data_kadr,
		'screen_film_scr' => $data_scrin,
	);

	foreach ($image_type as $it_key => $it_val) {

		$ni = 1;
		if (count($it_val) > 0) {
			foreach ($it_val as $value) {
				$arrSear = array(
					'[pkpgiven_'.$it_key.'_'.$ni.']',
					'[pkpvalue_'.$it_key.'_'.$ni.']',
					'[/pkpvalue_'.$it_key.'_'.$ni.']'
				);
				$arrRepl = array(
					$value,
					'',
					''
				);

				foreach ($templ_main as $name_x => $value_x) {
					$templ_main[$name_x] = str_replace($arrSear, $arrRepl, $value_x);
				}

				if (isset($templ_xfields) && count($templ_xfields)) {
					foreach ($templ_xfields as $name_x => $value_x) {
						$templ_xfields[$name_x] = str_replace($arrSear, $arrRepl, $value_x);
					}
				}

				if (isset($templ_person) && count($templ_person)) {
					foreach ($templ_person as $name_x => $value_x) {
						$templ_person[$name_x] = str_replace($arrSear, $arrRepl, $value_x);
					}
				}

				$ni++;
			}
		}

		$arrSear                   = array(
			'#\[pkpgiven_'.$it_key.'_[0-9]+\]#i',
			'#\[pkpvalue_'.$it_key.'_[0-9]+\](.*?)\[/pkpvalue_'.$it_key.'_[0-9]+\]#i',
		);
		$arrRepl                   = array(
			'',
			''
		);
		$templ_main['short_story'] = preg_replace($arrSear, $arrRepl, $templ_main['short_story']);
		$templ_main['full_story']  = preg_replace($arrSear, $arrRepl, $templ_main['full_story']);
		if (isset($templ_xfields) && count($templ_xfields)) {
			foreach ($templ_xfields as $name_x => $value_x) {
				$templ_xfields[$name_x] = preg_replace($arrSear, $arrRepl, $value_x);
			}
		}
		if (isset($templ_person) && count($templ_person)) {
			foreach ($templ_person as $name_x => $value_x) {
				$templ_person[$name_x] = preg_replace($arrSear, $arrRepl, $value_x);
			}
		}

	}
	//END Image separate

	/**
	 * Image custom
	 */
	$image_custom = array(
		'poster_film_big' => $poster_choose_list
	);

	/**
	 * @param $name
	 * @param $text
	 * @param $list
	 * @return string
	 */
	function imageCustom($name, $text, $list)
	{
		global $kp_id;

		if (preg_match_all('#\[pkpgiven_'.$name.'_([0-9]+) ([^\]]+)\]#is', $text, $match, PREG_SET_ORDER)) {

			foreach ($match as $val) {

				$size       = false;
				$thumb      = '';
				$thumb_type = 0;
				$wmo        = '';
				$ico        = '';

				if (!isset($list[$val[1]])) {
					continue;
				}

				if (preg_match('#size=([0-9x]+)#is', $val[2], $c_match)) {
					$size = $c_match[1];
				}

				if (preg_match('#thumb=([0-9x]+)#is', $val[2], $c_match)) {
					$thumb = $c_match[1];
				}

				if (preg_match('#thumb_type=([0-9])#is', $val[2], $c_match)) {
					$thumb_type = $c_match[1];
				}

				if (preg_match('#wmo=([0-9])#is', $val[2], $c_match)) {
					$wmo = $c_match[1];
				}

				if (preg_match('#ico=([0-9])#is', $val[2], $c_match)) {
					$ico = $c_match[1];
				}

				$ff = false;

				if (filter_var($list[$val[1]], FILTER_VALIDATE_URL)) {
					$file_name = time()."_".substr((md5($list[$val[1]]).microtime()), 0, 10).'.jpg';
					$ff        = uploads_pic($list[$val[1]], $file_name, 'http://www.kinopoisk.ru/film/'.$kp_id.'/', $thumb, $thumb_type, $wmo, $ico, $size);
				} else {
					$ff = downloadImageById($list[$val[1]], 'http://www.kinopoisk.ru/film/'.$kp_id.'/', $thumb, $thumb_type, $wmo, $ico, $size);
				}

				if ($ff) {

					$text = preg_replace('#\[pkpgiven_'.$name.'_'.$val[1].' ([^\]]+)\]#is', $ff, $text);

					$arrSear = array(
						'[pkpvalue_'.$name.'_'.$val[1].']',
						'[/pkpvalue_'.$name.'_'.$val[1].']'
					);
					$arrRepl = array(
						'',
						''
					);

					$text = str_replace($arrSear, $arrRepl, $text);

				}

			}

		}

		return $text;

	}

	foreach ($image_custom as $im_name => $im_list) {

		if (count($im_list)) {

			set_file_info('Загружаем кастомные изображения');

			foreach ($templ_main as $name_x => $value_x) {
				$templ_main[$name_x] = imageCustom($im_name, $value_x, $im_list);
			}

			if (isset($templ_xfields) && count($templ_xfields)) {
				foreach ($templ_xfields as $name_x => $value_x) {
					$templ_xfields[$name_x] = imageCustom($im_name, $value_x, $im_list);
				}
			}

			if (isset($templ_person) && count($templ_person)) {
				foreach ($templ_person as $name_x => $value_x) {
					$templ_person[$name_x] = imageCustom($im_name, $value_x, $im_list);
				}
			}

		}


		$arrSear = array(
			'#\[pkpgiven_'.$im_name.'_[0-9]+ [^\]]+\]#i',
			'#\[pkpvalue_'.$im_name.'_[0-9]+\](.*?)\[/pkpvalue_'.$im_name.'_[0-9]+\]#i',
		);
		$arrRepl = array(
			'',
			''
		);

		$templ_main['short_story'] = preg_replace($arrSear, $arrRepl, $templ_main['short_story']);
		$templ_main['full_story']  = preg_replace($arrSear, $arrRepl, $templ_main['full_story']);

		if (isset($templ_xfields) && count($templ_xfields)) {
			foreach ($templ_xfields as $name_x => $value_x) {
				$templ_xfields[$name_x] = preg_replace($arrSear, $arrRepl, $value_x);
			}
		}

		if (isset($templ_person) && count($templ_person)) {
			foreach ($templ_person as $name_x => $value_x) {
				$templ_person[$name_x] = preg_replace($arrSear, $arrRepl, $value_x);
			}
		}
	}


	$templ_tags .= ', ';

	/**
	 * Возвращает лимитировано значение
	 * @param $name
	 * @param $value
	 * @param $tpl
	 * @return mixed
	 */
	function given_and_given_limit($name, $value, $tpl)
	{
		$tpl = preg_replace('#\[pkpgiven_'.$name.'\]#is', $value, $tpl);

		if (preg_match_all('#\[pkpgiven_'.$name.' limit=[\'"]([0-9]+)[\'"]\]#is', $tpl, $match, PREG_SET_ORDER)) {
			foreach ($match as $lval) {
				$tpl = preg_replace('#\[pkpgiven_'.$name.' limit=[\'"]'.$lval[1].'[\'"]\]#is', mb_substr($value, 0, $lval[1]), $tpl);
			}
		}

		return $tpl;
	}

	foreach ($post_data as $name => $value) {
		$value = str_replace('$', '&#36;', $value);
		$value = preg_replace('#([\n\r\t]+)#is', '', $value);

		$value = $config_mod['conf']['del_tir'] && $value == '-' ? '' : $value;

		//No tag link
		foreach ($templ_main_nt as $tm_key => $tm_val) {
			$tm_val = given_and_given_limit($name, $value, $tm_val);

			if (!empty($value) || $value === '0') {
				$tm_val = preg_replace('#\[pkpvalue_'.$name.'\](.*?)\[/pkpvalue_'.$name.'\]#is', '$1', $tm_val);
			} else {
				$tm_val = preg_replace('#\[pkpvalue_'.$name.'\].*?\[/pkpvalue_'.$name.'\](\n|\r\n|)?#is', '', $tm_val);
			}

			$templ_main_nt[$tm_key] = $tm_val;
		}

		if (!empty($value) or $value === '0') {
			$templ_tags = preg_replace('#\[pkpvalue_'.$name.'\](.*?)\[/pkpvalue_'.$name.'\]#is', '$1', $templ_tags);
		} else {
			$templ_tags = preg_replace('#\[pkpvalue_'.$name.'\].*?\[/pkpvalue_'.$name.'\](\n|\r\n|)?#is', '', $templ_tags);
		}

		if (isset($config_mod['conf']['t_'.$name]) and $config_mod['conf']['t_'.$name] and !empty($value)) {
			$value = pkp_get_tags($value, $config_mod['conf']['t_'.$name], $config_mod['conf']['t_'.$name.'_prefix']);

			if (!empty($value[1])) {
				$templ_tags .= $value[1].', ';
			}

			$value = $value[0];
		}

		foreach ($templ_main as $tm_key => $tm_val) {
			$tm_val = given_and_given_limit($name, $value, $tm_val);

			if (!empty($value) || $value === '0') {
				$tm_val = preg_replace('#\[pkpvalue_'.$name.'\](.*?)\[/pkpvalue_'.$name.'\]#is', '$1', $tm_val);
			} else {
				$tm_val = preg_replace('#\[pkpvalue_'.$name.'\].*?\[/pkpvalue_'.$name.'\](\n|\r\n|)?#is', '', $tm_val);
			}

			$templ_main[$tm_key] = $tm_val;
		}

		if (isset($templ_xfields) && count($templ_xfields)) {
			foreach ($templ_xfields as $name_x => $value_x) {
				$value_x = given_and_given_limit($name, $value, $value_x);

				if ($value != '') {
					$value_x = preg_replace('#\[pkpvalue_'.$name.'\](.*?)\[/pkpvalue_'.$name.'\]#is', '$1', $value_x);
				} else {
					$value_x = preg_replace('#\[pkpvalue_'.$name.'\].*?\[/pkpvalue_'.$name.'\](\n|\r\n|)?#is', '', $value_x);
				}

				$templ_xfields[$name_x] = $value_x;
			}
		}

		if (isset($templ_person) && count($templ_person)) {
			foreach ($templ_person as $name_x => $value_x) {
				$value_x = given_and_given_limit($name, $value, $value_x);

				if ($value != '') {
					$value_x = preg_replace('/\[pkpvalue_'.$name.'\](.*?)\[\/pkpgiven_'.$name.'\]/is', '$1', $value_x);
				} else {
					$value_x = preg_replace('/\[pkpvalue_'.$name.'\].*?\[\/pkpgiven_'.$name.'\](\n|\r\n|)?/is', '', $value_x);
				}

				$templ_person[$name_x] = $value_x;
			}
		}
	}

	$templ_tags = trim($templ_tags, ' ,');

	$sr_s = array(
		"\n",
		'&#36;',
	);
	$sr_r = array(
		'<br>',
		'$',
	);

	foreach ($templ_main as $tm_key => $tm_val) {
		$templ_main[$tm_key] = str_replace($sr_s, $sr_r, $tm_val);
	}

	if (isset($templ_xfields) && count($templ_xfields)) {
		foreach ($templ_xfields as $name_x => $value_x) {
			$templ_xfields[$name_x] = str_replace($sr_s, $sr_r, $value_x);
		}
	}

	if (isset($templ_person) && count($templ_person)) {
		foreach ($templ_person as $tp_key => $tp_val) {
			$templ_person[$tp_key] = str_replace($sr_s, $sr_r, $tp_val);
		}
	}

	$time_ex = time() - $time_ex;

	//Alt name
	$langtranslit = array(
		'а' => 'a', 'б' => 'b', 'в' => 'v',
		'г' => 'g', 'д' => 'd', 'е' => 'e',
		'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
		'и' => 'i', 'й' => 'y', 'к' => 'k',
		'л' => 'l', 'м' => 'm', 'н' => 'n',
		'о' => 'o', 'п' => 'p', 'р' => 'r',
		'с' => 's', 'т' => 't', 'у' => 'u',
		'ф' => 'f', 'х' => 'h', 'ц' => 'c',
		'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
		'ь' => '', 'ы' => 'y', 'ъ' => '',
		'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
		"ї" => "yi", "є" => "ye",

		'А' => 'A', 'Б' => 'B', 'В' => 'V',
		'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
		'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
		'И' => 'I', 'Й' => 'Y', 'К' => 'K',
		'Л' => 'L', 'М' => 'M', 'Н' => 'N',
		'О' => 'O', 'П' => 'P', 'Р' => 'R',
		'С' => 'S', 'Т' => 'T', 'У' => 'U',
		'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
		'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
		'Ь' => '', 'Ы' => 'Y', 'Ъ' => '',
		'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
		"Ї" => "yi", "Є" => "ye",
	);

	$templ_main_nt['alt_name'] = totranslit($templ_main_nt['alt_name'], true, false);
	//END Alt name

	$fill_main = array_merge($templ_main, $templ_main_nt);

	/**
	 * До оформление
	 */
	$to_make_form = '';
	if ($config_mod['conf']['to_make_form'] and $news_id > 0) {

		$to_make_form = array();

		//Основное
		$fill_main_name = array(
			'title'       => 'Заголовок статьи',
			'short_story' => 'Краткое описание',
			'full_story'  => 'Полное описание',
			'meta_title'  => 'Метатег title',
			'descr'       => 'Описание для статьи',
			'keywords'    => 'Ключевые слова',
			'alt_name'    => 'ЧПУ URL статьи',
		);

		foreach ($fill_main as $key => $val) {
			if (!empty($val)) {
				$to_make_form[] = $fill_main_name[$key].' - <a href="#" onClick="PKP.to_make_form([\'fill_main\', \''.$key.'\'], \'s\'); return false;">в начало</a> / <a href="#" onClick="PKP.to_make_form([\'fill_main\', \''.$key.'\'], \'r\'); return false;">заменить</a> / <a href="#" onClick="PKP.to_make_form([\'fill_main\', \''.$key.'\'], \'e\'); return false;">в конец</a>';
			}
		}

		//Теги
		if (!empty($templ_tags)) {
			$to_make_form[] = 'Облако тегов - <a href="#" onClick="PKP.to_make_form([\'fill_tags\'], \'s\'); return false;">в начало</a> / <a href="#" onClick="PKP.to_make_form([\'fill_tags\'], \'r\'); return false;">заменить</a> / <a href="#" onClick="PKP.to_make_form([\'fill_tags\'], \'e\'); return false;">в конец</a>';
		}

		//Доп. поля
		$xfields      = xfieldsload();
		$xfields_name = array();
		foreach ($xfields as $val) {
			$xfields_name[$val[0]] = convert_charset_pkp($val[1], $config['charset']);
		}

		foreach ($templ_xfields as $key => $val) {
			if (!empty($val)) {
				$to_make_form[] = $xfields_name[$key].' - <a href="#" onClick="PKP.to_make_form([\'fill_xfield\', \''.$key.'\'], \'s\'); return false;">в начало</a> / <a href="#" onClick="PKP.to_make_form([\'fill_xfield\', \''.$key.'\'], \'r\'); return false;">заменить</a> / <a href="#" onClick="PKP.to_make_form([\'fill_xfield\', \''.$key.'\'], \'e\'); return false;">в конец</a>';
			}
		}

		//Поля пользователей
		foreach ($templ_person as $key => $val) {
			if (!empty($val)) {
				$to_make_form[] = 'ID '.$key.' - <a href="#" onClick="PKP.to_make_form([\'fill_person\', \''.$key.'\'], \'s\'); return false;">в начало</a> / <a href="#" onClick="PKP.to_make_form([\'fill_person\', \''.$key.'\'], \'r\'); return false;">заменить</a> / <a href="#" onClick="PKP.to_make_form([\'fill_person\', \''.$key.'\'], \'e\'); return false;">в конец</a>';
			}
		}

		//Категории
		if (count($cat_sel)) {
			$to_make_form[] = 'Категория - <a href="#" onClick="PKP.to_make_form([\'fill_category\'], \'r\'); return false;">дополнить</a>';
		}

		//Связи
		if ($cpm and count($cpm)) {
			$to_make_form[] = 'Связи - <a href="#" onClick="PKP.to_make_form([\'cpm\'], \'r\'); return false;">дополнить</a>';
		}

		//Все
		$to_make_form[] = '<b>Все - <a href="#" onClick="PKP.to_make_form([\'all\'], \'s\'); return false;">в начало</a> / <a href="#" onClick="PKP.to_make_form([\'all\'], \'r\'); return false;">заменить</a> / <a href="#" onClick="PKP.to_make_form([\'all\'], \'e\'); return false;">в конец</a></b>';

		$to_make_form = '<br><br>До оформить:<br>'.implode('<br>', $to_make_form);
	}

	$json = array(
		'fill_main'     => $fill_main,
		'fill_tags'     => $templ_tags,
		'fill_category' => $cat_sel,
		'fill_xfield'   => $templ_xfields,
		'fill_person'   => $templ_person,
		'cpm'           => $cpm,
		'runtime'       => $time_ex,
		'is_get_up'     => true, //В обработке ответа означает, что пришло оформление
		'text'          => '<a onClick="PKP.fill_form(); return false;" style="cursor: pointer;" href="#">Фильм загружен (заняло '.$time_ex.' сек.). Нажмите сюда чтобы оформить пост.</a>'.$to_make_form,
		'container'     => '#pkinopoisk_result',
	);

	$parser->initClose();

	printData($json);
}

set_file_info('delete');
