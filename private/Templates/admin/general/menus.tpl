{extends file='admin/admin.tpl'}
{block name=title}{t}Menus{/t}{/block}
{block name=toolbar}
    <a href="{$routes->getRouteString("menuCreate")}" class="btn btn-success btn-icon-fixed">
        <span class="btn-icon"><i class="fas fa-plus"></i></span> {t}Add Menu{/t}
    </a>
{/block}
{block name=content}
    <div class="row">
        <div class="col-md-12">
            <div class="card" id="menuList">
                {include "admin/general/menu-list.tpl"}
            </div>
        </div>
        {include "admin/general/pagination.tpl"}
    </div>
{/block}
{block name=footerPageJs append}{/block}
{block name=footerCustomJs append}
    <script>
        $(document).ready(function () {
            $(document).on('click', '.delete-menu', function (e) {
                var url = $(this).attr('data-url');
                var row = $(this).closest('tr');
                $.ajax({
                    dataType: 'json',
                    type: 'POST',
                    url: url,
                    beforeSend: function () {
                        app.card.loading.start('#menuList');
                    },
                    success: function (data) {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else {
                            app.notification('success', data.success);
                            $(row).remove();
                        }
                    },
                    complete: function () {
                        app.card.loading.finish('#menuList');
                    },
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    }
                });
            });
            $(document).on('click', '.ajax-page', function (e) {
                e.preventDefault();
                $.ajax({
                    dataType: 'json',
                    url: $(this).attr('href'),
                    beforeSend: function () {
                        app.card.loading.start($("#menuList"));
                    },
                    success: function (data) {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else {
                            $("#menuList").html(data.html);
                        }
                    },
                    complete: function () {
                        app.card.loading.finish($("#menuList"));
                    },
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    }
                });
            });
        });
    </script>
{/block}