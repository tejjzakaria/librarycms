{extends file='admin/admin.tpl'}
{block name=title}{if $action == "create"}{t}Add Role{/t}{else}{t}Edit Role{/t}{/if}{/block}
{block name=headerCss append}{/block}
{block name=content}
    {if $isDemoMode === true}
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-info text-center">In the demo version you cannot create/change role.</div>
            </div>
        </div>
    {/if}
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                {if $action == "create"}
                    {assign var=route value=$routes->getRouteString("roleCreate")}
                {elseif $action == "edit" and isset($role)}
                    {assign var=route value=$routes->getRouteString("roleEdit",["roleId"=>$role->getId()])}
                {elseif $action == "delete"}
                    {assign var=route value=""}
                {/if}
                <form action="{$route}" method="post" class="validate">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="form-group">
                                    <label for="name" class="control-label">{t}Role Name{/t}</label>
                                    <input type="text" class="form-control" autocomplete="off" name="name" value="{if $action == "edit"}{$role->getName()}{/if}" required>
                                    {if $action == "edit"}<input type="hidden" name="id" value="{$role->getId()}">{/if}
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="issueDayLimit" class="control-label">{t}Priority{/t}</label>
                                    <input type="text" class="form-control" autocomplete="off" name="priority" value="{if $action == "edit"}{$role->getPriority()}{/if}">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="issueDayLimit" class="control-label">{t}Issue Day Limit{/t}</label>
                                    <input type="text" class="form-control" autocomplete="off" name="issueDayLimit" value="{if $action == "edit"}{$role->getIssueDayLimit()}{/if}">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="issueBookLimit" class="control-label">{t}Issue Book Limit{/t}</label>
                                    <input type="text" class="form-control" autocomplete="off" name="issueBookLimit" value="{if $action == "edit"}{$role->getIssueBookLimit()}{/if}">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="finePerDay" class="control-label">{t}Fine Per Day{/t}</label>
                                    <input type="text" class="form-control" autocomplete="off" name="finePerDay" value="{if $action == "edit"}{$role->getFinePerDay()}{/if}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-striped role-permissions">
                                <thead>
                                    <tr>
                                        <th width="40">
                                            <label class="switch switch-sm">
                                                <input type="checkbox" id="checkAll">
                                            </label>
                                        </th>
                                        <th>{t}Route Name{/t}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {if isset($permissions) and $permissions != null}
                                        {foreach from=$permissions item=permission name=permission}
                                            <tr data-action="{$routes->getRouteString("createPermission")}" data-edit-row="true" data-changed="false" {if $permission->isPublic()}class="bg-success text-white"{/if}>
                                                <td>
                                                    <label class="switch switch-sm">
                                                        <input type="checkbox" name="permissions[]" value="{$permission->getId()}"{if $action == "edit" and ($permission->isRolePermission() or $permission->isPublic())} checked{/if}{if $permission->isPublic()} disabled{/if}>
                                                    </label>
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
                        <div class="col-sm-12 m-t-20">
                            <div class="form-group">
                                <button type="submit" class="btn btn-outline-success pull-right mr-3 mb-3" {if $isDemoMode === true}disabled{/if}>
                                    <i class="far fa-save mr-1"></i> {t}Save{/t}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
{/block}
{block name=footerPageJs append}
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/jquery-validate/jquery.validate.js"></script>
{/block}
{block name=footerCustomJs append}
    <script>
        $(document).ready(function () {
            $("#checkAll").click(function () {
                $('table input:checkbox').not(this).not(':disabled').prop('checked', this.checked);
            });
            $('.validate input,.validate select,.validate textarea').tooltipster({
                trigger: 'custom',
                onlyOne: false,
                position: 'bottom',
                offsetY: -5,
                theme: 'tooltipster-kaa'
            });
            $('.validate').validate({
                errorPlacement: function (error, element) {
                    if (element != undefined) {
                        $(element).tooltipster('update', $(error).text());
                        $(element).tooltipster('show');
                    }
                },
                success: function (label, element) {
                    $(element).tooltipster('hide');
                },
                rules: {
                    name: {
                        required: true
                    },
                    priority: {
                        required: true,
                        digits: true,
                        min: 1,
                        max: 255
                    },
                    issueDayLimit: {
                        number: true
                    },
                    issueBookLimit: {
                        number: true
                    },
                    finePerDay: {
                        number: true
                    }
                }
            });
        });
    </script>
{/block}