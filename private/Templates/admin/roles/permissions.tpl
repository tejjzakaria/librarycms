{extends file='admin/admin.tpl'}
{block name=title}{t}Permissions{/t}{/block}
{block name=breadcrumb}<li class="active">{t}Permissions{/t}</li>{/block}
{block name=headerCss append}{/block}
{block name=content}
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <form action="{$routes->getRouteString("permissionListUpdate")}" method="post" class="">
                    <div class="block-heading">
                        <div class="form-group">
                            <button type="submit" class="btn btn-outline-success pull-right mt-3 {if $activeLanguage->isRTL()}ml-3{else}mr-3{/if} mb-3 save-role">
                                <span class="btn-icon"><i class="far fa-save"></i></span> {t}Update Permissions{/t}
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive-sm">
                        <table class="table table-striped">
                        <thead>
                            <tr>
                                <th width="40">#</th>
                                <th width="70" class="text-center">{t}Public Access{/t}</th>
                                <th>{t}Permission Name{/t} (<span class="route-size"></span>)</th>
                            </tr>
                        </thead>
                        <tbody>
                            {if isset($permissions) and $permissions != null}
                                {foreach from=$permissions item=permission name=permission}
                                    <tr {if $permission->isPublic()}class="bg-success text-white"{/if}>
                                        <td>
                                            {$permission@index+1}
                                        </td>
                                        <td class="text-center">
                                            {if $permission->isPublic()}yes{else}no{/if}
                                        </td>
                                        <td>
                                            {$permission->getRouteTitle()}
                                        </td>
                                    </tr>
                                {/foreach}
                            {/if}
                        </tbody>
                    </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
{/block}
{block name=footerPageJs append}{/block}
{block name=footerCustomJs append}
    <script>
        $(document).ready(function () {
            var count = $('table tr').length;
            $('.route-size').text(count);
        });
    </script>
{/block}