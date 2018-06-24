<?PHP
if(!defined('DATALIFEENGINE'))die("Hacking attempt!");

$php_version = str_replace(array(".",","),"",substr(PHP_VERSION,0,3));
@include_once ENGINE_DIR."/mods/miniposter/lic_{$php_version}.php";

function mpic($matches){
	global $miniposter_config;

	$param_str = trim($matches[1]);
	if( preg_match( "#src=['\"]([^'\"]+)['\"]#i", $param_str, $match ) ) $src = $match[1];
	elseif( preg_match( "#default=['\"]([^'\"]+)['\"]#i", $param_str, $match ) ) return $match[1];
	else return $miniposter_config['default'];

	if( preg_match( "#width=['\"](\d+?)['\"]#i", $param_str, $match ) ){
		$w = $match[1];
		if($w>$miniposter_config['max_width']) $w = $miniposter_config['max_width'];
	}else $w = 0;

	if( preg_match( "#height=['\"](\d+?)['\"]#i", $param_str, $match ) ){
		$h = $match[1];
		if($h>$miniposter_config['max_height']) $h = $miniposter_config['max_height'];
	}else $h = 0;

	if( preg_match( "#q=['\"](\d+?)['\"]#i", $param_str, $match ) ){
		$q = $match[1];
		if($q>100) $q = 100;
		elseif($q<1) $q = 1;
	}else $q = $miniposter_config['quality'];

	if( preg_match( "#zoom=['\"]([^'\"]+?)['\"]#i", $param_str, $match ) ) $z = strtolower($match[1])=='yes'?1:0;
	else $z = $miniposter_config['zoom'];

	if( preg_match( "#jpg=['\"]([^'\"]+?)['\"]#i", $param_str, $match ) ) $jpg = strtolower($match[1])=='yes'?1:0;
	else $jpg = $miniposter_config['force_jpg'];

	$type = explode(".",$src);
	$type = strtolower(end($type));
	if(($type!='png' AND $type!='gif') OR $jpg) $type = 'jpg';

	$image_name = md5($src.$z.$q).".".$type;
	$path = $miniposter_config['save_path']."{$w}x{$h}/";
	$path2 = $path . substr($image_name,0,2)."/";
	$image_name = substr($image_name,2,50);

	if(file_exists(ROOT_DIR . $path2 . $image_name)){
		@touch(ROOT_DIR . $path2 . $image_name);
		return $path2 . $image_name;
	}
	$f = (substr($src,0,1)=="/")?ROOT_DIR.$src:$src;
	$rm = parse_url($f);
	if($rm['host'] AND clean_url($rm['host'])!=clean_url($_SERVER['HTTP_HOST'])) $is_remote = 1;
	else $is_remote = 0;
	if(!$miniposter_config['allow_remote'] AND $is_remote) return $miniposter_config['default'];

	if($is_remote){
		$cl = curl_init();
		curl_setopt($cl, CURLOPT_URL, $f);
		curl_setopt($cl, CURLOPT_HEADER, 0);
		curl_setopt($cl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($cl, CURLOPT_CONNECTTIMEOUT, $miniposter_config['timeout']);
		curl_setopt($cl, CURLOPT_TIMEOUT, $miniposter_config['timeout']);
		$str = curl_exec($cl);
		curl_close($cl);
	}else $str = @file_get_contents($f);
	if(!$str) return $miniposter_config['default'];

	$image = imagecreatefromstring($str);
	$iw = @imagesx( $image );
	$ih = @imagesy( $image );
	if($iw<1 OR $ih<1) return $miniposter_config['default'];

	// $pd = makePoster($z,$iw,$ih,$w,$h,$path,$path2);
	$poster = imagecreatetruecolor($pd[0],$pd[1]);
	if($type == 'png'){
		imagealphablending( $poster, false);
		imagesavealpha( $poster, true);
	}
	imagecopyresampled($poster,$image,$pd[2],$pd[3],0,0,$pd[4],$pd[5],$iw,$ih);
	imagedestroy($image);

	if($type == 'gif') imagegif( $poster, ROOT_DIR . $path2 . $image_name );
	elseif($type == 'png') imagepng( $poster, ROOT_DIR . $path2 . $image_name, 8 );
	else imagejpeg( $poster, ROOT_DIR . $path2 . $image_name, $q );

	imagedestroy($poster);
	return $path2 . $image_name;
}
$tpl->result['main'] = preg_replace_callback("#\\{poster(.+?)\\}#i","mpic",$tpl->result['main']);

?>
