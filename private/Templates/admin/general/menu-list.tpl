<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th width="40">{t}Id{/t}</th>
            <th>{t}Name{/t}</th>
            <th style="width: 80px;" class="text-center">{t}Actions{/t}</th>
        </tr>
    </thead>
    <tbody>
        {if isset($menus) and $menus != null}
            {foreach from=$menus item=menu name=menu}
                <tr>
                    <td class="text-center">{$menu->getId()}</td>
                    <td>
                        <a href="{$routes->getRouteString("menuEdit",["menuId"=>$menu->getId()])}">{$menu->getName()}</a>
                    </td>
                    <td class="text-center">
                        {if $menu->getId() != 1}
                            <div class="dropdown" data-trigger="hover" data-toggle="tooltip" title="{t}Delete{/t}">
                                <button class="btn btn-outline-info btn-sm no-border" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="far fa-trash-alt"></i>
                                </button>
                                <ul class="dropdown-menu delete-dropdown dropdown-menu-right">
                                    <li class="text-center">{t}Do you really want to delete?{/t}</li>
                                    <li class="divider"></li>
                                    <li class="text-center">
                                        <button class="btn btn-outline-danger delete-menu" data-url="{$routes->getRouteString("menuDelete",["menuId"=>$menu->getId()])}">
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