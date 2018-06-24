 <!--noindex-->
            <div class="genre-block cat-list ns">
					<div class="side-content clearfix">
    <form id="searchform" action="/">
    <div class="range">
        <input type="hidden" id="range" value="" />
        <input type="hidden" id="fromyear" value="" name="from-year"  />
        <input type="hidden" id="toyear" value="" name="to-year"  />
    </div>
<div class="filterbox">
	<div class="bestMoviesNav">
		<div class="lineinputform">
			<div class="selectCustom" id='genreListTitle2' onclick='KP.navigator.openSelect("genreList");'>Выберите жанры</div>
			<ul id='genreList' class='genreList selectList' onmouseover="KP.navigator.selectBlockClose=true;" onmouseout="KP.navigator.selectBlockClose=false;">
				<!--<li class='selectItem'><label><input name="cat" value="10" data-name="Аниме" onchange='KP.navigator.toggleCheckbox("genre", this);' type='checkbox'> Аниме</label></li>-->
                <li class='selectItem'><label><input name="cat" value="2" data-name="Биографии" onchange='KP.navigator.toggleCheckbox("genre", this);' type='checkbox'> Биографии</label></li>
                <li class='selectItem'><label><input name="cat" value="1" data-name="Боевики" onchange='KP.navigator.toggleCheckbox("genre", this);' type='checkbox'> Боевики</label></li>
                <li class='selectItem'><label><input name="cat" value="3" data-name="Вестерны" onchange='KP.navigator.toggleCheckbox("genre", this);' type='checkbox'> Вестерны</label></li>
                <li class='selectItem'><label><input name="cat" value="4" data-name="Военные" onchange='KP.navigator.toggleCheckbox("genre", this);' type='checkbox'> Военные</label></li>
                <li class='selectItem'><label><input name="cat" value="5" data-name="Детективы" onchange='KP.navigator.toggleCheckbox("genre", this);' type='checkbox'> Детективы</label></li>
                <li class='selectItem'><label><input name="cat" value="6" data-name="Документальные" onchange='KP.navigator.toggleCheckbox("genre", this);' type='checkbox'> Документальные</label></li>
				<li class='selectItem'><label><input name="cat" value="7" data-name="Драмы" onchange='KP.navigator.toggleCheckbox("genre", this);' type='checkbox'> Драмы</label></li>
                <li class='selectItem'><label><input name="cat" value="8" data-name="Исторические" onchange='KP.navigator.toggleCheckbox("genre", this);' type='checkbox'> Исторические</label></li>
                <li class='selectItem'><label><input name="cat" value="9" data-name="Комедии" onchange='KP.navigator.toggleCheckbox("genre", this);' type='checkbox'> Комедии</label></li>
                <li class='selectItem'><label><input name="cat" value="10" data-name="Криминал" onchange='KP.navigator.toggleCheckbox("genre", this);' type='checkbox'> Криминал</label></li>
                <li class='selectItem'><label><input name="cat" value="11" data-name="Мелодрамы" onchange='KP.navigator.toggleCheckbox("genre", this);' type='checkbox'> Мелодрамы</label></li>
                <li class='selectItem'><label><input name="cat" value="12" data-name="Мультфильмы" onchange='KP.navigator.toggleCheckbox("genre", this);' type='checkbox'> Мультфильмы</label></li>
				<li class='selectItem'><label><input name="cat" value="13" data-name="Отечественные" onchange='KP.navigator.toggleCheckbox("genre", this);' type='checkbox'> Отечественные</label></li>
                <li class='selectItem'><label><input name="cat" value="14" data-name="Приключения" onchange='KP.navigator.toggleCheckbox("genre", this);' type='checkbox'> Приключения</label></li>
                <li class='selectItem'><label><input name="cat" value="15" data-name="Семейные" onchange='KP.navigator.toggleCheckbox("genre", this);' type='checkbox'> Семейные</label></li>
                <li class='selectItem'><label><input name="cat" value="16" data-name="Триллеры" onchange='KP.navigator.toggleCheckbox("genre", this);' type='checkbox'> Триллеры</label></li>
                <li class='selectItem'><label><input name="cat" value="17" data-name="Ужасы" onchange='KP.navigator.toggleCheckbox("genre", this);' type='checkbox'> Ужасы</label></li>
                <li class='selectItem'><label><input name="cat" value="18" data-name="Фантастика" onchange='KP.navigator.toggleCheckbox("genre", this);' type='checkbox'> Фантастика</label></li>
				<li class='selectItem'><label><input name="cat" value="19" data-name="Фэнтези" onchange='KP.navigator.toggleCheckbox("genre", this);' type='checkbox'> Фэнтези</label></li>
			</ul>

			<div class="selectCustom" id='countryListTitle2' onclick='KP.navigator.openSelect("countryList");'>Выберите страну</div>
			<ul id='countryList' class='countryList selectList' onmouseover="KP.navigator.selectBlockClose=true;" onmouseout="KP.navigator.selectBlockClose=false;">
                <li class='selectItem'><label><input name="country" value="США" data-name="США" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> США</label></li>
                <li class='selectItem'><label><input name="country" value="Россия" data-name="Россия" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Рoссия</label></li>
                <li class='selectItem'><label><input name="country" value="Великобритания" data-name="Великобритания" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Великобритaния</label></li>
                <li class='selectItem'><label><input name="country" value="Франция" data-name="Франция" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Фрaнция</label></li>
                <li class='selectItem'><label><input name="country" value="Германия" data-name="Германия" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Гермaния</label></li>
                <li class='selectItem'><label><input name="country" value="Канада" data-name="Канада" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Кaнада</label></li>
                <li class='selectItem'><label><input name="country" value="Япония" data-name="Япония" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Япoния</label></li>
                <li class='selectItem'><label><input name="country" value="Испания" data-name="Испания" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Испaния</label></li>
                <li class='selectItem'><label><input name="country" value="Италия" data-name="Италия" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Итaлия</label></li>
                <li class='selectItem'><label><input name="country" value="Австралия" data-name="Австралия" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Авcтралия</label></li>
                <li class='selectItem'><label><input name="country" value="Корея Южная" data-name="Корея Южная" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Кoрея Южная</label></li>
                <li class='selectItem'><label><input name="country" value="Китай" data-name="Китай" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Китaй</label></li>
                <li class='selectItem'><label><input name="country" value="Бельгия" data-name="Бельгия" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Бельгия</label></li>
                <li class='selectItem'><label><input name="country" value="СССР" data-name="СССР" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> СССP</label></li>
                <li class='selectItem'><label><input name="country" value="Гонконг" data-name="Гонконг" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Гoнконг</label></li>
                <li class='selectItem'><label><input name="country" value="Швеция" data-name="Швеция" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Швеция</label></li>
                <li class='selectItem'><label><input name="country" value="Украина" data-name="Украина" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Укpаина</label></li>
                <li class='selectItem'><label><input name="country" value="Дания" data-name="Дания" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Дания</label></li>
                <li class='selectItem'><label><input name="country" value="Ирландия" data-name="Ирландия" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Ирландия</label></li>
                <li class='selectItem'><label><input name="country" value="Нидерланды" data-name="Нидерланды" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Нидерланды</label></li>
                <li class='selectItem'><label><input name="country" value="Норвегия" data-name="Норвегия" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Нoрвегия</label></li>
                <li class='selectItem'><label><input name="country" value="Польша" data-name="Польша" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Пoльша</label></li>
                <li class='selectItem'><label><input name="country" value="Мексика" data-name="Мексика" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Мeксика</label></li>
                <li class='selectItem'><label><input name="country" value="Швейцария" data-name="Швейцария" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Швейцария</label></li>
                <li class='selectItem'><label><input name="country" value="Аргентина" data-name="Аргентина" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Аpгентина</label></li>
                <li class='selectItem'><label><input name="country" value="Чехия" data-name="Чехия" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Чехия</label></li>
                <li class='selectItem'><label><input name="country" value="Новая Зеландия" data-name="Новая Зеландия" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Новая Зеландия</label></li>
                <li class='selectItem'><label><input name="country" value="Финляндия" data-name="Финляндия" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Финляндия</label></li>
                <li class='selectItem'><label><input name="country" value="Австрия" data-name="Австрия" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Авcтрия</label></li>
                <li class='selectItem'><label><input name="country" value="Турция" data-name="Турция" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Тyрция</label></li>
                <li class='selectItem'><label><input name="country" value="Люксембург" data-name="Люксембург" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Люксембург</label></li>
                <li class='selectItem'><label><input name="country" value="Израиль" data-name="Израиль" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Израиль</label></li>
                <li class='selectItem'><label><input name="country" value="ЮАР" data-name="ЮАР" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> ЮАР</label></li>
                <li class='selectItem'><label><input name="country" value="Таиланд" data-name="Таиланд" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Таиланд</label></li>
                <li class='selectItem'><label><input name="country" value="Венгрия" data-name="Венгрия" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Вeнгрия</label></li>
                <li class='selectItem'><label><input name="country" value="Бразилия" data-name="Бразилия" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Бpазилия</label></li>
                <li class='selectItem'><label><input name="country" value="Исландия" data-name="Исландия" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Исландия</label></li>
                <li class='selectItem'><label><input name="country" value="Румыния" data-name="Румыния" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Pумыния</label></li>
                <li class='selectItem'><label><input name="country" value="Казахстан" data-name="Казахстан" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Кaзахстан</label></li>
                <li class='selectItem'><label><input name="country" value="Беларусь" data-name="Беларусь" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Бeларусь</label></li>
                <li class='selectItem'><label><input name="country" value="Чили" data-name="Чили" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Чили</label></li>
                <li class='selectItem'><label><input name="country" value="Болгария" data-name="Болгария" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Болгария</label></li>
                <li class='selectItem'><label><input name="country" value="Сербия" data-name="Сербия" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Сербия</label></li>
                <li class='selectItem'><label><input name="country" value="Эстония" data-name="Эстония" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Эстония</label></li>
                <li class='selectItem'><label><input name="country" value="ОАЭ" data-name="ОАЭ" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> ОАЭ</label></li>
                <li class='selectItem'><label><input name="country" value="Тайвань" data-name="Тайвань" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Тайвань</label></li>
                <li class='selectItem'><label><input name="country" value="Хорватия" data-name="Хорватия" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Хорватия</label></li>
                <li class='selectItem'><label><input name="country" value="Армения" data-name="Армения" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Армeния</label></li>
                <li class='selectItem'><label><input name="country" value="Греция" data-name="Греция" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Греция</label></li>
                <li class='selectItem'><label><input name="country" value="Португалия" data-name="Португалия" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Португалия</label></li>
                <li class='selectItem'><label><input name="country" value="Индонезия" data-name="Индонезия" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Индонезия</label></li>
                <li class='selectItem'><label><input name="country" value="Грузия" data-name="Грузия" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Гpузия</label></li>
                <li class='selectItem'><label><input name="country" value="Малайзия" data-name="Малайзия" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Малайзия</label></li>
                <li class='selectItem'><label><input name="country" value="Латвия" data-name="Латвия" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Латвия</label></li>
                <li class='selectItem'><label><input name="country" value="Германия" data-name="Германия" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Гeрмания</label></li>
                <li class='selectItem'><label><input name="country" value="Словения" data-name="Словения" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Словения</label></li>
                <li class='selectItem'><label><input name="country" value="Сингапур" data-name="Сингапур" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Сингапур</label></li>
                <li class='selectItem'><label><input name="country" value="Литва" data-name="Литва" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Литва</label></li>
                <li class='selectItem'><label><input name="country" value="Перу" data-name="Перу" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Перу</label></li>
                <li class='selectItem'><label><input name="country" value="Иран" data-name="Иран" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Иран</label></li>
                <li class='selectItem'><label><input name="country" value="Филиппины" data-name="Филиппины" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Филиппины</label></li>
                <li class='selectItem'><label><input name="country" value="Куба" data-name="Куба" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Куба</label></li>
                <li class='selectItem'><label><input name="country" value="Монголия" data-name="Монголия" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Монголия</label></li>
                <li class='selectItem'><label><input name="country" value="Мальта" data-name="Мальта" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Мальта</label></li>
                <li class='selectItem'><label><input name="country" value="Уругвай" data-name="Уругвай" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Уругвай</label></li>
                <li class='selectItem'><label><input name="country" value="Марокко" data-name="Марокко" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Марокко</label></li>
                <li class='selectItem'><label><input name="country" value="Венесуэла" data-name="Венесуэла" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Венесуэла</label></li>
                <li class='selectItem'><label><input name="country" value="Колумбия" data-name="Колумбия" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Колумбия</label></li>
                <li class='selectItem'><label><input name="country" value="Ирак" data-name="Ирак" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Ирак</label></li>
                <li class='selectItem'><label><input name="country" value="Пуэрто Рико" data-name="Пуэрто Рико" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Пуэрто Рико</label></li>
                <li class='selectItem'><label><input name="country" value="Индия" data-name="Индия" onchange='KP.navigator.toggleCheckbox("country", this);' type='checkbox'> Индия</label></li>
			</ul>

            <div class="selectCustom" id="voiceListTitle2" onclick='KP.navigator.openSelect("quality");'>Выберите качество</div>
			<ul id='quality' class='quality selectList checkbox3' onmouseover="KP.navigator.selectBlockClose=true;" onmouseout="KP.navigator.selectBlockClose=false;">
				<li class='selectItem'><label><input name="quality" value="HDrip" data-name="HDrip" onchange='KP.navigator.toggleCheckbox("quality", this);' type='checkbox'> HDrip</label></li>
                <li class='selectItem'><label><input name="quality" value="DVDRip" data-name="DVDRip" onchange='KP.navigator.toggleCheckbox("quality", this);' type='checkbox'> DVDRip</label></li>
                <li class='selectItem'><label><input name="quality" value="TS" data-name="TS" onchange='KP.navigator.toggleCheckbox("quality", this);' type='checkbox'> TS</label></li>
                <li class='selectItem'><label><input name="quality" value="CAMrip" data-name="CAMrip" onchange='KP.navigator.toggleCheckbox("quality", this);' type='checkbox'> CAMrip</label></li>
                <li class='selectItem'><label><input name="quality" value="Трейлер" data-name="Трейлер" onchange='KP.navigator.toggleCheckbox("quality", this);' type='checkbox'> Трейлер</label></li>

            </ul>

		</div>

		
		
        
        
        
        <div class="bline"></div>
  
<div class="leftblok_sort">


 <div class="lineinput"><div style="float: left; width: 150px;">

    
    
			 <div class="boxfilter">
				<input type="radio" name="order_by" value="title" id="sort_alpha" checked="checked">
				<label for="sort_alpha">по алфавиту</label>
			</div>
			<div class="boxfilter">
				<input type="radio" name="order_by" value="rating" id="sort_rating">
				<label for="sort_rating">по рейтингу</label>
			</div>
			<div class="boxfilter">
				<input type="radio" name="order_by" value="date" id="sort_date">
				<label for="sort_date">по дате</label>
			</div> </div>
			
        
        <div style="float: right; width: 150px;">
            
            <div class="boxfilter">
				<input type="radio" name="order_by" value="comm_num" id="sort_comm">
				<label for="sort_comm">по комментам</label>
			</div>
			<div class="boxfilter">
<input type="radio" name="order_by" value="news_read" id="sort_view">
				<label for="sort_view">по просмотрам</label>
			</div> </div>
			
			
			</div>
		</div>
        
	<div style="padding:15px"><div style="float: left; width: 50%;">	
			<div class="boxfilter checkbox order">
                <input type="radio" name="order" value="desc" id="sort_desc">
				<label for="sort_desc">Больше</label>
			</div></div>
			<div style="float: right; width: 50%;"><div class="boxfilter checkbox order2">
				<input type="radio" name="order" value="asc" id="sort_asc">
				<label for="sort_asc">Меньше</label>
			</div></div>
		</div>
	</div>

	<div style="padding:15px">
    	<input class="filterbut" type="button" data-fieldsearch="submit" value="Поиск">
    	<input class="filterbut" type="button" data-fieldsearch="reset" value="Очистить">
	</div>
    </div>
    </form>
</div>
</div>
<!--/noindex-->