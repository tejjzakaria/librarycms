{extends file='admin/admin.tpl'}
{block name=title}{t}Book Visible Fields On Public{/t}{/block}
{block name=headerCss append}
    <link href="{$resourcePath}assets/js/plugins/jquery/jquery-ui.min.css" rel="stylesheet"/>
{/block}
{block name=content}
    {if $isDemoMode === true}
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-info text-center">In the demo version you can't change book visible fields.</div>
            </div>
        </div>
    {/if}
    <div class="row">
        <div class="col-lg-12">
            {*$bookFieldSettings|var_dump*}
            {if isset($bookFieldSettings) and $bookFieldSettings != null}
                <div class="card">
                    <div class="card-body">
                        <form action="{$routes->getRouteString("bookVisibleFieldsForPublic",[])}" method="post" class="row">
                            {foreach from=$bookFieldSettings->getBookFields() item=value key=key}
                                <div class="col-lg-2 col-md-4 mb-3">
                                    <div class="form-group">
                                        <label class="control-label">{t}{$value->getTitle()}{/t}</label><br>
                                        <label class="switch switch-sm">
                                            <input type="checkbox" name="bookFields[{$key}]"{if !$value->isEditable()} class="readonly" {/if}value="1"{if $value->isVisible()} checked{/if}{if !$value->isEditable()} onclick="return false;"{/if}>
                                        </label>
                                    </div>
                                </div>
                            {/foreach}
                            <div class="col-lg-12">
                                <button class="btn btn-primary m-t-20 pull-right" id="save-visible-fields"{if $isDemoMode === true} disabled{/if}>
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
{block name=footerPageJs append}
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/jquery/jquery-ui.min.js"></script>
{/block}
{block name=footerCustomJs append}
    {if $isDemoMode === false}
    <script>
        $(document).ready(function () {
            var bookVisibleFieldsForPublicUrl = '{$routes->getRouteString("bookVisibleFieldsForPublic",[])}';
            $('#save-visible-fields').on('click', function (e) {
                e.preventDefault();
                var form = $(this).closest('form');
                $.ajax({
                    dataType: 'json',
                    method: 'POST',
                    data: form.serialize(),
                    url: bookVisibleFieldsForPublicUrl,
                    beforeSend: function (data) {
                        app.card.loading.start('.card');
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
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    },
                    complete: function (data) {
                        app.card.loading.finish('.card');
                    }
                });

            })
        });
    </script>
    {/if}
{/block}