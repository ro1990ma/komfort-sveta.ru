<div class="viewn_loop vInner vInnerStatic">
	<div class="viewn_top">
		<div class="viewn_t_in">
			<div class="viewn_t_ins">
				<span class="title"><span>Статистика сайта</span></span>
			</div>
		</div>
	</div><!--/viewn_top-->
	<div class="viewn_cont">
			
		<div class="basecont statistics">
			<ul class="lcol reset">
				<li><h5 class="blue">Новости:</h5></li>
				<li>Общее кол-во новостей: <b class="blue">{news_num}</b></li>
		<li>Из них опубликовано: <b class="blue">{news_allow}</b></li>
		<li>Опубликовано на главной: <b class="blue">{news_main}</b></li>
		<li>Ожидает модерации: <b class="blue">{news_moder}</b></li>
			</ul>
			<ul class="lcol reset">
				<li><h5 class="blue">Пользователи:</h5></li>
				<li>Общее кол-во пользователей: <b class="blue">{user_num}</b></li>
		<li>Из них забанено: <b class="blue">{user_banned}</b></li>
			</ul>
			<ul class="lcol reset">
				<li><h5 class="blue">Комментарии:</h5></li>
				<li>Кол-во комментариев: <b class="blue">{comm_num}</b></li>
		<li><a href="/?do=lastcomments">Посмотреть последние</a></li>
			</ul>
			<br clear="all">
			<div class="dpad infoblock radial">
				<ul class="reset">
					<li>За сутки: Добавлено <b>{news_day} новостей</b> и <b>{comm_day} комментариев</b>, зарегистрировано <b>{user_day} пользователей</b></li>
		<li>За неделю: Добавлено <b>{news_week} новостей</b> и <b>{comm_week} комментариев</b>, зарегистрировано <b>{user_week} пользователей</b></li>
		<li>За месяц: Добавлено <b>{news_month} новостей</b> и <b>{comm_month} комментариев</b>, зарегистрировано <b>{user_month} пользователей</b></li>
				</ul>
			</div>
		</div>
		<div class="pheading"><p><b>Общий размер базы данных: {datenbank}</b></p></div>
		<div class="basecont">
			<div class="pheading">
				<h5 class="heading">Лучшие пользователи</h5>
				<table width="100%" class="userstop">{topusers}</table>
			</div>
		</div>
		
	</div><!--/viewn_cont-->
</div>