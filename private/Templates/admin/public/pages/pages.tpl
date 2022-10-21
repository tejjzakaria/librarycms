{extends file='admin/admin.tpl'}
{block name=title}{t}Pages{/t}{/block}
{block name=toolbar}
    <div class="heading-elements">
        <a href="{$routes->getRouteString("pageCreate")}" class="btn btn-success btn-icon-fixed" target="_blank">
            <i class="fas fa-plus"></i> {t}Add Page{/t}
        </a>
    </div>
{/block}
{block name=headerCss append}
    <link href="{$resourcePath}assets/css/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet"/>
{/block}
{block name=content}
    <div class="row">
        <div class="col-md-12">
            <div class="card" id="pageList">
                {include "admin/public/pages/page-list.tpl"}
            </div>
        </div>
    </div>
{/block}
{block name=footerPageJs append}
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/bootstrap-select/bootstrap-select.js"></script>
{/block}
{block name=footerCustomJs append}
    <script>
        $(document).ready(function () {
            $(document).on('click', '.delete-page', function (e) {
                var url = $(this).attr('data-url');
                var row = $(this).closest('tr');
                $.ajax({
                    dataType: 'json',
                    type: 'POST',
                    url: url,
                    beforeSend: function () {
                        app.card.loading.start('#pageList');
                    },
                    success: function (data) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                app.notification('success', data.success);
                                $(row).remove();
                            }
                        }
                    },
                    complete: function () {
                        app.card.loading.finish('#pageList');
                    },
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    }
                });
            });
            $(document).on('change', '#page-sort', function (e) {
                var url = '{$routes->getRouteString("pageListView")}';
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: url,
                    data: $('#page-sort, #countPerPage').serialize() + '&sortOrder=' + $('option:selected', '#page-sort').attr('data-order'),
                    beforeSend: function () {
                        app.card.loading.start('#pageList');
                    },
                    success: function (data) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                $('#pageList').html(data.html);
                                app.bootstrap_select();
                                app.tooltip_popover();
                            }
                        }
                    },
                    complete: function () {
                        app.card.loading.finish('#pageList');
                    },
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    }
                });
            });
            $(document).on('change', '#countPerPage', function (e) {
                var url = '{$routes->getRouteString("pageListView")}';
                $.ajax({
                    type: "POST",
                    url: url,
                    data: $('#page-sort, #countPerPage').serialize() + '&sortOrder=' + $('option:selected', '#page-sort').attr('data-order'),
                    dataType: 'json',
                    beforeSend: function () {
                        app.card.loading.start('#pageList');
                    },
                    success: function (data) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                $('#pageList').html(data.html);
                                app.bootstrap_select();
                                app.tooltip_popover();
                            }
                        }
                    },
                    complete: function () {
                        app.card.loading.finish('#pageList');
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
                        app.card.loading.start('#pageList');
                    },
                    success: function (data) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                $("#pageList").html(data.html);
                            }
                        }
                    },
                    complete: function () {
                        app.card.loading.finish('#pageList');
                    },
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    }
                });
            });
        });
    </script>
{/block}