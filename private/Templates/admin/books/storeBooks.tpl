{extends file='admin/admin.tpl'}
{if $store != null}
    {assign var=storeName value=$store->getName()}
{else}
    {assign var=storeName value=''}
{/if}
{block name=title}{t name=$storeName}Store %1 Books{/t}{/block}
{block name=toolbar}
{/block}
{block name=headerCss append}
    <link href="{$resourcePath}assets/css/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet"/>
{/block}
{block name=content}
    <div class="row">
        <div class="col-md-12">
            <div class="card" id="books-block">
                {include 'admin/books/storeBooks-list.tpl'}
            </div>
        </div>
    </div>
{/block}
{block name=footerPageJs append}
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/bootstrap-select/bootstrap-select.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/select2/select2.full.min.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/moment/moment.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>
{/block}
{block name=footerCustomJs append}
    <script>
        var booksDeleteUrl = '{$routes->getRouteString("bookBulkDelete",[])}';
        $(document).on('click', '.delete-books', function (e) {
            e.preventDefault();
            var ids = $('.books-id:checked').serialize();
            if ($('.books-id:checked').length > 0) {
                $.ajax({
                    dataType: 'json',
                    type: 'POST',
                    url: booksDeleteUrl,
                    data: ids,
                    beforeSend: function () {
                        app.card.loading.start('#books-block');
                    },
                    success: function (data) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                app.notification('success', data.success);
                                $('.books-id:checked').closest('tr').remove();
                                $('.header-cell').show();
                                $('.header-bulk-actions').addClass('d-none').hide();
                            }
                        }
                    },
                    complete: function () {
                        app.card.loading.finish('#books-block');
                    },
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    }
                });
            }
        });
        $(document).on('click', '#select-all-books', function (e) {
            $('.books-id').not(this).not(':disabled').prop('checked', this.checked);
            if ($('.books-id:checked').length > 0) {
                $('.header-cell').hide();
                $('.header-bulk-actions').removeClass('d-none').show();
            } else {
                $('.header-cell').show();
                $('.header-bulk-actions').addClass('d-none').hide();
            }
        });
        $(document).on('change', '.books-id', function (e) {
            if ($(this).is(':checked')) {
                $('.header-cell').hide();
                $('.header-bulk-actions').removeClass('d-none').show();
            } else {
                if (!$('.books-id:checked').length > 0) {
                    $('.header-cell').show();
                    $('.header-bulk-actions').addClass('d-none').hide();
                }
            }
        });
        $(document).on('click', '.delete-book', function (e) {
            var url = $(this).attr('data-url');
            var row = $(this).closest('tr');
            $.ajax({
                dataType: 'json',
                type: 'POST',
                url: url,
                beforeSend: function () {
                    app.card.loading.start('#books-block');
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
                    app.card.loading.finish('#books-block');
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        });
        $(document).on('change', '#countPerPage', function (e) {
            var url = '{$routes->getRouteString("storeBooksView")}';
            $.ajax({
                type: "POST",
                url: url.replace('[storeId]', {if $store != null}{$store->getId()}{/if}),
                data: $('#books-sort, #countPerPage').serialize() + '&sortOrder=' + $('option:selected', '#books-sort').attr('data-order'),
                dataType: 'json',
                beforeSend: function () {
                    app.card.loading.start('#books-block');
                },
                success: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else {
                            $('#books-block').html(data.html);
                            app.bootstrap_select();
                            app.tooltip_popover();
                            app.checkbox_radio();
                        }
                    }
                },
                complete: function () {
                    app.card.loading.finish('#books-block');
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        });
        $(document).on('change', '#books-sort', function (e) {
            var url = '{$routes->getRouteString("storeBooksView")}';
            $.ajax({
                type: "POST",
                url: url.replace('[storeId]', {if $store != null}{$store->getId()}{/if}),
                data: $('#books-sort, #countPerPage').serialize() + '&sortOrder=' + $('option:selected', '#books-sort').attr('data-order'),
                dataType: 'json',
                beforeSend: function () {
                    app.card.loading.start('#books-block');
                },
                success: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else {
                            $('#books-block').html(data.html);
                            app.bootstrap_select();
                            app.tooltip_popover();
                            app.checkbox_radio();
                        }
                    }
                },
                complete: function () {
                    app.card.loading.finish('#books-block');
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        });
        $(document).on('click', '.ajax-page', function (e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                data: $('#books-sort, #countPerPage, #searchText').serialize() + '&sortOrder=' + $('option:selected', '#books-sort').attr('data-order'),
                dataType: 'json',
                url: $(this).attr('href'),
                beforeSend: function () {
                    app.card.loading.start('#books-block');
                },
                success: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else {
                            $('#books-block').html(data.html);
                            app.bootstrap_select();
                            app.tooltip_popover();
                            app.checkbox_radio();
                        }
                    }
                },
                complete: function () {
                    app.card.loading.finish('#books-block');
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        });
    </script>
{/block}