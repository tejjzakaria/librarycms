{extends file='public.tpl'}
{block name=metaTitle}{t}My Books{/t} | {$siteViewOptions->getOptionValue("siteName")}{/block}
{block name=metaDescription}{/block}
{block name=metaKeywords}{/block}
{block name=headerCss append}{/block}
{block name=content}
    <div class="page-heading">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title text-center">
                        <h1>{t}My Books{/t}</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section class="user-books">
        <div class="container">
            <div class="row">
                <div class="col-md-12" id="books-block">
                    {include 'user/userBooks-list.tpl'}
                </div>
            </div>
        </div>
    </section>
{/block}
{block name=footerJs append}{/block}
{block name=customJs append}
    <script>
        {if $siteViewOptions->getOptionValue("enableBookIssueByUser")}
        $(document).on('click', '.book-is-lost', function (e) {
            e.preventDefault();
            var $this = $(this);
            var url = $this.attr('href').replace("[isLost]", 'true');
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: url,
                beforeSend: function () {
                    app.preloader.start('#books-block');
                },
                success: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else {
                            $this.closest('tr').addClass('book-lost');
                            $this.closest('tr').find('.action-btn a,.action-btn .dropdown').remove();
                            $this.closest('tr').find('.action-btn').appendTo('<span class="badge badge-danger">{t}lost{/t}</span>');
                        }
                    }
                },
                complete: function () {
                    app.preloader.finish('#books-block');
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        });
        $(document).on('click', '.return-book', function (e) {
            e.preventDefault();
            var $this = $(this);
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: $this.attr('href'),
                beforeSend: function () {
                    app.preloader.start('#books-block');
                },
                success: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else {
                            app.notification('success', '{t}Book successfully returned{/t}');
                            $this.tooltip('hide').remove();
                            $.ajax({
                                type: "POST",
                                url: '{$routes->getRouteString("userBooksView")}',
                                data: $('#books-sort, #countPerPage').serialize() + '&sortOrder=' + $('option:selected', '#books-sort').attr('data-order'),
                                dataType: 'json',
                                beforeSend: function () {
                                    app.preloader.start('#books-block');
                                },
                                success: function (data) {
                                    if (data.redirect) {
                                        window.location.href = data.redirect;
                                    } else {
                                        if (data.error) {
                                            app.notification('error', data.error);
                                        } else {
                                            $this.tooltip('hide').remove();
                                            $('#books-block').html(data.html);
                                        }
                                    }
                                },
                                complete: function () {
                                    app.preloader.finish('#books-block');
                                },
                                error: function (jqXHR, exception) {
                                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                                }
                            });
                        }
                    }
                },
                complete: function () {
                    app.preloader.finish('#books-block');
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        });
        {/if}
        $(document).on('change', '#countPerPage', function (e) {
            var url = '{$routes->getRouteString("userBooksView")}';
            $.ajax({
                type: "POST",
                url: url,
                data: $('#books-sort, #countPerPage').serialize() + '&sortOrder=' + $('option:selected', '#books-sort').attr('data-order'),
                dataType: 'json',
                beforeSend: function () {
                    app.preloader.start('#books-block');
                },
                success: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else {
                            $('#books-block').html(data.html);
                        }
                    }
                },
                complete: function () {
                    app.preloader.finish('#books-block');
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        });
        $(document).on('change', '#books-sort', function (e) {
            var url = '{$routes->getRouteString("userBooksView")}';
            $.ajax({
                type: "POST",
                url: url,
                data: $('#books-sort, #countPerPage').serialize() + '&sortOrder=' + $('option:selected', '#books-sort').attr('data-order'),
                dataType: 'json',
                beforeSend: function () {
                    app.preloader.start('#books-block');
                },
                success: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else {
                            $('#books-block').html(data.html);
                        }
                    }
                },
                complete: function () {
                    app.preloader.finish('#books-block');
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
                data: $('#books-sort, #countPerPage').serialize() + '&sortOrder=' + $('option:selected', '#books-sort').attr('data-order'),
                dataType: 'json',
                url: $(this).attr('href'),
                beforeSend: function () {
                    app.preloader.start('#books-block');
                },
                success: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else {
                            $('#books-block').html(data.html);
                        }
                    }
                },
                complete: function () {
                    app.preloader.finish('#books-block');
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        });
    </script>
{/block}