[studios]
<div class="tabsms_title">{title} {season} сезон [not-ower]{series} серия[/not-ower][ower]все серии[/ower] в русской озвучке (<span id="studios">{studios}</span>) </div>
<div class="tabsms" style="width: 100%">
    <ul>
        {tabs-title}
    </ul>
    <div>
    {tabs-content}
    </div>
</div>
[/studios]

[not-studios]
[not-soon][not-error]<div class="tabsms_title">{title} {season} сезон [not-ower]{series} серия[/not-ower][ower]все серии[/ower]</div>[/not-error][/not-soon]
<div class="tabsms">
    {tabs-content}
    [not-soon][error]<img src="{THEME}/images/serialvkpost.jpg" width="100%"/>[/error][/not-soon]
    [soon]<img src="{THEME}/images/serialvkpost.jpg" width="100%"/>[/soon]
</div>
[/not-studios]
