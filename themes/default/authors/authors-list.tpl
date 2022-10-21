<div class="top-filter row">
    <div class="col-lg-8 text">
        {t escape=no}Found <span>{/t}{$totalAuthors} {t escape=no}authors</span> in total{/t}
    </div>
    <div class="col-lg-4 pr-0 pl-0">
        <select name="sortColumn" id="books-sort" class="custom-select">
            <option value="Authors.lastName" data-order="DESC"{if $smarty.session.authorSortingOrderPublic == 'DESC' and $smarty.session.authorSortingColumnPublic == 'Authors.lastName'} selected{/if}>{t}Name Descending{/t}</option>
            <option value="Authors.lastName" data-order="ASC"{if $smarty.session.authorSortingOrderPublic == 'ASC' and $smarty.session.authorSortingColumnPublic == 'Authors.lastName'} selected{/if}>{t}Name Ascending{/t}</option>
        </select>
    </div>
</div>
<div class="row">
    {if isset($authors) and $authors != null}
        {foreach from=$authors item=author name=author}
            <div class="col-sm-4 col-md-3 col-lg-2 col-6">
                <div class="author">
                    {if $author->getPhoto() != null}
                        <div class="author-photo">
                            <a href="{$routes->getRouteString("authorBooksPublic",["authorId"=>$author->getId()])}" class="text-center"><img src="{$author->getPhoto()->getWebPath('medium')}" alt="{$author->getLastName()} {$author->getFirstName()}"></a>
                        </div>
                    {else}
                        <div class="author-photo">
                            <a href="{$routes->getRouteString("authorBooksPublic",["authorId"=>$author->getId()])}" class="text-center"><img src="{$siteViewOptions->getOptionValue("noBookImageFilePath")}" alt="{$author->getLastName()} {$author->getFirstName()}"></a>
                        </div>
                    {/if}
                    <div class="author-info">
                        <h4 class="author-name">
                            <a href="{$routes->getRouteString("authorBooksPublic",["authorId"=>$author->getId()])}">{$author->getLastName()} {$author->getFirstName()}</a>
                        </h4>
                        <span class="author-books">{$author->getBookCount()} {t}books{/t}</span>
                    </div>
                </div>
            </div>
        {/foreach}
    {/if}
</div>
<div class="books-per-page d-flex">
    {include "general/pagination.tpl"}
</div>
<div class="top-filter row">
    <div class="col-lg-8 text">
        <p class="m-0">{t}Authors per page:{/t}</p>
    </div>
    <div class="col-lg-4 pr-0 pl-0">
        <select name="perPage" id="countPerPage" class="custom-select">
            {foreach from=$siteViewOptions->getOption("authorsPerPagePublic")->getListValues() key=key item=value}
                <option value="{$key}"{if ($smarty.session.authorPerPagePublic == null and strcmp($key,$siteViewOptions->getOption("authorsPerPagePublic")->getValue()) === 0) or strcmp($key,$smarty.session.authorPerPagePublic) === 0} selected{/if}>{t count=$value 1=$value plural="%1 Authors"}1 Author{/t}</option>
            {/foreach}
        </select>
    </div>
</div>