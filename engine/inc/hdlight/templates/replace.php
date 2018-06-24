<?php

file_put_contents(ENGINE_DIR . '/inc/hdlight/reports/success.log', '', LOCK_EX);
file_put_contents(ENGINE_DIR . '/inc/hdlight/reports/found.log', '', LOCK_EX);
file_put_contents(ENGINE_DIR . '/inc/hdlight/reports/not_found.log', '', LOCK_EX);

?><!DOCTYPE html>
<html>
<head>
	
	<meta charset="<?=$this->dle_config['charset']?>" />

	<title>HDLight - Админпанель - Массовое проставление ссылок</title>

	<!-- HD Light CSS -->
	<link type="text/css" href="/engine/skins/hdlight/css/default.css" rel="stylesheet" />
	
	<!-- Bootstrap 3.2.0 CSS -->
	<link type="text/css" href="/engine/skins/hdlight/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
	<link type="text/css" href="/engine/skins/hdlight/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" />

	<!-- Подключаем jQuery для старых версий скрипта -->
	<script type="text/javascript" src="/engine/skins/hdlight/js/jquery-1.11.1.min.js"></script>

	<!-- Additional Scripts -->
	<script type="text/javascript" src="/engine/skins/hdlight/js/main.js"></script>

	<!-- Bootstrap 3.2.0 JavaScript -->
	<script type="text/javascript" src="/engine/skins/hdlight/bootstrap/js/bootstrap.min.js"></script>

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
					<li><a href="?mod=hdlight">Главная</a></li>
					<li><a href="?mod=hdlight&amp;action=settings">Настройки</a></li>
					<li class="active"><a href="?mod=hdlight&amp;action=replace">Массовое проставление ссылок</a></li>
				</ul>
				<div class="navbar-form navbar-right">
					<button type="button" class="btn btn-primary" onclick="location.href = '?';">Админпанель сайта</button>
				</div>
			</div>
		</div>
	</div>
	
	<div class="container">
		
		<div class="alert alert-warning" role="alert">
			Массовое проставление ссылок производится по полям <b>ID видео в базе kinopoisk.ru</b>, <b>world-art.ru</b> и <b>pornolab.net</b>!
			<br/>Перед началом массового проставления ссылок убедитесь что вы настроили все необходимые доп. поля!
		</div>
		
		<div class="alert alert-info" role="alert">
			Выполнение может занять некоторое время, длительность зависит от количества новостей и скорости работы сервера.
		</div>
		
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Массовое проставление ссылок</h3>
			</div>
			<table class="table table-bordered">
				<thead>
					<tr>
						<td colspan="2">
							<div class="form-group">
								<label for="moduleType">Тип</label>
								<div class="col-sm-4 settings-input-col">
									<select id="moduleType" class="form-control hdlight-replace-field">
										<option value="1" selected>Все</option>
										<option value="2">Опубликованные</option>
										<option value="3">Неопубликованные</option>
									</select>
								</div>
								<span class="help-block">Выберите тип записей которым будет проставляться ссылка</span>
							</div>
							<div class="form-group">
								<label for="moduleType">Категории</label>
								<div class="col-sm-8 settings-input-col">
									<?php
										$xfieldsaction = "categoryfilter";
										include ENGINE_DIR . '/inc/xfields.php';
										echo $categoryfilter;

										$categories_list = CategoryNewsSelection(0, 0);
									?>
									<select data-placeholder="Выберите категории ..." name="category[]" id="category" onchange="onCategoryChange(this)" class="categoryselect" multiple style="width:100%;max-width:350px;"><?=$categories_list?></select>
									<div class="category-inverse"><input class="checkbox-inline hdlight-replace-field" type="checkbox" id="category_inverse" name="category_inverse" value="1" title="Не проходить выбранные категории" /></div>
									<script type="text/javascript">
									<!--
										$(".categoryselect").chosen({
											allow_single_deselect: true,
											no_results_text: "Ничего не найдено"
										});
									-->
									</script>
								</div>
								<span class="help-block">Категории в которых будет массовое проставление ссылок</span>
							</div>
							<div class="form-group">
								<label for="moduleRewrite">Перезаписывать</label>
								<div class="col-sm-2 settings-input-col">
									<button type="button" id="moduleRewriteButtom" class="btn btn-sm btn-danger hdlight-replace-field" style="margin: 2px 0; font-weight: bold;">НЕТ</button>
									<input type="hidden" id="moduleRewrite" value="0" />
								</div>
								<span class="help-block">Перезаписывать ссылку у тех записей, у которых она уже есть</span>
							</div>
						</td>
						<td colspan="2">
							<div class="form-group">
								<label for="moduleField">По полю ID из базы</label>
								<div class="col-sm-12 settings-input-col">
									<div class="fields-list">
										<label>
											<input type="checkbox" class="moduleField hdlight-replace-field" name="field[]" value="kinopoisk_id"> kinopoisk.ru
										</label>
										<label>
											<input type="checkbox" class="moduleField hdlight-replace-field" name="field[]" value="world_art_id"> world-art.ru
										</label>
										<label>
											<input type="checkbox" class="moduleField hdlight-replace-field" name="field[]" value="pornolab_id"> pornolab.net
										</label>
									</div>
								</div>
								<span class="help-block">Выберите поля по которым будет производится поиск видео</span>
							</div>
							<div class="form-group">
								<label for="moduleThreads">Потоки</label>
								<div class="col-sm-4 settings-input-col">
									<select id="moduleThreads" class="form-control hdlight-replace-field">
										<option value="1">1</option>
										<option value="2">2</option>
										<option value="3">3</option>
										<option value="5" selected>5</option>
										<option value="7">7</option>
										<option value="10">10</option>
									</select>
								</div>
								<span class="help-block">Выставите оптимальное кол-во одновременных потоков<br><i>Рекомендуемое значение (<b>5</b>)</i></span>
							</div>
							<div class="form-group">
								<label for="moduleInterval">Интервал</label>
								<div class="col-sm-4 settings-input-col">
									<select id="moduleInterval" class="form-control hdlight-replace-field">
										<option value="0" selected>0 мс</option>
										<option value="100">100 мс</option>
										<option value="200">200 мс</option>
										<option value="300">300 мс</option>
										<option value="500">500 мс</option>
										<option value="1000">1 сек</option>
										<option value="2000">2 сек</option>
										<option value="3000" selected>3 сек</option>
									</select>
								</div>
								<span class="help-block">Выставите оптимальный интервал межу запуском потовков<br><i>Рекомендуемое значение (<b>3 сек</b>)</i></span>
							</div>
						</td>
					</tr>
					<tr>
						<th width="25%" class="info">Всего записей в базе</th>
						<th width="25%" class="success">Ссылка проставлена успешно</th>
						<th width="25%" class="warning">Ссылка уже существует</th>
						<th width="25%" class="danger">Видео не найдено</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="info">
							<span id="hdlightReplaceStatusInfo" class="label label-info hdlight-status-label"><?=$post['count']?></span>
						</td>
						<td class="success">
							<a href="/engine/inc/hdlight/reports/success.log" target="_blank" class="report-not-found"><span id="hdlightReplaceStatusSuccess" class="label label-success hdlight-status-label">0</span></a>
						</td>
						<td class="warning">
							<a href="/engine/inc/hdlight/reports/found.log" target="_blank" class="report-not-found"><span id="hdlightReplaceStatusWarning" class="label label-warning hdlight-status-label">0</span></a>
						</td>
						<td class="danger">
							<a href="/engine/inc/hdlight/reports/not_found.log" target="_blank" class="report-not-found"><span id="hdlightReplaceStatusDanger" class="label label-danger hdlight-status-label">0</span></a>
						</td>
					</tr>
				</tbody>
			</table>
			<?php if ($post['count']) { ?>
			<div class="panel-footer" id="hdlightReplacePanel">
				<button type="button" id="hdlightReplaceBeginButton" class="btn btn-success">Начать</button>
				<button type="button" id="hdlightReplaceStopButton" class="btn btn-default" style="display:none">Остановить</button>
				<button type="button" id="hdlightReplaceCancelButton" class="btn btn-danger" style="display:none">Отменить</button>
			</div>
			<?php } ?>
		</div>

		<footer>
			<p><?php echo $this->copyright; ?></p>
		</footer>
	</div>
	
	<div id="hdlightReplaceStatus" style="display:none">canchel</div>
	<div id="hdlightReplaceLastPostId" style="display:none"></div>

	<!-- HD Light JavaScript -->
	<script type="text/javascript">
	<!--
		function hdlight_replace_start() {
			$(".hdlight-replace-field").attr("disabled", "disabled");
			$("#category").prop("disabled", true).trigger("liszt:updated");
			
			$("#hdlightReplaceBeginButton").hide().text("Продолжить");
			$("#hdlightReplaceStopButton, #hdlightReplaceCancelButton").show();
			
			$("#hdlightReplaceStatus").text("working");
			
			hdlight_replace_working();
		}
			function hdlight_replace_working() {
				if ($("#hdlightReplaceStatus").text() == "cancel") {
					hdlight_replace_cancel(true);
				}
				if ($("#hdlightReplaceStatus").text() == "working") {
					var threads = parseInt($("#moduleThreads").val());
					var interval = parseInt($("#moduleInterval").val());
					var type = parseInt($("#moduleType").val());

					var fields = [];
					var i = 0;
					$.each($(".moduleField"), function(key, element) {
						if ($(element).is(":checked")) {
							fields[i] = $(element).val();
							i++;
						}
					});

					if (fields.length == 0) {
						hdlight_replace_cancel(true);
						return false;
					}

					var category = ""; 
					$("#category :selected").each(function(i, sel){ 
						if (category == "")
							category += $(sel).val();
						else
							category += "," + $(sel).val();
					});
					if ($("#category_inverse").is(":checked"))
						var category_inverse = 1;
					else
						var category_inverse = 0;
					
					var get_replace_threads_url = "/engine/ajax/hdlight.php?action=get_replace_threads&type=" + type + "&threads=" + threads + "&category=" + category + "&category_inverse=" + category_inverse;
					
					var last_post_id = parseInt($("#hdlightReplaceLastPostId").text());
					if (last_post_id)
						get_replace_threads_url += "&last_post_id=" + last_post_id;
					else
						$("#hdlightReplaceStatusSuccess, #hdlightReplaceStatusWarning, #hdlightReplaceStatusDanger").text("0");
					
					if (threads) {
						$.ajax({
							url: get_replace_threads_url,
							dataType: "json",
							cache: false,
							success: function(data) {
								if (data.status == "ok") {
									if (data.count)
										$("#hdlightReplaceStatusInfo").text(data.count);
									
									$.each(data.next_posts_id, function(i, val) {
										var num = i + 1;
										
										if (num == data.next_posts_id.length) {
											$("#hdlightReplaceLastPostId").text(val);
											
											if ($("#hdlightReplaceStatus").text() == "stop") {
												$(document).ajaxStop(function() {
													hdlight_replace_stop();
												});
											}
										}
										
										hdlight_replace_thread(val, fields);
									});
								}
								if (data.status == "end") {
									if (data.code && data.code == "#00")
										alert("По вашему критерию в базе не найдено ни одной записи.");
									
									hdlight_replace_cancel(true);
								}
								
								setTimeout("hdlight_replace_working()", interval);
							}
						});
					}
				}
			}
				function hdlight_replace_thread(post_id, fields) {
					if ($("#hdlightReplaceStatus").text() == "cancel")
						return;
					
					var replace_thread_url = "/engine/ajax/hdlight.php?action=replace_thread&post_id=" + post_id + "&fields=" + fields.join("|");

					var rewrite = parseInt($("#moduleRewrite").val());
					
					if (rewrite) {
						replace_thread_url += "&rewrite=" + rewrite;
					}
					
					$.ajax({
						url: replace_thread_url,
						dataType: "json",
						cache: false,
						success: function(data) {
							if ($("#hdlightReplaceStatus").text() == "cancel")
								return;
							
							if (data.status == "ok") {
								var status_ok = parseInt($("#hdlightReplaceStatusSuccess").text());
								$("#hdlightReplaceStatusSuccess").text(status_ok + 1);
							}
							if (data.status == "exist") {
								var status_exist = parseInt($("#hdlightReplaceStatusWarning").text());
								$("#hdlightReplaceStatusWarning").text(status_exist + 1);
							}
							if (data.status == "error") {
								var status_error = parseInt($("#hdlightReplaceStatusDanger").text());
								$("#hdlightReplaceStatusDanger").text(status_error + 1);
							}
						}
					});
				}
		function hdlight_replace_stop() {
			setTimeout(function () {
				$("#hdlightReplaceBeginButton").show();
				$("#hdlightReplaceStopButton").removeAttr("disabled").hide().removeAttr("disabled");
			}, 0);
		}
		function hdlight_replace_cancel(end) {
			$("#hdlightReplaceStatus").text("cancel");
			
			setTimeout(function () {
				$(".hdlight-replace-field").removeAttr("disabled");
				$("#category").prop("disabled", false).trigger("liszt:updated");
				
				$("#hdlightReplaceBeginButton").text("Начать").show();
				$("#hdlightReplaceStopButton, #hdlightReplaceCancelButton").hide();
				
				if (!end)
					$("#hdlightReplaceStatusSuccess, #hdlightReplaceStatusWarning, #hdlightReplaceStatusDanger").text("0");
				
				$("#hdlightReplaceLastPostId").text("");
			}, 0);
		}
		
		$("#moduleRewriteButtom").click(function() {
			var on = parseInt($(this).next("input").val());

			if (on) {
				$(this).removeClass("btn-success").addClass("btn-danger").text("НЕТ");
				$(this).next("input").val("0");
			} else {
				$(this).removeClass("btn-danger").addClass("btn-success").text("ДА");
				$(this).next("input").val("1");
			}
		});
		
		$("#hdlightReplaceBeginButton").click(function() {
			hdlight_replace_start();
		});
		$("#hdlightReplaceStopButton").click(function() {
			$("#hdlightReplaceStatus").text("stop");
			
			$("#hdlightReplaceStopButton").attr("disabled", "disabled");
		});
		$("#hdlightReplaceCancelButton").click(function() {
			hdlight_replace_cancel();
		});
	-->
	</script>
	
</body>
</html>