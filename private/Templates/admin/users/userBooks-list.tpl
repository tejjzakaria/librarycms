<div class="table-responsive-sm">
    <table class="table table-striped table-hover user-books">
        <thead>
        <tr>
            <th style="width: 80px;">{t}Book{/t}</th>
            <th></th>
            <th style="width: 140px;" class="text-center">{t}Issue Date{/t}</th>
            <th style="width: 140px;" class="text-center">{t}Expiry Date{/t}</th>
            <th style="width: 160px;" class="text-center">{t}Fine / Penalty{/t} ({$siteViewOptions->getOptionValue("priceCurrency")})</th>
            <th style="width: 110px;" class="text-center">{t}Status{/t}</th>
            <th style="width: 180px;" class="text-center">{t}Actions{/t}</th>
        </tr>
        </thead>
        <tbody>
        {if isset($books) and $books != null}
            {foreach from=$books item=book name=book}
                <tr {if $book->getIssue()->isLost()}class="book-lost"{/if}>
                    <td class="book-cover">
                        {if $book->getCover()}
                            <a href="{$routes->getRouteString("bookEdit",["bookId"=>$book->getId()])}"><img src="{$book->getCover()->getWebPath('small')}" alt="{$book->getTitle()}" class="img-fluid"></a>
                        {else}
                            <a href="{$routes->getRouteString("bookEdit",["bookId"=>$book->getId()])}"><img src="{$siteViewOptions->getOptionValue("noBookImageFilePath")}" alt="{$book->getTitle()}" class="img-fluid"></a>
                        {/if}
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
                    <td class="book-status text-center">
                        <span class="badge {if $book->getIssue()->isLost()}badge-danger{elseif $book->getIssue()->getReturnDate()}badge-success{/if}">
                             {if $book->getIssue()->isLost()}
                                 {t}lost{/t}
                             {elseif $book->getIssue()->getReturnDate()}
                                 {t}returned{/t}
                             {/if}
                        </span>
                    </td>
                    <td class="text-center">
                        {if $book->getIssue()->getReturnDate() == null}
                            <a href="{$routes->getRouteString("bookSetReturned",["issueId"=>$book->getIssue()->getId()])}" class="btn btn-outline-info btn-sm no-border return-book" data-container="body" data-toggle="tooltip" title="{t}Return Book{/t}"><i class="fas fa-undo-alt"></i></a>
                        {/if}
                        <div class="dropdown d-inline" data-container="body" data-toggle="tooltip" title="{t}Notify Delayed Members{/t}">
                            <button class="btn btn-outline-info btn-sm no-border" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="far fa-bell"></i>
                            </button>
                            <div class="dropdown-menu text-center dropdown-menu-right">
                                <a class="dropdown-item" href="#" data-target="#userSendEmailModal" data-book="{$book->getId()}" data-user="{if $book->getIssue()->getUser()}{$book->getIssue()->getUser()->getId()}{/if}" data-toggle="modal">{t}Email{/t}</a>
                                <a class="dropdown-item" href="#" data-target="#userSendSMSModal" data-book="{$book->getId()}" data-user="{if $book->getIssue()->getUser()}{$book->getIssue()->getUser()->getId()}{/if}" data-toggle="modal">{t}SMS{/t}</a>
                            </div>
                        </div>

                        <a href="{$routes->getRouteString("issueEdit",["issueId"=>$book->getIssue()->getId()])}" class="btn btn-outline-info btn-sm no-border" data-container="body" data-toggle="tooltip" title="{t}Edit{/t}"><i class="fas fa-pencil-alt"></i></a>

                        <a href="{$routes->getRouteString("bookSetLost",["issueId"=>$book->getIssue()->getId()])}" class="btn btn-outline-info btn-sm no-border lost-book" data-lost="{if $book->getIssue()->isLost()}true{else}false{/if}" data-container="body" data-toggle="tooltip" title="{if $book->getIssue()->isLost()}{t}Book Not Lost{/t}{else}{t}Book Is Lost{/t}{/if}"><i class="fa fa-times"></i></a>

                        <div class="dropdown d-inline" data-trigger="hover" data-toggle="tooltip" title="{t}Delete{/t}">
                            <button class="btn btn-outline-info btn-sm no-border" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="far fa-trash-alt"></i>
                            </button>
                            <ul class="dropdown-menu delete-dropdown dropdown-menu-right">
                                <li class="text-center">{t}Do you really want to delete?{/t}</li>
                                <li class="divider"></li>
                                <li class="text-center">
                                    <button class="btn btn-outline-danger delete-issue" data-url="{$routes->getRouteString("issueDelete",["issueId"=>$book->getIssue()->getId()])}">
                                        <span class="btn-icon"><i class="far fa-trash-alt"></i></span> {t}Delete{/t}
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            {/foreach}
        {else}
            <tr>
                <td colspan="7" class="text-center">{t}You don't have any books yet{/t}</td>
            </tr>
        {/if}
        </tbody>
    </table>
</div>
{include "admin/general/pagination.tpl"}