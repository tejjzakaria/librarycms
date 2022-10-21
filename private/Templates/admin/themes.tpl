{extends file='admin/admin.tpl'}
{block name=title}{t}Themes{/t}{/block}
{block name=headerCss append}
    <link href="{$resourcePath}assets/css/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet"/>
{/block}
{block name=content}
    <div class="row">
        {if isset($themes) and count($themes) > 0}
            {foreach $themes as $themeName => $theme}
                <div class="col-lg-3 col-md-4">
                    <div class="card">
                        <img class="card-img-top" src="{$resourcePath}{$theme->getLocation()}{$theme->getCover()}" alt="{$theme->getTitle()}">
                        <div class="card-body">
                            <h5 class="text-center mb-3">{$theme->getTitle()}</h5>
                            <p class="mb-1"><strong class="pr-1">{t}Author{/t}:</strong>
                                <a href="http://kaasoft.pro">{$theme->getAuthor()}</a>
                            </p>
                            <p class="mb-1"><strong class="pr-1">{t}Version{/t}:</strong> {$theme->getVersion()}</p>
                            {if isset($theme->getColorSchemas()) and count($theme->getColorSchemas()) > 0}
                                <p class="mb-1"><strong>{t}Color Schema{/t}:</strong></p>
                                <select name="activeColorSchema" id="changeColorSchemas" class="form-control custom-select">
                                    {foreach from=$theme->getColorSchemas() key="key" item="schema"}
                                        <option value="{$key}"{if $key == $theme->getActiveColorSchema()} selected{/if}>{$schema->getTitle()}</option>
                                    {/foreach}
                                </select>
                            {/if}
                            {*<p class="mb-1">
                                <strong>{t}Quantity Ads Place{/t}:</strong> {count($theme->getAdvertisementLocations())}
                            </p>
                            <p class="mb-1">
                                <strong>{t}Quantity Menu Place{/t}:</strong> {count($theme->getMenuLocations())}
                            </p>*}
                            <a href="#" class="btn btn-block {if strcmp($themeName,$activeTheme) === 0}btn-success disabled{else}btn-light{/if} mt-3 activateTheme" data-theme="{$themeName}">{if strcmp($themeName,$activeTheme) === 0}{t}Activated{/t}{else}{t}Activate{/t}{/if}</a>
                        </div>
                    </div>
                </div>
            {/foreach}
        {/if}
    </div>
{/block}
{block name=footerPageJs append}
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/jasnyupload/fileinput.min.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/jquery-validate/jquery.validate.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/bootstrap-select/bootstrap-select.js"></script>
{/block}
{block name=footerCustomJs append}
    <script>
        var colorSchemaActivateUrl = '{$routes->getRouteString("colorSchemaActivate")}';
        $('#changeColorSchemas').on('change', function (e) {
            e.preventDefault();
            var schema = $(this).val();
            var _this = $(this);
            $.ajax({
                dataType: 'json',
                method: 'POST',
                data: 'activeColorSchema=' + schema,
                url: colorSchemaActivateUrl,
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
        });


        var themeActivateUrl = '{$routes->getRouteString("themeActivate")}';
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
        });
    </script>
{/block}