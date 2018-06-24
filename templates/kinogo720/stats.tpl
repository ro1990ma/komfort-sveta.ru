[group=1,2,3] <div class="form-wrap">
	<header class="form-title"><h1>Статистика сайта</h1></header>			
	<div class="sep-textarea">
							<ul class="statsbox">
								<li>За сутки: <b>{news_day}</b> новостей, <b>{comm_day}</b> комментариев и <b>{user_day}</b> пользователей</li>
								<li>За неделю: <b>{news_week}</b> новостей, <b>{comm_week}</b> комментариев и <b>{user_week}</b> пользователей</li>
								<li>За месяц: <b>{news_month}</b> новостей, <b>{comm_month}</b> комментариев и <b>{user_month}</b> пользователей</li>
							</ul>
	</div>
	<div class="sep-textarea clearfix statistics">
								<ul class="left">
									<li><h4>Новости:</h4></li>
									<li>Общее кол-во: <b>{news_num}</b></li>
									<li>Опубликовано: <b>{news_allow}</b></li>
									<li>На главной: <b>{news_main}</b></li>
									<li>На модерации: <b>{news_moder}</b></li>
								</ul>
								<ul class="left">
									<li><h4>Пользователи:</h4></li>
									<li>Общее кол-во: <b>{user_num}</b></li>
									<li>Из них забанено: <b>{user_banned}</b></li>
								</ul>
								<ul class="left">
									<li><h4>Комментарии:</h4></li>
									<li>Общее кол-во: <b>{comm_num}</b></li>
									<li><a href="/?do=lastcomments">Посмотреть последние</a></li>
								</ul>
	</div>
	<div class="sep-textarea">
		<header class="form-title"><h1>Список лучших пользователей</h1></header>
		<table width="100%" class="userstop">{topusers}</table>
	</div>
</div>
[/group]