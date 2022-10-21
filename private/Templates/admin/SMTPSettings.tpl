{extends file='admin/admin.tpl'}
{block name=title}{t}SMTP Settings{/t}{/block}
{block name=headerCss append}
    <link href="{$resourcePath}assets/css/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet"/>
{/block}
{block name=content}
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{$routes->getRouteString("smtpSettings")}" method="post" class="validate">
                        <div class="form-group">
                            <label class="control-label">{t}Server Name{/t}</label>
                            <input type="text" name="server" class="form-control" value="{if isset($SMTPSettings)}{$SMTPSettings->getServer()}{/if}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">{t}Port{/t}</label>
                            <input type="text" name="port" class="form-control" value="{if isset($SMTPSettings)}{$SMTPSettings->getPort()}{/if}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">{t}Connection security{/t}</label>
                            <select name="security" class="form-control select-picker">
                                <option value="None"{if isset($SMTPSettings)}{if $SMTPSettings->getSecurity() == 'None'} selected{/if}{/if}>None</option>
                                <option value="SSL"{if isset($SMTPSettings)}{if $SMTPSettings->getSecurity() == 'SSL'} selected{/if}{/if}>SSL</option>
                                <option value="TLS"{if isset($SMTPSettings)}{if $SMTPSettings->getSecurity() == 'TLS'} selected{/if}{/if}>TLS</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">{t}User Name{/t}</label>
                            <input type="text" name="userName" class="form-control" value="{if isset($SMTPSettings)}{$SMTPSettings->getUserName()}{/if}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">{t}Password{/t}</label>
                            <input type="password" name="password" class="form-control" autocomplete="off" id="password">
                        </div>
                        <div class="form-group">
                            <label class="control-label">{t}Password Confirm{/t}</label>
                            <input type="password" name="passwordConfirm" class="form-control" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success pull-right mt-2">
                                <span class="btn-icon"><i class="far fa-save"></i></span> {t}Save{/t}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
{/block}
{block name=footerPageJs append}
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/jquery-validate/jquery.validate.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/bootstrap-select/bootstrap-select.js"></script>
{/block}
{block name=footerCustomJs append}
    <script>
        $(document).ready(function () {
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
                    passwordConfirm: {
                        equalTo: "#password"
                    }
                }
            });
        });
    </script>
{/block}