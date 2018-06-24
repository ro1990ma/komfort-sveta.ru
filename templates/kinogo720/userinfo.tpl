<div class="user-wr">
	<div class="user-inner clearfix">
		<div class="user-left">
			<div class="user-avatar"><img src="{foto}" alt=""/></div>
			<div class="user-status">
				[online]
				<p class="online">� ����</p>
				[/online]
				[offline]
				<p class="offline">�� � ����</p>
				[/offline]
			</div>
			<div class="user-connect">
				{email}
				[not-group=5]{pm}[/not-group]
			</div>
		</div>
		<div class="user-right">
			<h1>������������: {usertitle}</h1>
			<div class="ur-item clearfix">
				<div class="ur-left">������:</div>
				<div class="ur-right">{status} [time_limit]&nbsp;� ������ ��: {time_limit}[/time_limit]</div>
			</div>
			<div class="ur-item clearfix">
				<div class="ur-left">�����������:</div>
				<div class="ur-right">{registration}</div>
			</div>
			<div class="ur-item clearfix">
				<div class="ur-left">�������(�):</div>
				<div class="ur-right">{lastdate}</div>
			</div>
			<div class="ur-item clearfix">
				<div class="ur-left">����������:</div>
				<div class="ur-right">{news-num} [news-num]{news} [rss] RSS [/rss][/news-num]</div>
			</div>
			<div class="ur-item clearfix">
				<div class="ur-left">������������:</div>
				<div class="ur-right">{comm-num} [comm-num]{comments}[/comm-num]</div>
			</div>
			[not-group=5]
			[fullname]<div class="ur-item clearfix">
				<div class="ur-left">������ ���:</div>
				<div class="ur-right">{fullname}</div>
			</div>[/fullname]
			[land]<div class="ur-item clearfix">
				<div class="ur-left">����� ����������:</div>
				<div class="ur-right">{land}</div>
			</div>[/land]
			<div class="ur-item clearfix">
				<div class="ur-left">������� � ����:</div>
				<div class="ur-right">{info}</div>
			</div>
			[/not-group]
		</div>
	</div>
	[not-logged]<div class="user-edit"> {edituser} </div>[/not-logged]
</div>


<script>
$(document).ready(function(){
	$(".short-item").wrapAll("<div class='clearfix' />");
	});
</script>



<div id="options" style="display:none; margin-bottom: 30px" class="form-wrap">
<header class="form-title"><h1>�������������� �������:</h1></header>

<div class="sep-input clearfix">
<div class="label"><label>���� ���:</div>
<div class="input"><input type="text" name="fullname" value="{fullname}" placeholder="���� ���" /></div>
</div>

<div class="sep-input clearfix">
<div class="label"><label>��� E-Mail:</label></div>
<div class="input"><input type="text" name="email" value="{editmail}" placeholder="��� E-Mail: {editmail}" /></div>
</div>

<div class="sep-checks">
{hidemail}
<input style="margin-left: 50px" type="checkbox" id="subscribe" name="subscribe" value="1" /> <label for="subscribe">���������� �� ����������� ��������</label>
</div>

<div class="sep-input clearfix">
<div class="label"><label>����� ����������:</label></div>
<div class="input"><input type="text" name="land" value="{land}" placeholder="����� ����������" /></div>
</div>

<div class="sep-textarea">
<div class="textarea-title">������ ������������ �������������:</div>
{ignore-list}
</div>

<div class="sep-input clearfix">
<div class="label"><label>������ ������:</label></div>
<div class="input"><input type="password" name="altpass" placeholder="������ ������" /></div>
</div>

<div class="sep-input clearfix">
<div class="label"><label>����� ������:</label></div>
<div class="input"><input type="password" name="password1" placeholder="����� ������" /></div>
</div>

<div class="sep-input clearfix">
<div class="label"><label>��������� ������:</label></div>
<div class="input"><input type="password" name="password2" placeholder="��������� ����� ������" /></div>
</div>

<div class="sep-textarea">
<div class="textarea-title">���������� �� IP (��� IP: {ip}):</div>
<div><textarea name="allowed_ip" style="height: 160px" rows="5" class="f_textarea" >{allowed-ip}</textarea></div>
<div style="margin-top: 10px">
					<span class="small" style="color:red;">
					* ��������! ������ ��������� ��� ��������� ������ ���������.
					������ � ������ �������� ����� �������� ������ � ���� IP-������ ��� �������, ������� �� �������.
					�� ������ ������� ��������� IP �������, �� ������ ������ �� ������ �������.
					<br />
					������: 192.48.25.71 ��� 129.42.*.*</span>
</div>
</div>

<div class="sep-input clearfix">
<div class="label"><label>������:</label></div>
<div class="input"><input type="file" name="image" size="28" /></div>
</div>

<div class="sep-input clearfix">
<div class="label"><label>������ <a href="http://www.gravatar.com/" target="_blank">Gravatar</a>:</label></div>
<div class="input"><input type="text" name="gravatar" value="{gravatar}" placeholder="������� E-Mail � ���� �������" /></div>
</div>

<div class="sep-checks"><input type="checkbox" name="del_foto" id="del_foto" value="yes" /> <label for="del_foto">������� ������</label></div>

<div class="sep-input clearfix">
<div class="label"><label>������� ����:</label></div>
<div class="input">{timezones}</div>
</div>

<div class="sep-textarea">
<div class="textarea-title">� ����:</div>
<div><textarea name="info" rows="5" class="f_textarea">{editinfo}</textarea></div>
</div>

<div class="sep-textarea">
<div class="textarea-title">�������:</div>
<div><textarea name="signature" rows="5" class="f_textarea">{editsignature}</textarea></div>
</div>

<div class="sep-xfield">
<div><table class="tableform">{xfields}</table></div>
</div>

<div class="sep-submit">
<button name="submit" class="fbutton" type="submit"><span>���������</span></button>
<input name="submit" type="hidden" id="submit" value="submit" />
</div>
			
</div>