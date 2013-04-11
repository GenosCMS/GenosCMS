<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="content-type" content="text/html" charset="utf-8" />
	<title>{title}</title>
    {meta}
    {style}
</head>

<body>
    <div class="header"></div>
    <div class="container">
        <div id="installer">
            <ul class="nav nav-tabs">
                {foreach from=$steps item=step}
                <li class="step{if $step.is_active} active{/if}"><a href="index.php?action={$step.step}"><i class="icon-{$step.icon}"></i> {$step.name}</a></li>
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