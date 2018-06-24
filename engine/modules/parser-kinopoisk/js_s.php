<?php

if (!defined('DATALIFEENGINE')) {
    die("Hacking attempt!");
}

$news_nomer = $row['id'];

if ($news_nomer) {
$news_nomer = $row['id'];
} else {
$news_nomer = '0';
}

$avtor = $row['autor'];

if ($avtor) {
$avtor = $row['autor'];
} else {
$avtor = $member_id['name'];
}

if ($config['version_id'] > 10.2) {
$tokenfield = '$(\'input\').tokenfield(\'destroy\'); $(\'textarea\').tokenfield(\'destroy\');';
}

if ($config['version_id']>="12.0") {

$parser_chosen = 'chosen';

} else {

$parser_chosen = 'liszt';

}

$parsing_script = <<<JS
<script type="text/javascript">
JS;

$parsing_script .= "

function htmlDecode( html ) {
    var a = document.createElement( 'a' ); a.innerHTML = html;
    return a.textContent;
};

/*!
 * JavaScript
*/


$(function () {
    $('input[name=\"film_title\"]').keypress(function (event) {
        if (event.which == '13') {
            event.preventDefault();
        }
    })
});

$(function () {
    $('input[name=\"torrent_search\"]').keypress(function (event) {
        if (event.which == '13') {
            event.preventDefault();
        }
    })
});

function clickButton(e) {

 if(window.event) // IE
 {
  keynum = e.keyCode
 }
 else if(e.which) // Netscape/Firefox/Opera
 {
  keynum = e.which
 }
 if(keynum == 13) document.getElementById('button_kp').click();
return false;
};


function films_kinopoisk ( all )
	{
	

	  var all_film = all;
		var film_title = document.getElementById('film_title').value;
    var search_film = 'name_film';
    var id_news = '{$news_nomer}';
    var avtor = '{$avtor}';
    
    var myParser = document.getElementById('torrent_info');
    myParser.innerHTML = '';
    
		ShowLoading('');
      
		$.post('{$config['http_home_url']}engine/modules/parser-kinopoisk/search_kinopoisk.php', { all_film: all_film, film_title: film_title, search_film: search_film, id_news: id_news, avtor: avtor }, function(data){
	
			HideLoading('');
	
			$('#torrent_info').html(data);
	
		});

	return false;

};


function parsing_kinopoisk ( id_films, step )
	{
	  var id_kinopoisk = id_films;
    var kino_film = 'film_film';
    var id_postersv = $('#posters_vibor_g').val();
    var id_screenv = $('#screen_vibor_g').val();
    var id_stillsv = $('#stills_vibor_g').val();
    var id_wallv = $('#wall_vibor_g').val();
    var id_postes = $('#postes_vibor_g').val();
    var steps = step;
    var id_news = '{$news_nomer}';
    var avtor = '{$avtor}';
    
    var myParser = document.getElementById('torrent_info');
    myParser.innerHTML = '';
    
		ShowLoading('');
      
		$.post('{$config['http_home_url']}engine/modules/parser-kinopoisk/parser_kinopoisk.php', { id_kinopoisk: id_kinopoisk, kino_film: kino_film, id_postersv: id_postersv, id_screenv: id_screenv, id_stillsv: id_stillsv, id_wallv: id_wallv, id_postes: id_postes, steps: steps, id_news: id_news, avtor: avtor }, function(data){

			HideLoading('');
	
			$('#torrent_info').html(data);
	
		});

	return false;

};


function hdlight_kino ( id_films, n_hdlight, search_name )
	{
	  var id_hdlight = id_films;
	  var nn_hdlight = n_hdlight;
	  var name_hdlight = search_name;
    var search_hdlight = 'on';
    var id_news = '{$news_nomer}';
    var avtor = '{$avtor}';
    
		ShowLoading('');
      
		$.post('{$config['http_home_url']}engine/modules/parser-kinopoisk/hdlight.php', { id_hdlight: id_hdlight, search_hdlight: search_hdlight, name_hdlight: name_hdlight, nn_hdlight: nn_hdlight, id_news: id_news, avtor: avtor }, function(data){

			HideLoading('');
	
			$('#torrent_info').html(data);
	
		});

	return false;

};

function stormo_kino ( id_films, n_stormo, search_name )
	{
	  var id_stormo = id_films;
	  var nn_stormo = n_stormo;
	  var name_stormo = search_name;
    var search_stormo = 'on';
    var id_news = '{$news_nomer}';
    var avtor = '{$avtor}';
    
		ShowLoading('');
      
		$.post('{$config['http_home_url']}engine/modules/parser-kinopoisk/stormo.php', { id_stormo: id_stormo, search_stormo: search_stormo, name_stormo: name_stormo, nn_stormo: nn_stormo, id_news: id_news, avtor: avtor }, function(data){

			HideLoading('');
	
			$('#torrent_info').html(data);
	
		});

	return false;

};


function freerutor_kino ( id_films, n_freerutor, search_name )
	{
	  var id_freerutor = id_films;
	  var nn_freerutor = n_freerutor;
	  var name_freerutor = search_name;
    var search_freerutor = 'on';
    var id_news = '{$news_nomer}';
    var avtor = '{$avtor}';
    var torrent_title = document.getElementById('torrent_freerutor').value;
		ShowLoading('');
      
		$.post('{$config['http_home_url']}engine/modules/parser-kinopoisk/freerutor.php', { id_freerutor: id_freerutor, search_freerutor: search_freerutor, name_freerutor: name_freerutor, nn_freerutor: nn_freerutor, torrent_title: torrent_title, id_news: id_news, avtor: avtor }, function(data){

			HideLoading('');
	
			$('#torrent_info').html(data);
	
		});

	return false;

};


function rutor_kino ( id_films, n_rutor, search_name )
	{
	  var id_rutor = id_films;
	  var nn_rutor = n_rutor;
	  var name_rutor = search_name;
    var search_rutor = 'on';
    var id_news = '{$news_nomer}';
    var avtor = '{$avtor}';
    var torrent_title = document.getElementById('torrent_rutor').value;
		ShowLoading('');
      
		$.post('{$config['http_home_url']}engine/modules/parser-kinopoisk/rutor.php', { id_rutor: id_rutor, search_rutor: search_rutor, name_rutor: name_rutor, nn_rutor: nn_rutor, torrent_title: torrent_title, id_news: id_news, avtor: avtor }, function(data){

			HideLoading('');
	
			$('#torrent_info').html(data);
	
		});

	return false;

};

function katushka_kino ( id_films, n_katushka, search_name, to_name )
	{
	  var id_katushka = id_films;
	  var nn_katushka = n_katushka;
	  var name_katushka = search_name;
	  var tor_name = to_name;
    var search_katushka = 'on';
    var id_news = '{$news_nomer}';
    var avtor = '{$avtor}';
    var torrent_title = document.getElementById('torrent_katushka').value;
		ShowLoading('');
      
		$.post('{$config['http_home_url']}engine/modules/parser-kinopoisk/katushka.php', { id_katushka: id_katushka, search_katushka: search_katushka, name_katushka: name_katushka, nn_katushka: nn_katushka, torrent_title: torrent_title, tor_name: tor_name, id_news: id_news, avtor: avtor }, function(data){

			HideLoading('');
	
			$('#torrent_info').html(data);
	
		});

	return false;

};

function fast_torrent_kino ( id_films, n_fast_torrent, search_name, to_name )
	{
	  var id_fast_torrent = id_films;
	  var nn_fast_torrent = n_fast_torrent;
	  var name_fast_torrent = search_name;
	  var tor_name = to_name;
    var search_fast_torrent = 'on';
    var id_news = '{$news_nomer}';
    var avtor = '{$avtor}';
    var torrent_title = document.getElementById('torrent_fast_torrent').value;
		ShowLoading('');
      
		$.post('{$config['http_home_url']}engine/modules/parser-kinopoisk/fast_torrent.php', { id_fast_torrent: id_fast_torrent, search_fast_torrent: search_fast_torrent, name_fast_torrent: name_fast_torrent, nn_fast_torrent: nn_fast_torrent, torrent_title: torrent_title, tor_name: tor_name, id_news: id_news, avtor: avtor }, function(data){

			HideLoading('');
	
			$('#torrent_info').html(data);
	
		});

	return false;

};

function kinopoikcategory( categoryMINI ){

    $('#category').val('').trigger('{$parser_chosen}:updated');
    
    var Selectcat = document.getElementById('category');
     
    for (var i=0; i<categoryMINI.length; i++) {
        for (var j=0; j < Selectcat.options.length; j++) {                
        if (Selectcat.options[j].value == categoryMINI[i]) {
                Selectcat.options[j].selected=true;
            }     
        }
        
        $('#category').trigger('{$parser_chosen}:updated');
 
    }
    return false;
   
}

</script>\n";
?>