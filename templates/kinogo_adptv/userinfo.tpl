<form method="post" name="userinfo" id="userinfo" enctype="multipart/form-data" action="{usertitle}"><div class="padding_border">
<img src="{foto}" alt="" style="float: right;margin-bottom: 10px;"><b>Ник</b>: <b>{usertitle}</b>  
<span style="color: #C33;">[online]Online[/online][offline]Offline[/offline]</span> [not-logged] [ {edituser} ] [/not-logged] <br> <br> 

<b>Всего комментариев</b>:  {comm-num} {comments}<br>
<b>Дата регистрации</b>: {registration} <br>
<b>Последнее посещение</b>: {lastdate} <br>
<b>Группа</b>: {status} [time_limit] в группе до: {time_limit}[/time_limit]  <br><br>
<br><br>

<span class="fbutton">{pm}</span>
<span class="fbutton">{email}</span>
<br>

[not-logged]
<div id="options" style="display:none;">
	<br><br>
	<div class="pheading"><h2>Редактирование профиля</h2></div>
	<div class="baseform">
		<table class="tableform">
			<tbody><tr>
				<td class="label">Ваше Имя:</td>
				<td><input type="text" name="fullname" value="{fullname}" class="f_input"></td>
			</tr>
			<tr>
				<td class="label">Ваш E-Mail:</td>
				<td><input type="text" name="email" value="{editmail}" class="f_input"><br>
				{hidemail}</td>
			</tr>
			<tr>
				<td class="label">Место жительства:</td>
				<td><input type="text" name="land" value="{land}" class="f_input"></td>
			</tr>
			<tr>
				<td class="label">Старый пароль:</td>
				<td><input type="password" name="altpass" class="f_input"></td>
			</tr>
			<tr>
				<td class="label">Новый пароль:</td>
				<td><input type="password" name="password1" class="f_input"></td>
			</tr>
			<tr>
				<td class="label">Повторите:</td>
				<td><input type="password" name="password2" class="f_input"></td>
			</tr>
			<tr>
				<td class="label" valign="top">Блокировка по IP:<br>Ваш IP: {ip}</td>
				<td>
				<div><textarea name="allowed_ip" style="width:98%;" rows="5" class="f_textarea"></textarea></div>
				<div>
					<span class="small" style="color:red;">
					* Внимание! Будьте бдительны при изменении данной настройки.
					Доступ к Вашему аккаунту будет доступен только с того IP-адреса или подсети, который Вы укажете.
					Вы можете указать несколько IP адресов, по одному адресу на каждую строчку.
					<br>
					Пример: 192.48.25.71 или 129.42.*.*</span>
				</div>
				</td>
			</tr>
			<tr>
				<td class="label">Аватар:</td>
				<td>
				<input type="file" name="image" class="f_input" style="padding-bottom: 4px;"><br>
				<div class="checkbox"><input type="checkbox" name="del_foto" id="del_foto" value="yes">&nbsp;<label for="del_foto">Удалить фотографию</label></div>
				</td>
			</tr>
			<tr>
				<td class="label">О себе:</td>
				<td><textarea name="info" style="width:98%;" rows="5" class="f_textarea">{editinfo}</textarea></td>
			</tr>
			<tr>
				<td class="label">Подпись:</td>
				<td><textarea name="signature" style="width:98%;" rows="5" class="f_textarea">{editsignature}</textarea></td>
			</tr>
			
		</tbody></table>
		<div class="fieldsubmit">
			<input class="fbutton" type="submit" name="submit" value="Сохранить">
			<input name="submit" type="hidden" id="submit" value="submit">
		</div>
	</div>
</div>
    [/not-logged]
</div>