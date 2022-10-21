<div class="card-header">
    <div class="heading-elements">
        <select name="sortColumn" id="page-sort" class="select-picker {if $activeLanguage->isRTL()}pl-2{else}pr-2{/if} d-tc">
            <option value="Pages.publishDateTime" data-order="DESC"{if $smarty.session.pageSortingOrder == 'DESC' and $smarty.session.pageSortingColumn == 'Pages.publishDateTime'} selected{/if}>{t}Date Descending{/t}</option>
            <option value="Pages.publishDateTime" data-order="ASC"{if $smarty.session.pageSortingOrder == 'ASC' and $smarty.session.pageSortingColumn == 'Pages.publishDateTime'} selected{/if}>{t}Date Ascending{/t}</option>
            <option value="Pages.title" data-order="DESC"{if $smarty.session.pageSortingOrder == 'DESC' and $smarty.session.pageSortingColumn == 'Pages.title'} selected{/if}>{t}Title Descending{/t}</option>
            <option value="Pages.title" data-order="ASC"{if $smarty.session.pageSortingOrder == 'ASC' and $smarty.session.pageSortingColumn == 'Pages.title'} selected{/if}>{t}Title Ascending{/t}</option>
        </select>
        <select name="perPage" id="countPerPage" class="select-picker d-tc">
            {foreach from=$siteViewOptions->getOption("pagesPerPage")->getListValues() key=key item=value}
                <option value="{$key}"{if ($smarty.session.pagePerPage == null and strcmp($key,$siteViewOptions->getOption("pagesPerPage")->getValue()) === 0) or strcmp($key,$smarty.session.pagePerPage) === 0} selected{/if}>{t count=$value 1=$value plural="%1 Pages"}1 Page{/t}</option>
            {/foreach}
        </select>
    </div>
</div>
<div class="table-responsive-sm">
    <table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>{t}Title{/t}</th>
            <th>{t}Parent{/t}</th>
            <th>{t}User{/t}</th>
            <th>{t}Status{/t}</th>
            <th>{t}Date{/t}</th>
            <th style="width: 130px;" class="text-center">{t}Actions{/t}</th>
        </tr>
    </thead>
    <tbody>
        {if isset($postPages) and $postPages != null}
            {foreach from=$postPages item=page name=page}
                <tr>
                    <td>
                        <a href="{$routes->getRouteString("pageEdit",["pageId"=>$page->getId()])}">{$page->getTitle()} </a>
                    </td>
                    <td>
                        {if $page->getBreadcrumbs() != null}
                            {foreach from=$page->getBreadcrumbs() item=breadcrumb name=breadcrumb}
                                {if !$smarty.foreach.breadcrumb.last }
                                    <a href="{$routes->getRouteString("editPage",["pageId"=>$breadcrumb->getId()])}">{$breadcrumb->getTitle()}</a>
                                {/if}
                            {/foreach}
                        {/if}
                    </td>
                    <td>{if $page->getUser() !== null}
                            <a href="{$routes->getRouteString("userEdit",["userId"=>$page->getUserId()])}">
                                {$page->getUser()->getLastName()} {$page->getUser()->getFirstName()}
                            </a>
                        {/if}
                    </td>
                    <td>{$page->getStatus()}</td>
                    <td>{$page->getPublishDateTime()|date_format:$siteViewOptions->getOptionValue("dateTimeFormat")}</td>
                    <td class="text-center">
                        <a href="{$routes->getRouteString("pageViewPublic",["pageUrl"=>$page->getUrl()])}" class="btn btn-outline-info btn-sm no-border{if $activeLanguage->isRTL()} ml-1{else} mr-1{/if}" data-container="body" data-toggle="tooltip" title="{t}View{/t}"><i class="far fa-eye"></i></a>
                        <a href="{$routes->getRouteString("pageEdit",["pageId"=>$page->getId()])}" class="btn btn-outline-info btn-sm no-border{if $activeLanguage->isRTL()} ml-1{else} mr-1{/if}" data-container="body" data-toggle="tooltip" title="{t}Edit{/t}"><i class="fas fa-pencil-alt"></i></a>
                        {if $page->getId() != 0}
                            <div class="dropdown d-inline" data-trigger="hover" data-toggle="tooltip" title="{t}Delete{/t}">
                                <button class="btn btn-outline-info btn-sm no-border" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="far fa-trash-alt"></i>
                                </button>
                                <ul class="dropdown-menu delete-dropdown dropdown-menu-right">
                                    <li class="text-center">{t}Do you really want to delete?{/t}</li>
                                    <li class="divider"></li>
                                    <li class="text-center">
                                        <button class="btn btn-outline-danger delete-page" data-url="{$routes->getRouteString("pageDelete",["pageId"=>$page->getId()])}">
                                            <span class="btn-icon"><i class="far fa-trash-alt"></i></span> {t}Delete{/t}
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        {/if}
                    </td>
                </tr>
            {/foreach}
        {/if}
    </tbody>
</table>
</div>
{if isset($pages)}
    {include "admin/general/pagination.tpl"}
{/if}