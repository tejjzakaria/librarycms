{extends file='admin/admin.tpl'}
{block name=title}{t}SMS Settings{/t}{/block}
{block name=headerCss append}{/block}
{block name=content}
    <div class="row">
        <div class="col-md-12">
            {*$smsSettings|var_dump*}
            {if $smsSettings != null}
                {assign var=providers value=$smsSettings->getProviders()}
                <form action="{$routes->getRouteString("smsSettings")}" method="post" class="validate" id="general-form">
                    <div class="card">
                        <ul class="nav nav-tabs customtab settings-nav-tabs" role="tablist">
                            {if $providers != null and count($providers) > 0}
                                {foreach from=$providers item=provider name=provider}
                                    {if $provider !== null}
                                        <li class="nav-item">
                                            <label class="switch switch-sm" data-container="body" data-toggle="tooltip" title="{t}Status (Active/Inactive){/t}">
                                                <input type="checkbox" name="activeProvider" class="active-provider" value="{$provider->getName()}"{if strcmp($smsSettings->getActiveProvider(),$provider->getName()) == 0} checked{/if}>
                                            </label>
                                            <a class="nav-link {if $smarty.foreach.provider.first}active{/if}" data-toggle="tab" href="#provider-{$provider->getName()}" role="tab" aria-expanded="true">
                                                <span class="hidden-xs-down">{t}{$provider->getTitle()}{/t}</span>
                                            </a>
                                        </li>
                                    {/if}
                                {/foreach}
                            {/if}
                        </ul>
                        {if $providers != null and count($providers) > 0}
                            <div class="tab-content">
                                {foreach from=$providers item=provider name=provider}
                                    {if $provider !== null}
                                        <div class="tab-pane p-20 {if $smarty.foreach.provider.first}active{/if}" id="provider-{$provider->getName()}" role="tabpanel" aria-expanded="true">
                                            <input type="hidden" name="providers[{$smarty.foreach.provider.index}][name]" class="provider-name" value="{$provider->getName()}">
                                            <input type="hidden" name="providers[{$smarty.foreach.provider.index}][title]" class="provider-title" value="{$provider->getTitle()}">
                                            {assign var=customFields value=$provider->getTitledConfig()}
                                            <div class="row">
                                                {foreach from=$customFields item=field name=field}
                                                    <div class="col-lg-6">
                                                        <div class="form-group">
                                                            <label class="control-label">{$field->getTitle()}</label>
                                                            <input type="text" class="form-control" name="providers[{$smarty.foreach.provider.index}][{$field->getKey()}]" value="{$field->getValue()}">
                                                        </div>
                                                    </div>
                                                {/foreach}
                                            </div>
                                        </div>
                                    {/if}
                                {/foreach}
                            </div>
                        {/if}
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label class="control-label">{t}Sender{/t}</label>
                                <input type="text" class="form-control" name="sender" value="{$smsSettings->getSender()}">
                            </div>
                            <div class="form-group">
                                <label for="content">{t}Default Message Template For Notify Delayed Users{/t}</label>
                                <textarea name="defaultMessage" class="form-control">{$smsSettings->getDefaultMessage()}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <button type="submit" class="btn btn-success pull-right mt-1 mb-4">
                            <span class="btn-icon"><i class="far fa-save"></i></span> {t}Save{/t}
                        </button>
                    </div>
                </form>
            {/if}
        </div>
    </div>
    </div>
{/block}
{block name=footerPageJs append}{/block}
{block name=footerCustomJs append}
    <script type="text/javascript">
        /*$('.settings-nav-tabs .nav-item').on('click', function (e) {
            $('.settings-nav-tabs .nav-item').addClass('active').not(this).removeClass('active');
        });*/
        $('.active-provider').on("change", function() {
            $('.active-provider').not(this).prop('checked', false);
        });
    </script>
{/block}