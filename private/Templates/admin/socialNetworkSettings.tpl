{extends file='admin/admin.tpl'}
{block name=title}{t}Social Network Settings{/t}{/block}
{block name=headerCss append}{/block}
{block name=content}
    {if $isDemoMode === true}
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-info text-center">In the demo version you can't change social network settings.</div>
            </div>
        </div>
    {/if}
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <form action="{$routes->getRouteString("socialNetworkSettings")}" method="post" class="validate" id="general-form">
                    <ul class="nav nav-tabs customtab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#facebook" role="tab" aria-expanded="true">
                                <span class="hidden-xs-down">{t}Facebook{/t}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#google" role="tab" aria-expanded="false">
                                <span class="hidden-xs-down">{t}Google{/t}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#twitter" role="tab" aria-expanded="false">
                                <span class="hidden-xs-down">{t}Twitter{/t}</span>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane p-20 active" id="facebook" role="tabpanel" aria-expanded="true">
                            <div class="row">
                                <div class="col-lg-5">
                                    <div class="form-group">
                                        <label class="control-label">{t}Application Id{/t}</label>
                                        <input type="text" class="form-control" name="facebook[applicationId]" value="{$socialNetworkSettings->getProvider('facebook')->getApplicationId()}"{if $isDemoMode === true} readonly{/if}>
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class="form-group">
                                        <label class="control-label">{t}Application Secret{/t}</label>
                                        <input type="text" class="form-control" name="facebook[applicationSecret]" value="{$socialNetworkSettings->getProvider('facebook')->getApplicationSecret()}"{if $isDemoMode === true} readonly{/if}>
                                    </div>
                                </div>

                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label class="control-label">{t}Status{/t}</label><br>
                                        <label class="switch switch-sm">
                                            <input type="checkbox" name="facebook[isActive]" value="1"{if $socialNetworkSettings->getProvider('facebook')->isActive()} checked{/if}{if $isDemoMode === true} disabled{/if}>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane p-20" id="google" role="tabpanel" aria-expanded="true">
                            <div class="row">
                                <div class="col-lg-5">
                                    <div class="form-group">
                                        <label class="control-label">{t}Application Id{/t}</label>
                                        <input type="text" class="form-control" name="google[applicationId]" value="{$socialNetworkSettings->getProvider('google')->getApplicationId()}"{if $isDemoMode === true} readonly{/if}>
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class="form-group">
                                        <label class="control-label">{t}Application Secret{/t}</label>
                                        <input type="text" class="form-control" name="google[applicationSecret]" value="{$socialNetworkSettings->getProvider('google')->getApplicationSecret()}"{if $isDemoMode === true} readonly{/if}>
                                    </div>
                                </div>

                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label class="control-label">{t}Status{/t}</label><br>
                                        <label class="switch switch-sm">
                                            <input type="checkbox" name="google[isActive]" value="1"{if $socialNetworkSettings->getProvider('google')->isActive()} checked{/if}{if $isDemoMode === true} disabled{/if}>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane p-20" id="twitter" role="tabpanel" aria-expanded="false">
                            <div class="row">
                                <div class="col-lg-5">
                                    <div class="form-group">
                                        <label class="control-label">{t}Application Id{/t}</label>
                                        <input type="text" class="form-control" name="twitter[applicationId]" value="{$socialNetworkSettings->getProvider('twitter')->getApplicationId()}"{if $isDemoMode === true} readonly{/if}>
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class="form-group">
                                        <label class="control-label">{t}Application Secret{/t}</label>
                                        <input type="text" class="form-control" name="twitter[applicationSecret]" value="{$socialNetworkSettings->getProvider('twitter')->getApplicationSecret()}"{if $isDemoMode === true} readonly{/if}>
                                    </div>
                                </div>

                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label class="control-label">{t}Status{/t}</label><br>
                                        <label class="switch switch-sm">
                                            <input type="checkbox" name="twitter[isActive]" value="1"{if $socialNetworkSettings->getProvider('twitter')->isActive()} checked{/if}{if $isDemoMode === true} disabled{/if}>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <button type="submit" class="btn btn-success pull-right mt-1 mb-4"{if $isDemoMode === true} disabled{/if}>
                            <span class="btn-icon"><i class="far fa-save"></i></span> {t}Save{/t}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
{/block}
{block name=footerPageJs append}{/block}
{block name=footerCustomJs append}{/block}