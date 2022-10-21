{if isset($pages) and ($pages != null and count($pages) > 1)}
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            {foreach from=$pages item=page}
                <li class="page-item{if $page->isCurrent()} active{/if}">
                    <a href="{$page->getLink()}" class="page-link ajax-page">
                        {if $page->isFirst()}
                            {t}First Page{/t}
                        {elseif $page->isLast()}
                            {t}Last Page{/t}
                        {elseif $page->isNext()}
                            {t}Next Page{/t}
                            {if $activeLanguage->isRTL()}<i class="fa fa-angle-left"></i>{else}<i class="fa fa-angle-right"></i>{/if}
                        {elseif $page->isPrevious()}
                            {if $activeLanguage->isRTL()}<i class="fa fa-angle-right"></i>{else}<i class="fa fa-angle-left"></i>{/if}
                            {t}Previous Page{/t}
                        {else}
                            {$page->getTitle()}
                        {/if}
                    </a>
                </li>
            {/foreach}
        </ul>
    </nav>
{/if}