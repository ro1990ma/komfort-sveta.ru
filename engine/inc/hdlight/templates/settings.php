<!DOCTYPE html>
<html>
<head>
	
	<meta charset="<?=$this->dle_config['charset']?>" />

	<title>HDLight - Админпанель - Настройки</title>

	<!-- HD Light CSS -->
	<link type="text/css" href="/engine/skins/hdlight/css/default.css" rel="stylesheet" />
	
	<!-- Bootstrap 3.2.0 CSS -->
	<link type="text/css" href="/engine/skins/hdlight/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
	<link type="text/css" href="/engine/skins/hdlight/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" />

	<!-- Подключаем jQuery UI CSS -->
	<link type="text/css" href="/engine/skins/hdlight/css/jquery-ui.css" rel="stylesheet" />

	<!-- Подключаем jQuery для старых версий скрипта -->
	<script type="text/javascript" src="/engine/skins/hdlight/js/jquery-1.11.1.min.js"></script>

	<!-- Подключаем jQuery UI -->
	<script type="text/javascript" src="/engine/skins/hdlight/js/jquery-ui.min.js"></script>
	<script type="text/javascript">
	<!--
		jQuery.fn.outerHTML = function() {
			return jQuery('<div />').append(this.eq(0).clone()).html();
		};
	-->
	</script>

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
					<li class="active"><a href="?mod=hdlight&amp;action=settings">Настройки</a></li>
					<li><a href="?mod=hdlight&amp;action=replace">Массовое проставление ссылок</a></li>
				</ul>
				<div class="navbar-form navbar-right">
					<button type="button" class="btn btn-primary" onclick="location.href = '?';">Админпанель сайта</button>
				</div>
			</div>
		</div>
	</div>

	<div class="container">
		<?php if ($_GET['success']): ?>
			<div class="alert alert-success" role="alert">Настройки модуля сохранены</div>
		<?php endif; ?>
		
		<form action="" method="POST" role="form">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Общие настройки</h3>
				</div>
				<table class="table table-bordered" style="margin: 0;">
					<tbody>
						<tr>
							<td width="50%">
								<div class="form-group">
									<label for="moduleOn">Модуль</label>
									<div class="col-sm-12 settings-input-col">
										<button type="button" id="moduleOn" class="btn btn-sm btn-<?=($this->config['on'] ? 'success' : 'danger')?>" style="margin: 2px 0; font-weight: bold;"><?=($this->config['on'] ? 'ВКЛЮЧЕН' : 'ВЫКЛЮЧЕН')?></button>
										<input type="hidden" name="settings[on]" value="<?=($this->config['on'] ? 1 : 0)?>" />
										<span class="help-block">Включение и выключение модуля</span>
									</div>
								</div>
								<div class="form-group">
									<label for="moduleMyDomain">Группы пользователей</label>
									<div class="col-sm-8 settings-input-col">
										<?php

											$this->config['allowgroups'] = explode(',', $this->config['allowgroups']);

											$allowgroups = array();
											$result = $this->dle_db->query("SELECT * FROM " . USERPREFIX . "_usergroups WHERE id != 5 ORDER BY id ASC");
											while ($row = $result->fetch_assoc()) {
												$allowgroups[$row['id']] = $row['group_name'];
											}

											$newsallowfields = array(
												'title' => 'Заголовок',
												'category' => 'Категория',
												'short_story' => 'Краткое описание',
												'full_story' => 'Полное описание',
												'tags' => 'Облако тегов',
											);

											$allowgroups_list = '';
											foreach ($allowgroups as $key => $value) {
												if (in_array($key, $this->config['allowgroups']))
													$selected = ' selected';
												else
													$selected = '';

												$allowgroups_list .= "<option value=\"$key\"$selected>$value</option>";
											}

										?>
										<select data-placeholder="Выберите группу ..." name="settings[allowgroups][]" id="allowgroups" onchange="onNewsAllowFieldsChange(this)" class="allowgroups" multiple style="width:350px;"><?=$allowgroups_list?></select>
										<script type="text/javascript">
										<!--
											$(".allowgroups").chosen({
												allow_single_deselect: true,
												no_results_text: "Ничего не найдено"
											});

											function ShowOrHideEx(id, show) {
												var item = null;

												if (document.getElementById) {
													item = document.getElementById(id);
												} else if (document.all) {
													item = document.all[id];
												} else if (document.layers) {
													item = document.layers[id];
												}

												if (item && item.style) {
													item.style.display = show ? "" : "none";
												}
											}

											function onNewsAllowFieldsChange(obj) {
												var value = $(obj).val();

												if ($.isArray(value)) {
													<?php
													foreach ($allowgroups as $key => $value) {
														if ($key) {
															?>
															ShowOrHideEx("xfield_holder_<?=$key?>", jQuery.inArray("<?=$key?>", value) != -1);
															<?php
														}
													}
													?>
												}
											}
										-->
										</script>
									</div>
									<span class="help-block">Группы пользователей которым будет доступен модуль</span>
								</div>
								<div class="form-group">
									<label for="moduleApiToken">API Ключ</label>
									<div class="col-sm-8 settings-input-col">
										<input type="text" name="settings[api_token]" class="form-control" id="moduleApiToken" value="<?=$this->config['api_token']?>" placeholder="Введите персональный API Ключ" />
									</div>
									<span class="help-block">Введите ваш личный API ключ</span>
								</div>
								<div class="form-group">
									<label>Размер видео</label>
									<div class="col-sm-12 settings-input-col">
										<input type="text" name="settings[video_width]" class="form-control settings-videosize-input" id="moduleVideoWidth" value="<?=$this->config['video_width']?>" placeholder="ШИРИНА" />
										<div class="settings-videosize-delimiter">X</div>
										<input type="text" name="settings[video_height]" class="form-control settings-videosize-input" id="moduleVideoHeight" value="<?=$this->config['video_height']?>" placeholder="ВЫСОТА" />
										<div class="clear"></div>
									</div>
									<span class="help-block">Выставите оптимальный размер видео <b>ШИРИНА</b> х <b>ВЫСОТА</b></span>
								</div>
								<div class="form-group">
									<label for="moduleMyDomain">Свой домен для плеера</label>
									<div class="col-sm-8 settings-input-col">
										<input type="text" name="settings[domain]" class="form-control" id="moduleMyDomain" value="<?=$this->config['domain']?>" placeholder="http://moonwalk.cc" />
									</div>
									<span class="help-block">Введите свой домен, по умолчанию <b>http://moonwalk.cc</b></span>
								</div>
							</td>
							<td width="50%">
								<div class="form-group">
									<label for="moduleFieldTitle">Название фильма из поля</label>
									<div class="col-sm-8 settings-input-col">
										<select name="settings[title]" id="moduleFieldTitle" class="form-control">
											<?php if ($this->fields['title']) foreach ($this->fields['title'] as $key => $value) {
												?><option value="<?=$key?>"<?=($key == $this->config['title'] ? ' selected' : '')?>><?=$value?></option><?php
											} ?>
										</select>
									</div>
									<span class="help-block">Выберите поле из которого брать название фильма</span>
								</div>
								<div class="form-group">
									<label for="moduleFieldKinopoisk">ID видео на kinopoisk.ru из поля</label>
									<div class="col-sm-8 settings-input-col">
										<select name="settings[kinopoisk_id]" id="moduleFieldKinopoisk" class="form-control">
											<?php if ($this->fields['kinopoisk']) foreach ($this->fields['kinopoisk'] as $key => $value) {
												?><option value="<?=$key?>"<?=($key == $this->config['kinopoisk_id'] ? ' selected' : '')?>><?=$value?></option><?php
											} ?>
										</select>
									</div>
									<span class="help-block">Выберите поле из которого брать ID видео на kinopoisk.ru</span>
								</div>
								<div class="form-group">
									<label for="moduleFieldWorldArt">ID видео на world-art.ru из поля</label>
									<div class="col-sm-8 settings-input-col">
										<select name="settings[world_art_id]" id="moduleFieldWorldArt" class="form-control">
											<?php if ($this->fields['kinopoisk']) foreach ($this->fields['kinopoisk'] as $key => $value) {
												?><option value="<?=$key?>"<?=($key == $this->config['world_art_id'] ? ' selected' : '')?>><?=$value?></option><?php
											} ?>
										</select>
									</div>
									<span class="help-block">Выберите поле из которого брать ID видео на world-art.ru</span>
								</div>
								<div class="form-group">
									<label for="moduleFieldPornolab">ID видео в базе pornolab.net из поля</label>
									<div class="col-sm-8 settings-input-col">
										<select name="settings[pornolab_id]" id="moduleFieldPornolab" class="form-control">
											<?php if ($this->fields['kinopoisk']) foreach ($this->fields['kinopoisk'] as $key => $value) {
												?><option value="<?=$key?>"<?=($key == $this->config['pornolab_id'] ? ' selected' : '')?>><?=$value?></option><?php
											} ?>
										</select>
									</div>
									<span class="help-block">Выберите поле из которого брать ID видео на pornolab.net</span>
								</div>
								<div class="form-group">
									<label for="moduleFieldOutput">Ссылка для вывода плеера в поле</label>
									<div class="col-sm-8 settings-input-col">
										<select name="settings[output]" id="moduleFieldOutput" class="form-control">
											<?php if ($this->fields['output']) foreach ($this->fields['output'] as $key => $value) {
												?><option value="<?=$key?>"<?=($key == $this->config['output'] ? ' selected' : '')?>><?=$value?></option><?php
											} ?>
										</select>
									</div>
									<span class="help-block">Выберите поле в которое вставлять сгенерированную ссылку</span>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
				<?php if ($voice_settings): ?>
					<div class="panel-heading panel-heading-bt">
						<h3 class="panel-title">Настройки определения лучшего качества <i id="VoiceHelp" class="glyphicon glyphicon-exclamation-sign text-danger" data-placement="top" data-toggle="popover" data-trigger="hover" data-content="Доступно только для массового проставления ссылок по полю Kinopoisk ID, World-art ID или Pornolab ID и автоматического обновления лучшего качества!"></i></h3>
					</div>
					<table class="table table-bordered" style="margin: 0;">
						<tbody>
							<tr>
								<td width="50%">
									<div class="form-group">
										<label for="filmVoice">Настройки качества для фильмов</label>
										<div class="col-sm-12 settings-input-col">

											<div class="radio">
												<label>
													<input type="radio" name="settings[film_quality]" value="0"<?=($this->config['film_quality'] == 0 ? ' checked' : '')?>>
													Не использовать приоритет
													<br/><small><i>(по умолчанию)</i></small>
												</label>
											</div>
											<div class="radio">
												<label>
													<input type="radio" name="settings[film_quality]" value="1"<?=($this->config['film_quality'] == 1 ? ' checked' : '')?>>
													Использовать только приоритет качества видео
													<br/><small><i>(обязательно HD качетсво видео)</i></small>
												</label>
											</div>
											<div class="radio">
												<label>
													<input type="radio" name="settings[film_quality]" value="2"<?=($this->config['film_quality'] == 2 ? ' checked' : '')?>>
													Использовать только приоритет озвучек
													<br/><small><i>(приоритет озвучек)</i></small>
												</label>
											</div>
											<div class="radio">
												<label>
													<input type="radio" name="settings[film_quality]" value="3"<?=($this->config['film_quality'] == 3 ? ' checked' : '')?>>
													Использовать приоритет качества видео и озвучки
													<br/><small><i>(доминирует приоритет озвучки)</i></small>
												</label>
											</div>
											<div class="radio">
												<label>
													<input type="radio" name="settings[film_quality]" value="4"<?=($this->config['film_quality'] == 4 ? ' checked' : '')?>>
													Использовать строгий приоритет качества видео и озвучки
													<br/><small><i>(доминирует HD качетсво видео)</i></small>
												</label>
											</div>

										</div>
									</div>
									<div class="form-group">
										<label for="filmVoice">Приоритет озвучек для фильмов</label>
										<div class="col-sm-12 settings-input-col">

											<button data-toggle="modal" data-target="#filmVoiceModal" type="button" id="filmVoice" class="btn btn-sm btn-primary" style="margin: 2px 0; font-weight: bold;">Настроить приоритет озвучек</button>

										</div>
									</div>
								</td>
								<td width="50%">
									<div class="form-group">
										<label for="filmVoice">Настройки качества для сериалов</label>
										<div class="col-sm-12 settings-input-col">

											<div class="radio">
												<label>
													<input type="radio" name="settings[serial_quality]" value="0"<?=($this->config['serial_quality'] == 0 ? ' checked' : '')?>>
													Не использовать приоритет
													<br/><small><i>(по умолчанию)</i></small>
												</label>
											</div>
											<div class="radio">
												<label>
													<input type="radio" name="settings[serial_quality]" value="1"<?=($this->config['serial_quality'] == 1 ? ' checked' : '')?>>
													Использовать только приоритет качества видео
													<br/><small><i>(обязательно HD качетсво видео)</i></small>
												</label>
											</div>
											<div class="radio">
												<label>
													<input type="radio" name="settings[serial_quality]" value="2"<?=($this->config['serial_quality'] == 2 ? ' checked' : '')?>>
													Использовать только приоритет озвучек
													<br/><small><i>(приоритет озвучек)</i></small>
												</label>
											</div>
											<div class="radio">
												<label>
													<input type="radio" name="settings[serial_quality]" value="3"<?=($this->config['serial_quality'] == 3 ? ' checked' : '')?>>
													Использовать приоритет качества видео и озвучки
													<br/><small><i>(доминирует приоритет озвучки)</i></small>
												</label>
											</div>
											<div class="radio">
												<label>
													<input type="radio" name="settings[serial_quality]" value="4"<?=($this->config['serial_quality'] == 4 ? ' checked' : '')?>>
													Использовать строгий приоритет качества видео и озвучки
													<br/><small><i>(доминирует HD качетсво видео)</i></small>
												</label>
											</div>

										</div>
									</div>
									
									<div class="form-group">
										<label for="serialVoice">Приоритет озвучек для сериалов</label>
										<div class="col-sm-12 settings-input-col">
											
											<button data-toggle="modal" data-target="#serialVoiceModal" type="button" id="serialVoice" class="btn btn-sm btn-primary" style="margin: 2px 0; font-weight: bold;">Настроить приоритет озвучек</button>

										</div>
									</div>
								</td>
							</tr>
						</tbody>
					</table>

					<div class="panel-heading panel-heading-bt">
						<h3 class="panel-title">Настройки автоматического обновления лучшего качества <i id="CronHelp" class="glyphicon glyphicon-exclamation-sign text-danger" data-placement="top" data-toggle="popover" data-trigger="hover" data-content="Автоматическое обновление доступно только для новостей у которых заполнено поле Kinopoisk ID, World-art ID или Pornolab ID!"></i></h3>
					</div>
					<table class="table table-bordered" style="margin: 0;">
						<tbody>
							<tr>
								<td width="50%">
									<div class="form-group">
										<label for="moduleCronType">Тип</label>
										<div class="col-sm-4 settings-input-col">
											<select name="settings[cron_type]" id="moduleCronType" class="form-control hdlight-replace-field">
												<option value="1"<?=($this->config['cron_type'] == 1 ? ' selected' : '')?>>Все</option>
												<option value="2"<?=($this->config['cron_type'] == 2 ? ' selected' : '')?>>Опубликованные</option>
												<option value="3"<?=($this->config['cron_type'] == 3 ? ' selected' : '')?>>Неопубликованные</option>
											</select>
										</div>
										<span class="help-block">Выберите тип новостей которым будет обновлятся ссылка</span>
									</div>

									<div class="form-group">
										<label>Категории</label>
										<div class="col-sm-8 settings-input-col">
											<?php
												$xfieldsaction = "categoryfilter";
												include ENGINE_DIR . '/inc/xfields.php';
												echo $categoryfilter;

												$categories_list = CategoryNewsSelection(explode(',', $this->config['cron_cats']), 0);
											?>
											<select data-placeholder="Выберите категории ..." name="settings[cron_cats][]" id="category" onchange="onCategoryChange(this)" class="categoryselect" multiple style="width:100%;max-width:350px;"><?=$categories_list?></select>
											<div class="category-inverse"><input class="checkbox-inline hdlight-replace-field" type="checkbox" id="category_inverse" name="settings[cron_cats_inverse]" value="1" title="Не обновлять выбранные категории"<?=($this->config['cron_cats_inverse'] ? ' checked' : '')?>/></div>
											<script type="text/javascript">
											<!--
												$(".categoryselect").chosen({
													allow_single_deselect: true,
													no_results_text: "Ничего не найдено"
												});
											-->
											</script>
										</div>
										<span class="help-block" style="padding-top: 5px;">Категории в которых будет массовое обновление ссылок</span>
									</div>
								
									<div class="form-group">
										<label for="moduleCronKey">Белый список ID новостей</label>
										<div class="col-sm-12 settings-input-col">
											<textarea name="settings[cron_white_list]" class="form-control" rows="3"><?=$this->config['cron_white_list']?></textarea>
										</div>
										<span class="help-block">Новости в этом списке в обязательном порядке будут участвовать в автоматическом обновлении ссылок даже если не находятся в указанных категориях</span>
									</div>

									<div class="form-group">
										<label for="moduleCronKey">Чёрный список ID новостей</label>
										<div class="col-sm-12 settings-input-col">
											<textarea name="settings[cron_black_list]" class="form-control" rows="3"><?=$this->config['cron_black_list']?></textarea>
										</div>
										<span class="help-block">Новости в этом списке не будут участвовать в автоматическом обновлении ссылок</span>
									</div>
								</td>
								<td width="50%">
									<div class="form-group">
										<label for="serialVoice">Ссылка для планировщика заданий</label>
										<div class="col-sm-12 settings-input-col">
											
											<?=($this->config['cron_key'] ? "<input type=\"text\" class=\"form-control\" value=\"{$this->dle_config['http_home_url']}hdlight_cron.php?key=" . htmlspecialchars($this->config['cron_key'], ENT_QUOTES, $this->dle_config['charset']) . "\" />" : "<i class=\"text-danger\">Вы не заполнили секретный ключ</i>")?>
										
										</div>
										<span class="help-block">Ссылка будет доступна если вы заполните секретный ключ</span>
									</div>

									<div class="form-group">
										<label for="moduleCronKey">Секретный ключ</label>
										<div class="col-sm-8 settings-input-col">
											<input type="text" name="settings[cron_key]" class="form-control" id="moduleCronKey" value="<?=$this->config['cron_key']?>" placeholder="Введите секретный ключ" />
										</div>
										<span class="help-block">Случайный ключ: <b><?=md5(time() . $this->dle_config['http_home_url'] . $_SESSION['user_id']['email'])?></b></span>
									</div>

									<?php
										if ($this->config['fields']) {
											$this->config['fields'] = explode('|', $this->config['fields']);

											foreach ($this->config['fields'] as $field_name) {
												$$field_name = true;
											}
										}
									?>
									<div class="form-group">
										<label for="moduleField">По полю ID из базы</label>
										<div class="col-sm-12 settings-input-col">
											<div class="fields-list">
												<label>
													<input type="checkbox" class="moduleField" name="settings[fields][]" value="kinopoisk_id" <?=($kinopoisk_id ? ' checked' : '')?>> kinopoisk.ru
												</label>
												<label>
													<input type="checkbox" class="moduleField" name="settings[fields][]" value="world_art_id" <?=($world_art_id ? ' checked' : '')?>> world-art.ru
												</label>
												<label>
													<input type="checkbox" class="moduleField" name="settings[fields][]" value="pornolab_id" <?=($pornolab_id ? ' checked' : '')?>> pornolab.net
												</label>
											</div>
										</div>
										<span class="help-block">Выберите поля по которым будет производится поиск видео</span>
									</div>

									<div class="form-group">
										<label for="moduleThreads">Потоки</label>
										<div class="col-sm-4 settings-input-col">
											<select name="settings[cron_threads]" id="moduleThreads" class="form-control hdlight-replace-field">
												<option value="1"<?=($this->config['cron_threads'] == 1 ? ' selected' : '')?>>1</option>
												<option value="2"<?=($this->config['cron_threads'] == 2 ? ' selected' : '')?>>2</option>
												<option value="3"<?=($this->config['cron_threads'] == 3 ? ' selected' : '')?>>3</option>
												<option value="5"<?=($this->config['cron_threads'] == 5 ? ' selected' : '')?>>5</option>
												<option value="7"<?=($this->config['cron_threads'] == 7 ? ' selected' : '')?>>7</option>
												<option value="10"<?=($this->config['cron_threads'] == 10 ? ' selected' : '')?>>10</option>
											</select>
										</div>
										<span class="help-block">Выставите оптимальное кол-во одновременных потоков<br><i>Рекомендуемое значение (<b>5</b>)</i></span>
									</div>

									<div class="form-group">
										<label for="moduleInterval">Интервал</label>
										<div class="col-sm-4 settings-input-col">
											<select name="settings[cron_interval]" class="form-control hdlight-replace-field">
												<option value="0"<?=($this->config['cron_interval'] == 0 ? ' selected' : '')?>>0 сек</option>
												<option value="1"<?=($this->config['cron_interval'] == 1 ? ' selected' : '')?>>1 сек</option>
												<option value="2"<?=($this->config['cron_interval'] == 2 ? ' selected' : '')?>>2 сек</option>
												<option value="3"<?=($this->config['cron_interval'] == 3 ? ' selected' : '')?>>3 сек</option>
												<option value="4"<?=($this->config['cron_interval'] == 4 ? ' selected' : '')?>>4 сек</option>
												<option value="5"<?=($this->config['cron_interval'] == 5 ? ' selected' : '')?>>5 сек</option>
											</select>
										</div>
										<span class="help-block">Выставите оптимальный интервал между каждым последующим обновлением ссылки чтобы снизить нагрузку на базу данных<br><i>Рекомендуемое значение (<b>3 сек</b>)</i></span>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				<?php endif; ?>
				<div class="panel-footer">
					<textarea id="filmVoicePrioritySave" name="film_voice_save" style="display: none;"></textarea>
					<textarea id="serialVoicePrioritySave" name="serial_voice_save" style="display: none;"></textarea>
					<button type="submit" class="btn btn-default" id="SaveSettings">Сохранить</button>
				</div>
			</div>
		</form>

		<footer>
			<p><?php echo $this->copyright; ?></p>
		</footer>
	</div>

	<?php if ($voice_settings): ?>
		<div class="modal fade" id="filmVoiceModal" tabindex="-1" role="dialog" aria-labelledby="filmVoiceModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="filmVoiceModalLabel">Приоритет озвучек фильмов</h4>
					</div>
					<div class="modal-body">

						<div class="alert alert-info" role="alert">После изменений не забудьте закрыть это окно и сохранить настройки!</div>

						<div class="voice-priority">
							<div id="filmVoiceUseContainer" class="sortContainer">
								<?php if ($film_voice_use) foreach ($film_voice_use as $item) { ?>
									<div id="film_<?=$item['id']?>" class="sortable voice-use" data-id="<?=$item['id']?>"><?=$item['voice_name']?><a href="javascript:void(0)" class="del-voice btn-xs" data-id="<?=$item['id']?>" title="Удалить озвучку"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a></div>
								<?php } ?>
							</div>
							<div id="filmVoiceOffContainer" class="sortContainer">
								<?php if ($film_voice_off) foreach ($film_voice_off as $item) { ?>
									<div id="film_<?=$item['id']?>" class="sortable" data-id="<?=$item['id']?>"><?=$item['voice_name']?><a href="javascript:void(0)" class="add-voice btn-xs text-success" data-id="<?=$item['id']?>" title="Добавить озвучку"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a></div>
								<?php } ?>
							</div>
						</div>

						<script type="text/javascript">
							$(function() {
								$("#filmVoiceUseContainer").sortable({
									connectWith: "#filmVoiceOffContainer",
									placeholder: "emptySpace",
									update: function(event, ui) {
										var e = $("#film_" + ui.item.data("id"));

										if (e.parent().attr("id") == "filmVoiceOffContainer") {
											e.removeClass("voice-use");
											e.find("a").addClass("text-success").attr("title", "Добавить озвучку").removeClass("del-voice").addClass("add-voice");
											e.find("a").find("span").removeClass("glyphicon-remove").addClass("glyphicon-plus");
										}
									}
								});
								$("#filmVoiceOffContainer").sortable({
									connectWith: "#filmVoiceUseContainer",
									placeholder: "emptySpace",
									update: function(event, ui) {
										var e = $("#film_" + ui.item.data("id"));

										if (e.parent().attr("id") == "filmVoiceUseContainer") {
											e.addClass("voice-use");
											e.find("a").removeClass("text-success").attr("title", "Удалить озвучку").removeClass("add-voice").addClass("del-voice");
											e.find("a").find("span").removeClass("glyphicon-plus").addClass("glyphicon-remove");
										}
									}
								});
								$("#filmVoiceOffContainer").on("click", ".add-voice", function() {
									var e = $(this).parent();

									e.addClass("voice-use");
									e.find("a").removeClass("text-success").attr("title", "Удалить озвучку").removeClass("add-voice").addClass("del-voice");
									e.find("a").find("span").removeClass("glyphicon-plus").addClass("glyphicon-remove");

									$("#filmVoiceUseContainer").append(e.outerHTML());

									e.remove();
								});
								$("#filmVoiceUseContainer").on("click", ".del-voice", function() {
									var e = $(this).parent();

									e.removeClass("voice-use");
									e.find("a").addClass("text-success").attr("title", "Добавить озвучку").removeClass("del-voice").addClass("add-voice");
									e.find("a").find("span").removeClass("glyphicon-remove").addClass("glyphicon-plus");

									$("#filmVoiceOffContainer").append(e.outerHTML());

									e.remove();
								});
							});
						</script>

					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="serialVoiceModal" tabindex="-1" role="dialog" aria-labelledby="serialVoiceModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="serialVoiceModalLabel">Приоритет озвучек сериалов</h4>
					</div>
					<div class="modal-body">
						
						<div class="alert alert-info" role="alert">После изменений не забудьте закрыть это окно и сохранить настройки!</div>

						<div class="voice-priority">
							<div id="serialVoiceUseContainer" class="sortContainer">
								<?php if ($serial_voice_use) foreach ($serial_voice_use as $item) { ?>
									<div id="serial_<?=$item['id']?>" class="sortable voice-use" data-id="<?=$item['id']?>"><?=$item['voice_name']?><a href="javascript:void(0)" class="del-voice btn-xs" data-id="<?=$item['id']?>" title="Удалить озвучку"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a></div>
								<?php } ?>
							</div>
							<div id="serialVoiceOffContainer" class="sortContainer">
								<?php if ($serial_voice_off) foreach ($serial_voice_off as $item) { ?>
									<div id="serial_<?=$item['id']?>" class="sortable" data-id="<?=$item['id']?>"><?=$item['voice_name']?><a href="javascript:void(0)" class="add-voice btn-xs text-success" data-id="<?=$item['id']?>" title="Добавить озвучку"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a></div>
								<?php } ?>
							</div>
						</div>

						<script type="text/javascript">
							$(function() {
								$("#serialVoiceUseContainer").sortable({
									connectWith: "#serialVoiceOffContainer",
									placeholder: "emptySpace",
									update: function(event, ui) {
										var e = $("#serial_" + ui.item.data("id"));

										if (e.parent().attr("id") == "serialVoiceOffContainer") {
											e.removeClass("voice-use");
											e.find("a").addClass("text-success").attr("title", "Добавить озвучку").removeClass("del-voice").addClass("add-voice");
											e.find("a").find("span").removeClass("glyphicon-remove").addClass("glyphicon-plus");
										}
									}
								});
								$("#serialVoiceOffContainer").sortable({
									connectWith: "#serialVoiceUseContainer",
									placeholder: "emptySpace",
									update: function(event, ui) {
										var e = $("#serial_" + ui.item.data("id"));

										if (e.parent().attr("id") == "serialVoiceUseContainer") {
											e.addClass("voice-use");
											e.find("a").removeClass("text-success").attr("title", "Удалить озвучку").removeClass("add-voice").addClass("del-voice");
											e.find("a").find("span").removeClass("glyphicon-plus").addClass("glyphicon-remove");
										}
									}
								});
								$("#serialVoiceOffContainer").on("click", ".add-voice", function() {
									var e = $(this).parent();

									e.addClass("voice-use");
									e.find("a").removeClass("text-success").attr("title", "Удалить озвучку").removeClass("add-voice").addClass("del-voice");
									e.find("a").find("span").removeClass("glyphicon-plus").addClass("glyphicon-remove");

									$("#serialVoiceUseContainer").append(e.outerHTML());

									e.remove();
								});
								$("#serialVoiceUseContainer").on("click", ".del-voice", function() {
									var e = $(this).parent();

									e.removeClass("voice-use");
									e.find("a").addClass("text-success").attr("title", "Добавить озвучку").removeClass("del-voice").addClass("add-voice");
									e.find("a").find("span").removeClass("glyphicon-remove").addClass("glyphicon-plus");

									$("#serialVoiceOffContainer").append(e.outerHTML());

									e.remove();
								});
							});
						</script>

					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<!-- HD Light JavaScript -->
	<script type="text/javascript">
	<!--
		$("#moduleOn").click(function() {
			var on = parseInt($(this).next("input").val());

			if (on) {
				$(this).removeClass("btn-success").addClass("btn-danger").text("ВЫКЛЮЧЕН");
				$(this).next("input").val("0");
			} else {
				$(this).removeClass("btn-danger").addClass("btn-success").text("ВКЛЮЧЕН");
				$(this).next("input").val("1");
			}
		});

		$("#VoiceHelp, #CronHelp").hover(function() {
			$(this).popover("show");
		}, function() {
			$(this).popover("hide");
		});

		$("#SaveSettings").click(function() {
			var film_voice_save = new Array();
			var serial_voice_save = new Array();

			$.each($("#filmVoiceUseContainer .sortable"), function(i, e) {
				var priority = i + 1;

				film_voice_save[i] = priority + "|" + $(e).data("id");
			});

			$.each($("#serialVoiceUseContainer .sortable"), function(i, e) {
				var priority = i + 1;

				serial_voice_save[i] = priority + "|" + $(e).data("id");
			});

			film_voice_save = film_voice_save.join();
			serial_voice_save = serial_voice_save.join();

			$("#filmVoicePrioritySave").val(film_voice_save);
			$("#serialVoicePrioritySave").val(serial_voice_save);
		});
	-->
	</script>
	
</body>
</html>