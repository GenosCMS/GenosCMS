            <div class="navbar">
                <div class="navbar-inner">
                    <div class="container">
                        <ul class="nav">
                            {foreach from=$menus item=menu}
                            <li{if isset($menu.children) && count($menu.children)} class="dropdown"{/if}>
                                <a href="{url link=$menu.url}"{if isset($menu.children) && count($menu.children)} class="dropdown-toggle" data-toggle="dropdown"{/if}>{phrase var=$menu.module'.'$menu.var_name}</a>
                                {if isset($menu.children) && count($menu.children)}
                                <ul class="dropdown-menu">
                                    {foreach from=$menu.children item=child}
                                    <li><a href="{url link=$child.url}">{phrase var=$child.module'.'$child.var_name}</a></li>
                                    {/foreach}
                                </ul>
                                {/if}
                            </li>
                            {/foreach}
                            {unset var=$menus var1=$menu}
                        </ul>
                    </div>
                </div>
            </div>