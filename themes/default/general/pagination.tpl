{if isset($pages) and ($pages != null and count($pages) > 1)}
    <nav class="m-auto">
        <ul class="pagination">
            {foreach from=$pages item=page}
                <li class="page-item {if $page->isCurrent()} active{/if}">
                    <a href="{$page->getLink()}" class="page-link ajax-page">
                        {if $page->isFirst()}
                            {t}First Page{/t}
                        {elseif $page->isLast()}
                            {t}Last Page{/t}
                        {elseif $page->isNext()}
                            {if $activeLanguage->isRTL()}<i class="fa fa-angle-left"></i>{else}<i class="fa fa-angle-right"></i>{/if}
                        {elseif $page->isPrevious()}
                            {if $activeLanguage->isRTL()}<i class="fa fa-angle-right"></i>{else}<i class="fa fa-angle-left"></i>{/if}
                        {else}
                            {$page->getTitle()}
                        {/if}
                    </a>
                </li>
            {/foreach}
        </ul>
    </nav>
{/if}