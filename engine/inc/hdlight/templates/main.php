<!DOCTYPE html>
<html>
<head>
	
	<meta charset="<?=$this->dle_config['charset']?>" />

	<title>HDLight - Админпанель</title>

	<!-- HD Light CSS -->
	<link type="text/css" href="/engine/skins/hdlight/css/default.css" rel="stylesheet" />
	
	<!-- Bootstrap 3.2.0 CSS -->
	<link type="text/css" href="/engine/skins/hdlight/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
	<link type="text/css" href="/engine/skins/hdlight/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" />

</head>
<body>
	
	<div class="navbar navbar-default navbar-fixed-top" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="sr-only">Включить навигацию</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="?mod=hdlight">HD Light</a>
			</div>
			<div class="navbar-collapse collapse">
				<ul class="nav navbar-nav">
					<li class="active"><a href="?mod=hdlight">Главная</a></li>
					<li><a href="?mod=hdlight&amp;action=settings">Настройки</a></li>
					<li><a href="?mod=hdlight&amp;action=replace">Массовое проставление ссылок</a></li>
				</ul>
				<div class="navbar-form navbar-right">
					<button type="button" class="btn btn-primary" onclick="location.href = '?';">Админпанель сайта</button>
				</div>
			</div>
		</div>
	</div>

	<div class="container">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Добро пожаловать</h3>
			</div>
			<div class="panel-body">
				<p style="float: left; margin-top: 5px; margin-bottom: 0;">Админпанель модуля HD Light</p>
				<p style="float: right; margin-bottom: 0;"><a href="?mod=hdlight&amp;action=delete" class="btn btn-danger btn-sm" onclick="if (!confirm('Вы уверены что хотите удалить модуль? Действие нельзя будет отменить.')) return false;">Удалить модуль</a></p>
			</div>
		</div>
	</div>

	<div class="container">
		<footer>
			<p><?php echo $this->copyright; ?></p>
		</footer>
	</div>

	<!-- Подключаем jQuery для старых версий скрипта -->
	<script type="text/javascript">
	<!--
		if (!window.jQuery)
			document.write(unescape("<script type=\"text/javascript\" src=\"http://code.jquery.com/jquery-1.11.1.min.js\">%3C/script%3E"));
	-->
	</script>

	<!-- Bootstrap 3.2.0 JavaScript -->
	<script type="text/javascript" src="/engine/skins/hdlight/bootstrap/js/bootstrap.min.js"></script>

	<!-- HD Light JavaScript -->
	<script type="text/javascript">
	<!--
		
	-->
	</script>

</body>
</html>