<div class="card-header">
    <div class="row">
        <div class="col-lg-12">
            <div class="heading-elements">
                <select name="sortColumn" id="books-sort" class="select-picker pr-2 d-tc" autocomplete="off">
                    <option value="Books.creationDateTime" data-order="DESC"{if $smarty.session.bookSortingOrder == 'DESC' and $smarty.session.bookSortingColumn == 'Books.creationDateTime'} selected{/if}>{t}Date Descending{/t}</option>
                    <option value="Books.creationDateTime" data-order="ASC"{if $smarty.session.bookSortingOrder == 'ASC' and $smarty.session.bookSortingColumn == 'Books.creationDateTime'} selected{/if}>{t}Date Ascending{/t}</option>
                    <option value="Books.title" data-order="DESC"{if $smarty.session.bookSortingOrder == 'DESC' and $smarty.session.bookSortingColumn == 'Books.title'} selected{/if}>{t}Title Descending{/t}</option>
                    <option value="Books.title" data-order="ASC"{if $smarty.session.bookSortingOrder == 'ASC' and $smarty.session.bookSortingColumn == 'Books.title'} selected{/if}>{t}Title Ascending{/t}</option>
                    <option value="Books.publishingYear" data-order="DESC"{if $smarty.session.bookSortingOrder == 'DESC' and $smarty.session.bookSortingColumn == 'Books.publishingYear'} selected{/if}>{t}Year Descending{/t}</option>
                    <option value="Books.publishingYear" data-order="ASC"{if $smarty.session.bookSortingOrder == 'ASC' and $smarty.session.bookSortingColumn == 'Books.publishingYear'} selected{/if}>{t}Year Ascending{/t}</option>
                </select>
                <select name="perPage" id="countPerPage" class="select-picker d-tc" autocomplete="off">
                    {foreach from=$siteViewOptions->getOption("booksPerPageAdmin")->getListValues() key=key item=value}
                        <option value="{$key}"{if ($smarty.session.bookPerPage == null and strcmp($key,$siteViewOptions->getOption("booksPerPageAdmin")->getValue()) === 0) or strcmp($key,$smarty.session.bookPerPage) === 0} selected{/if}>{t count=$value 1=$value plural="%1 Books"}1 Book{/t}</option>
                    {/foreach}
                </select>
            </div>
        </div>
    </div>
</div>
<div class="table-responsive-sm">
<table class="table table-striped table-bordered">
    <thead class="table-vertical-header" style="height: 56px;">
        <tr>
            <th style="width: 40px;">
                <div class="app-checkbox small-size m-0 inline" data-container="body" data-toggle="tooltip" title="{t}Select All{/t}">
                    <label class="pl-0">
                        <input type="checkbox" name="ids[]" class="books_ids" id="select-all-books">
                    </label>
                </div>
            </th>
            <th class="d-none header-bulk-actions">
                <div class="btn-group" role="group">
                    <button class="btn btn-danger btn-sm" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="far fa-trash-alt"></i> {t}Delete Books{/t}
                    </button>
                    <ul class="dropdown-menu text-initial text-regular">
                        <li class="text-center">{t}Do you really want to delete?{/t}</li>
                        <li class="divider"></li>
                        <li class="text-center">
                            <button class="btn btn-outline-secondary btn-sm delete-books">
                                <i class="far fa-trash-alt"></i> {t}Delete{/t}
                            </button>
                        </li>
                    </ul>
                    {*<button class="btn btn-success btn-sm"><i class="fas fa-pencil-alt"></i> {t}Edit Books{/t}</button>*}
                </div>
            </th>
            <th class="header-cell">{t}Book{/t}</th>
            <th style="width: 130px;" class="text-center"><span class="header-cell">{t}Store{/t}</span></th>
            <th style="width: 150px;" class="text-center"><span class="header-cell">{t}ISBN (10/13){/t}</span></th>
            <th style="width: 140px;" class="text-center"><span class="header-cell">{t}Quantity{/t}</span></th>
            <th style="width: 120px;" class="text-center"><span class="header-cell">{t}Issued{/t}</span></th>
            <th style="width: 130px;" class="text-center"><span class="header-cell">{t}Actions{/t}</span></th>
        </tr>
    </thead>
    <tbody>
        {if isset($books) and $books != null}
            {foreach from=$books item=book name=book}
                <tr>
                    <td>
                        <div class="app-checkbox small-size">
                            <label class="pl-0">
                                <input type="checkbox" name="bookIds[]" class="books-id" value="{$book->getId()}">
                            </label>
                        </div>
                    </td>
                    <td>
                        <a href="{$routes->getRouteString("bookEdit",["bookId"=>$book->getId()])}">{$book->getTitle()}</a>
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
                                <a href="{$routes->getRouteString("electronicBookGet",["electronicBookId"=>$book->getEBookId()])}" class="ml-1"><i class="fa fa-download" aria-hidden="true"></i> {t}Download{/t}</a>
                                <a href="{$routes->getRouteString("electronicBookViewAdmin",["bookId"=>$book->getId()])}" class="ml-1"><i class="fa fa-eye" aria-hidden="true"></i> {t}Read{/t}
                                </a>
                            </div>
                        {/if}
                    </td>
                    <td class="text-center">
                        {if $book !== null and $book->getStores() !== null and is_array($book->getStores())}
                            {foreach from=$book->getStores() item=store name=store}
                                <a href="{$routes->getRouteString("storeBooksView",["storeId"=>$store->getId()])}">{$store->getName()}</a>{if $smarty.foreach.store.last}{else},{/if}
                            {/foreach}
                        {/if}
                    </td>
                    <td class="text-center">
                        {if $book->getISBN13() != null}
                            {$book->getISBN13()}
                        {else}
                            {$book->getISBN10()}
                        {/if}
                    </td>
                    <td class="text-center">{$book->getQuantity()}</td>
                    <td class="text-center">{$book->getIssuedQuantity()}</td>
                    <td class="text-center">
                        <a href="{$routes->getRouteString("bookEdit",["bookId"=>$book->getId()])}" class="btn btn-outline-info no-border btn-sm mr-1" data-container="body" data-toggle="tooltip" title="{t}Edit{/t}"><i class="fas fa-pencil-alt"></i></a>
                        <a href="{$routes->getRouteString("bookClone",["bookId"=>$book->getId()])}" class="btn btn-outline-info no-border btn-sm mr-1" data-container="body" data-toggle="tooltip" title="{t}Clone{/t}"><i class="far fa-clone"></i></a>
                        <div class="dropdown d-inline" data-trigger="hover" data-toggle="tooltip" title="{t}Delete{/t}">
                            <button class="btn btn-outline-info no-border btn-sm" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="far fa-trash-alt"></i>
                            </button>
                            <ul class="dropdown-menu delete-dropdown dropdown-menu-right">
                                <li class="text-center">{t}Do you really want to delete?{/t}</li>
                                <li class="divider"></li>
                                <li class="text-center">
                                    <button class="btn btn-outline-danger delete-book" data-url="{$routes->getRouteString("bookDelete",["bookId"=>$book->getId()])}">
                                        <span class="btn-icon"><i class="far fa-trash-alt"></i></span> {t}Delete{/t}
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            {/foreach}
        {/if}
    </tbody>
</table>
    </div>
{include "admin/general/pagination.tpl"}