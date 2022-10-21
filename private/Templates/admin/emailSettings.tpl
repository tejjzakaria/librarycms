{extends file='admin/admin.tpl'}
{block name=title}{t}Email Settings{/t}{/block}
{block name=headerCss append}
    <link href="{$resourcePath}assets/css/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet"/>
    <link href="{$resourcePath}assets/js/plugins/summernote/summernote-bs4.css" rel="stylesheet"/>
{/block}
{block name=content}
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{$routes->getRouteString("emailSettings")}" method="post" class="validate">
                        <div class="form-group">
                            <label class="control-label">{t}Send Method{/t}</label>
                            <select name="sendMethod" class="form-control select-picker">
                                <option value="mail" {if $emailSettings->getSendMethod() == 'mail'}selected{/if}>{t}PHP Mail{/t}</option>
                                <option value="smtp" {if $emailSettings->getSendMethod() == 'smtp'}selected{/if}>{t}SMTP{/t}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">{t}Default From Email Name{/t}</label>
                            <input type="text" class="form-control" name="defaultFromEmailName" value="{$emailSettings->getDefaultFromEmailName()}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">{t}Default From Email Address{/t}</label>
                            <input type="text" class="form-control" name="defaultFromEmailAddress" value="{$emailSettings->getDefaultFromEmailAddress()}">
                        </div>
                        <div class="form-group">
                            <label for="content">{t}Default Message Template For Notify Delayed Users{/t}</label>
                            <textarea name="dynamicEmailTemplate" id="staticEmailTemplate">{$emailSettings->getDynamicEmailTemplate()}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="content">{t}HTML Template For Notify Delayed Users{/t}</label>
                            <textarea name="staticEmailTemplate">{$emailSettings->getStaticEmailTemplate()}</textarea>
                            <textarea name="code" id="code">{$emailSettings->getStaticEmailTemplate()}</textarea>
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
    <div class="row">
        <div class="col-md-12">
            <div class="page-title">
                <h3 class="text-primary mb-4">{t}Email Configuration Test{/t}</h3>
                <div class="alert alert-info">
                    {t escape=no}You can check email configuration by sending test email to your own email address. You need to type your email address and press
                        <strong>Send</strong>
                        button. If you received test email it means email is configured properly.{/t}
                </div>
                <div class="alert alert-warning">
                    {t}Email will be sent by selected Send Method{/t}.
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card" id="test-email-block">
                <div class="card-body">
                    <form action="{$routes->getRouteString("emailSend")}" method="post" class="validate">
                        <div class="form-group">
                            <label class="control-label">{t}Email{/t}</label>
                            <input type="text" name="email" class="form-control" value="">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success test-email pull-right mt-2">
                                <i class="fa fa-send"></i> {t}Send{/t}
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
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/ace/ace.js" charset="utf-8"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/summernote/summernote-bs4.min.js"></script>
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
            var textarea = $('textarea[name="staticEmailTemplate"]').hide();
            editor.getSession().setValue(textarea.val());
            editor.getSession().on('change', function(){
                textarea.val(editor.getSession().getValue());
            });
            $('#staticEmailTemplate').summernote({
                toolbar: [
                    // [groupName, [list of button]]
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough', 'hr']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['misc', ['codeview']]
                ]
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
                    sendMethod: {
                        required: true
                    }
                }
            });
            $(document).on('click', '.test-email', function (e) {
                e.preventDefault();
                var form = $(this).closest('form');
                $.ajax({
                    url: $(form).attr('action'),
                    dataType: 'json',
                    data: $(form).serialize(),
                    type: "POST",
                    beforeSend: function () {
                        app.card.loading.start($("#test-email-block"));
                    },
                    success: function (data) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                app.notification('success', data.success);
                            }
                        }
                    },
                    complete: function () {
                        app.card.loading.finish($("#test-email-block"));
                    },
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    }
                });
            });
        });
    </script>
{/block}