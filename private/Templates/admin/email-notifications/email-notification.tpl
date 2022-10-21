{extends file='admin/admin.tpl'}
{block name=title}{if $action == "create"}{t}Add Email Notification{/t}{else}{t}Edit Email Notification{/t}{/if}{/block}
{block name=headerCss append}
    <link href="{$resourcePath}assets/css/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet"/>
{/block}
{block name=content}
    <div class="row">
        {if $action == "create"}
            {assign var=route value=$routes->getRouteString("emailNotificationCreate")}
        {elseif $action == "edit" and isset($emailNotification)}
            {assign var=route value=$routes->getRouteString("emailNotificationEdit",["route"=>$emailNotification->getRoute()])}
        {elseif $action == "delete"}
            {assign var=route value=""}
        {/if}
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form action="{$route}" method="post" class="validate">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">{t}Route Name{/t}</label>
                                        <input type="hidden" name="id" value="{if isset($emailNotification)}{$emailNotification->getId()}{/if}">
                                        <select name="route" class="form-control select-picker" {if $action == "edit"}disabled{/if}>
                                            <option value="null">Select trigger:</option>
                                            {foreach from=$sortedRoutes key=routeName item=selectRoute}
                                                <option value="{$routeName}" {if isset($emailNotification) and $emailNotification->getRoute() == $routeName}selected{/if}>{$selectRoute->getTitle()}</option>
                                            {/foreach}
                                        </select>
                                        {if $action == "edit"}
                                            <input type="hidden" name="route" value="{$emailNotification->getRoute()}">
                                        {/if}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">{t}Subject{/t}</label>
                                        <input type="text" name="subject" class="form-control" value="{if $action == "edit" and isset($emailNotification)}{$emailNotification->getSubject()}{/if}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">{t}From Name{/t}</label>
                                        <input type="text" name="fromName" class="form-control" value="{if $action == "edit" and isset($emailNotification)}{$emailNotification->getFrom()->getName()}{/if}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">{t}From{/t}</label>
                                        <input type="text" name="from" class="form-control" value="{if $action == "edit" and isset($emailNotification)}{$emailNotification->getFrom()->getEmail()}{/if}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">{t}To{/t}</label>
                                        <input type="text" name="to" class="form-control" value="{if $action == "edit" and isset($emailNotification)}{foreach $emailNotification->getTo() as $emailAddress}{$emailAddress->getEmail()}{if $emailAddress->getName() != null}|{$emailAddress->getName()}{/if}{if $smarty.foreach.emailAddress.last}{else};{/if}{/foreach}{/if}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="isEnabled">{t}Enabled{/t}</label><br>
                                    <label class="switch switch-sm">
                                        <input type="checkbox" name="isEnabled" value="1"{if $action == "edit" and $emailNotification->isEnabled()} checked{/if}>
                                    </label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <textarea name="content">{if isset($emailNotification)}{$emailNotification->getContent()}{/if}</textarea>
                                    <textarea name="code" id="code">{if isset($emailNotification)}{$emailNotification->getContent()}{/if}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 margin-top-30">
                            <div class="form-group">
                                <button type="submit" class="btn btn-success pull-right">
                                    <span class="btn-icon"><i class="far fa-save"></i></span> {t}Save{/t}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
{/block}
{block name=footerPageJs append}
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/jquery-validate/jquery.validate.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/select2/select2.full.min.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/ace/ace.js" charset="utf-8"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/bootstrap-select/bootstrap-select.js"></script>
{/block}
{block name=footerCustomJs append}
    <script>
        $(document).ready(function () {
            var editor = ace.edit("code");
            editor.setTheme("ace/theme/monokai");
            editor.session.setMode("ace/mode/smarty");
            editor.setOptions({
                maxLines: 50,
                showPrintMargin: false,
                fontSize: '14px'
            });
            var textarea = $('textarea[name="content"]').hide();
            editor.getSession().setValue(textarea.val());
            editor.getSession().on('change', function(){
                textarea.val(editor.getSession().getValue());
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
                    route: {
                        required: true
                    },
                    from: {
                        required: true
                    }
                }
            });
        });
    </script>
{/block}