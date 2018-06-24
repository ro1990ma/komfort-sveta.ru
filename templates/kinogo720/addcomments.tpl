<div id="addcform">
<!--noindex-->
<div class="add-com-form clearfix">
	<div class="add-com-textarea clearfix">
		<div class="add-comm">
<div class="comm-title">Прокомментировать <i class="fa fa-angle-down"></i></div>
			[not-logged]
			[vk]<div class="login-social comm-social clearfix" data-label="Войти через соц.сети">[/vk]
						[vk]<a href="{vk_url}" target="_blank"><img src="{THEME}/images/social/vkontakte.png" /></a>[/vk]
						[odnoklassniki]<a href="{odnoklassniki_url}" target="_blank"><img src="{THEME}/images/social/odnoklassniki.jpg" /></a>[/odnoklassniki]
						[facebook]<a href="{facebook_url}" target="_blank"><img src="{THEME}/images/social/facebook.jpg" /></a>[/facebook]
						[mailru]<a href="{mailru_url}" target="_blank"><img src="{THEME}/images/social/mailru.gif" /></a>[/mailru]
						[yandex]<a href="{yandex_url}" target="_blank"><img src="{THEME}/images/social/yandex.png" /></a>[/yandex]
						[google]<a href="{google_url}" target="_blank"><img src="{THEME}/images/social/google.jpg" /></a>[/google]
			[vk]</div>[/vk]
			<div class="ac-inputs clearfix">
				<input type="text" maxlength="35" name="name" id="name" placeholder="Ваше имя" />
				<input type="text" maxlength="35" name="mail" id="mail" placeholder="Ваш e-mail (необязательно)" />
			</div>
			[/not-logged]
			<div class="ac-textarea">{editor}</div>
[not-group=1]
			<div class="ac-protect">
				[question]
				<div class="sep-input clearfix">
					<div class="label"><span>Вопрос:</span><span class="impot">*</span> {question}</div>
					<div class="input"><input type="text" name="question_answer" id="question_answer" placeholder="Впишите ответ на вопрос" /></div>
				</div>
				[/question]
				[sec_code]
				<div class="sep-input clearfix">
					<div class="label">Введите код с картинки:<span class="impot">*</span></div>
					<div class="input"><input type="text" name="sec_code" id="sec_code" maxlength="45" />{sec_code}</div>
				</div>
				[/sec_code]
				[recaptcha]
				<div class="sep-input clearfix">
					<div class="label"><span>Введите два слова, показанных на изображении:</span><span class="impot">*</span></div>
					<div class="input">{recaptcha}</div>
				</div>
				[/recaptcha]
			</div>
[/not-group]
			<div class="ac-submit"><button name="submit" class="fbutton" type="submit">Отправить</button></div>
		</div>
	</div>
</div>
<!--/noindex-->
</div>