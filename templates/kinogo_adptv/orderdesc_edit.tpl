<div class="orderdesc-add-area orderdesc-edit-area">
	<form method="post">
		<h1>Редактирование заказа #{id}</h1>
		<h4>Название на русском языке(*):</h4>
		<input type="text" name="title" class="orderdesc-add-input orderdesc-inputclass" value="{title}" />
		<h4>Название на оригинальном языке:</h4>
		<input type="text" name="orig_title" class="orderdesc-add-input orderdesc-inputclass" value="{orig_title}" />
		<h4>Категория (*):</h4>
		<select name="category" class="orderdesc-inputclass">{catlist}</select>
		<h4>Год выпуска:</h4>
		<input type="text" name="year" class="orderdesc-add-input orderdesc-inputclass" value="{year}" />
		<h4>Описание:</h4>
		<textarea name="descr" class="orderdesc-inputclass">{descr}</textarea>
<hr/>
		<h4>Автор заявки:</h4>
		<input type="text" name="autor" class="orderdesc-add-input orderdesc-inputclass" value="{autor}" />
		<h4>Дата подачи заявки:</h4>
		<input type="text" name="date" class="orderdesc-add-input orderdesc-inputclass" value="{date}" />
<hr/>
		<h4>Комментарий к статусу:</h4>
		<input type="text" name="comment" class="orderdesc-add-input orderdesc-inputclass" value="{comment}" />
		<h4>Статус:</h4>
		{status}
		<h4>Ссылка на выполненную заявку:</h4>
		<input type="text" name="link" class="orderdesc-add-input orderdesc-inputclass" value="{link}" />
		<input type="hidden" name="do" value="orderdesc" /><input type="hidden" name="action" value="doedit" /><input type="hidden" name="id" value="{id}" />
		<input type="submit" value="Удалить" id="orderdesc-del-submit" /><a href="/index.php?do=orderdesc" id="orderdesc-edit-cancel">Отмена</a><input type="submit" value="Сохранить" id="orderdesc-add-submit" />
	</form>
</div>
<script>
$(function(){
	$("#orderdesc-del-submit").click(function(){
		$(this).parents('form').find("input[name='action']").val('del');
	});
})
</script>