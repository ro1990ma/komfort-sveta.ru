<?php

@error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
@ini_set('display_errors', true);
@ini_set('html_errors', true);
@ini_set('error_reporting', E_ALL ^ E_WARNING ^ E_NOTICE);

define('DATALIFEENGINE', true);
define('ROOT_DIR', substr(dirname(__FILE__), 0, -12));
define('ENGINE_DIR', ROOT_DIR . '/engine');

include ENGINE_DIR.'/data/config.php';
include ENGINE_DIR.'/data/iprem_options.php';

if ($config['http_home_url'] == '') {
	$config['http_home_url'] = explode('engine/ajax/iprem_ajax.php', $_SERVER['PHP_SELF']);
	$config['http_home_url'] = reset($config['http_home_url']);
	$config['http_home_url'] = 'http://' . $_SERVER['HTTP_HOST'] . $config['http_home_url'];
}

//require_once ENGINE_DIR . '/modules/functions.php';

if (function_exists("dle_session")) dle_session();

/**
 * Модуль    iPrem
 * =======================================================
 * Версия:   2.0.3.12
 * =======================================================
 * Файл:     iprem_ajax.php
 * =======================================================
 * Автор:    Sistemos
 * Telegram: @Sistemos
 * E-mail:   sistemos-art@yandex.ru
 * Skype:    Sistemos
 * =======================================================
 */

// Если прилетел id для добавления в закладки
if ($_POST["idbm"] && !$_POST["del"] ) {
    $idbm = intval($_POST["idbm"]);
    
    // получаю массив из временного файла
    $temp_file = ENGINE_DIR . '/inc/iprem/temp.txt';
    $temp_mass = fileEx ($temp_file); 

    // получаю массив из файла для избранных фильмов
    $favorites_file = ENGINE_DIR . '/inc/iprem/favorites_films.txt';
    $favorites_mass = fileEx ($favorites_file);

    // если нахожу совпадение по id полученного на вход во временном файле, 
    // то закидываю фильм в массив для избранного
    foreach ($temp_mass as $key => $val) {
        if ($key == $idbm) {
            // нечем заняться - перекладываю из массива в массив :)
            $favorites_mass[$key]['idkp'] = $temp_mass[$key]['idkp'];
            $favorites_mass[$key]['title'] = $temp_mass[$key]['title'];
            $favorites_mass[$key]['date'] = $temp_mass[$key]['date'];
            $favorites_mass[$key]['rating'] = $temp_mass[$key]['rating'];
        } 
    }
   
    // записываю массив в файл для избранного
    $str_bm = serialize ($favorites_mass);
    file_put_contents ($favorites_file, $str_bm); 

    echo $idbm; 
}

// Если прилетел id и команда на удаление из закладок
if ($_POST["idbm"] && $_POST["del"] == 1) {
    $idbm = intval($_POST["idbm"]);

    // получаю массив из файла для избранных фильмов
    $favorites_file = ENGINE_DIR . '/inc/iprem/favorites_films.txt';
    $favorites_mass = fileEx ($favorites_file);

    // удаляю фильм из массива по его id kp который является ключом
    unset($favorites_mass[$idbm]);
   
    // записываю обратно массив в файл для избранного
    $str_bm = serialize ($favorites_mass);
    file_put_contents ($favorites_file, $str_bm); 

    echo $idbm; 
}

// Если прилетел данные из блока поиска для добавления в избранное
if ($_POST["id_search"]) {
    $idkp = intval($_POST["id_search"]);
    $favorites_file = ENGINE_DIR . '/inc/iprem/favorites_films.txt';
    $favorites_mass = fileEx ($favorites_file);

    $favorites_mass[$idkp]['idkp'] = $idkp;
    $favorites_mass[$idkp]['title'] = $_POST["title_s"];
    $favorites_mass[$idkp]['date'] = $_POST["year_s"];
    $favorites_mass[$idkp]['rating'] = $_POST["rating_s"];

    $favorites_str = serialize($favorites_mass);
    file_put_contents($favorites_file, $favorites_str);
    echo 'Добавлен!';
}


// Если прилетел массив для проверки наличия
if ($_POST["ids"]) {
    $ids = $_POST["ids"];
    $ids_arr = explode(",", $ids);

    $moon_ids = array();
    $hdgo_ids = array();

    if ($iPremOptions['api_token_moon']) {
        // файл в который сохраняются прочеканные id moowalk
        $moon_file = ENGINE_DIR . '/inc/iprem/moonwalk_films.txt';
        $moon_ids = fileEx ($moon_file);        
    }

    if ($iPremOptions['api_token_hdgo']) {
        // файл в который сохраняются прочеканные id hdgo
        $hdgo_file = ENGINE_DIR . '/inc/iprem/hdgo_films.txt';
        $hdgo_ids = fileEx ($hdgo_file);        
    }
  
    // Если требуется прочекать
    if ($moon_file || $hdgo_file) {

         // проверяю наличие фильма в базе moonwalk.cc по id kp. Если есть, то id фильма в массиве ставлю 1, иначе 2 - нет в наличии   
        foreach ($ids_arr as $key => $value) {
            $value = intval($value);
            // чекаю в мунвалк 
            if  ($moon_file) {
                $film_check_moon = apiCheck ($value, 'moon', $iPremOptions); // чекаю фильм в базе moonwalk
                $moon_ids[$value] = $film_check_moon ? 1 : 2;
            } 
            // чекаю в hdgo 
            if  ($hdgo_file) {
                $film_check_hdgo = apiCheck ($value, 'hdgo', $iPremOptions); // чекаю фильм в базе moonwalk
                $hdgo_ids[$value] = $film_check_hdgo ? 1 : 2;
            }                          
            sleep(1); // задержка между итерациями - 1 секунда
        }

        // записываю обновленный массив MoonWalk в файл
        if  ($moon_file) {
            $str_id_moon = serialize($moon_ids);
            file_put_contents($moon_file, $str_id_moon);           
        }
        // записываю обновленный массив HDGO в файл
        if  ($hdgo_file) {
            $str_id_hdgo = serialize($hdgo_ids);
            file_put_contents($hdgo_file, $str_id_hdgo);    
        }
        
        echo ' Loading...'; 

    } else {

        echo 'Error! API-tokens.';
        
    }   

}




// Если прилетело название для поиска на кинопоиске
if ($_POST["kp_query"]) {

    $query = trim($_POST["kp_query"]);
    $kp_content = apiCheck ($query, 'kinopoisk', $iPremOptions);

    preg_match_all ('#class="name"><a\s+href="\/film\/.*-(\d+?)\/sr\/1\/".*?data-type=".*?">(.+?)<\/a>.*?class="year">(.+?)<\/span>#', $kp_content, $kp_mass);  
    preg_match_all ('#class="rating.+?title=.+?\((.+?)\)">(.+?)<\/div>#', $kp_content, $kp_rating);

    // $kp_mass[1] - id
    // $kp_mass[2] - название
    // $kp_mass[3] - год
    // $kp_rating[2], $kp_rating[1] - рейтинг и голоса

echo '<table class="table table-striped table-xs" style="border: 5px solid #d9e4d5;">';
echo <<<HTML
<thead>
    <tr>
        <th style="text-align: center; font-size:14px;">id КП</a></td>
        <th style="font-size: 14px;" >Название</td>
        <th style="text-align: center; font-size: 14px;">Год</td>
        <th style="text-align: center; font-size: 14px;">Рейтинг</td>
        <th style="text-align: center; font-size: 14px;"></td>
    </tr>
</thead>
<tbody>
HTML;

    $rating_n = count($kp_rating[2]);
    $films_n =  count($kp_mass[1]);
    if ($rating_n != $films_n) $films_n = 1;

    // рейтинг бывает не для всех фильмов
    // если есть для всех, то вывожу все фильмы, иначе только первый, чтобы не было проблем

    for ($i=0; $i < $films_n; $i++) { 
        $title = iconv("cp1251", "utf8", $kp_mass[2][$i]);
        $year = $kp_mass[3][$i];
        if ($kp_rating[1][$i]) $rating_2 = '&nbsp;('.$kp_rating[1][$i].')';
        else  $rating_2 = '';
        $rating_1 = $kp_rating[2][$i].$rating_2;


echo <<<HTML

    <tr>
        <td style="text-align: center; font-size:14px;"><a href="https://www.kinopoisk.ru/film/{$kp_mass[1][$i]}" target="_blank" data-original-title="Фильм на Кинопоиске" class="status-info tip idkplink">{$kp_mass[1][$i]}</a></td>
        <td style="font-size: 14px;">{$title}</td>
        <td style="text-align: center; font-size: 14px;">{$year}</td>
        <td style="text-align: center; font-size: 14px;">{$rating_1}</td>
        <td style="text-align: center; font-size: 14px; width: 200px;" id="td-film-{$kp_mass[1][$i]}"><button class="btn bg-teal btn-sm btn-raised legitRipple btn-favorites" data-id="{$kp_mass[1][$i]}" data-title="{$title}" data-year="{$year}" data-rating="{$rating_1}" style="margin-bottom:0;" id="btn-favor">Добавить в избранное</button></td>
    </tr>

HTML;

    }

echo '</tbody></table>';


}


// Если файл существует, то читает и возвращает массив, иначе возвращает пустой массив
function fileEx ($path_file){
    $mass = array();    
    if( file_exists ($path_file) ){
        $id_str = file_get_contents($path_file);
        $mass = unserialize($id_str);
    }
    return $mass;   
}

// возвращает true, если фильм есть в базе MoonWalk.cc или HDGO.cc, иначе false
function apiCheck ($kp_id, $base, $iPremOptions){
    $check = false;
    $proxy = $iPremOptions['proxy'];
    $proxy_auth = $iPremOptions['proxy_auth'];

    if ($base == 'moon') {
       $url_api = 'http://moonwalk.cc/api/videos.json?kinopoisk_id='.$kp_id.'&api_token='.$iPremOptions['api_token_moon'];
       $referer = 'http://moonwalk.cc/'; 
    }
    if ($base == 'hdgo') {
        $url_api = 'http://hdgo.cc/api/video.json?token='.$iPremOptions['api_token_hdgo'].'&kinopoisk_id='.$kp_id;
        $referer = 'http://hdgo.cc/'; 
    } 
    if ($base == 'kinopoisk') {
        $query = iconv("utf8", "cp1251", $kp_id);
        $query = urlencode($query);
        $url_api = 'https://www.kinopoisk.ru/index.php?first=no&what=&kp_query='.$query;
        $referer = 'https://www.kinopoisk.ru/'; 
    }
    
    if ( $curl = curl_init() ) {
        curl_setopt($curl, CURLOPT_URL, $url_api);
        if ($proxy) curl_setopt($curl, CURLOPT_PROXY, $proxy);    
        if ($proxy_auth) curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxy_auth);        
        curl_setopt($curl, CURLOPT_TIMEOUT,5);
        curl_setopt($curl, CURLOPT_USERAGENT,"Mozilla/5.0 (Windows; U; Windows NT 5.1; ru-RU; rv:1.7.12) Gecko/".rand(10000000, 30000000)." Firefox/1.0.7");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($curl, CURLOPT_REFERER, $referer);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_ENCODING, "");
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        $out = curl_exec($curl);
        $output = json_decode($out);
        curl_close($curl);
    } else die( "Error! CURL on the server is not installed!" );

    if ($base == 'moon' || $base == 'hdgo') {
          $data = @end($output);
        if (isset($data->{'kinopoisk_id'})) $check = true;
        return $check;  // возвращаю true     
    } elseif($base == 'kinopoisk') {         
        return $out;  // возвращаю массив\объект
    }
}