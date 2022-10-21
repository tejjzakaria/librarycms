<div class="table-responsive-sm">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>{t}Title{/t}</th>
                <th style="width: 95px;" class="text-center">{t}Actions{/t}</th>
            </tr>
        </thead>
        <tbody>
            {if isset($tags) and $tags != null}
                {foreach from=$tags item=tag name=tag}
                    <tr>
                        <td>
                            <a href="{$routes->getRouteString("tagEdit",["tagId"=>$tag->getId()])}">{$tag->getName()} </a>
                        </td>
                        <td class="text-center">
                            <a href="{$routes->getRouteString("tagEdit",["tagId"=>$tag->getId()])}" class="btn btn-outline-info btn-sm no-border{if $activeLanguage->isRTL()} ml-1{else} mr-1{/if}" data-container="body" data-toggle="tooltip" title="{t}Edit{/t}"><i class="fas fa-pencil-alt"></i></a>
                            <div class="dropdown d-inline" data-trigger="hover" data-toggle="tooltip" title="{t}Delete{/t}">
                                <button class="btn btn-outline-info btn-sm no-border" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="far fa-trash-alt"></i>
                                </button>
                                <ul class="dropdown-menu delete-dropdown dropdown-menu-right">
                                    <li class="text-center">{t}Do you really want to delete?{/t}</li>
                                    <li class="divider"></li>
                                    <li class="text-center">
                                        <button class="btn btn-outline-danger delete-tag" data-url="{$routes->getRouteString("tagDelete",["tagId"=>$tag->getId()])}">
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
{if isset($pages)}
    {include "admin/general/pagination.tpl"}
{/if}
