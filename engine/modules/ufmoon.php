<?php 

if (!defined('DATALIFEENGINE')) die("Go fuck yourself!");
include_once('engine/data/ufmoon_options.php');

$id = intval($id);
$kp_id = intval($kp_id);
$kach = strip_tags($kach);

if (empty($kach)) {
	$kach = $ufMoonOptions['kash_treiler'];
	$nokach = true;
}
if (empty($ufMoonOptions['kash_ts'])) $ufMoonOptions['kash_ts'] = $ufMoonOptions['kash_cam'];
if (empty($ufMoonOptions['kash_dvd'])) $ufMoonOptions['kash_dvd'] = $ufMoonOptions['kash_cam'];

$myConfig  = array(
	'cachePrefix' => !empty($cachePrefix) ? $cachePrefix : 'archives',
	'cacheSuffix' => !empty($cacheSuffix) ? $cacheSuffix : false
);


$kach_array = array($ufMoonOptions['kash_cam'], $ufMoonOptions['kash_ts'], $ufMoonOptions['kash_treiler'], $ufMoonOptions['kash_dvd'], "DVDScr", "DVDRip", "WEBRip"); 
$ignoreMassHD = array($ufMoonOptions['kash_hd'], "WEB-DL", "BDRip", "BluRay", "HDTV", "BDRemux");

if($ufMoonOptions['dop_ignore']){
	$ignoreMass = explode(",", $ufMoonOptions['dop_ignore']);
	array_walk($ignoreMass, 'trim_value');
}

// -- c версии 1.9
// проверка параметров опции
$zvuk = strip_tags($zvuk);
$long_conf = longReload($ufMoonOptions['long_conf'],$kach,$zvuk,$god);
if ($long_conf) { 
	if (!in_array($kach, $ignoreMass)) $no_ignore = true;
	if (in_array($kach, $kach_array) || in_array($kach, $ignoreMassHD)) $face_control = true; 
} else { // 1.8
	if (!in_array($kach, $ignoreMass) AND !in_array($kach, $ignoreMassHD)) $no_ignore = true;
	if (in_array($kach, $kach_array)) $face_control = true;
}
// -- end 1.9


if ($ufMoonOptions['allow_module_on'] > 0){
	
if ($no_ignore) { 
	
	if ($face_control) { // фейс-контроль
		
		$cacheName = md5(implode('_', $myConfig));
		$myModule  = false;
		$myModule  = dle_cache($myConfig['cachePrefix'] . $id, $kp_id.$cacheName, $myConfig['cacheSuffix']); 
		if (!$myModule) {			
		
			// проверяю фильм на мунвалке
			$moonMass = moonkach ($kp_id,$ufMoonOptions['api_token'],$ufMoonOptions['audio_replace']);
			
			if($moonMass){

				// новая дата, если нужно
				$newDate = ($ufMoonOptions['up_date'] > 0 AND !$nokach ) ? ", date = '".date('Y-m-d H:i:s')."'" : '';
					
				// если в мунвалке camrip 
				if ( $moonMass['kach_moon'] == 'camrip' ){
					
					// сохраняю в кеш camrip чисто длЯ временной метки
					$myModule = $ufMoonOptions['kash_cam'];
					create_cache($myConfig['cachePrefix'] . $id, $myModule, $kp_id.$cacheName, $myConfig['cacheSuffix']);					
					
					// если включен парсинг названий файлов (доп.проверка камрипов) и на сайте CAM или TS или трейлер
					if ( $ufMoonOptions['cam_pars'] > 0) {
						
						if ( in_array($kach, $kach_array) ){

							$qArray = HvostPars ($moonMass,$ufMoonOptions);						
							if ($qArray['flagkach']){
								$newKach = false;
								if ($qArray['flagkach'] == 'hd') {
									if ($ufMoonOptions['big_qual'] > 0 AND $qArray['quality']) $newKach = $qArray['quality']; 
									else $newKach = $ufMoonOptions['kash_hd'];
								}																	
								if ($qArray['flagkach'] == 'dvd' AND $kach == $ufMoonOptions['kash_ts'] || $kach == $ufMoonOptions['kash_cam'] || $kach == $ufMoonOptions['kash_treiler']) {
									if ($ufMoonOptions['big_qual'] > 0 AND $qArray['quality']) $newKach = $qArray['quality']; 
									else $newKach = $ufMoonOptions['kash_dvd'];
								}
								if ($qArray['flagkach'] == 'ts' AND $kach == $ufMoonOptions['kash_cam'] || $kach == $ufMoonOptions['kash_treiler']) $newKach = $ufMoonOptions['kash_ts'];																						
								if ($newKach) {									
									$bdMass = $db->super_query("SELECT xfields, category FROM ". PREFIX ."_post  WHERE id = {$id}");						
									$newXfields = xfUpdate ($bdMass['xfields'], $newKach, $moonMass['iframe_url'], $moonMass['translator'], $ufMoonOptions, $qArray['ufm_size']);									
									// монипулЯции с категориЯми длЯ TS или DVD качества
									if ($qArray['flagkach'] == 'dvd' || $qArray['flagkach'] == 'ts' and $kach == $ufMoonOptions['kash_cam']) $newCategory = ''; 
									elseif ($qArray['flagkach'] == 'dvd' || $qArray['flagkach'] == 'ts' and $kach == $ufMoonOptions['kash_treiler']) $newCategory = catUpdate ('camrip', $bdMass['category'], $ufMoonOptions['cat_treiler'], $ufMoonOptions['cat_cam'], $ufMoonOptions['cat_hd']);	
									else $newCategory = catUpdate ('hdrip', $bdMass['category'], $ufMoonOptions['cat_treiler'], $ufMoonOptions['cat_cam'], $ufMoonOptions['cat_hd']);													 
									
									if ($nokach) $newCategory = ''; // не менЯю категорию если доп.поле ранее было пустое и заполнЯетсЯ впервые
									
									$db->query("UPDATE " . PREFIX . "_post SET xfields = '$newXfields' {$newDate} {$newCategory} WHERE id = {$id}");
									clear_cache( array( 'news_', 'full_' ) );
								}							
							}												
						} elseif (in_array($kach, $ignoreMassHD)) $rusHD = true;
					}
					
					// если на сайте трейлер и парсинг названиЯ не дал результатов, то обновлЯю на CAM, который получен по API
					if ( $kach == $ufMoonOptions['kash_treiler'] AND empty($newKach)){
						$bdMass = $db->super_query("SELECT xfields, category FROM ". PREFIX ."_post  WHERE id = {$id}");
						$newKach = $ufMoonOptions['kash_cam'];
						$newXfields = xfUpdate ($bdMass['xfields'], $newKach, $moonMass['iframe_url'], $moonMass['translator'], $ufMoonOptions, $qArray['ufm_size']);
						$newCategory = catUpdate ($moonMass['kach_moon'], $bdMass['category'], $ufMoonOptions['cat_treiler'], $ufMoonOptions['cat_cam'], $ufMoonOptions['cat_hd']);				
						if ($nokach) $newCategory = '';
						$db->query("UPDATE " . PREFIX . "_post SET xfields = '$newXfields' {$newDate} {$newCategory} WHERE id = {$id}");
						clear_cache( array( 'news_', 'full_' ) );
					}
					
				}	
				
				// если в мунвалке hdrip, то обновлЯю на HD
				if ( $moonMass['kach_moon'] == 'hdrip' || $rusHD){
					
					$newKach = $ufMoonOptions['kash_hd'];
					// сохраняю в кеш hdrip чисто длЯ временной метки
					create_cache($myConfig['cachePrefix'] . $id, $newKach, $kp_id.$cacheName, $myConfig['cacheSuffix']);					
					
					if ($ufMoonOptions['ufm_size'] > 0 OR $ufMoonOptions['big_qual'] > 0) {		
					
						$qArray = HvostPars ($moonMass,$ufMoonOptions);
						if ($ufMoonOptions['big_qual'] > 0) $newKach = ($qArray['quality'] AND $qArray['flagkach'] != 'dvd') ? $qArray['quality'] : $ufMoonOptions['kash_hd'];
					}
					if ($kach != $newKach || $moonMass['translator'] != $zvuk) { 
						$bdMass = $db->super_query("SELECT xfields, category FROM   ". PREFIX ."_post  WHERE id = {$id}");
						$newXfields = xfUpdate ($bdMass['xfields'], $newKach, $moonMass['iframe_url'], $moonMass['translator'], $ufMoonOptions, $qArray['ufm_size']);
						$newCategory = catUpdate ($moonMass['kach_moon'], $bdMass['category'], $ufMoonOptions['cat_treiler'], $ufMoonOptions['cat_cam'], $ufMoonOptions['cat_hd']);					
						// с версии 1.9							
						if ($nokach || in_array($kach, $ignoreMassHD)) $newCategory = '';
						if ($long_conf['up'] == 'no' && !in_array($kach, $kach_array)) $newDate = '';
						
						$db->query("UPDATE " . PREFIX . "_post SET xfields = '$newXfields' {$newDate} {$newCategory} WHERE id = {$id}");	
						clear_cache( array( 'news_', 'full_' ) );
					}
				}				
				
				
			} else {
				
				// если фильма на мунвалке нет (или мунвалк недоступен), то сохраняю в кеш трейлер чисто длЯ временной метки
				create_cache($myConfig['cachePrefix'] . $id, $ufMoonOptions['kash_treiler'], $kp_id.$cacheName, $myConfig['cacheSuffix']);
				
				if ($nokach) {
					// заполнЯю доп.поле с качеством, если ранее оно не существовало (было пустым)
					$bdMass = $db->super_query("SELECT xfields FROM ". PREFIX ."_post  WHERE id = {$id}");						
					$newXfields = qualityDefault ($bdMass['xfields'], $ufMoonOptions);
					$db->query("UPDATE " . PREFIX . "_post SET xfields = '$newXfields' WHERE id = {$id}");
					clear_cache( array( 'news_', 'full_' ) );				
				}						
			}	
			
		} else {
			
			// если кеш не пустой
			$fileT = cashTiming ($myConfig['cachePrefix'] . $id, $ufMoonOptions['time_cash'], $kp_id.$cacheName, $myConfig['cacheSuffix']);
			if (!$fileT) { clear_cache($myConfig['cachePrefix'] . $id.'_'); } // очищаю кеш, если требуется				
		}
		if ($nokach) echo 'null';
		else echo $kach;
		
	} else echo 'error';


} 
else echo $kach; // фильм игнорируется

} 
else echo $kach; // модуль выключен




// актуальность кеша (отвечает за периодичность проверки качества)
function cashTiming ($prefix, $timeout=300, $cache_id = false, $member_prefix = false){	
	global $is_logged, $member_id;
	if( $is_logged ) $end_file = $member_id['user_group'];
	else $end_file = "0";

	if( ! $cache_id ) {		
		$key = $prefix;	
	} else {		
		$cache_id = md5( $cache_id );		
		if( $member_prefix ) $key = $prefix . "_" . $cache_id . "_" . $end_file;
		else $key = $prefix . "_" . $cache_id;	
	}	
	$buffer = @file_get_contents( ENGINE_DIR . "/cache/" . $key . ".tmp" );
	if ( $buffer !== false) {
		$file_date = @filemtime( ENGINE_DIR . "/cache/" . $key . ".tmp" );
		$file_date = time()-$file_date;		
		if ( $file_date >  $timeout ) {
			$buffer = false; // устарел
		}
		return $buffer;		
	} else return $buffer;	
}


// возвращает массив: с качеством, url плеера, озвучка, категориЯ. если false.
function moonkach ($kp_id,$api_token,$str_audio){
	
	$url_api = 'http://moonwalk.cc/api/videos.json?kinopoisk_id='.$kp_id.'&api_token='.$api_token;
	if ( $curl = curl_init() ) {
		curl_setopt($curl, CURLOPT_URL, $url_api);
		curl_setopt($curl, CURLOPT_TIMEOUT,10);
		curl_setopt($curl, CURLOPT_USERAGENT,"Mozilla/5.0 (Windows; U; Windows NT 5.1; ru-RU; rv:1.7.12) Gecko/".rand(10000000, 30000000)." Firefox/1.0.7");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($curl, CURLOPT_REFERER, "http://moonwalk.cc/");
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_ENCODING, "");
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 120);
		curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
		$out = curl_exec($curl);
		$output = json_decode($out);
		curl_close($curl);
	} else $output = json_decode(file_get_contents($url_api));
	
	$data = @end($output);
	if (!isset($data->{'camrip'})) $data = false;		
	
	if ($data) {   
		$film_kach_m = array();
		$camrip = $data->{'camrip'};		
		if ($camrip) {
			$film_kach_m['kach_moon'] = 'camrip';
			if (count ($output) > 1 AND !$output[0]->{'camrip'}) { // если кривой ответ по API и в первой ячейке HD
				$data = $output[0];
				$film_kach_m['kach_moon'] = 'hdrip';
			}
		}
        else {
            $film_kach_m['kach_moon'] = 'hdrip';
            if (count ($output) > 1 AND $data->{'translator_id'} != '21' AND !$output[0]->{'camrip'} ) $data = $output[0];       
        } 			         
		$film_kach_m['iframe_url'] = $data->{'iframe_url'}; 
		$film_kach_m['translator'] = $data->{'translator'};
		
		if(!empty($str_audio)) $sound = audioReplace($str_audio,$film_kach_m['translator']); // автозамена озвучек
		if ($sound) $film_kach_m['translator'] = $sound;
		
		$film_kach_m['category'] = $data->{'category'};		
		return $film_kach_m;
	} 	
	
}

// возвращает строку $xfields с обновленными доп.полями
function xfUpdate ($bdMass, $kash_new, $iframe_url, $audio_new, $ufMoonOptions, $ufm_size) {

	$xfieldsdata = xfieldsdataload($bdMass);
	
	if ($kash_new) $xfieldsdata[$ufMoonOptions['dp_kach']] = $kash_new;
	if ($iframe_url) $xfieldsdata[$ufMoonOptions['dp_player']] = $iframe_url;
	
	if ($ufMoonOptions['dp_audio']) { // если используетсЯ доп.поле длЯ озвучки
		if ($audio_new) $xfieldsdata[$ufMoonOptions['dp_audio']] = $audio_new;
		elseif ($ufMoonOptions['na_audio']) $xfieldsdata[$ufMoonOptions['dp_audio']] = $ufMoonOptions['na_audio'];
	}
	if ($ufMoonOptions['ufm_size'] > 0 and $ufm_size) $xfieldsdata['ufm_size'] = $ufm_size; // если вкл. опциЯ добавлениЯ 1080 и 750 в поле ufm_size

	$xfields = array();
	foreach ($xfieldsdata as $key => $value) {
		$value = str_replace('|', '&#124;', $value);
		$xfields[] = "$key|$value";
	}
	$xfields = implode('||', $xfields);
	$xfields = addslashes($xfields);
	return $xfields;
}

// простановка дефолтного значениЯ качества
function qualityDefault ($bdMass, $ufMoonOptions) {
	$xfieldsdata = xfieldsdataload($bdMass);					
	$xfieldsdata[$ufMoonOptions['dp_kach']] = $ufMoonOptions['kash_treiler'];	
	$xfields = array();
	foreach ($xfieldsdata as $key => $value) {
		$value = str_replace('|', '&#124;', $value);
		$xfields[] = "$key|$value";
	}
	$xfields = implode('||', $xfields);
	$xfields = addslashes($xfields);
	return $xfields;
}
		
// добавление или удаление id категорий для фильма в зависимости от обновленного качества, если выбраны id категорий
// параметры: качество на мунвалке 'camrip' или 'hdrip', строка с категориЯми фильма из Ѓ„, id категории ’рейлера, id категории с CAM-рипами, id категории с HD-рипами
function catUpdate ($moonKach, $bdCategory, $catTreiler, $catCam, $catHD)
{	
	$catTreiler = (!empty($catTreiler) AND $catTreiler > 0) ? $catTreiler : false; 
	$catCam = (!empty($catCam) AND $catCam > 0) ? $catCam : false; 
	$catHD = (!empty($catHD) AND $catHD > 0) ? $catHD : false; 	
	$newCateg = explode(",", $bdCategory);	
	if ($catTreiler) $newCateg = array_diff ($newCateg, array($catTreiler));					
	if ($moonKach == 'camrip') {		
		if ( $catCam && !in_array($catCam, $newCateg) ) $newCateg[] = $catCam;	
	}	
	if ($moonKach == 'hdrip') {		
		if ($catCam) $newCateg = array_diff ($newCateg, array($catCam));					
		if ( $catHD && !in_array($catHD, $newCateg) ) $newCateg[] = $catHD;		
	}
	$newCategStr = implode(",", $newCateg);
	if(!empty($newCategStr)){
		if ($newCategStr != $bdCategory) $newCategStr = ", category = '".$newCategStr."'";
		else $newCategStr = '';		
	} 
	return $newCategStr;			
}

// используетсЯ длЯ чистки пробелов в массиве
function trim_value(&$value)
{
    $value = trim($value);
}



// парсит имена файлов и возвращает массив значение качеcтва c флажками. 
// длЯ российcких и зарубежных фильмов разный результат
function HvostPars ($filmInfoArray,$ufMoonOptions){
	
	$catFilm = $filmInfoArray['category'];
	$urlFilm = $filmInfoArray['iframe_url']; 
	

	$dataVideo = curlPars ($urlFilm);	 	
	preg_match_all ("/<script.*>insertVideo\('(.*?)',\s+'player'\);<\/script>/", $dataVideo, $fileNameArray);
	$fileName = $fileNameArray[1][0];


	$HD_mass = array("WEB-DL", "WEB.DL", "BDRip", "BluRay", "Blu-ray", "BDRemux", "Rip1080_HDR", "HDTV", "HDRip");
	$TS_mass = array("TS.avi", "TS.720", "TS.PROPER");
	$DVD_mass = array("DVDScr", "DVDRip", "WEBRip");
		
	$qArray = array();
	$qArray['flagkach'] = false;
	
	foreach($HD_mass as $k=>$val){
		$hd_hvost = strstr($fileName, $val);
		if ($hd_hvost) {
			$qArray['quality'] = $val;
			if ($catFilm == 'russian') $qArray['flagkach'] = 'hd'; // если российский фильм
			elseif ($catFilm != 'russian') {
				if ( $filmInfoArray['kach_moon'] == 'camrip' ) { // если зарубежный фильм в разделе с камрипами					
					$qArray['quality'] = false;
				    $qArray['flagkach'] = 'dvd';
				} 
				else $qArray['flagkach'] = 'hd';
			}
		}		
	}
	
	if ($qArray['quality'] == "Rip1080_HDR") $qArray['quality'] = false; //потом проставитсЯ качество длЯ HD согласно настройкам модулЯ
	if ($qArray['quality'] == "HDRip")       $qArray['quality'] = false; // та же хернЯ
	if ($qArray['quality'] == "WEB.DL")      $qArray['quality'] = "WEB-DL"; // привожу к единому названию
	if ($qArray['quality'] == "Blu-ray")     $qArray['quality'] = "BluRay";
	if ($qArray['quality'] == "BDRemux")     $qArray['quality'] = "BDRip";
	
	if (!$qArray['flagkach']) {
	foreach($DVD_mass as $k=>$val){
		$dvd_hvost = strstr($fileName, $val);	
		if ($dvd_hvost) {
			$qArray['quality'] = $val;
			$qArray['flagkach'] = 'dvd';
		}		
	}		
	}
	
	if (!$qArray['flagkach']) {
	foreach($TS_mass as $k=>$val){
		$ts_hvost = strstr($fileName, $val);	
		if ($ts_hvost) {
			$qArray['quality'] = 'TS';
			$qArray['flagkach'] = 'ts';
		}		
	}		
	}
	
	if (!$qArray['flagkach']) $qArray['quality'] = false; // CAMRip	
	$qArray['ufm_size'] = ( strstr($fileName, '1080') ) ? '1080' : '720'; 
	
	return $qArray;
}


function curlPars ($url){
	if( $curl = curl_init($url) ){
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_TIMEOUT,10);
		curl_setopt($curl, CURLOPT_USERAGENT,"Mozilla/5.0 (Windows; U; Windows NT 5.1; ru-RU; rv:1.7.12) Gecko/".rand(10000000, 30000000)." Firefox/1.0.7");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($curl, CURLOPT_REFERER, "http://moonwalk.cc/");
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_ENCODING, "");
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 120);
		curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
		$content = curl_exec($curl);
		curl_close($curl);
	} else $content = file_get_contents($url);
	return $content;
}

// функция замены стандартных названий озвучек
function audioReplace($str,$translator) {
	$soundMass = explode(",", $str);
	foreach($soundMass as $val){
	   $sm = explode('=', trim($val));
	   $fsm[$sm[0]] = $sm[1];
	}
	$sound = ( array_key_exists($translator, $fsm) ) ? $fsm[$translator] : false;	
	return $sound;	
}

// установка продолжительности проверки исходя из заданного качества, озвучки, возраста (с версии 1.9)
function longReload($long_conf,$kach,$zvuk,$god) {
	$conf = false;		
	if ($long_conf) {		
		$god = (empty($god)) ? date('Y') : intval(strip_tags($god));								
		$confMass = explode("|", $long_conf);
		if (empty($zvuk) && $confMass[1]) $zvuk = $confMass[1];
		if ( (date('Y') - $god) < $confMass[3]) { 
			$sound_fin = !strstr($confMass[1], $zvuk);
			$kach_fin = !strstr($confMass[0], $kach);
			if ( ( !$confMass[0] && $sound_fin )    OR  // если качество не задано и озвучка не совпадает
				 ( !$confMass[1] && $kach_fin  )    OR  // если озвучка не задана  или качество не совпадает
				 ( $confMass[0]  && $kach_fin  )    OR  // если качество задано, но не совпадает
				 ( $confMass[1]  && $sound_fin )    OR  // если озвучка задана, но не совпадает
				 ( !$confMass[0] && !$confMass[1] )	){	// если озвучка и качество не заданы										 
				$conf['up'] = ($confMass[2] > 0) ? 'yes' : 'no'; // нужен ли ап даты 
			}
		}
	} 
	return $conf;	
}
 
?>