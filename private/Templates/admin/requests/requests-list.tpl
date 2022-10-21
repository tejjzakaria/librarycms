<div class="card-header">
    <div class="heading-elements">
        <select name="sortColumn" id="books-sort" class="select-picker {if $activeLanguage->isRTL()}pl-2{else}pr-2{/if} d-tc">
            <option value="Requests.creationDate" data-order="DESC"{if $smarty.session.requestSortingOrder == 'DESC' and $smarty.session.requestSortingColumn == 'Requests.creationDate'} selected{/if}>{t}Request Date Descending{/t}</option>
            <option value="Requests.creationDate" data-order="ASC"{if $smarty.session.requestSortingOrder == 'ASC' and $smarty.session.requestSortingColumn == 'Requests.creationDate'} selected{/if}>{t}Request Date Ascending{/t}</option>
        </select>
        <select name="perPage" id="countPerPage" class="select-picker d-tc">
            {foreach from=$siteViewOptions->getOption("requestsPerPage")->getListValues() key=key item=value}
                <option value="{$key}"{if ($smarty.session.requestPerPage == null and strcmp($key,$siteViewOptions->getOption("requestsPerPage")->getValue()) === 0) or strcmp($key,$smarty.session.requestPerPage) === 0} selected{/if}>{t count=$value 1=$value plural="%1 Requests"}1 Request{/t}</option>
            {/foreach}
        </select>
    </div>
</div>
<div class="table-responsive-sm">
    <table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>{t}Book{/t}</th>
            <th>{t}Request From{/t}</th>
            <th>{t}Creation Date{/t}</th>
            <th style="width: 110px;" class="text-center">{t}Status{/t}</th>
            <th style="width: 200px;" class="text-center">{t}Actions{/t}</th>
        </tr>
    </thead>
    <tbody>
        {if isset($requests) and $requests != null}
            {foreach from=$requests item=request name=request}
                <tr>
                    <td>
                        <a href="{$routes->getRouteString("bookEdit",["bookId"=>$request->getBook()->getId()])}">{$request->getBook()->getTitle()}</a>
                        {if $request->getNotes()}
                            <div class="book-list-info">
                                <strong class="text-uppercase">{t}Notes{/t}:</strong>
                                {$request->getNotes()}
                            </div>
                        {/if}
                        {*$request->getBook()->getBookCopies()|var_dump*}
                    </td>
                    <td>
                        {if $request->getUser()}
                            <a href="{$routes->getRouteString("userEdit",["userId"=>$request->getUser()->getId()])}">{$request->getUser()->getFirstName()} {$request->getUser()->getLastName()}</a>
                        {/if}
                    </td>
                    <td>
                        {$request->getCreationDate()}
                    </td>
                    <td class="request-status text-center">
                        <span class="badge {if $request->getStatus() == 'Pending'}badge-warning{elseif $request->getStatus() == 'Accepted'}badge-success{elseif $request->getStatus() == 'Rejected'}badge-danger{/if}">
                            {if $request->getStatus() == 'Pending'}
                                {t}Pending{/t}
                            {elseif $request->getStatus() == 'Accepted'}
                                {t}Accepted{/t}
                            {elseif $request->getStatus() == 'Rejected'}
                                {t}Rejected{/t}
                            {/if}
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="{$routes->getRouteString("requestSetStatus",["requestId"=>$request->getId(),"status"=>"Accepted"])}" class="btn btn-outline-info btn-sm no-border{if $activeLanguage->isRTL()} ml-1{else} mr-1{/if} accepted-book" data-container="body" data-toggle="tooltip" title="{t}Accepted{/t}"><i class="fa fa-check"></i></a>
                        <a href="{$routes->getRouteString("requestSetStatus",["requestId"=>$request->getId(),"status"=>"Rejected"])}" class="btn btn-outline-info btn-sm no-border{if $activeLanguage->isRTL()} ml-1{else} mr-1{/if} rejected-book" data-container="body" data-toggle="tooltip" title="{t}Rejected{/t}"><i class="fa fa-times"></i></a>
                        {if $siteViewOptions->getOptionValue("enableBookIssue") and $request->getBook() != null and !$request->getIssue()}
                            <div class="dropdown d-inline" data-container="body" data-toggle="tooltip" title="{t}Issue Book{/t}">
                                <button class="btn btn-outline-info btn-sm no-border" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-book"></i>
                                </button>
                                <div class="dropdown-menu book-copy-dropdown dropdown-menu-right">
                                    <div class="request-issue-book-block card text-center m-0">
                                        <div class="text-center">{t}Please select Book Copy{/t}</div>
                                        <div class="divider"></div>
                                        <div class="form-group">
                                            {if $request->getBook()->getBookCopies() != null and count($request->getBook()->getBookCopies()) > 0}
                                                <select name="bookCopyId" class="form-control select2-picker">
                                                    {foreach from=$request->getBook()->getBookCopies() item=bookCopy name=bookCopy}
                                                        {if $bookCopy->getIssueStatus() != 'ISSUED' and $bookCopy->getIssueStatus() != 'LOST'}
                                                            <option value="{$bookCopy->getId()}">{$bookCopy->getBookSN()}</option>
                                                        {/if}
                                                    {/foreach}
                                                </select>
                                            {/if}
                                        </div>
                                        <div class="text-center">
                                            <button class="btn btn-outline-info issue-book" data-url="{$routes->getRouteString("requestedBookIssue",["requestId"=>$request->getId()])}">
                                                <span class="btn-icon"><i class="fas fa-book"></i></span> {t}Issue Book{/t}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {/if}
                        <a href="{$routes->getRouteString("requestEdit",["requestId"=>$request->getId()])}" class="btn btn-outline-info btn-sm no-border{if $activeLanguage->isRTL()} ml-1{else} mr-1{/if}" data-container="body" data-toggle="tooltip" title="{t}Edit{/t}"><i class="fas fa-pencil-alt"></i></a>
                        <div class="dropdown d-inline" data-trigger="hover" data-toggle="tooltip" title="{t}Delete{/t}">
                            <button class="btn btn-outline-info btn-sm no-border" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="far fa-trash-alt"></i>
                            </button>
                            <ul class="dropdown-menu delete-dropdown dropdown-menu-right">
                                <li class="text-center">{t}Do you really want to delete?{/t}</li>
                                <li class="divider"></li>
                                <li class="text-center">
                                    <button class="btn btn-outline-danger delete-request" data-url="{$routes->getRouteString("requestDelete",["requestId"=>$request->getId()])}">
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