<div class="viewn_loop vInner vInnerStatic">
	<div class="viewn_top">
		<div class="viewn_t_in">
			<div class="viewn_t_ins">
				<span class="title"><span>Восстановить пароль</span></span>
			</div>
		</div>
	</div><!--/viewn_top-->
<div class="baseform">
  <table class="tableform">
    <tr>
      <td class="label">
        Ваш логин или E-Mail на сайте:
      </td>
      <td><input class="f_input" type="text" name="lostname" /></td>
    </tr>
    [sec_code]<tr>
      <td class="label">
       Введите код<br />с картинки:<span class="impot">*</span>
      </td>
      <td>
        <div>{code}</div>
        <div><input class="f_input" style="width:115px" maxlength="45" name="sec_code" size="14" /></div>
      </td>
    </tr>[/sec_code]
    [recaptcha]
    <tr>
      <td class="label">
       Введите два слова,<br />показанных на изображении: <span class="impot">*</span>
      </td>
      <td>
        <div>{recaptcha}</div>
      </td>
    </tr>
    [/recaptcha]
  </table>
  <div class="fieldsubmit">
    <td><button name="send_btn" class="btn" type="submit"><span>Отправить</span></button></td>
  </div>
</div>