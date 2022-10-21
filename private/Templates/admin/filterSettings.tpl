{extends file='admin/admin.tpl'}
{block name=title}{t}Book Filter Settings{/t}{/block}
{block name=headerCss append}{/block}
{block name=content}
    {if $isDemoMode === true}
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-info text-center">In the demo version you can't change filter custom book fields.</div>
            </div>
        </div>
    {/if}
    <div class="row">
        <div class="col-lg-12">
            {*$customFields|var_dump*}
            {if isset($customFields) and $customFields != null}
                <div class="card">
                    <div class="card-body">
                        <form action="{$routes->getRouteString("filterSettings",[])}" method="post" class="row">
                            {foreach from=$customFields item=value key=key}
                                <div class="col-lg-2 col-md-4 mb-3">
                                    <div class="form-group">
                                        <label class="control-label">{t}{$value->getTitle()}{/t}</label><br>
                                        <label class="switch switch-sm">
                                            <input type="checkbox" name="filterableFields[]" value="{$value->getId()}"{if $value->isFilterable()} checked{/if}>
                                        </label>
                                    </div>
                                </div>
                            {/foreach}
                            <div class="col-lg-12">
                                <button class="btn btn-primary m-t-20 pull-right"{if $isDemoMode === true} disabled{/if}>
                                    <span class="btn-icon"><i class="far fa-save"></i></span> {t}Save{/t}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            {/if}
        </div>
    </div>
{/block}
{block name=footerPageJs append}{/block}
{block name=footerCustomJs append}{/block}