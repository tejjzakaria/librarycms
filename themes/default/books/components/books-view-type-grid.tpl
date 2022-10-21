<div class="top-filter row">
    <div class="col-lg-7 text">
        {t escape=no}Found <span>{/t}{$totalBooks} {t escape=no}books</span> in total{/t}
    </div>
    <div class="col-lg-5 pl-0 pr-0">
        <div class="input-group">
            <select name="sortColumn" id="books-sort" class="custom-select">
                <option value="Books.creationDateTime" data-order="DESC"{if $smarty.session.bookSortingOrderPublic == 'DESC' and $smarty.session.bookSortingColumnPublic == 'Books.creationDateTime'} selected{/if}>{t}Date Descending{/t}</option>
                <option value="Books.creationDateTime" data-order="ASC"{if $smarty.session.bookSortingOrderPublic == 'ASC' and $smarty.session.bookSortingColumnPublic == 'Books.creationDateTime'} selected{/if}>{t}Date Ascending{/t}</option>
                <option value="Books.title" data-order="DESC"{if $smarty.session.bookSortingOrderPublic == 'DESC' and $smarty.session.bookSortingColumnPublic == 'Books.title'} selected{/if}>{t}Title Descending{/t}</option>
                <option value="Books.title" data-order="ASC"{if $smarty.session.bookSortingOrderPublic == 'ASC' and $smarty.session.bookSortingColumnPublic == 'Books.title'} selected{/if}>{t}Title Ascending{/t}</option>
                <option value="Books.publishingYear" data-order="DESC"{if $smarty.session.bookSortingOrderPublic == 'DESC' and $smarty.session.bookSortingColumnPublic == 'Books.publishingYear'} selected{/if}>{t}Year Descending{/t}</option>
                <option value="Books.publishingYear" data-order="ASC"{if $smarty.session.bookSortingOrderPublic == 'ASC' and $smarty.session.bookSortingColumnPublic == 'Books.publishingYear'} selected{/if}>{t}Year Ascending{/t}</option>
            </select>
            <div class="input-group-append layout-settings">
                <a class="view-type type-grid{if $smarty.session.bookViewType == 'grid'} active{/if}" data-type="grid"></a>
                <a class="view-type type-list{if $smarty.session.bookViewType == 'list'} active{/if}" data-type="list"></a>
                <a class="view-type type-mini-list{if $smarty.session.bookViewType == 'miniList'} active{/if}" data-type="miniList"></a>
            </div>
        </div>
    </div>
</div>
<div class="row book-grid">
    {if isset($books) and $books != null}
        {foreach from=$books item=book name=book}
            <div class="col-lg-6 col-md-6">
                <div class="row book" itemscope itemtype="http://schema.org/Book">
                    {if $book->getISBN13() != null}
                        <meta itemprop="isbn" content="{$book->getISBN13()}"/>
                    {elseif $book->getISBN10() != null}
                        <meta itemprop="isbn" content="{$book->getISBN10()}"/>
                    {/if}
                    <meta itemprop="name" content="{$book->getTitle()}"/>
                    {if $book->getPublishingYear() != null}
                        <meta itemprop="datePublished" content="{$book->getPublishingYear()}"/>
                    {/if}
                    <div class="book-cover col-lg-3 col-3">
                        {if $book->getCover()}
                            <a href="{if $siteViewOptions->getOptionValue("bookUrlType")}{$routes->getRouteString("bookViewPublic",["bookId"=>$book->getId()])}{else}{$routes->getRouteString("bookViewViaUrlPublic",["bookUrl"=>$book->getUrl()])}{/if}"><img src="{$book->getCover()->getWebPath('small')}" alt="{$book->getTitle()}" class="img-fluid"></a>
                        {else}
                            <a href="{if $siteViewOptions->getOptionValue("bookUrlType")}{$routes->getRouteString("bookViewPublic",["bookId"=>$book->getId()])}{else}{$routes->getRouteString("bookViewViaUrlPublic",["bookUrl"=>$book->getUrl()])}{/if}"><img src="{$siteViewOptions->getOptionValue("noBookImageFilePath")}" alt="{$book->getTitle()}" class="img-fluid"></a>
                        {/if}
                    </div>
                    <div class="book-info col-lg-9 col-9">
                        <div class="book-title">
                            <a href="{if $siteViewOptions->getOptionValue("bookUrlType")}{$routes->getRouteString("bookViewPublic",["bookId"=>$book->getId()])}{else}{$routes->getRouteString("bookViewViaUrlPublic",["bookUrl"=>$book->getUrl()])}{/if}">{$book->getTitle()}</a>
                        </div>
                        <div class="book-attr">
                            {if $book->getPublishingYear() != null}
                                <span class="book-publishing-year">{$book->getPublishingYear()}{if $book->getAuthors() !== null}, {/if}</span>{/if}
                            {if $book->getAuthors() !== null and is_array($book->getAuthors()) and count($book->getAuthors()) > 0}
                                {assign var="authors" value=$book->getAuthors()}
                                <span class="book-author" itemprop="author">{$authors[0]->getLastName()}{$authors[0]->getFirstName()}</span>
                            {/if}
                        </div>
                        <div class="book-rating">
                            {include 'books/components/rating.tpl' rating=$book->getRating() readOnly=true}
                            {*<div class="whole-rating d-inline-block">
                                <span>{$book->getBookRatingVotesNumber()}</span> {t}Votes{/t}
                            </div>*}
                        </div>
                        {if $book->getDescription()}
                            <div class="book-short-description">{$book->getDescription()|strip_tags:true|strip|truncate:90}</div>
                        {/if}

                        {if $siteViewOptions->getOptionValue("enableBookRequest")}
                            <div class="book-settings">
                                <div class="dropdown">
                                    <a href="#" role="button"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="icon-options-vertical"></i>
                                    </a>
                                    <div class="dropdown-menu {if $activeLanguage->isRTL()}dropdown-menu-left{else}dropdown-menu-right{/if}">
                                        {if $siteViewOptions->getOptionValue("enableBookIssueByUser")}
                                            <a class="dropdown-item issue-book" data-id="{$book->getId()}" data-url="{$routes->getRouteString("issueCreatePublic")}" href="#"><i class="ti-book"></i> {t}Issue Book{/t}</a>
                                        {else}
                                            <a class="dropdown-item request-book" data-id="{$book->getId()}" data-url="{$routes->getRouteString("requestCreate")}" href="#"><i class="ti-book"></i> {t}Request Book{/t}</a>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                        {/if}
                    </div>
                </div>
            </div>
        {/foreach}
    {/if}
</div>
<div class="books-per-page d-flex">
    {include "general/pagination.tpl"}
</div>
{block name=perPageFilter}
    <div class="top-filter row">
        <div class="col-lg-8 text">
            {t}Books per page:{/t}
        </div>
        <div class="col-lg-4 pr-0 pl-0">
            <select name="perPage" id="countPerPage" class="custom-select">
                {foreach from=$siteViewOptions->getOption("booksPerPagePublicFilter")->getListValues() key=key item=value}
                    <option value="{$key}"{if ($smarty.session.bookPerPageFilterPublic == null and strcmp($key,$siteViewOptions->getOption("booksPerPagePublicFilter")->getValue()) === 0) or strcmp($key,$smarty.session.bookPerPageFilterPublic) === 0} selected{/if}>{t count=$value 1=$value plural="%1 Books"}1 Book{/t}</option>
                {/foreach}
            </select>
        </div>
    </div>
{/block}