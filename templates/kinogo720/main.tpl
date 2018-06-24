<html lang="ru-RU" prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# video: http://ogp.me/ns/video# ya: http://webmaster.yandex.ru/vocabularies/">
<head>
{headers}
<link rel="apple-touch-icon" sizes="57x57" href="{THEME}/images/s/apple-icon-57x57.png"/>
<link rel="apple-touch-icon" sizes="60x60" href="{THEME}/images/s/apple-icon-60x60.png"/>
<link rel="apple-touch-icon" sizes="72x72" href="{THEME}/images/s/apple-icon-72x72.png"/>
<link rel="apple-touch-icon" sizes="76x76" href="{THEME}/images/s/apple-icon-76x76.png"/>
<link rel="apple-touch-icon" sizes="114x114" href="{THEME}/images/s/apple-icon-114x114.png"/>
<link rel="apple-touch-icon" sizes="120x120" href="{THEME}/images/s/apple-icon-120x120.png"/>
<link rel="apple-touch-icon" sizes="144x144" href="{THEME}/images/s/apple-icon-144x144.png"/>
<link rel="apple-touch-icon" sizes="152x152" href="{THEME}/images/s/apple-icon-152x152.png"/>
<link rel="apple-touch-icon" sizes="180x180" href="{THEME}/images/s/apple-icon-180x180.png"/>
<link rel="icon" type="image/png" sizes="192x192" href="{THEME}/images/s/android-icon-192x192.png"/>
<link rel="icon" type="image/png" sizes="32x32" href="{THEME}/images/s/favicon-32x32.png"/>
<link rel="icon" type="image/png" sizes="96x96" href="{THEME}/images/s/favicon-96x96.png"/>
<link rel="icon" type="image/png" sizes="16x16" href="{THEME}/images/s/favicon-16x16.png"/>
<link rel="manifest" href="{THEME}/images/s/manifest.json">
<script src="{THEME}/js/libs.js"></script>
<script src="{THEME}/js/moonserials.js"></script>
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="{THEME}/images/s/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<link href="{THEME}/style/styles.css" type="text/css" rel="stylesheet"/>
<link href="{THEME}/style/frameworks.css" type="text/css" rel="stylesheet"/>
<link href="{THEME}/style/engine.css" type="text/css" rel="stylesheet"/>
<link href="{THEME}/style/reset-settings.css" type="text/css" rel="stylesheet"/>    
<link media="screen" href="{THEME}/comm/style.css" type="text/css" rel="stylesheet"/>
    <link rel="stylesheet" async href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<!--[if lt IE 9]><script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,400italic,600,700&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
<script src="{THEME}/js/share.js" async="async"></script>

</head>
<body>
{ajax}
<div class="wr" id="wr-bg">
<header class="top-wr">
<div class="top center" id="top">
<a href="/" class="logo"></a>
<div class="search-wrap" id="search-wrap">
<form id="quicksearch" method="post">
<input type="hidden" name="do" value="search"/>
<input type="hidden" name="subaction" value="search"/>
<div class="search-box">
<input id="story" name="story" placeholder="Поиск..." type="text"/>
<button type="submit" title="Найти"><i class="fa fa-search"></i></button>
</div>
</form>
</div>
[group=5]<div class="log-buts icon-left clearfix">
<a rel="nofollow" href="#" id="show-login"><i class="fa fa-sign-in"></i><span>Вход</span></a>
 <a href="/?do=register" rel="nofollow" class="reg-link"><i class="fa fa-user-plus"></i>Регистрация</a> 
</div>[/group]
[not-group=5]<div class="log-buts icon-left clearfix">
<a rel="nofollow" href="#" id="show-login"><i class="fa fa-cog"></i><span>Управление</span></a>
</div>[/not-group]
</div>
</header>
<div class="block center">
[aviable=main]
<div class="carousel-wr">
<div id="owl-carou">
{custom category="1-24" template="slider" xfields="popf" from="0" limit="7" cache="no"}
</div>
</div>[/aviable]
<ul class="second-menu clearfix">
<li><a href="/">Главная</a></li>
<li><a href="/serialy/">Сериалы</a></li>
    <li><a href="/serialy/">Подборки</a></li>
<li><a href="/novinki-2016/">Новинки</a></li>
</ul>
<div class="cols clearfix" id="cols">
<div class="col-cont">
[aviable=main]<h1>Киного фильмы</h1><br>[/aviable]
[not-aviable=main]<div class="speedbar">{speedbar}</div><br>[/not-aviable]
<div class="main-items clearfix grid grid-list" id="grid">
<div id='dle-content'>
[aviable=main|cat]{include file="engine/modules/catface.php"}[/aviable]
{info}{content}
[aviable=cat]{include file="category.tpl"}[/aviable]
<div class="clr"></div>

 </div>
</div>
</div>
 
<aside class="col-side">
<div class="side-box">
<div class="sb-title">Панель навигации </div>
<br>
<div class="sb-cont clearfix">
<ul class="main-menu mob-menu clearfix" id="mob-menu"></ul>
 
<ul class="main-menu genres-menu clearfix">
<li><a href="/">Главная</a></li>
<li><a href="/novinki-2016/">Новинки</a></li>
<li><a href="/biografiya/">Биографии</a></li>
<li><a href="/boeviki/">Боевики</a></li>
<li><a href="/vesterny/">Вестерны</a></li>
<li><a href="/voennye/">Военные</a></li>
<li><a href="/detektivy/">Детективы</a></li>
<li><a href="/dokumentalnye">Документальные</a></li>
<li><a href="/dramy/">Драмы</a></li>
<li><a href="/istoriya/">Исторические</a></li>
</ul>
<ul class="main-menu genres-menu clearfix">
<li><a href="/komedii/">Комедии</a></li>
<li><a href="/kriminal/">Криминал</a></li>
<li><a href="/melodramy/">Мелодрамы</a></li>
<li><a href="/multfilmy/">Мультфильмы</a></li>
<li><a href="/priklyucheniya/">Приключения</a></li>
<li><a href="/semeynye/">Семейные</a></li>
<li><a href="/trillery/">Триллеры</a></li>
<li><a href="/uzhasy/">Ужасы</a></li>
<li><a href="/fantastika/">Фантастика</a></li>
<li><a href="/serialy/">Сериалы</a></li>
</ul>
</div>
</div>
<div class="side-box">
<div class="side-box"></div>
<br>
<div class="sb-cont clearfix">
{custom category="30" template="skoro" aviable="global" from="0" limit="2" cache="no"}
</div>
</div>
 
 
 
 
[category=25,26,27] 
<div class="side-box">
<div class="sb-title">Обновления сериалов</div>
</div>
<br>
<div class="sb-cont clearfix" id="grid-serial">
{include file="/engine/modules/moonserials_block.php"}
	</div>	{banner_header}	
[/category]			
[aviable=main]                 
 <div class="side-box">
<div class="sb-title">Обновления сериалов</div>
</div>
<br>
<div class="sb-cont clearfix" id="grid-serial">
{include file="/engine/modules/moonserials_block.php"}
	</div>		
	[/aviable]				 

                <!--/свежие сериалы -->
              
      
                
  
      
			</aside> 
			<!-- end col-side -->
			{banner_header}
				</div>
		<!-- end cols -->
		
		<!-- подключаем нижний текст описаний -->
		
        [aviable=main]
			<div class="site-desc clearfix">
				
<br>
<h2>Киного фильмы смотреть бесплатно в хорошем качестве</h2>{banner_header}
    <div style="text-align: justify">Здравствуйте, уважаемые гости сайта! Вы по-настоящему цените кино и всегда следите за появлением более нового кино? Любите провести досуг перед экраном своего монитора? Отлично, тогда вы не ошиблись с местом! Именно здесь на <strong>киного смотреть</strong> 2017 можно регулярно <b>смотреть фильмы онлайн бесплатно</b> позабыв об ограничениях! <br />Если нужно что-либо увидеть, быть в курсе последних дат выпуска, то наша команда сделает всё возможное, для регулярного добавления новостей. Но ведь иногда хочется что-то из уже проверенного временем? В коллекции этого онлайн-кинотеатра хранится самый актуальный, большой список фильмов на киного720 с высокими рейтингами!<br /><br /><img style="border-radius: 7px; float: left; margin: 4px 14px 1px 0px; width: 240px; height: 140px;" src="{THEME}/images/footer-main.jpg" title="кино онлайн" alt="кино онлайн на киного" />Кинофильмы существуют разные, как по жанрам, так и по странам. На <a href="/">Kinogo</a> вы сможете найти всё. Есть классика европейской видеоиндустрии, золотые хиты Голливуда, французские вещи, азиатские творения, советское кино и современные хиты любых мастей для планшета. Стоит отметить, что большинство киношек нужно смотреть в хорошем качестве для максимального эффекта. Когда скорость вашего соединения имеет широкую пропускную способность, качество в плеере автоматически примет наилучшее значение. При более низком уровне сигнала соответственно картинка будет в 480. Но всегда существует возможность найти на киного< фильмы онлайн в FULL-HD для широкоформатных дисплеев.<br /><br />Здесь сайте есть картины абсолютно каждого жанра, от комедий до ужасов. Находите разнообразные сериалы онлайн бесплатно всей семьёй не выходя из дома.<br />На Киного вы найдёте только лучшее, ведь здесь регулярно обновляется контент в разных спектрах своей тематики. Минимум рекламы с удобным html5 плеером превратят ваш досуг в прекрасное времяпровождение, а возможность смотреть онлайн <a href="/novinki-2016/">новинки 2016</a> бесплатно на планшете. Перед началом рекомендуем ознакомиться с правилами пользования, зарегистрироваться и получить дополнительные бонусы, упрощающие пользователям навигацию.<br /><br />Для того, чтобы нашим посетителям было удобно, существует специальная строка поиска, в куда вводится название. Когда у вас не получается найти желаемое, то воспользуйтесь расширенным поиском или напишите администрации через обратную связь. Добавление контента происходит после выхода материала в прокат на релизах DVD и Blu-ray. Мы хотим стать верным помощником для тех, кто предпочитает свободный доступ к новинкам. Детям от шести + смотреть мультфильмы будет всегда в радость. Включайте кино онлайн бесплатно в хорошем качестве, без платной регистрации!</div>

		</div>[/aviable]




	<br>
		<footer class="bottom">
	<ul class="bot-menu clearfix">
								<li><a href="/" class="active" rel="nofollow">Киного</a></li>
							
							
			</ul>
			<!--noindex--><a href="/?do=feedback" rel="nofollow" class="pravo-link">Информация для правообладателей</a><!--/noindex-->
		</footer>
	
	</div>
	<!-- end block -->

	
<!-- end wr -->
</div>
<!--noindex-->
{login}
<!--/noindex-->
    
 

  

    
    
<!--LiveInternet counter--><script type="text/javascript"><!--
new Image().src = "//counter.yadro.ru/hit?r"+
escape(document.referrer)+((typeof(screen)=="undefined")?"":
";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
";"+Math.random();//--></script><!--/LiveInternet-->    
    


</body>
</html>