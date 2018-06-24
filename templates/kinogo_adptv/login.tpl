[not-group=5]
<a class="lbn" id="logbtn" href="{profile-link}" rel="nofollow">{login}</a>  / 	 
<a href="{favorites-link}" rel="nofollow">Закладки ({favorite-count})</a>  / 
<a href="{pm-link}" rel="nofollow">Сообщений ({new-pm})</a> /
<a class="thide lexit" href="{logout-link}" rel="nofollow">Выход</a>
[/not-group]
[group=5]
<script>
function change(idName) {
  if(document.getElementById(idName).style.display=='none') {
    document.getElementById(idName).style.display = '';
  } else {
    document.getElementById(idName).style.display = 'none';
  }
  return false;
}
</script>
<a href="#" onclick="change('test')" rel="nofollow">Вход</a> / <a href="{registration-link}" rel="nofollow">Регистрация</a>  					
[/group]