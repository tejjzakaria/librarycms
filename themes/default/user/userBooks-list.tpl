<div class="heading-elements">
    <select name="sortColumn" id="books-sort" class="form-control custom-select p-r-10" autocomplete="off">
        <option value="Issues.issueDate" data-order="DESC"{if $smarty.session.issuedBookSortingOrder == 'DESC' and $smarty.session.issuedBookSortingColumn == 'Issues.issueDate'} selected{/if}>{t}Issue Date Descending{/t}</option>
        <option value="Issues.issueDate" data-order="ASC"{if $smarty.session.issuedBookSortingOrder == 'ASC' and $smarty.session.issuedBookSortingColumn == 'Issues.issueDate'} selected{/if}>{t}Issue Date Ascending{/t}</option>
        <option value="Issues.expiryDate" data-order="DESC"{if $smarty.session.issuedBookSortingOrder == 'DESC' and $smarty.session.issuedBookSortingColumn == 'Issues.expiryDate'} selected{/if}>{t}Expiry Date Descending{/t}</option>
        <option value="Issues.expiryDate" data-order="ASC"{if $smarty.session.issuedBookSortingOrder == 'ASC' and $smarty.session.issuedBookSortingColumn == 'Issues.expiryDate'} selected{/if}>{t}Expiry Date Ascending{/t}</option>
        <option value="Books.title" data-order="DESC"{if $smarty.session.bookSortingOrder == 'DESC' and $smarty.session.bookSortingColumn == 'Books.title'} selected{/if}>{t}Title Descending{/t}</option>
        <option value="Books.title" data-order="ASC"{if $smarty.session.bookSortingOrder == 'ASC' and $smarty.session.bookSortingColumn == 'Books.title'} selected{/if}>{t}Title Ascending{/t}</option>
        <option value="Books.publishingYear" data-order="DESC"{if $smarty.session.bookSortingOrder == 'DESC' and $smarty.session.bookSortingColumn == 'Books.publishingYear'} selected{/if}>{t}Year Descending{/t}</option>
        <option value="Books.publishingYear" data-order="ASC"{if $smarty.session.bookSortingOrder == 'ASC' and $smarty.session.bookSortingColumn == 'Books.publishingYear'} selected{/if}>{t}Year Ascending{/t}</option>
    </select>
    <select name="perPage" id="countPerPage" class="form-control custom-select" autocomplete="off">
        {foreach from=$siteViewOptions->getOption("booksPerPageAdmin")->getListValues() key=key item=value}
            <option value="{$key}"{if ($smarty.session.bookPerPage == null and strcmp($key,$siteViewOptions->getOption("booksPerPageAdmin")->getValue()) === 0) or strcmp($key,$smarty.session.bookPerPage) === 0} selected{/if}>{t count=$value 1=$value plural="%1 Books"}1 Book{/t}</option>
        {/foreach}
    </select>
</div>
<div class="table-responsive-sm">
    <table class="table table-striped table-hover user-books">
        <thead>
            <tr>
                <th style="width: 80px;">{t}Book{/t}</th>
                <th></th>
                <th style="width: 140px;" class="text-center">{t}Issue Date{/t}</th>
                <th style="width: 140px;" class="text-center">{t}Expiry Date{/t}</th>
                <th style="width: 160px;" class="text-center">{t}Fine / Penalty{/t} ({$siteViewOptions->getOptionValue("priceCurrency")})</th>
                {if $siteViewOptions->getOptionValue("enableBookIssueByUser")}<th style="width: 90px;" class="text-center">{t}Actions{/t}</th>{/if}
            </tr>
        </thead>
        <tbody>
            {if isset($books) and $books != null}
                {foreach from=$books item=book name=book}
                    <tr {if $book->getIssue()->isLost()}class="book-lost"{/if}>
                        <td class="book-cover">
                            {if $book->getCover()}
                                <a href="{if $siteViewOptions->getOptionValue("bookUrlType")}{$routes->getRouteString("bookViewPublic",["bookId"=>$book->getId()])}{else}{$routes->getRouteString("bookViewViaUrlPublic",["bookUrl"=>$book->getUrl()])}{/if}"><img src="{$book->getCover()->getWebPath('small')}" alt="{$book->getTitle()}" class="img-fluid"></a>
                            {else}
                                <a href="{if $siteViewOptions->getOptionValue("bookUrlType")}{$routes->getRouteString("bookViewPublic",["bookId"=>$book->getId()])}{else}{$routes->getRouteString("bookViewViaUrlPublic",["bookUrl"=>$book->getUrl()])}{/if}"><img src="{$siteViewOptions->getOptionValue("noBookImageFilePath")}" alt="{$book->getTitle()}" class="img-fluid"></a>
                            {/if}
                        </td>
                        <td>
                            <a href="{if $siteViewOptions->getOptionValue("bookUrlType")}{$routes->getRouteString("bookViewPublic",["bookId"=>$book->getId()])}{else}{$routes->getRouteString("bookViewViaUrlPublic",["bookUrl"=>$book->getUrl()])}{/if}">{$book->getTitle()}</a>
                            {if $book->getPublishingYear() != null}
                                <span class="text-muted ml-1">({$book->getPublishingYear()})</span>
                            {/if}
                            {if $book->getPublisher() != null}
                                <div class="book-list-info">
                                    <strong class="text-uppercase">{t}Publisher{/t}:</strong>
                                    {$book->getPublisher()->getName()}
                                </div>
                            {/if}
                            {if $book->getGenres() !== null and is_array($book->getGenres()) and count($book->getGenres()) > 0}
                                <div class="book-list-info">
                                    <strong class="text-uppercase">{t}Genre{/t}:</strong>
                                    {foreach from=$book->getGenres() item=genre name=genre}
                                        {$genre->getName()}{if $smarty.foreach.genre.last}{else},{/if}
                                    {/foreach}
                                </div>
                            {/if}
                            {if $book->getAuthors() !== null and is_array($book->getAuthors()) and count($book->getAuthors()) > 0}
                                <div class="book-list-info">
                                    <strong class="text-uppercase">{t}Author{/t}:</strong>
                                    {foreach from=$book->getAuthors() item=author name=author}
                                        {$author->getLastName()} {$author->getFirstName()}{if $smarty.foreach.author.last}{else},{/if}
                                    {/foreach}
                                </div>
                            {/if}
                            {if $book->getEBookId() != null}
                                <div class="book-list-info">
                                    <strong class="text-uppercase">{t}eBook{/t}:</strong>
                                    {if $siteViewOptions->getOptionValue("showDownloadLink")}
                                    <a href="{$routes->getRouteString("electronicBookGet",["electronicBookId"=>$book->getEBookId()])}" class="ml-1"><i class="far fa-hdd"></i> {t}Download{/t}</a>
                                    {/if}
                                    <a href="{$routes->getRouteString("electronicBookView",["bookId"=>$book->getId()])}" class="ml-1"><i class="fas fa-glasses"></i> {t}Read{/t}</a>
                                </div>
                            {/if}
                        </td>
                        <td class="text-center">
                            {$book->getIssue()->getIssueDate()|date_format:$siteViewOptions->getOptionValue("dateFormat")}
                        </td>
                        <td class="text-center">
                            {$book->getIssue()->getExpiryDate()|date_format:$siteViewOptions->getOptionValue("dateFormat")}
                        </td>
                        <td class="text-center">
                            {$book->getIssue()->getPenalty()}
                        </td>
                        {if $siteViewOptions->getOptionValue("enableBookIssueByUser")}
                            <td class="text-center action-btn">
                                {if $book->getIssue()->isLost()}
                                    <span class="badge badge-danger">{t}lost{/t}</span>
                                {else}
                                    <a href="{$routes->getRouteString("bookSetReturnedPublic",["issueId"=>$book->getIssue()->getId()])}" class="btn btn-outline-info btn-sm no-border return-book" data-bookId="{$book->getId()}" data-container="body" data-toggle="tooltip" title="{t}Return Book{/t}"><i class="fas fa-undo-alt"></i></a>
                                    <div class="dropdown d-inline" data-container="body" data-toggle="tooltip" title="{t}Book Is Lost{/t}">
                                        <button class="btn btn-outline-info btn-sm no-border" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <ul class="dropdown-menu delete-dropdown {if $activeLanguage->isRTL()}dropdown-menu-left{else}dropdown-menu-right{/if}">
                                            <li class="text-center">{t}Do you really lost this book?{/t}</li>
                                            <li class="divider"></li>
                                            <li class="text-center">
                                                <a class="btn btn-outline-danger book-is-lost" href="{$routes->getRouteString("bookSetLostPublic",["issueId"=>$book->getIssue()->getId()])}">{t}Book Is Lost{/t}</a>
                                            </li>
                                        </ul>
                                    </div>
                                {/if}
                            </td>
                        {/if}
                    </tr>
                {/foreach}
            {else}
                <tr>
                    <td colspan="5" class="text-center">{t}You don't have any books yet{/t}</td>
                </tr>
            {/if}
        </tbody>
    </table>
</div>
{include "general/pagination.tpl"}