<link media="screen" href="{THEME}/images/orderdesc/style.css" type="text/css" rel="stylesheet" />
<script src="{THEME}/images/orderdesc/script.js"></script>
<div class="rek_top"><h1>Стол заказов фильмов и сериалов</h1></div>
<div id="orderdesc-area">
	<p>Вам достаточно заполнить необходимые поля для поиска фильма/мультфильма/сериала/игры и  автоматическая система в ближайшее время его сможет опубликовать на сайте.<br />
    </p><br>
    <p><div style="color:#FF6600 !important; font-weight:bold; font-size:13px;">Перед созданием запроса воспользуйтесь ПОИСКом вверху страницы!</div><div style="color:yellow !important; font-weight:bold; font-size:13px;">К сожалению, запросы на российские фильмы/сериалы НЕ выполняются!</div><div style="font-weight:bold; font-size:13px;">Запросы на фильмы, которые уже есть на сайте - будут удаляться!</div></p>
	<div id="orderdesc-actions"><br />
		[guest]<a href="#" id="orderdesc-add">Добавить заявку</a>[/guest]
		<form id="orderdesc-search-area" method="get">
			{searchqueries}
			<input type="text" value="Что ищем?" onfocus="if(this.value=='Что ищем?')this.value='';" onblur="if(this.value=='') this.value='Что ищем?';" MAXLENGTH="50" name="search" id="orderdesc-search-input" /><input type="submit" value="" id="orderdesc-search-find" />
		</form>
	</div>
	<div class="odclear"></div>
[guest]	<div id="orderdesc-add-area">
		<form method="post">
			<h4>Название на русском языке(*):</h4>
			<input type="text" name="title" id="orderdesc_title" class="orderdesc-add-input orderdesc-inputclass" />
			<ul id="orderdesc_related"></ul>
			<h4>Название на оригинальном языке:</h4>
			<input type="text" name="orig_title" class="orderdesc-add-input orderdesc-inputclass" />
			<h4>Что заказываем?:</h4>
			<select name="category" class="orderdesc-inputclass">{catlist}</select>
			{*<h4>Движок:</h4>
			<select name="year" class="orderdesc-inputclass">
				
				<option value="DLE">DLE</option>
				<option value="LB">LogicBoard</option>
				<option value="BE">Bullet Energy</option>
				<option value="FAPOS">FAPOS</option>
				<option value="" selected></option>
			</select>*}
			<h4>Ваши пожелания:</h4>
			<textarea name="descr" class="orderdesc-inputclass"></textarea>
			<input type="hidden" name="do" value="orderdesc" /><input type="hidden" name="action" value="addorder" />
			<input type="submit" value="Добавить" id="orderdesc-add-submit" />
		</form>
	</div>
[/guest]
[filters]	<div id="orderdesc-filters">{filters}<div class="odclear"></div></div>[/filters]
	<table id="orderdesc-table">
		<thead><tr>
			<td title="Статус заявки" width="16px"><i class="orderdesc-icon"></i></td>
			<td><a href="{url}&amp;sort=title" title="Сортировать по заголовку">Название</a></td>
			<td width="85px">Категория</td>
			<td width="90px">Заказчик</td>
			<td class="odtdcenter" width="75px"><a href="{url}&amp;sort=date" title="Сортировать по дате подачи заявки">Дата заказа</a></td>
			<td class="odtdcenter" width="30px"><a href="{url}&amp;sort=year" title="Сортировать по дате выхода">Версия</a></td>
			<td class="odtdcenter" width="40px" title="Рейтинг"><a class="orderdesc-icon orderdesc-rating-td" href="{url}&amp;sort=rating" title="Сортировать по рейтингу"></a></td>
		</tr></thead>
		<tbody>{list}</tbody>
	</table>
	
	[navigation]<div id="orderdesc-navigation">{navigation}</div>[/navigation]
</div>