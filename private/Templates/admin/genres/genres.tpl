{extends file='admin/admin.tpl'}
{block name=title}{t}Genres{/t}{/block}
{block name=toolbar}
    <div class="heading-elements">
        <a href="{$routes->getRouteString("genreCreate")}" class="btn btn-success">
            <span class="btn-icon"><i class="fas fa-plus"></i></span> {t}Add Genre{/t}
        </a>
    </div>
{/block}
{block name=content}
    <div class="row">
        <div class="col-md-12">
            <div class="card" id="genreList">
                {include "admin/genres/genre-list.tpl"}
            </div>
        </div>
    </div>
{/block}
{block name=footerCustomJs append}
    <script>
        $(document).ready(function () {
            $(document).on('click', '.delete-genre', function (e) {
                var url = $(this).attr('data-url');
                var row = $(this).closest('tr');
                $.ajax({
                    dataType: 'json',
                    type: 'POST',
                    url: url,
                    beforeSend: function () {
                        app.card.loading.start('#genreList');
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
                        app.card.loading.finish('#genreList');
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
                        app.card.loading.start($("#genreList"));
                    },
                    success: function (data) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                $("#genreList").html(data.html);
                            }
                        }
                    },
                    complete: function () {
                        app.card.loading.finish($("#genreList"));
                    },
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    }
                });
            });
        });
    </script>
{/block}