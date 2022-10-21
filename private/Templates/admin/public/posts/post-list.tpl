<div class="card-header">
    <div class="heading-elements">
        <select name="sortColumn" id="post-sort" class="select-picker {if $activeLanguage->isRTL()}pl-2{else}pr-2{/if} d-tc">
            <option value="Posts.publishDateTime" data-order="DESC"{if $smarty.session.postSortingOrder == 'DESC' and $smarty.session.postSortingColumn == 'Posts.publishDateTime'} selected{/if}>{t}Date Descending{/t}</option>
            <option value="Posts.publishDateTime" data-order="ASC"{if $smarty.session.postSortingOrder == 'ASC' and $smarty.session.postSortingColumn == 'Posts.publishDateTime'} selected{/if}>{t}Date Ascending{/t}</option>
            <option value="Posts.title" data-order="DESC"{if $smarty.session.postSortingOrder == 'DESC' and $smarty.session.postSortingColumn == 'Posts.title'} selected{/if}>{t}Title Descending{/t}</option>
            <option value="Posts.title" data-order="ASC"{if $smarty.session.postSortingOrder == 'ASC' and $smarty.session.postSortingColumn == 'Posts.title'} selected{/if}>{t}Title Ascending{/t}</option>
        </select>
        <select name="perPage" id="countPerPage" class="select-picker d-tc">
            {foreach from=$siteViewOptions->getOption("postsPerPage")->getListValues() key=key item=value}
                <option value="{$key}"{if ($smarty.session.postPerPage == null and strcmp($key,$siteViewOptions->getOption("postsPerPage")->getValue()) === 0) or strcmp($key,$smarty.session.postPerPage) === 0} selected{/if}>{t count=$value 1=$value plural="%1 Posts"}1 Post{/t}</option>
            {/foreach}
        </select>
    </div>
</div>
<div class="table-responsive-sm">
    <table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>{t}Title{/t}</th>
            <th>{t}Categories{/t}</th>
            <th>{t}User{/t}</th>
            <th>{t}Status{/t}</th>
            <th>{t}Date{/t}
            </th>
            <th style="width: 130px;" class="text-center">{t}Actions{/t}</th>
        </tr>
    </thead>
    <tbody>
        {if isset($posts) and $posts != null}
            {foreach from=$posts item=post name=post}
                <tr>
                    <td>
                        <a href="{$routes->getRouteString("postEdit",["postId"=>$post->getId()])}">{$post->getTitle()} </a>
                    </td>
                    <td>
                        {if count($post->getCategories()) > 0}
                            {foreach from=$post->getCategories() item=category name=categories}
                                {$category->getName()}{if $smarty.foreach.categories.last !== true}, {/if}
                            {/foreach}
                        {/if}
                    </td>
                    <td>
                        {if $post->getUser() !== null}
                            <a href="{$routes->getRouteString("userEdit",["userId"=>$post->getUserId()])}">
                                {$post->getUser()->getFirstName()} {$post->getUser()->getLastName()}
                            </a>
                        {/if}
                    </td>
                    <td>{$post->getStatus()}</td>
                    <td>{$post->getPublishDateTime()|date_format:$siteViewOptions->getOptionValue("dateTimeFormat")}</td>
                    <td class="text-center">
                        <a href="{$routes->getRouteString("postViewPublic",["postUrl"=>$post->getUrl()])}" class="btn btn-outline-info btn-sm no-border{if $activeLanguage->isRTL()} ml-1{else} mr-1{/if}" data-container="body" data-toggle="tooltip" title="{t}View{/t}"><i class="far fa-eye"></i></a>
                        <a href="{$routes->getRouteString("postEdit",["postId"=>$post->getId()])}" class="btn btn-outline-info btn-sm no-border{if $activeLanguage->isRTL()} ml-1{else} mr-1{/if}" data-container="body" data-toggle="tooltip" title="{t}Edit{/t}"><i class="fas fa-pencil-alt"></i></a>
                        {if $post->getId() != 0}
                        <div class="dropdown d-inline" data-trigger="hover" data-toggle="tooltip" title="{t}Delete{/t}">
                            <button class="btn btn-outline-info btn-sm no-border" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="far fa-trash-alt"></i>
                            </button>
                            <ul class="dropdown-menu delete-dropdown dropdown-menu-right">
                                <li class="text-center">{t}Do you really want to delete?{/t}</li>
                                <li class="divider"></li>
                                <li class="text-center">
                                    <button class="btn btn-outline-danger delete-post" data-url="{$routes->getRouteString("postDelete",["postId"=>$post->getId()])}">
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