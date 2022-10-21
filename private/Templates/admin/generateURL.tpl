{extends file='admin/admin.tpl'}
{block name=title}{t}Generate Book Url{/t}{/block}
{block name=headerCss append}
    <link href="{$resourcePath}assets/css/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet"/>
{/block}
{block name=content}
    {if $isDemoMode === true}
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-info text-center">In the demo version you can't generate book urls.</div>
            </div>
        </div>
    {/if}
    <div class="card">
        <div class="card-body">
            <form method="post">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="urlFormat" class="control-label">{t}URL Format{/t} <i class="icon-info" data-toggle="tooltip" data-placement="top" title="{t}Select URL format to generate book URLs.{/t}"></i></label>
                            <select name="urlFormat" class="custom-select form-control" id="urlFormat"{if $isDemoMode === true} disabled{/if}>
                                <option value="1">{t}Book Name{/t}</option>
                                <option value="3">{t}Book Name - ISBN13{/t}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>{t}Override URL's{/t} <i class="icon-info" data-toggle="tooltip" data-placement="top" title="{t}Select this option to override all book URLs by using selected URL format. Otherwise empty URLs will be replaced only.{/t}"></i></label><br>
                            <label class="switch switch-sm">
                                <input type="checkbox" name="override" value="1"{if $isDemoMode === true} disabled{/if}>
                            </label>
                        </div>
                    </div>
                    <div class="col-lg-12 text-right">
                        <button class="btn btn-primary" id="gen-url"{if $isDemoMode === true} disabled{/if}>{t}Generate URL's{/t}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
{/block}
{block name=footerPageJs append}
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/jasnyupload/fileinput.min.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/jquery-validate/jquery.validate.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/bootstrap-select/bootstrap-select.js"></script>
{/block}
{block name=footerCustomJs append}
    <script>
        {*var themeActivateUrl = '{$routes->getRouteString("themeActivate")}';
        $('.activateTheme').on('click', function (e) {
            e.preventDefault();
            var theme = $(this).attr('data-theme');
            var _this = $(this);
            $.ajax({
                dataType: 'json',
                method: 'POST',
                data: 'activeThemeName=' + theme,
                url: themeActivateUrl,
                beforeSend: function (data) {
                    app.card.loading.start($(_this).closest('.card'));
                },
                success: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else {
                            app.notification('success', data.success);
                            $(".activateTheme").toggleClass('btn-light btn-success disabled').not(_this).text('{t}Activate{/t}');
                            //$(".activateTheme")
                            $(_this).text('{t}Activated{/t}');
                        }
                    }
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                },
                complete: function (data) {
                    app.card.loading.finish($(_this).closest('.card'));
                }
            });
        });*}
    </script>
{/block}