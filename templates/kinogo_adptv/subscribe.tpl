<script type="text/javascript">
function newsSubs(a, b, c) {
	if( dle_group == 5 && c == 0 ) {
		var d = {};
		d[dle_act_lang[3]] = function() {
			$(this).dialog('close');
		},
		d[dle_p_send] = function() {
			if( $('#email').val().length < 6 ) {
				DLEalert(dle_req_field, dle_info);
				return false;
			}
    
			$.post(dle_root + 'engine/ajax/subscribe.news.php', {news_id: a, user_id: b, email: $('#email').val(), action: c}, function(data) {
				$('#count').text(data.count);
				$('#news_subscribe').text(data.text);
				$('#news_subscribe').attr('onclick', data.value);
				$('#news_subscribe').attr('title', data.text);
				if( c == 1 ) {
					$('#news_subscribe').removeClass('link-unsubscribe').addClass('link-subscribe');
				} else {
					$('#newsSubs').remove();
					$('#news_subscribe').removeClass('link-subscribe').addClass('link-unsubscribe');
				}
				ShowLoading(data.loading);
				setTimeout("HideLoading('');", 5000);
			}, 'json');
		}

		$('#newsSubs').remove();
		$('body').append('<div id="newsSubs" title="Уведомление о выходе фильма"><center>Введите ваш E-Mail<br /><br /><input type="text" name="email" id="email" class="ui-widget-content ui-corner-all" style="width:215px; padding: .4em;" /></center></div>');
		$('#newsSubs').dialog({
			dialogClass: 'modalfixed', autoOpen: true, show: 'fade', hide: 'fade', width: 350, buttons: d
		});
		$('.modalfixed.ui-dialog').css({position:'fixed'});
		$('#newsSubs').dialog('option', 'position', ['0','0']);
	} else {
		$.post(dle_root + 'engine/ajax/subscribe.news.php', {news_id: a, user_id: b, action: c}, function(data) {
			$('#count').text(data.count);
			$('#news_subscribe').text(data.text);
			$('#news_subscribe').attr('onclick', data.value);
			$('#news_subscribe').attr('title', data.text);
			if( c == 1 ) {
				$('#news_subscribe').removeClass('link-unsubscribe').addClass('link-subscribe');
			} else {
				$('#news_subscribe').removeClass('link-subscribe').addClass('link-unsubscribe');
			}
			ShowLoading(data.loading);
			setTimeout("HideLoading('');", 5000);
		}, 'json');
	}   
}

function ShowSubs() {
	ShowLoading('');
	$('#news-sub').remove();
	var id = ($('div[id^=news-id-]').length > 0) ? $('div[id^=news-id-]').attr('id').replace('news-id-', '') : false;
	$.post(dle_root + 'engine/ajax/subscribe.php', {news_id: id}, function(data) {
		HideLoading('');
		$('body').append(data);
		$('#news-sub').dialog({
			dialogClass: 'modalfixed', autoOpen: true, show: 'fade', hide: 'fade', width: 460, height: 350, buttons: {
				'Закрыть' : function() { 
					$(this).dialog('close');							
				}
			}
		});
	});
	return false;
}
</script>
<div id="news-id-{news-id}"></div>
<table class="ignore-select" width="100%" style="margin-bottom:10px; background:#4D4D4D; border-radius:5px;">
	<tbody>
		<tr>
			<td style="padding-left:8px; padding-top:8px; padding-bottom:8px;">
				<b style="font-size: 18px;">Уведомление о выходе фильма</b>
				<p style="padding-top: 10px;">При выходе фильма онлайн Вы получите уведомление прямо на почту.</p>
			</td>
			<td width="230px" align="center">
                <a id="news_subscribe" href="#" class="{link-subscribe}" onclick="{subopt_n} return false;" title="{subscribe}">{subscribe}</a><br />
                <div style="padding-top: 10px;">Уже включили: <b style="color:red; cursor:pointer;" id="count" onclick="ShowSubs(); return false;">{sub_count}</b></div>
			</td>
		</tr>
	</tbody>
</table>