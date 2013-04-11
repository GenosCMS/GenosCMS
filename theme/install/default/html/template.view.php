<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="content-type" content="text/html" charset="utf-8" />
	<title>{title}</title>
    {meta}
    {style}
</head>

<body>
    <div class="header">
        {img src='logo.png'}
        <hr />
        <h5>
            <a href="#">Genos CMS <sup>&reg;</sup></a>
            es software libre liberado bajo la
            <a href="#">GNU General Public License</a>.
        </h5>
    </div>
    <div class="container">
        <div id="installer">
            <ul class="nav nav-tabs">
                {foreach from=$steps item=step}
                <li class="step{if $step.is_active} active{/if}">{if $step.is_active}<a href="#"><span class="badge">{$step.count}</span> {$step.name}</a>{else}<span><span class="badge">{$step.count}</span> {$step.name}</span>{/if}</li>
                {/foreach}
            </ul>
            {layout file=$template}
        </div>
        <div class="footer">
            {debug}
        </div>
    </div>
    {script}
</body>
</html>