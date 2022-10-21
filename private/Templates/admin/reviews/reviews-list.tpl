<div class="table-responsive-sm">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>{t}Book{/t}</th>
                <th>{t}User{/t}</th>
                <th>{t}Review{/t}</th>
                <th style="width: 110px;" class="text-center">{t}Date{/t}</th>
                <th style="width: 110px;" class="text-center">{t}Status{/t}</th>
                <th style="width: 95px;" class="text-center">{t}Actions{/t}</th>
            </tr>
        </thead>
        <tbody>
            {if isset($reviews) and $reviews != null}
                {foreach from=$reviews item=review name=review}
                    <tr>
                        <td>
                            {if $review->getBook() != null}
                                <a href="{$routes->getRouteString("bookEdit",["bookId"=>$review->getBookId()])}">{$review->getBook()->getTitle()}</a>
                            {/if}
                        </td>
                        <td>
                            {if $review->getUserId() != null and $review->getUser() != null}
                                <a href="{$routes->getRouteString("userEdit",["userId"=>$review->getUser()->getId()])}">{$review->getUser()->getLastName()} {$review->getUser()->getFirstName()}</a>
                            {else}
                                {$review->getName()}
                            {/if}
                        </td>
                        <td>
                            {$review->getText()|truncate:50}
                        </td>
                        <td class="text-center">
                            {$review->getCreationDateTime()|date_format:$siteViewOptions->getOptionValue("dateTimeFormat")}
                        </td>
                        <td class="text-center">
                        <span class="badge {if $review->isPublish()}badge-success{else}badge-warning{/if}">
                             {if $review->isPublish()}
                                 {t}approved{/t}
                             {else}
                                 {t}pending{/t}
                             {/if}
                        </span>
                        </td>
                        <td class="text-center">
                            <a href="{$routes->getRouteString("reviewEdit",["reviewId"=>$review->getId()])}" class="btn btn-outline-info btn-sm no-border{if $activeLanguage->isRTL()} ml-1{else} mr-1{/if}" data-container="body" data-toggle="tooltip" title="{t}Edit{/t}"><i class="fas fa-pencil-alt"></i></a>
                            <div class="dropdown d-inline" data-trigger="hover" data-toggle="tooltip" title="{t}Delete{/t}">
                                <button class="btn btn-outline-info btn-sm no-border" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="far fa-trash-alt"></i>
                                </button>
                                <ul class="dropdown-menu delete-dropdown dropdown-menu-right">
                                    <li class="text-center">{t}Do you really want to delete?{/t}</li>
                                    <li class="divider"></li>
                                    <li class="text-center">
                                        <button class="btn btn-outline-danger delete-review" data-url="{$routes->getRouteString("reviewDelete",["reviewId"=>$review->getId()])}">
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