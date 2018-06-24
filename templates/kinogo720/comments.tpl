[aviable=lastcomments]<div class="last-comm-link">{news_title}</div>[/aviable]

<div class="comm-item clearfix">
	<div class="comm-left">
		<img src="{foto}" alt="{login}"/>
	</div>
	<div class="comm-right">
		<div class="comm-top-info clearfix">
			<div class="comm-info-line clearfix">
				<div class="comm-author">{author}</div>
				<div class="comm-date">{date}</div>
				[rating-type-3] 
				<div class="comm-rate[positive-comment] pos-comm[/positive-comment][negative-comment] neg-comm[/negative-comment]">
					[rating-plus]+[/rating-plus]
					{rating}
					[rating-minus]-[/rating-minus] 
				</div>
				[/rating-type-3]
			</div>
		</div>
		<div class="comm-text">
			<div class="comm-body clearfix">
				{comment}
			</div>
			[signature]<div class="signature">{signature}</div>[/signature]
		</div>
		<div class="comm-bottom-info">
			<ul class="clearfix">
				[not-aviable=lastcomments]
				<li[not-treecomments] class="reply"[/not-treecomments]><i class="fa fa-reply"></i>[reply]Ответить[/reply]</li>
				<li class="mob-vis"><i class="fa fa-quote-right"></i>[fast]Цитата[/fast]</li>
				[/not-aviable]
				[not-group=5]
				<li>[spam]Спам[/spam]</li>
				<li>[complaint]Пожаловаться[/complaint]</li>
				<li>[com-edit]Редактировать[/com-edit]</li>
				<li>[com-del]Удалить[/com-del]</li>
				<li>{mass-action}</li>
				[/not-group]
			</ul>
		</div>
	</div>
</div>
