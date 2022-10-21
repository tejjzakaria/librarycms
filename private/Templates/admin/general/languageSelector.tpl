{if isset($languages)}
    <a class="nav-link dropdown-toggle text-muted" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="icon-globe"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-right animated bounceIn">
        {foreach from=$languages item=language}
            {if $language->isActive()}
                <a href="{$routes->getRouteString("languageChange",["languageCode"=>$language->getCode()])}" class="dropdown-item {if strcmp($language->getCode(),$activeLanguage->getCode()) === 0}active{/if}"><i class="flag flag-{$language->getShortCode()}"></i>{$language->getName()}</a>
            {/if}
        {/foreach}
    </div>
{/if}