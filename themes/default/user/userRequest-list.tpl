<div class="heading-elements">
    <a class="request-book-collapse" data-toggle="collapse" href="#request-book-block" aria-expanded="false" aria-controls="collapseExample">
        <i class="ti-book"></i> {t}Request Book{/t}
    </a>
    <select name="sortColumn" id="books-sort" class="form-control custom-select {if $activeLanguage->isRTL()}p-l-10{else}p-r-10{/if}">
        <option value="Requests.creationDate" data-order="DESC"{if $smarty.session.requestSortingOrder == 'DESC' and $smarty.session.requestSortingColumn == 'Requests.creationDate'} selected{/if}>{t}Request Date Descending{/t}</option>
        <option value="Requests.creationDate" data-order="ASC"{if $smarty.session.requestSortingOrder == 'ASC' and $smarty.session.requestSortingColumn == 'Requests.creationDate'} selected{/if}>{t}Request Date Ascending{/t}</option>
    </select>
    <select name="perPage" id="countPerPage" class="form-control custom-select">
        {foreach from=$siteViewOptions->getOption("requestsPerPage")->getListValues() key=key item=value}
            <option value="{$key}"{if ($smarty.session.requestPerPage == null and strcmp($key,$siteViewOptions->getOption("requestsPerPage")->getValue()) === 0) or strcmp($key,$smarty.session.requestPerPage) === 0} selected{/if}>{t count=$value 1=$value plural="%1 Requests"}1 Request{/t}</option>
        {/foreach}
    </select>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="collapse position-relative" id="request-book-block">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <label for="bookId" class="control-label">{t}Book{/t}</label>
                            <div class="input-group">
                                <select name="bookIds[]" id="bookId" class="form-control"></select>
                                <div class="input-group-append">
                                    <a href="#" class="btn" id="add-book" data-container="body" data-toggle="tooltip" title="{t}Add Another Book{/t}"><i class="fas fa-plus" aria-hidden="true"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 align-items-center">
                            <div class="form-group">
                                <label for="note" class="control-label">{t}Notes{/t}</label>
                                <textarea name="notes" id="note" cols="30" rows="2" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <form action="{$routes->getRouteString("requestCreate")}" id="request-form">
                {if isset($user)}
                    <input type="hidden" name="userId" value="{$user->getId()}">
                {/if}
                <input type="hidden" name="notes" id="notes">
                <table class="table table-hover d-none" id="request-result">
                    <thead>
                        <tr>
                            <th>{t}Books{/t}</th>
                            <th style="width: 150px;">{t}Publisher{/t}</th>
                            <th style="width: 150px;">{t}Publishing Year{/t}</th>
                            <th style="width: 150px;">{t}ISBN10/13{/t}</th>
                            <th style="width: 65px;">{t}Delete{/t}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-lg-12">
                        <a href="#" class="btn btn-info pull-right mr-2 mb-3" id="request-book">{t}Request{/t}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="table-responsive-sm request-book-listing">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>{t}Book{/t}</th>
                <th style="width: 140px;">{t}Creation Date{/t}</th>
                <th style="width: 110px;" class="text-center">{t}Status{/t}</th>
                <th style="width: 90px;" class="text-center">{t}Actions{/t}</th>
            </tr>
        </thead>
        <tbody>
            {if isset($requests) and $requests != null}
                {foreach from=$requests item=request name=request}
                    <tr>
                        <td>
                            <a href="{if $siteViewOptions->getOptionValue("bookUrlType")}{$routes->getRouteString("bookViewPublic",["bookId"=>$request->getBook()->getId()])}{else}{$routes->getRouteString("bookViewViaUrlPublic",["bookUrl"=>$request->getBook()->getUrl()])}{/if}">{$request->getBook()->getTitle()}</a>
                            {if $request->getNotes()}
                                <div class="book-list-info">
                                    <strong class="text-uppercase">{t}Notes{/t}:</strong>
                                    {$request->getNotes()}
                                </div>
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
                            {if $request->getStatus() == 'Pending'}
                                <div class="dropdown d-inline" data-trigger="hover" data-toggle="tooltip" title="{t}Delete{/t}">
                                    <button class="btn btn-outline-info btn-sm no-border" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                    <ul class="dropdown-menu delete-dropdown {if $activeLanguage->isRTL()}dropdown-menu-left{else}dropdown-menu-right{/if}">
                                        <li class="text-center">{t}Do you really want to delete?{/t}</li>
                                        <li class="divider"></li>
                                        <li class="text-center">
                                            <button class="btn btn-outline-danger delete-request" data-url="{$routes->getRouteString("requestDelete",["requestId"=>$request->getId()])}">
                                                <i class="far fa-trash-alt mr-1"></i> {t}Delete{/t}
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            {/if}
                        </td>
                    </tr>
                {/foreach}
            {else}
                <tr>
                    <td colspan="4" class="text-center">{t}You don't have any requests yet{/t}</td>
                </tr>
            {/if}
        </tbody>
    </table>
</div>
{include "general/pagination.tpl"}