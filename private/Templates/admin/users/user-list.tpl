<div class="table-responsive-sm">
    <table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>{t}Email{/t}</th>
            <th>{t}User Name{/t}</th>
            <th>{t}User Role{/t}
                <div class="dropdown d-inline-block pull-right">
                    <a href="#" class="text-dark" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-filter"></i></a>
                    <div class="dropdown-menu dropdown-menu-right" style="min-width: 260px">
                        <form class="px-3 py-3 text-center">
                            <div class="form-group">
                                <label for="genres" class="control-label text-sm">{t}Role{/t}</label>
                                <select class="form-control" name="roleId" id="roleId" {*multiple="multiple"*}></select>
                            </div>
                            <button type="submit" class="btn btn-primary" id="role-filter">
                                <span class="btn-icon"><i class="fas fa-check"></i></span> {t}Filter{/t}
                            </button>
                        </form>
                    </div>
                </div>
            </th>
            <th style="width: 140px;" class="text-center">{t}Registration Type{/t}</th>
            <th style="width: 90px;" class="text-center">{t}Status{/t}</th>
            <th style="width: 95px;" class="text-center">{t}Actions{/t}</th>
        </tr>
    </thead>
    <tbody>
        {assign var="currentUser" value=$user}
        {if isset($users) and $users != null}
            {foreach from=$users item=user name=user}
                <tr>
                    <td>
                        {if isset($currentUser) and $currentUser->getRole() != null and $currentUser->getRole()->getPriority() < 255}
                            {if $user->getRole() !== null and $user->getRole()->getId() == 1}
                                {$user->getEmail()}
                            {else}
                                <a href="{$routes->getRouteString("userEdit",["userId"=>$user->getId()])}">{$user->getEmail()}</a>
                            {/if}
                        {else}
                            <a href="{$routes->getRouteString("userEdit",["userId"=>$user->getId()])}">{$user->getEmail()}</a>
                        {/if}
                    </td>
                    <td>{$user->getFirstName()} {$user->getLastName()}</td>
                    <td>{if $user->getRole() !== null}<a href="{$routes->getRouteString("roleUserListView",["roleId"=>$user->getRole()->getId()])}">{$user->getRole()->getName()}</a>{/if}</td>
                    <td class="text-center">
                        <span class="badge {if $user->getProvider() == 'facebook'}badge-facebook{elseif $user->getProvider() == 'google'}badge-google{elseif $user->getProvider() == 'twitter'}badge-twitter{else}badge-default{/if}">
                            {if $user->getProvider() != null}
                                {$user->getProvider()}
                            {else}
                                {t}Email{/t}
                            {/if}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge {if $user->isActive() == '1'}badge-success{else}badge-danger{/if}">
                            {if $user->isActive() == '1'}
                                {t}Active{/t}
                            {else}
                                {t}Inactive{/t}
                            {/if}
                        </span>
                    </td>
                    <td class="text-center">
                        {if isset($currentUser) and $currentUser->getRole() != null and $currentUser->getRole()->getPriority() <= 200}
                            {if $user->getRole() !== null and $user->getRole()->getId() != 1}
                                <a href="{$routes->getRouteString("userEdit",["userId"=>$user->getId()])}" class="btn btn-outline-info btn-sm no-border{if $activeLanguage->isRTL()} ml-1{else} mr-1{/if}" data-container="body" data-toggle="tooltip" title="{t}Edit{/t}"><i class="fas fa-pencil-alt"></i></a>
                            {/if}
                        {else}
                            <a href="{$routes->getRouteString("userEdit",["userId"=>$user->getId()])}" class="btn btn-outline-info btn-sm no-border{if $activeLanguage->isRTL()} ml-1{else} mr-1{/if}" data-container="body" data-toggle="tooltip" title="{t}Edit{/t}"><i class="fas fa-pencil-alt"></i></a>
                        {/if}
                        {if $user->getRole() !== null and $user->getId() != 1 and $currentUser->getRole()->getPriority() >= 255}
                        {if ($isDemoMode === true and $user->getId() > 3) or $isDemoMode === false}
                            <div class="dropdown d-inline" data-trigger="hover" data-toggle="tooltip" title="{t}Delete{/t}">
                                <button class="btn btn-outline-info btn-sm no-border" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="far fa-trash-alt"></i>
                                </button>
                                <ul class="dropdown-menu delete-dropdown dropdown-menu-right">
                                    <li class="text-center">{t}Do you really want to delete?{/t}</li>
                                    <li class="divider"></li>
                                    <li class="text-center">
                                        <button class="btn btn-outline-danger delete-user" data-url="{$routes->getRouteString("userDelete",["userId"=>$user->getId()])}">
                                            <span class="btn-icon"><i class="far fa-trash-alt"></i></span> {t}Delete{/t}
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        {/if}
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