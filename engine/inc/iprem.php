<?php
/**
 * Модуль    iPrem
 * =======================================================
 * Версия:   2.0.3.12
 * =======================================================
 * Файл:     iprem.php
 * =======================================================
 * Автор:    Sistemos
 * E-mail:   sistemos-art@yandex.ru
 * Skype:    Sistemos
 * Telegram: @Sistemos
 * =======================================================
 */

if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) die("Hacking attempt!");
include ENGINE_DIR.'/data/iprem_options.php';

$user_group = explode(",", $iPremOptions['user_group'] );
if( !in_array( $member_id['user_group'], $user_group) ) msg("error", $lang['index_denied'], $lang['index_denied']);	

if(!empty($_GET['tab'])) $tab = $_GET['tab'];
else $tab = 'soon';

if(!empty($_GET['year'])) $year = intval($_GET['year']);
else $year = date("Y");
${'y_'.$year} = 'class="active"';

if(!empty($_GET['month'])) $month = intval($_GET['month']);
else { $month = intval(date("m")); }
${'m_'.$month} = 'class="active"';

if(!empty($_GET['page'])) $page = intval($_GET['page']);
else $page = 0;
${'p_'.$page} = 'class="active"';

if(!empty($_GET['sortst'])) $sortst = $_GET['sortst'];
else $sortst = 'date';
${'st_'.$sortst} = 'class="active"';

// если на вкладке сериалов
if ($tab == 'serials') {
	if(!empty($_GET['s_tab'])) $s_tab = $_GET['s_tab'];
	else $s_tab = 'new';
	${'st_serials_'.$serials} = 'class="active"';	
}

$months = array(1 => 'Январь', 2 => 'Февраль', 3 => 'Март', 4 => 'Апрель', 5 => 'Май', 6 => 'Июнь', 7 => 'Июль', 8 => 'Август', 9 => 'Сентябрь', 10 => 'Октябрь', 11 => 'Ноябрь', 12 => 'Декабрь');

// По годам
if ($tab == 'soon') {
	$url = 'https://www.kinopoisk.ru/premiere/ru/'.$year.'/month/'.$month.'/?page='.$page;
	$c_soon = 'class="active"';
	if ($page == 1) $page_t = ' (страница 2)';
	$title_date = 'Дата выхода';
	$title_tab = 'Фильмы за '.$months[$month].' '.$year.'-го'.$page_t;
} 

//Топ ожидаемых
if ($tab == 'soontop') {
	if ($sortst == 'date') $url = 'https://www.kinopoisk.ru/comingsoon/sex/all/sort/date/period/year/';
	if ($sortst == 'rate') $url = 'https://www.kinopoisk.ru/comingsoon/sex/all/period/year/';	
	$c_soontop = 'class="active"';
	$title_date = 'Дата выхода';
	$title_tab = 'Топ ожидаемых фильмов на ближайший год';
} 	

// Топ 250	
if ($tab == 'top250') {
	
	$c_top250 = 'class="active"';
	$title_date = 'Год выхода';
	$title_tab = 'Топ 250 лучших фильмов по версии Кинопоиска';

	if (issetTopFile ('top250')) {
		$Top_file = fileEx (ENGINE_DIR . '/inc/iprem/top250.txt');
		$Films = rasparsFile ($Top_file); // получаю нужные данные из файла
		$kash = true;
	}
	else {
		$url = 'https://www.kinopoisk.ru/top/';
	} 
}


/*
https://www.kinopoisk.ru/top/lists/289/ - списки
https://www.kinopoisk.ru/top/lists/45/film/404900/ - топ 100 сериалов по рейтингу
Заметки разраба:
- Если не сохраняется файл кэша, значит нет условий в функции inFileList() для этого таба
*/

// Сериалы	
if ($tab == 'serials') {

	$c_serials = 'class="active"'; // делаю активной вкладку

	if ($s_tab == 'top') {
		$c_serials_top = 'class="active"';
		$title_date = 'Год выхода';
		$title_tab = 'Топ 100 лучших сериалов, отсортированы по году';	
		// если есть файл, то проверить его на возраст
		// парсить заново, или брать из файла кэша
		if (issetTopFile ('top_serials')) {			
			$Top_file = fileEx (ENGINE_DIR . '/inc/iprem/top_serials.txt');
			$Films = rasparsFile ($Top_file); // получаю нужные данные из файла
			$kash = true;						
		}
		else {
			$url = 'https://www.kinopoisk.ru/top/lists/45/filtr/all/sort/year/perpage/100/';						
		} 	
	} 
	// $s_tab == 'new' - Сериалы - новинки и ожидаемые
	if ($s_tab == 'new') {		

		$c_serials_new = 'class="active"';
		$title_date = 'Год выхода';
		$title_tab = 'Новинки и ожидаемые сезоны сериалов';
		
		if (issetTopFile ('new_serials')) {
			$Serials_new = fileEx (ENGINE_DIR . '/inc/iprem/new_serials.txt');
			$kash = true;
		}
		else {
			$url = 'http://serialochka.ru/novie-seriali/';			
		} 
	}
	
}



// Получение массива избранного
$favorites_mass = massFavor ();
$favor_num = count($favorites_mass);

// Избранное
if ($tab == 'favorites') {
	$c_favorites = 'class="active"';
	$title_date = 'Дата выхода';	
	$title_tab = ($favor_num > 0) ? 'Список избранных' : 'Список избранного пустой.';
}


// версия модуля и основные вкладки

echoheader( "<i class=\"fa fa-play\"></i> "."<span class=\"text-semibold\">iPrem v.2.0.3.12 от 12.09.2017</span>", "Автор: <b>Sistemos.ru</b>. E-mail <b>sistemos-art@yandex.ru</b>. Telegram: <b>@Sistemos</b>" );
echo	"<div class=\"panel panel-default\">
		  <div class=\"panel-heading\">
			<ul class=\"nav nav-tabs nav-tabs-solid\" style=\"width: 100%;\">
			   <li {$c_soon}><a href=\"{$config['admin_path']}?mod=iprem&tab=soon\">По годам</a></li>
				<li {$c_soontop}><a href=\"{$config['admin_path']}?mod=iprem&tab=soontop\">Топ ожидаемых</a></li>
				<li {$c_top250}><a href=\"{$config['admin_path']}?mod=iprem&tab=top250\">Топ 250</a></li>
				<li {$c_serials}><a href=\"{$config['admin_path']}?mod=iprem&tab=serials\">Сериалы</a></li>
				<li {$c_favorites}><a href=\"{$config['admin_path']}?mod=iprem&tab=favorites\">Избранное (<span id=\"favor-n\">{$favor_num}</span>)</a></li></ul>";
// показ ссылки на настройки только админу (есть доп.проверка)
if ($member_id['user_group'] == 1)
	echo "<div class=\"heading-elements\"><ul class=\"icons-list\"><li><a href=\"{$config['admin_path']}?mod=iprem&tab=config\"><i class=\"fa fa-cog position-left\"></i>Настройки iPrem</a></li></ul></div>";			
						
echo "</div>";



/*
* ===================================== START кода для всех вкладок, кроме настроек =====================================
*/ 



if ($tab != 'config') {

$kpid_name = $iPremOptions['kpid']; // для удобства

// получаю контент страницы, если есть страница для парсинга
if ($url) $content = parsUrl ($url, $iPremOptions['proxy'], $iPremOptions['proxy_auth']);

// получаю в массив нужные данные по фильмам
if ($tab == 'favorites') {

	foreach ($favorites_mass as $key => $val) {
		$Films['id'][] = $val['idkp']; // список избранных из файла
	}

} 
// Если вкладка с новиками сериалов
elseif ($s_tab == 'new') {

	// если из файла не получены данные
	if (!$Serials_new['title']){
		// регулярками получаю нужные данные из контента, прямой парсинг
		$Serials_new = serialsNewRegular($content);		
	}

/*
	Заметки разраба:
	- Надо переделать так, чтобы это вызывалось в условиях выше
*/

	// Запись массива в файл, типа кэша на 6 часов.	
	inFileList ($Serials_new, 'new');

	// Дополняю массив инфой о имеющихся сериалах в базе сайта
	$Serials_new = checkSerialBaseTitle ($Serials_new, $kpid_name, $iPremOptions['serial_title'], $iPremOptions['season']);

} 
// для всех остальных вкладок
else {
	if (!$Films) $Films = filmMassRegular($tab,$content); // регулярками	
	if (!$Films['id']) {
		$Films = rasparsFile ($Top_file); // получаю нужные данные из файла		
		if ($Films['id']) {
			echo '<b>Не удалось получить контент со страницы кинопоиска! (взято из файла)</b>';	
		} else {
			die( 'Не удалось получить контент!' );	
		}		
	}
}

if ($s_tab != 'new') {

	// все новости из базы сайта
	$out = bdSelect ();

	// количество фильмов в базе сайта
	$nFilmBD = count ($out['id']); 

	// кол-во фильмов со страницы кинопоиска или вкладки с избранными
	$nFilmKP = ($tab != 'favorites') ? count ($Films['id']) : $favor_num;         

	// Отбираю по id в отдельный массив фильмы которые есть в базе
	$films_yes = checkFilmBaseID ($out, $nFilmKP, $Films, $kpid_name);

	// получаю массив прочеканных id в видеобазах, если они есть, иначе пустой массив
	if ($tab == 'soon' || $tab == 'favorites' AND $iPremOptions['api_token_moon']) $moon_check_mass = filmCheck ('moon'); 	
	if ($tab == 'soon' || $tab == 'favorites' AND $iPremOptions['api_token_hdgo']) $hdgo_check_mass = filmCheck ('hdgo');

}

// Вывод дополнительных вкладок, в зависимости от основного таба
if ($tab == 'soon') {
echo <<<HTML

    <div style="text-align: center;margin-bottom: 10px;">
        <ul class="pagination pagination-sm" style="margin:10px 10px 0 10px;-webkit-box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
    box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
    background-color: #f5f5f5;">
			<li {$y_2006}><a href="?mod=iprem&year=2006">2006</a></li>
			<li {$y_2007}><a href="?mod=iprem&year=2007">2007</a></li>
			<li {$y_2008}><a href="?mod=iprem&year=2008">2008</a></li>
			<li {$y_2009}><a href="?mod=iprem&year=2009">2009</a></li>
			<li {$y_2010}><a href="?mod=iprem&year=2010">2010</a></li>
			<li {$y_2011}><a href="?mod=iprem&year=2011">2011</a></li>
			<li {$y_2012}><a href="?mod=iprem&year=2012">2012</a></li>
			<li {$y_2013}><a href="?mod=iprem&year=2013">2013</a></li>
			<li {$y_2014}><a href="?mod=iprem&year=2014">2014</a></li>
			<li {$y_2015}><a href="?mod=iprem&year=2015">2015</a></li>
			<li {$y_2016}><a href="?mod=iprem&year=2016">2016</a></li>
			<li {$y_2017}><a href="?mod=iprem&year=2017">2017</a></li>
			<li {$y_2018}><a href="?mod=iprem&year=2018">2018</a></li>
			<li {$y_2019}><a href="?mod=iprem&year=2019">2019</a></li>
			<li {$y_2020}><a href="?mod=iprem&year=2020">2020</a></li>
		</ul>  
        <ul class="pagination pagination-sm" style="margin:10px 10px 0 10px;-webkit-box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
    box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
    background-color: #f5f5f5;">
			<li {$m_1}><a href="?mod=iprem&year={$year}&month=1">{$months[1]}</a></li>
			<li {$m_2}><a href="?mod=iprem&year={$year}&month=2">{$months[2]}</a></li>
			<li {$m_3}><a href="?mod=iprem&year={$year}&month=3">{$months[3]}</a></li>
			<li {$m_4}><a href="?mod=iprem&year={$year}&month=4">{$months[4]}</a></li>
			<li {$m_5}><a href="?mod=iprem&year={$year}&month=5">{$months[5]}</a></li>
			<li {$m_6}><a href="?mod=iprem&year={$year}&month=6">{$months[6]}</a></li>
			<li {$m_7}><a href="?mod=iprem&year={$year}&month=7">{$months[7]}</a></li>
			<li {$m_8}><a href="?mod=iprem&year={$year}&month=8">{$months[8]}</a></li>
			<li {$m_9}><a href="?mod=iprem&year={$year}&month=9">{$months[9]}</a></li>
			<li {$m_10}><a href="?mod=iprem&year={$year}&month=10">{$months[10]}</a></li>
			<li {$m_11}><a href="?mod=iprem&year={$year}&month=11">{$months[11]}</a></li>
			<li {$m_12}><a href="?mod=iprem&year={$year}&month=12">{$months[12]}</a></li>
		</ul> 		
    </div>
HTML;

} elseif ($tab == 'soontop') {
	
echo <<<HTML
    <div style="text-align: center; margin-bottom: 10px;">
        <ul class="pagination pagination-sm" style="margin:10px 0 0 0;-webkit-box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
    box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
    background-color: #f5f5f5;">
			<li {$st_date}><a href="?mod=iprem&tab=soontop&sortst=date">По дате выхода</a></li>
			<li {$st_rate}><a href="?mod=iprem&tab=soontop&sortst=rate">По рейтингу</a></li>
		</ul>
	</div>
HTML;

} elseif ($tab == 'serials') {
	
echo <<<HTML
    <div style="text-align: center; margin-bottom: 10px;">
         <ul class="pagination pagination-sm" style="margin:10px 0 0 0;-webkit-box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
    box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
    background-color: #f5f5f5;">
         <li {$c_serials_new}><a href="?mod=iprem&tab=serials&s_tab=new">Новинки и ожидаемые</a></li>
			<li {$c_serials_top}><a href="?mod=iprem&tab=serials&s_tab=top">Топ 100 сериалов</a></li>			
		</ul>
	</div>
HTML;

}

echo '<div class="panel-heading" style="height: 40px;">
    		<div class="col-lg-4 col-md-4 col-xs-4">'.$title_tab.'</div>';
echo <<<HTML
<div class="col-lg-5 col-md-5 col-xs-5" style="margin-top:-5px; text-align:center;">
<span id="load-gif-search" style="display: none;"><img src="/engine/inc/iprem/loader.gif" width="16" height="16"></span>
<input type="text" style="width:63%;max-width:485px;" class="form-control width-550 position-left" name="id_poisk" id="poisk_input" value="" placeholder="Введите название" >&nbsp;
<button class="btn bg-teal btn-sm btn-raised legitRipple" id="poisk_btn" ><i class="fa fa-search position-left"></i> Найти</button>&nbsp;<span class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right" style="margin-top: -5px;" data-rel="popover" data-trigger="hover" data-placement="right" data-content="Ищет на Кинопоиске по названию фильма\сериала\мультфильма." data-original-title="" title=""></span>
</div>
HTML;

// вывод кнопки апдейта, если вкладка "по годам" или "избранное" и заполнен api_token в настройках
if ($tab == 'soon' || $tab == 'favorites' AND $iPremOptions['api_token_moon'] || $iPremOptions['api_token_hdgo']) {
	if ($iPremOptions['api_token_moon']) $text_update = 'MoonWalk';
	if ($iPremOptions['api_token_hdgo']) $text_update = 'HDGO';
	if ($iPremOptions['api_token_moon'] && $iPremOptions['api_token_hdgo']) $text_update = 'MoonWalk\HDGO';
   echo '<div class="title" id="mw-btn" style="float: right;"><span id="load-gif" style="display: none;"><img src="/engine/inc/iprem/loader.gif" width="16" height="16"></span><i class="fa fa-refresh tip"></i><span id="btn-text" style="cursor: pointer;"> '.$text_update.'</span>';
   echo ' <span class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Проверяются только отмеченные галочками! Это кнопка только для проверки наличия в Moonwalk и HDGO. Наличие в БД проверяется автоматически - жать по кнопке не нужно!" data-original-title="" title=""></span></div>';
}

echo '</div>
		<div id="poisk_result_title" style="display:none;">
			<div id="poisk-result-close" style="float: right;
		    cursor: pointer;
		    position: absolute;
		    right: 40px;
		    padding: 8px;
		    font-size: 13px;">Закрыть [X]</div>
			<div id="poisk_result"></div>			
		</div>				
  		<div class="box-content">';



echo '<table class="table table-striped table-xs" style="margin-bottom: 20px;">';

// шапка таблицы для новинок
if ($s_tab == 'new') {
echo <<<HTML
<thead>
	<tr>
		<th style="width: 60px;text-align:center;">#</td>
		<th style="width: 100px;text-align:center;"> id КП </td>
        <th>Название</td>
		<th style="width: 60px;text-align:center;">Edit</td>
		<th style="width: 120px;text-align:center;"> Сезон </td> 
		<th style="width: 120px;text-align:center;"> Дата выхода </td>	    
		<th style="width: 80px;text-align:center;"> Рейтинг </td>
		<th style="width: 140px;text-align:center;"> Когда </td>
		<th style="width: 50px;text-align:center;"> БД </td>	
	</tr>
</thead>	

HTML;

} else {

echo <<<HTML
 <thead>
		<tr>
		<th style="width: 60px;text-align:center;">#</td>
		<th style="width: 100px;text-align:center;"> id КП </td>
        <th>Название</td>	
		<th style="width: 60px;text-align:center;">Edit</td>
        <th style="width: 140px;text-align:center;"> {$title_date} </td>
		<th style="width: 140px;text-align:center;"> Рейтинг </td>		
HTML;
		
		// вывод столбца апдейта, если вкладка "по годам" или "избранное" и заполнен api_token в настройках
		if ($tab == 'soon' || $tab == 'favorites' AND $iPremOptions['api_token_moon']) 
			echo '<th style="width: 50px" title="Наличие фильмов в базе moonwalk.cc"> Moon </td>';

		if ($tab == 'soon' || $tab == 'favorites' AND $iPremOptions['api_token_hdgo']) 
			echo '<th style="width: 50px" title="Наличие фильмов в базе hdgo.cc"> HDGO </td>';


echo '<th style="width: 50px;text-align:center;" title="Наличие фильмов в базе вашего сайта"> БД </td>
      <th style="width: 30px;text-align:center;"><span><input type="checkbox" id="check-all" class="icheck"/></span></td>';   
      // если не разрешено в настройках, то показ только админу
      if ($iPremOptions['user_favorites'] || $member_id['user_group'] == 1)
      	echo '<th style="width: 50px;text-align:center;"> # </td>';
echo '</tr>
  </thead>';

}

// массив данных для вывода в таблицу	
$massShow = array( 
						'nomer'     => 1,
						'idkp'      => '', 
						'idbd'      => '', 
						'title'     => '', 
						'link'      => '', 
						'date'      => '', 
						'rating'    => '', 
						'have'      => '', 
						'have_moon' => '',
						'have_hdgo' => ''
						);
	

$tempMass = array();

// для вкладок "по годам", "топ лжидаемых", "топ 250"
if ($tab == 'soon' || $tab == 'soontop' || $tab == 'top250' || $tab == 'serials') {

	if ($page == 1) $massShow['nomer'] = 26;

	for ( $i=0; $i < $nFilmKP; $i++ ){
		// заполняю массив
		// $Films['id'][$i] - id kp
		$massShow['rating'] = $Films['rating'][1][$i].' '.$Films['rating'][2][$i];
		// топ ожидаемых
		if ($tab == 'soontop') {
			$massShow['date'] = $Films['date'][1][$i].' '.$Films['date'][2][$i].' '.$Films['date'][3][$i];
			$massShow['date'] = iconv("cp1251", "utf8", $massShow['date']);
			$massShow['rating'] = $Films['rating'][1][$i].'('.$Films['rating'][2][$i].')';			
		}
		// по годам			
		if ($tab == 'soon') {
			$massShow['date'] = $Films['date'][3][$i].'-'.$Films['date'][2][$i].'-'.$Films['date'][1][$i]; 
			$massShow['rating'] = $Films['rating'][$Films['id'][$i]]; // по id kp беру рейтинг из массива			
		}
		// топ 250
		if ($tab == 'top250') {
			$massShow['date'] = $Films['date'][$i];	
			$massShow['rating'] = ratingKash ($Films,$i,$kash);
		} 
		// топ сериалов
		if ($tab == 'serials') {
			$massShow['rating'] = ratingKash ($Films,$i,$kash);
			$massShow['date'] = $Films['date'][$i];
		}
		
		$massShow['title'] = iconv("cp1251", "utf8", $Films['title'][$i]);
		$massShow['idkp'] = $Films['id'][$i];
		$massShow['idbd'] = $films_yes['idbd'][$i];
		$massShow['link'] = $films_yes['link'][$i];
		$massShow['have'] = $films_yes['have'][$i];
		$massShow['have_moon'] = $moon_check_mass[$Films['id'][$i]]; // наличие в moonwalk
		$massShow['have_hdgo'] = $hdgo_check_mass[$Films['id'][$i]]; // наличие в hdgo

		// вывод в таблицу
		showRow($massShow,$tab,$favorites_mass);

		// массив для сохранения во временный файл
		$tempMass[$massShow['idkp']] = $massShow;

		$massShow['nomer']++;
	}	
} 



// для вкладки "избранное"
if ($tab == 'favorites') {
	$vn = 1;
	foreach ($favorites_mass as $key => $val) {
		$val['nomer'] = $vn;
		// Добавление в массив $val данных о наличие фильма в БД сайта, из массива $films_yes	
		$nf = $vn-1;
		if ($films_yes['have'][$nf]) {
			$val['have'] = $films_yes['have'][$nf];
			$val['idbd'] = $films_yes['idbd'][$nf];
			$val['link'] = $films_yes['link'][$nf];
		}
		// Добавление в массив $val прочеканых id в видеобазах
		if ($moon_check_mass[$val['idkp']]) $val['have_moon'] = $moon_check_mass[$val['idkp']];
		if ($hdgo_check_mass[$val['idkp']]) $val['have_hdgo'] = $hdgo_check_mass[$val['idkp']];
		// вывод в таблицу
		showRow($val,$tab,$favorites_mass);
		$vn++;
	}	
}

// для вкладки "избранное"
if ($s_tab == 'new') {
	serialsNewTableShow ($Serials_new); // вывод тавлицы для новинок сериалов	
}

 // Запись списка в файл
inFileList ($tempMass,$tab);

echo '</table></div> 
    <div class="box-footer padded">';

// если фильмов больше 25-ти и вкладка "по годам", то вывожу кнопки страниц
if ($nFilmKP >= 25 || $page == 1 AND $tab == 'soon') {
	echo '<div style="text-align: center;">
		        <ul class="pagination pagination-sm" style="-webkit-box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
    box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
    background-color: #f5f5f5;">
					<li '.$p_0.'><a href="?mod=iprem&year='.$year.'&month='.$month.'&page=0">Страница 1</a></li>
					<li '.$p_1.'><a href="?mod=iprem&year='.$year.'&month='.$month.'&page=1">Страница 2</a></li>
				</ul> 
			</div>';	
}
		
echo '<div class="panel-footer" style="margin-top:20px">
            <div class="pull-right"><span class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-left" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Получите список фильмов, которые вы отметили галочками!" data-original-title="" title=""></span><div class="btn bg-slate-600 btn-sm btn-raised position-left legitRipple" id="getids">Получить список</div></div><br><br>
            <textarea name="notice" style="width:100%;height:200px;background-color:#fff;" cols="150" rows="3" id="kpids"></textarea>
        </div>        
      </div>
';


echo '<script type="text/javascript">';
include ENGINE_DIR.'/inc/iprem/iprem.js';
echo '</script>';

}

/*
* ===================================== END кода для всех вкладок, кроме настроек =====================================
*/




/*
*	================ Настройки =============================
*/

if ($tab == 'config') {
	
// Доступ к настройкам только админу
if($member_id['user_group'] != 1) {
	msgNoEnter ();
} else {

	// при сохранении
	if( $action == "save" ) {

		if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) die( "Hacking attempt! User not found" );

		$save_con = array();
		$save_con = $_POST['save_con'];

		$save_con['kpid'] = strip_tags ( trim($save_con['kpid']) );
		$save_con['api_token_moon'] = strip_tags ( trim($save_con['api_token_moon']) );
		$save_con['api_token_hdgo'] = strip_tags ( trim($save_con['api_token_hdgo']) );
		$save_con['proxy'] = strip_tags ( trim($save_con['proxy']) );
		$save_con['proxy_auth'] = strip_tags ( trim($save_con['proxy_auth']) );
		$save_con['user_favorites'] = intval($save_con['user_favorites']);

		// если нет сопадения по группам, то даю доступ только админу
		$save_con['user_group'] = trim($save_con['user_group']);
		if ($save_con['user_group'] != '1' && $save_con['user_group'] != '1,2' && $save_con['user_group'] != '1,2,3')
			$save_con['user_group'] = 1;		

		$find = array();
		$replace = array();
		
		$find[] = "'\r'";
		$replace[] = "";
		$find[] = "'\n'";
		$replace[] = "";
		
		$save_con = $save_con + $iPremOptions;	
		$handler = fopen( ENGINE_DIR . '/data/iprem_options.php', "w+" );

		fwrite( $handler, "<?PHP \n\n//iPrem Configurations\n\n\$iPremOptions = array (\n\n" );
		foreach ( $save_con as $name => $value ) {
			fwrite( $handler, "'{$name}' => '{$value}',\n\n" );
		}
		fwrite( $handler, ");\n\n?>" );
		fclose( $handler );
		
		$user_group = $save_con['user_group'];
		$db->query("UPDATE " . PREFIX . "_admin_sections SET allow_groups = '$user_group' WHERE name = 'iprem' ");	

		clear_cache();
		msgGood(); // настройки успешно сохранены

	} else {

echo <<<HTML
</div>
<form action="$PHP_SELF?mod=iprem&tab=config&action=save" name="conf" id="conf" method="post">
<div class="panel panel-default" style="margin-top: -20px;">
  <div class="panel-body">
    Настройки iPrem (* доступны только администратору)
  </div>
  <div class="table-responsive">
  <table class="table table-striped">
HTML;

	$fields = array(
		'kp_id' => array( '' => '----' ),
		'season' => array( '' => '----' ),
		'serial_title' => array( '' => '----' ),
	);

	$xfields = xfieldsload();
	if ($xfields) foreach ($xfields as $key => $value) {
		$fields['kp_id']["{$value[0]}"] = " {$value[1]} ";
		$fields['season']["{$value[0]}"] = " {$value[1]} ";
		$fields['serial_title']["{$value[0]}"] = " {$value[1]} ";
	}

	showRowConfig( 'Доп.поле с ID кинопоиска', 'Доп.поле, в котором находится id кинопоиска. Дожно быть заполнено для каждого фильма.', makeDropDown( ( $fields['kp_id'] ), "save_con[kpid]", "{$iPremOptions['kpid']}" ) );
	showRowConfig( 'API-token Moonwalk', 'Ваш API-token Moonwalk.cc, если нужна проверка в этой видеобазе. <br>API-token посмотреть можно в настройках личного кабинета http://moonwalk.cc/partners/edit_profile <br><b>* Не обязательно</b>', "<input type=text class=\"form-control width-550 position-left\" name=\"save_con[api_token_moon]\" value=\"{$iPremOptions['api_token_moon']}\" >");
	showRowConfig( 'API-token HDGO', 'Ваш API-token HDGO.cc. Не обязательно, если не нужна проверка в этой видеобазе. <br>API-token посмотреть можно здесь http://hdgo.cc/content/base/api_index.php <br><b>* Не обязательно</b>', "<input type=text class=\"form-control width-550 position-left\"  name=\"save_con[api_token_hdgo]\" value=\"{$iPremOptions['api_token_hdgo']}\" >");
	showRowConfig( 'Группы пользователей', 'Каким группам пользователей разрешен доступ к модулю. <br>Допустимые варианты (<b>через запятую, без пробелов</b>): <br><b>1</b> - админу;<br><b>1,2</b> - админу и глав.редактору;<br><b>1,2,3</b> - админу, глав.редактору и журналисту;', "<input type=text class=\"form-control width-550 position-left\" name=\"save_con[user_group]\" value=\"{$iPremOptions['user_group']}\" >");
	showRowConfig( 'Разрешить "Избранное" не только админу?', 'Если включено, то другим разрешенным группам пользователей будет возможно<br> добавлять фильмы и сериалы во вкладку "Избранное" и удалять их оттуда.<br> Иначе это сможет делать только админ.', makeCheckBox( "save_con[user_favorites]", "{$iPremOptions['user_favorites']}" ) );	
	showRowConfig( 'Доп.поле с сезоном сериала', 'Доп.поле в котором находится номер сезона сериала. <br>В нем может находится как просто цифра, так и значение "4 сезон", например. <br><b>* Не обязательно</b>', makeDropDown( ( $fields['season'] ), "save_con[season]", "{$iPremOptions['season']}" ) );
	showRowConfig( 'Доп.поле с названием сериала', 'Доп.поле в котором находится точное название сериала. Если не указывать, то будет поиск сериала по заголовку. <br><b>* Не обязательно</b>', makeDropDown( ( $fields['serial_title'] ), "save_con[serial_title]", "{$iPremOptions['serial_title']}" ) );	
	showRowConfig( 'Прокси', 'Например, 216.115.147.25:19306  <br><b>* Не обязательно</b>', "<input type=text class=\"form-control width-550 position-left\" name=\"save_con[proxy]\" value=\"{$iPremOptions['proxy']}\" >");
	showRowConfig( 'Логин и пароль для прокси', 'Логин и пароль для прокси, если прокси с авторизацией. Например, <b>pupkin:12345</b>  <br><b>* Не обязательно</b>', "<input type=text class=\"form-control width-550 position-left\" name=\"save_con[proxy_auth]\" value=\"{$iPremOptions['proxy_auth']}\" >");
	
	echo <<<HTML
</table></div></div>
<div style="margin-bottom:30px;">
<input type="hidden" name="user_hash" value="{$dle_login_hash}" />
<button type="submit" class="btn bg-teal btn-raised position-left legitRipple"><i class="fa fa-floppy-o position-left"></i>{$lang['user_save']}</button>
</div>
</form>
HTML;



		if(!is_writable(ENGINE_DIR . '/data/iprem_options.php')) {
			$lang['stat_system'] = str_replace ("{file}", "engine/data/iprem_options.php", $lang['stat_system']);
			echo "<div class=\"alert alert-error\">{$lang['stat_system']}</div>";
		}

	}

} // доступ
} // config

echofooter();



/*
* ===================================== Ниже функции ==================================================================
*/

// все новости из базы сайта
function bdSelect (){
	global $db;
	$sql = $db->query("SELECT id, title, alt_name, xfields FROM ". PREFIX ."_post");
	$out = array();

	while ( $row = $db->get_row( $sql ) ) {
		$out['id'][]       = $row['id'];
		$out['title'][] = $row['title'];
		$out['alt_name'][] = $row['alt_name'];
		$out['xfields'][]  = xfieldsdataload($row['xfields']);
	}
	$db->free( $sql );
	return $out;	
}

// Отбираю по id в отдельный массив фильмы которые есть в базе
function checkFilmBaseID ($out, $nFilmKP, $Films, $kpid_name) {
	// $out = массив фильмов из базы сайта
	// $nFilmKP - кол-во фильмов со страницы кинопоиска
	// $Films = массив чистых фильмов со страницы кинопоиска
	// $kpid_name - название доп.поля id кинопоиска
	$nFilmBD = count ($out['id']); // количество фильмов в базе сайта
	
	$films_yes = array();
	for ( $m=0; $m < $nFilmKP; $m++ ){
		for ( $i=0; $i < $nFilmBD; $i++ ){
			$kp_id_pars = $Films['id'][$m];
			// если kinopoisk_id совпадают, то забираю данные и выбрасываю из цикла
			if ( $out['xfields'][$i][$kpid_name] == $kp_id_pars AND $kp_id_pars !== NULL ) {
				$films_yes['have'][$m] = '1';
				$films_yes['link'][$m] = '/'.$out['id'][$i].'-'.$out['alt_name'][$i].'.html';
				$films_yes['idbd'][$m] = $out['id'][$i];
				break;
			}
		}
	}	
	return $films_yes;
}


// Регулярки для получения массива данных по сериям для вкладки new
function serialsNewRegular ($content){
	// Для вкладки "Новинки и ожиждаемые" сериалов
	$content = iconv("cp1251", "utf8", $content);
	preg_match_all ('#title=".*?">(.+?)<\/a>.*?\n.*class="rengname">(|(.+?))<\/span>#', $content, $title_mass);
	preg_match_all ('#class="newspr">(.*?)<\/span><\/td>\n.*?<td\s+class="tvs">(.*?)<\/td>\n.*?<td\s+class="tvd">(.*?)<\/td>\n.*?<td\s+class="tvd">(.*?)<\/td>\n.*?<td class="tvo">(.*?)<\/td>#', $content, $td_mass);

	$Serials = array(
		'idkp'    =>  '',
		'title'    => $title_mass[1], 
		'title_en' => $title_mass[2], 
		'country'  => $td_mass[1], 
		'season'   => $td_mass[2], 
		'date'     => $td_mass[3], 
		'rating'   => $td_mass[4], 
		'soon'     => $td_mass[5] // как скоро / как давно
		);

	return $Serials;
}

// вывод данных в таблицу
function serialsNewTableShow ($Serials){

	echo '<div class="alert alert-info alert-styled-left alert-arrow-left alert-component"><b>Внимание!</b> Это тестовый раздел (недоработанный).<br>Наличие сериалов из ЭТОГО СПИСКА в БД сайта проверяются по точному совпадению их названий с заголовками новостей, 
	<br>или с доп.полем для заголовка, указанного в настройках.
	<br>Также, если в настройках указано доп.поле с сезоном, то проверяется совпадение по сезону.</div>';

	echo '<tbody>';
	$num = 1;
	$n = count($Serials['title']);
	for ($i=0; $i < $n; $i++) { 
		echo '<tr>
		   	<td style="text-align: center; font-size:14px;">'.$num.'</td>
		   	<td style="text-align: center; font-size:14px;">'.$Serials['idkp'][$i].'</td>
		   	<td style="font-size: 15px;">'.$Serials['title'][$i].'<br><span style="font-size:11px;">'.$Serials['title_en'][$i].' ('.$Serials['country'][$i].')</span></td>
		   	<td style="text-align: center; font-size: 14px;">'.$Serials['edit'][$i].'</td>
		   	<td style="text-align: center; font-size: 14px;">'.$Serials['season'][$i].'</td>
		   	<td style="text-align: center; font-size: 14px;">'.$Serials['date'][$i].'</td>
		   	<td style="text-align: center; font-size: 14px;">'.$Serials['rating'][$i].'</td>
		   	<td style="text-align: center; font-size: 14px;">'.$Serials['soon'][$i].'</td>
		   	<td style="text-align: center; font-size: 14px;">'.$Serials['have'][$i].'</td>
		   	</tr>';
		$num++;
	}
	echo '</tbody>';
}

	/*
		Отбираю по названию сериалы которые есть в базе
		
		*** Надо допилить поиск через id категорий сериалов

		$Serials - массив спарсенных сериалов
		$serials_title - название доп.поля с точным названием сериала, если существует
		$kpid_name - название доп.поля id кинопоиска

	*/
function checkSerialBaseTitle ($Serials, $kpid_name, $serials_title = '', $pole_season_name = '') {
	
	$bd = bdSelect (); // результат из базы
	$nNewsBD = count ($bd['id']);    // количество фильмов в базе сайта
	$nNewsPars = count ($Serials['title']); // количество фильмов в базе сайта

	$serials_yes = array();
	for ( $m=0; $m < $nNewsPars; $m++ ){
		for ( $i=0; $i < $nNewsBD; $i++ ){

			// название спарсенного сериала
			$title_pars = $Serials['title'][$m];
			
			// если есть доп.поле с точным названием сериала и оно не пустое, то поиск по нему, иначе поиск по тайтлу новости из базы
			if ($serials_title) {
				$title_bd = $bd['xfields'][$i][$serials_title];
				if ($title_bd == '') $title_bd = $bd['title'][$i];			
			} 				
			else 	
				$title_bd = $bd['title'][$i];

			// если тайтлы совпадают, то забираю данные и выбрасываю из цикла
			if ( $title_bd == $title_pars AND $title_pars !== NULL ) {				

				$Serials['link'][$m] = '/'.$bd['id'][$i].'-'.$bd['alt_name'][$i].'.html';
				$Serials['idbd'][$m] = $bd['id'][$i];
				$Serials['idkp'][$m] = $bd['xfields'][$i][$kpid_name];

				$Serials['title'][$m] = '<a href="'.$Serials['link'][$m].'" target="_blank" data-original-title="Перейти на страницу сериала" class="status-info tip idkplink">'.$Serials['title'][$m].'</a>';
				$Serials['edit'][$m] = '<a href="?mod=editnews&action=editnews&id='.$Serials['idbd'][$m].'" target="_blank" data-original-title="Перейти к редактированию" class="status-info tip"><i class="fa fa-edit"></i></a>';
				$Serials['idkp'][$m] = '<a href="https://www.kinopoisk.ru/film/'.$Serials['idkp'][$m].'" target="_blank" data-original-title="Фильм на Кинопоиске" class="status-info tip idkplink">'.$Serials['idkp'][$m].'</a>';
				
				$Serials['have'][$m] = '<span class="text-success" title="Сериал найден в базе и сезоны совпадают"><i class="fa fa-check-circle" style="font-size: 16px;"></i></span>';
				
				// если заполнено доп.поле с номером сезона		
				if ($pole_season_name) {

					$season_bd = intval ( $bd['xfields'][$i][$pole_season_name] ); // сезон из бд сайта
					$season_pars = intval ( $Serials['season'][$m] ); // сезон из спасенного массива
					// то проверяю больше ли сезон из массива, чем сезон из базы И заполнено ли доп.поле
					if ( $season_pars > $season_bd AND $season_bd != '' ) 
					$Serials['have'][$m] = '<span class="status-pending" title="Сериал найден в базе, но сезон меньше '.$season_pars.'"><i class="fa fa-check-circle" style="font-size: 16px;"></i></span>';
				}				

				break;
			}
		}
	}	
	return $Serials;
}



// вывод данных в таблице
function showRow($massShow, $tab, $favorites_mass) {
	global $iPremOptions;
	global $member_id;

	$massShow['edit'] = '';	
	// наличие в базе
	if ($massShow['have'] == '1') // есть
		$massShow['have'] = '<span class="text-success"><b><i class="fa fa-check-circle" style="font-size: 16px;"></i></b></span>';
	else // ytn
		$massShow['have'] = '<span class="text-danger"><b><i class="fa fa-exclamation-circle" style="font-size: 16px;"></i></b></span>';	

	// наличие в moonwalk
	if ($massShow['have_moon'] == '1') // есть 
		$massShow['have_moon'] = '<span class="text-success"><b><i class="fa fa-check-circle" style="font-size: 16px;"></i></b></span>';
	elseif ($massShow['have_moon'] == '2') // нет
		$massShow['have_moon'] = '<span class="text-danger"><b><i class="fa fa-exclamation-circle" style="font-size: 16px;"></i></b></span>';
	else // не проверялось
		$massShow['have_moon'] = 'n/a';	

	// наличие в HDGO
	if ($massShow['have_hdgo'] == '1') // есть 
		$massShow['have_hdgo'] = '<span class="text-success"><b><i class="fa fa-check-circle" style="font-size: 16px;"></i></b></span>';
	elseif ($massShow['have_hdgo'] == '2') // нет
		$massShow['have_hdgo'] = '<span class="text-danger"><b><i class="fa fa-exclamation-circle" style="font-size: 16px;"></i></b></span>';
	else // не проверялось
		$massShow['have_hdgo'] = 'n/a';		

	if ($massShow['link']) {
		$massShow['title'] = '<a href="'.$massShow['link'].'" target="_blank" data-original-title="Перейти на страницу с фильмом" class="status-info tip" >'.$massShow['title'].'</a>';
		$massShow['edit'] = '<a href="?mod=editnews&action=editnews&id='.$massShow['idbd'].'" target="_blank" data-original-title="Перейти к редактированию" class="status-info tip"><i class="fa fa-edit"></i></a>';
	}	

	$massShow['title_clear'] = strip_tags($massShow['title']);

	echo '<tr data-tr-id="'.$massShow['idkp'].'">
	   	<td style="text-align: center; font-size:14px;">'.$massShow['nomer'].'</td>
	   	<td style="text-align: center; font-size:14px;"><a href="https://www.kinopoisk.ru/film/'.$massShow['idkp'].'" target="_blank" data-original-title="Фильм на Кинопоиске" class="status-info tip idkplink">'.$massShow['idkp'].'</a></td>
	   	<td style="font-size: 14px;" for="film'.$massShow['idkp'].'">'.$massShow['title'].'</td>
	   	<td style="text-align: center; font-size: 14px;">'.$massShow['edit'].'</td>
	   	<td style="text-align: center; font-size: 14px;">'.$massShow['date'].'</td>
	   	<td style="text-align: center; font-size: 14px;">'.$massShow['rating'].'</td>';

	if ($tab == 'soon' || $tab == 'favorites' AND $iPremOptions['api_token_moon']) 
	echo '<td style="text-align: center; font-size: 14px;">'.$massShow['have_moon'].'</td>'; 

	if ($tab == 'soon' || $tab == 'favorites' AND $iPremOptions['api_token_hdgo']) 
	echo '<td style="text-align: center; font-size: 14px;">'.$massShow['have_hdgo'].'</td>';

	echo '<td style="text-align: center;">'.$massShow['have'].'</td>
			<td style="text-align: center;"><input type="checkbox" class="kp-filmid" id="film'.$massShow['idkp'].'" data-check-id="'.$massShow['idkp'].'" name="filmid" value="'.$massShow['title_clear'].' https://www.kinopoisk.ru/film/'.$massShow['idkp'].'" /></td>';

	// если в массиве избранного есть масиив с ключом id kp, но иконку плюса не вывожу
	$plus = ( $favorites_mass[$massShow['idkp']] ) ? $plus = '' : '<i title="Добавить в избранное" class="fa fa-plus-circle"></i>';
	$bmarks_class = 'bookmarks';
	if ($tab == 'favorites') {
		$plus = '<i title="удалить из избранного" alt="удалить" class="fa fa-trash-o text-danger"></i>';
		$bmarks_class = 'bookmarks-del';
	} 

	// если не разрешено в настройках, то показ только админу
   if ($iPremOptions['user_favorites'] || $member_id['user_group'] == 1)
	echo '<td style="text-align: center;"><span class="'.$bmarks_class.'" for="'.$massShow['idkp'].'" style="cursor:pointer;">'.$plus.'</span></td></tr>';

}

// Парсинг контента по урлу
function parsUrl ($url, $proxy = false, $proxy_auth = false) {
    if ( $curl = curl_init() ) {
        curl_setopt($curl, CURLOPT_URL, $url);
		  if ($proxy) curl_setopt($curl, CURLOPT_PROXY, $proxy);	
		  if ($proxy_auth) curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxy_auth);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERAGENT,"Mozilla/5.0 (Windows; U; Windows NT 5.1; ru-RU; rv:1.7.12) Gecko/".rand(10000000, 30000000)." Firefox/1.0.7");
        curl_setopt($curl, CURLOPT_REFERER, "https://www.kinopoisk.ru/");
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_ENCODING, "");
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 6);
        curl_setopt($curl, CURLOPT_TIMEOUT, 9);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $content = curl_exec($curl);
        curl_close($curl);
    } else
        $content = file_get_contents($url);	
	return $content;
}

// Получение рейтинга для фильмов по годам
function ratingPars ($content) {	
	preg_match_all ('/data-film-id="(\d+)"\s+class="ajax_rating">\s+.+?<u>(.+?)<b>(.+?)<\/b><\/u><\/i>/', $content, $r_mass);
		
	$rating_sort = array ();
	$nr = count ($r_mass[1]);
	for ($i=0; $i < $nr; $i++) {
		$rating_sort[$r_mass[1][$i]] = $r_mass[2][$i].'('.$r_mass[3][$i].')';
	}	
	return $rating_sort;
}


// Получение рейтинга для фильмов по годам из Ajax
function ratingParsAjax ($films_id,$date_mass) {
	$n = count ($films_id);
	for ($i=0; $i < $n; $i++) {
		// рейтинг ожидания
		if ($date_mass[1][$i] == date("Y") && $date_mass[2][$i] > date("m")) {
			if ($i > 0) $soon_id .= '%7C'.$films_id[$i];
			else $soon_id .= $films_id[$i];			
		} 
		// основной рейтинг
		else {
			if ($i > 0) $film_id .= '%7C'.$films_id[$i];
			else $film_id .= $films_id[$i];				
		}
	}
	
	if ($soon_id) $what = 'what=await_rating&where='.$soon_id;
	if ($film_id) $what2 = 'what2=rating&where2='.$film_id;
	
	$url = 'https://www.kinopoisk.ru/handler_get_parameter.php?'.$what.'&'.$what2;
	$rating_content = parsUrl ($url, $iPremOptions['proxy'], $iPremOptions['proxy_auth']);	
	
	preg_match_all ('/rating_\'\+(.+?)\).*?<u>(.+?)<b>(.+?)<\/b><\/u>/', $rating_content, $r_mass);
	
	$rating_sort = array ();
	$nr = count ($r_mass[1]);
	for ($i=0; $i < $nr; $i++) {
		$rating_sort[$r_mass[1][$i]] = $r_mass[2][$i].'('.$r_mass[3][$i].')';
	}	

	return $rating_sort;
}


// Регулярки для получения массива данных по фильмам, в зависимости от вкладки
// на вход подается: $tab - название вкладки, $content - контент страницы
function filmMassRegular ($tab,$content){
	// Для вкладки "По годам"
	if ($tab == 'soon') {
		preg_match_all ('/itemprop="name".*><a\shref="\/film\/.*-(\d+?)\/">(.+?)<\/a>/', $content, $title_id_mass);	
		preg_match_all ('/<meta\s+itemprop="startDate"\s+content="(\d+)-(\d+)-(\d+)"\s+\/>/', $content, $date_mass);
		// Если не получилось взять данные функцией ratingPars, то пробую ratingParsAjax
		$rating_mass = ratingPars ($content);
		if (!$rating_mass) $rating_mass = ratingParsAjax ($title_id_mass[1],$date_mass);
	} 

	// Для вкладки "Топ Ожидаемых"
	if ($tab == 'soontop') {
		preg_match_all ('/<a\shref="\/film\/.*-(\d+?)\/">(.+?)\s\(\w+.+?\)<\/a>/', $content, $title_id_mass);
		preg_match_all ('/<div\s+class="bar_statistics">(.+?)\((.+?)\)<\/div>/', $content, $rating_mass);
		preg_match_all ('/<div\s+class="day\s+day_(.+?)"><\/div>\n.*<div\s+class="month">(.+?)<\/div>\n.*<div\s+class="year">(.+?)<\/div>/', $content, $date_mass);		
	}

	// Для вкладки "Топ 250"
	if ($tab == 'top250') {
		preg_match_all ('/<a\s+href="\/film\/.*-(\d+?)\/"\s+class="all">(.+?)\s+\((.+?)\)<\/a>/', $content, $title_id_mass);
		preg_match_all ('/<a\s+href="\/film\/.*-\d+?\/votes\/"\s+class="continue">(.+?)<\/a>\s.*>(.+?)<\/span>/', $content, $rating_mass);
		$date_mass = $title_id_mass[3];

	}

	// Для вкладки "Топ 100" сериалов
	if ($tab == 'serials') {
		preg_match_all ('#filmId:(.+?),\n.*filmName:\'(.+?)\'#', $content, $title_id_mass);
		preg_match_all ('#class="ratingBlock\s+.*\n.*?>(.+?)<.*?>(.+?)<\/span>#', $content, $rating_mass);
		preg_match_all ('#>.*?<\/a><span.*?>.*?\((.+?)\)\s+<nobr>#', $content, $date_mass);
		$date_mass = $date_mass[1];
	}
	
	$films_mass = array('title' => $title_id_mass[2], 'id' => $title_id_mass[1], 'date' => $date_mass, 'rating' => $rating_mass);
	return $films_mass;
}




// Если файл есть, то читает и возвращает массив, иначе возвращает пустой массив
function fileEx ($path_file){
	$mass = array();    
	if( file_exists ($path_file) ){
	  $id_str = file_get_contents($path_file);
	  $mass = unserialize($id_str);
	}
	return $mass;   
}

// Количество фильмов в избранном
function massFavor () {
	$favorites_file = ENGINE_DIR . '/inc/iprem/favorites_films.txt';	
	$favorites_mass = fileEx ($favorites_file); // получаю массив
	return $favorites_mass;	
}


// Чтение файла с прочекаными id фильмов в Moonwalk, возврат массива
function filmCheck ($base){
	// файл в который сохраняются проверенные
	if ($base == 'moon') $path_file = ENGINE_DIR . '/inc/iprem/moonwalk_films.txt';
	if ($base == 'hdgo') $path_file = ENGINE_DIR . '/inc/iprem/hdgo_films.txt';
	// получаю массив наличия id в moonwalk, если они есть, иначе пустой массив
	$id_mass = fileEx ($path_file);
	return $id_mass;	 	
}

// Запись во временный файл, типа буфера, на вход - массив для записи в файл
// Или запись в файл списка сериалов
function inFileList ($mass,$tab){
	if ( $tab == 'top250' && !issetTopFile ('top250') ){
		$file = ENGINE_DIR . '/inc/iprem/top250.txt';
		$str = serialize ($mass);
		if ($str) file_put_contents ($file, $str);		
	} 
	elseif ( $tab == 'serials' && !issetTopFile ('top_serials') ){
		$file = ENGINE_DIR . '/inc/iprem/top_serials.txt';
		$str = serialize ($mass);
		if ($str) file_put_contents ($file, $str);		
	}
	elseif ( $tab == 'new' && !issetTopFile ('new_serials') ){
		$file = ENGINE_DIR . '/inc/iprem/new_serials.txt';
		$str = serialize ($mass);
		if ($str) file_put_contents ($file, $str);		
	} 
	else {
		$file = ENGINE_DIR . '/inc/iprem/temp.txt';
		$str = serialize ($mass);
		if ($str) file_put_contents ($file, $str);		
	}
}

// Проверка на существование файла топ сериалов и проверка его на возраст
function issetTopFile ($topfile_name) {
	$res = false;
	$chekfile = ENGINE_DIR . '/inc/iprem/'.$topfile_name.'.txt';
	// если файл существует и больше 20 байт
	if( file_exists ($chekfile) AND filesize($chekfile) > 20) {
		// чекаю на возраст на актуальность
		$age = fileAge ($chekfile);
		if ($age) $res = true;	
	}
	return $res;
}

// актуальный возраст файла, если старше - не актуальный (6 часов по-умаолчанию)
function fileAge ($chekfile, $interval = 6) {
	$timeout = $interval * 60 * 60; // 6 часов
	$file_date = @filemtime($chekfile);
	$file_date = time()-$file_date;	
	$buffer = ( $file_date >  $timeout ) ? false : true;
	return $buffer;
}



// Сборка нужного массива из данных файла кэша
function rasparsFile ($mass) {
	foreach ($mass as $key => $val) {
		$Films['id'][] = $val['idkp']; 
		$Films['title'][] = iconv("utf8", "cp1251", $val['title']); 
		$Films['date'][] = $val['date']; 
		$Films['rating'][] = $val['rating']; 
	}
	return $Films;	
}

// Если рейтинг из кэш-файла, то допиливаю
function ratingKash ($Films,$i,$kash) {
	if ($kash) $rating = $Films['rating'][$i]; // если из файла (кэша)
	else $rating = round($Films['rating'][1][$i], 2).' '.$Films['rating'][2][$i]; 
	return $rating;	
}

function myPrint ($mass) {
	echo "<pre>";
	print_r($mass);
	echo "</pre>";	
}

/*
	Функции для страницы настроек
*/

function showRowConfig($title = "", $description = "", $field = "", $class = "") {
	echo "<tr>
       <td class=\"col-xs-6 col-sm-6 col-md-7 {$class}\"><h6 class=\"media-heading text-semibold\">{$title}</h6><span class=\"text-muted text-size-small hidden-xs\">{$description}</span></td>
       <td class=\"col-xs-6 col-sm-6 col-md-5 settingstd {$class}\">{$field}</td>
       </tr>";
}
	
function makeDropDown($options, $name, $selected) {
	$output = "<select class=\"uniform\" style=\"min-width:100px;\" name=\"$name\">\r\n";
	foreach ( $options as $value => $description ) {
		$output .= "<option value=\"$value\"";
		if( $selected == $value ) {
			$output .= " selected ";
		}
		$output .= ">$description</option>\n";
	}
	$output .= "</select>";
	return $output;
}

function makeCheckBox($name, $selected) {
	$selected = $selected ? "checked" : "";
	return "<input class=\"switch\" type=\"checkbox\" name=\"$name\" value=\"1\" {$selected}>";
}

// сообщение об успешном сохранении настроек
function msgGood (){

echo <<<HTML
</div>
<div class="alert alert-success alert-styled-left alert-arrow-left alert-component message_box">
  <h4>Изменения сохранены</h4>
  <div class="panel-body">
		<table width="100%">
		    <tbody><tr>
		        <td height="80" class="text-center">Настройки были успешно сохранены</td>
		    </tr>
		</tbody></table>
	</div>
	<div class="panel-footer"><div class="text-center"><a class="btn btn-sm bg-teal btn-raised position-left legitRipple" href="$PHP_SELF?mod=iprem&tab=config">Вернуться назад</a></div></div>
</div>
HTML;

}

// сообщение о закрытом доступе
function msgNoEnter (){

echo <<<HTML
</div>
<div class="alert alert-danger alert-styled-left alert-arrow-left alert-component message_box">
  <h4>У вас нет доступа к настройкам модуля iPrem</h4>
  <div class="panel-body">
		<table width="100%">
		    <tbody><tr>
		        <td height="80" class="text-center">Даступ к настройкам модуля разрешен только администратору!</td>
		    </tr>
		</tbody></table>
	</div>
</div>
HTML;

}

?>