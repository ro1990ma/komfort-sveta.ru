<?PHP

define ( 'ROOT_DIR', dirname ( __FILE__ ) );
@header( "Content-type: text/html; charset=utf-8" );

$too_old = 7; // days num
$dirpath = ROOT_DIR."/uploads/mini";

$too_old = $too_old * 86400;
function goClean($dir,$start=false){
	global $too_old;
	$cl = 0;
	$files = scandir($dir);
	foreach($files as $v){
		if($v!='.' AND $v!='..'){
			$f = $dir."/".$v;
			if(is_dir($f)) $cl += goClean($f);
			elseif(filemtime($f)+$too_old<time() AND !$start){
				$cl++;
				unlink($f);
			}
		}
	}
	return $cl;
}

function dirsize($dir){
	$size = array();
	$files = scandir($dir);
	foreach($files as $v){
		if($v!='.' AND $v!='..'){
			$f = $dir."/".$v;
			if(is_dir($f)){
				$temp = dirsize($f);
				$size[0] += $temp[0];
				$size[1] += $temp[1];
			}else{
				$size[0] += filesize($f);
				$size[1]++;
			}
		}
	}
	return $size;
}
function deldir($dir){
	$files = scandir($dir);
	foreach($files as $v){
		if($v!='.' AND $v!='..'){
			$f = $dir."/".$v;
			if(is_dir($f)) deldir($f);
			else unlink($f);
		}
	}
	rmdir($dir);
}

if( $cron == 2 ) goClean($dirpath,true);
else{
	if($_GET['do']=='folder'){
		$deldir = str_replace(".","",$_GET['dir']);
		if($deldir AND is_dir($dirpath."/".$deldir)){
			deldir($dirpath."/".$deldir);
		}
		@header("Location: {$_SERVER['PHP_SELF']}");
		die();
	}

	$dirs = scandir($dirpath);
	foreach($dirs as $d){
		if($d!='.' AND $d!='..' AND is_dir($dirpath."/".$d)){
			$ds = dirsize($dirpath."/".$d);
			$dsize = round($ds[0]/1024,1)." Kb";
			echo "<p><b>$d</b> &mdash; ($dsize / {$ds[1]}) <a href=\"{$_SERVER['PHP_SELF']}?do=folder&dir=$d\">delete</a></p>\n";
		}
	}

	echo "<br/><br/><a href=\"{$_SERVER['PHP_SELF']}?do=clear\">Clear old images</a><br/>\n";

	if( $_GET['do']=='clear' ){
		$cleared = goClean($dirpath,true);
		echo "<p>Cleared $cleared old images</p>";
	}
}

?>