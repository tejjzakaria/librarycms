{extends file='admin/admin.tpl'}
{block name=title}{t}Requested Books{/t}{/block}
{block name=toolbar}
    <div class="heading-elements">
        <a class="btn btn-success btn-icon-fixed" data-toggle="collapse" href="#request-book-block" aria-expanded="false" aria-controls="collapseExample">
            <span class="btn-icon"><i class="fas fa-plus"></i></span> {t}Request Book{/t}
        </a>
    </div>
{/block}
{block name=headerCss append}
    <link href="{$resourcePath}assets/css/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet"/>
{/block}
{block name=content}
    <div class="row">
        <div class="col-md-12">
            <div class="collapse" id="request-book-block">
                <div class="card">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width: 50%;">{t}Book{/t}</th>
                                <th style="width: 50%;">{t}Notes{/t}</th>
                                <th style="width: 65px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select name="bookIds[]" id="bookId" class="form-control"></select>
                                </td>
                                <td>
                                    <textarea name="notes" id="note" cols="30" rows="2" class="form-control"></textarea>
                                </td>
                                <td class="text-center">
                                    <a href="#" class="btn btn-outline-success no-border" id="add-book" data-container="body" data-toggle="tooltip" title="{t}Add Another Book{/t}"><i class="fas fa-plus" aria-hidden="true"></i></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <form action="{$routes->getRouteString("requestCreate")}" id="request-form">
                        {if isset($user)}
                            <input type="hidden" name="userId" value="{$user->getId()}">
                        {/if}
                        <input type="hidden" name="notes" id="notes">
                        <table class="table table-hover d-none" id="request-result">
                            <thead>
                                <tr>
                                    <th>{t}Books{/t}</th>
                                    <th style="width: 150px;">{t}Publisher{/t}</th>
                                    <th style="width: 150px;">{t}Publishing Year{/t}</th>
                                    <th style="width: 150px;">{t}ISBN10/13{/t}</th>
                                    <th style="width: 65px;">{t}Delete{/t}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <div class="row">
                            <div class="col-lg-12">
                                <a href="#" class="btn btn-success pull-right {if $activeLanguage->isRTL()}ml-2{else}mr-2{/if} mb-3" id="request-book">{t}Request{/t}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card" id="requests-card">
                {include 'admin/requests/requests-list.tpl'}
            </div>
        </div>
    </div>
{/block}
{block name=footerPageJs append}
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/jquery-validate/jquery.validate.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/select2/select2.full.min.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/bootstrap-select/bootstrap-select.js"></script>
{/block}
{block name=footerCustomJs append}
    <script>
        $(document).on('click', '.request-issue-book-block', function (e) {
            e.stopPropagation();
        });
        var bookSearchUrl = '{$routes->getRouteString("bookSearch",[])}';
        $('#bookId').select2({
            ajax: {
                url: function () {
                    return bookSearchUrl;
                },
                dataType: 'json',
                type: 'POST',
                delay: 250,
                data: function (params) {
                    return {
                        searchText: params.term
                    };
                },
                error: function (jqXHR, exception) {
                    if (jqXHR.statusText == 'abort') {
                        return;
                    }
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                },
                processResults: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else {
                            return {
                                results: data.books
                            };
                        }
                    }
                },
                cache: true
            },
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 2,
            templateResult: formatBook,
            templateSelection: formatBookSelection
        });
        function formatBook(book) {
            if (book.loading) return book.text;
            var i, lastIndex, markup = "<div class='select-book'>";
            markup += "<div class='select-book-cover'>";
            if (book.cover) {
                markup += '<img src="' + book.cover.webPath + '" class="img-fluid">';
            } else {
                markup += '<img src="{$siteViewOptions->getOptionValue("noBookImageFilePath")}" class="img-fluid">';
            }
            markup += "</div>";
            markup += "<div class='select-book-info'>";
            markup += "<div class='select-book-title'>" + book.title + "";
            if (book.publishingYear) {
                markup += " <span>(" + book.publishingYear + ")</span>";
            }
            markup += "</div>";
            if (book.publisher) {
                markup += "<div class='select-book-publisher'><strong>{t}Publisher:{/t}</strong> " + book.publisher.name + "</div>";
            }
            if (book.ISBN10) {
                markup += "<div class='select-book-isbn'><strong>{t}ISBN10:{/t}</strong> " + book.ISBN10 + "</div>";
            } else if (book.ISBN13) {
                markup += "<div class='select-book-isbn'><strong>{t}ISBN13:{/t}</strong> " + book.ISBN13 + "</div>";
            }
            if (book.genres.length > 0) {
                markup += "<div class='select-book-genre'><strong>{t}Genres:{/t}</strong> ";
                lastIndex = book.genres.length - 1;
                for (i = 0; i < book.genres.length; i++) {
                    markup += book.genres[i].name;
                    if (lastIndex != i) {
                        markup += ", ";
                    }
                }
                markup += "</div>";
            }
            if (book.authors.length > 0) {
                markup += "<div class='select-book-author'><strong>{t}Authors:{/t}</strong> ";
                lastIndex = book.authors.length - 1;
                for (i = 0; i < book.authors.length; i++) {
                    if (book.authors[i].firstName) {
                        var text = book.authors[i].firstName + ' ' + book.authors[i].lastName;
                    } else {
                        text = book.authors[i].lastName;
                    }
                    markup += text;
                    if (lastIndex != i) {
                        markup += ", ";
                    }
                }
                markup += "</div>";
            }
            markup += "</div></div>";
            return markup;
        }
        function formatBookSelection(book) {
            return book.title || book.text;
        }
        $('#add-book').on('click', function (e) {
            e.preventDefault();
            var book = $("#bookId");
            var bookVal = book.val();
            var note = $("#note");
            var noteVal = note.val();
            if (!bookVal) {
                app.notification('error', '{t}Book is required{/t}');
                return false;
            }
            if (bookVal) {
                $('#notes').val(noteVal);
                $('#request-result').removeClass('d-none').slideDown();
                var data = $(book).select2('data');
                var bookId = data[0].id;
                var title = data[0].title;
                if (data[0].publisher) {
                    var publisherName = data[0].publisher.name;
                }
                var publishingYear = data[0].publishingYear;
                var ISBN10 = data[0].ISBN10;
                var ISBN13 = data[0].ISBN13;

                var markup = "<tr>";
                markup += "<td>";
                markup += title;
                markup += "</td>";
                markup += "<td>";
                if (publisherName) {
                    markup += publisherName;
                }
                markup += "</td>";
                markup += "<td>";
                if (publishingYear) {
                    markup += publishingYear;
                }
                markup += "</td>";
                markup += "<td>";
                if (ISBN10) {
                    markup += ISBN10;
                } else if (ISBN13) {
                    markup += ISBN13;
                }
                markup += "</td>";
                markup += "<td class='text-center'>";
                markup += "<input type='hidden' name='bookIds[]' value=" + bookId + ">";
                markup += "<a href='#' class='btn btn-default remove-book' title='{t}Delete Book{/t}'><i class='fa fa-remove'></i></a>";
                markup += "</td>";
                markup += "</tr>";
                $('#request-result tbody').append(markup);
                $('#bookId').empty().trigger('change');
            }
        });
        $('#request-book').on('click', function (e) {
            e.preventDefault();
            var formData, form = $('#request-form');
            var bookVal = $("#bookId").val();
            if ($('#request-result tbody tr').length < 1) {
                if (!bookVal) {
                    app.notification('error', '{t}Book is required{/t}');
                    return false;
                }
                formData = $('#bookId, #note').serialize();
            } else {
                formData = $(form).serialize();
            }
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: formData,
                url: $(form).attr('action'),
                beforeSend: function () {
                    app.card.loading.start('#request-book-block .card');
                },
                success: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else {
                            $('#bookId').empty().trigger('change');
                            $('#note').val('');
                            $('#request-result').addClass('d-none').find('tbody tr').remove();
                            var url = '{$routes->getRouteString("requestListView")}';
                            $.ajax({
                                type: "POST",
                                url: url,
                                data: $('#books-sort, #countPerPage').serialize() + '&sortOrder=' + $('option:selected', '#books-sort').attr('data-order'),
                                dataType: 'json',
                                beforeSend: function () {
                                    app.card.loading.start('#requests-card');
                                },
                                success: function (data) {
                                    if (data.redirect) {
                                        window.location.href = data.redirect;
                                    } else {
                                        if (data.error) {
                                            app.notification('error', data.error);
                                        } else {
                                            $('#requests-card').html(data.html);
                                            app.bootstrap_select();
                                            app.tooltip_popover();
                                        }
                                    }
                                },
                                complete: function () {
                                    app.card.loading.finish('#requests-card');
                                },
                                error: function (jqXHR, exception) {
                                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                                }
                            });
                        }
                    }
                },
                complete: function () {
                    app.card.loading.finish('#request-book-block .card');
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        });
        $(document).on('click', '.remove-book', function (e) {
            e.preventDefault();
            $(this).closest('tr').remove();
            if ($('#request-result tbody tr').length < 1) {
                $('#request-result').addClass('d-none');
            }
        });
        $(document).on('click', '.accepted-book', function (e) {
            e.preventDefault();
            var $this = $(this);
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: $this.attr('href'),
                beforeSend: function () {
                    app.card.loading.start('#requests-card');
                },
                success: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else {
                            $this.closest('tr').find('.request-status .badge').removeClass().addClass('badge badge-success').text('{t}Accepted{/t}');
                        }
                    }
                },
                complete: function () {
                    app.card.loading.finish('#requests-card');
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        });
        $(document).on('click', '.rejected-book', function (e) {
            e.preventDefault();
            var $this = $(this);
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: $this.attr('href'),
                beforeSend: function () {
                    app.card.loading.start('#requests-card');
                },
                success: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else {
                            $this.closest('tr').find('.request-status .badge').removeClass().addClass('badge badge-danger').text('{t}Rejected{/t}');
                        }
                    }
                },
                complete: function () {
                    app.card.loading.finish('#requests-card');
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        });
        $(document).on('click', '.issue-book', function (e) {
            e.preventDefault();
            var $this = $(this);
            var btn = $(this).closest('.dropdown');
            var data = $this.closest('.request-issue-book-block').find('select').serialize();
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: data,
                url: $this.attr('data-url'),
                beforeSend: function () {
                    app.card.loading.start($this.closest('.card'));
                },
                success: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else {
                            app.notification('success', '{t}Book is successfully issued.{/t}');
                            $this.closest('tr').find('.request-status .badge').removeClass().addClass('badge badge-success').text('{t}Accepted{/t}');
                            btn.remove();
                            $('.tooltip.show').remove();
                        }
                    }
                },
                complete: function () {
                    app.card.loading.finish($this.closest('.card'));
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        });
        $(document).on('click', '.delete-request', function (e) {
            var url = $(this).attr('data-url');
            var row = $(this).closest('tr');
            $.ajax({
                dataType: 'json',
                type: 'POST',
                url: url,
                beforeSend: function () {
                    app.card.loading.start('#requests-card');
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
                    app.card.loading.finish('#requests-card');
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        });
        $(document).on('change', '#countPerPage', function (e) {
            var url = '{$routes->getRouteString("requestListView")}';
            $.ajax({
                type: "POST",
                url: url,
                data: $('#books-sort, #countPerPage').serialize() + '&sortOrder=' + $('option:selected', '#books-sort').attr('data-order'),
                dataType: 'json',
                beforeSend: function () {
                    app.card.loading.start('#requests-card');
                },
                success: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else {
                            $('#requests-card').html(data.html);
                            app.bootstrap_select();
                            app.tooltip_popover();
                        }
                    }
                },
                complete: function () {
                    app.card.loading.finish('#requests-card');
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        });
        $(document).on('change', '#books-sort', function (e) {
            var url = '{$routes->getRouteString("requestListView")}';
            $.ajax({
                type: "POST",
                url: url,
                data: $('#books-sort, #countPerPage').serialize() + '&sortOrder=' + $('option:selected', '#books-sort').attr('data-order'),
                dataType: 'json',
                beforeSend: function () {
                    app.card.loading.start('#requests-card');
                },
                success: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else {
                            $('#requests-card').html(data.html);
                            app.bootstrap_select();
                            app.tooltip_popover();
                        }
                    }
                },
                complete: function () {
                    app.card.loading.finish('#requests-card');
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
                    app.card.loading.start('#requests-card');
                },
                success: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else {
                            $('#requests-card').html(data.html);
                            app.bootstrap_select();
                            app.tooltip_popover();
                        }
                    }
                },
                complete: function () {
                    app.card.loading.finish('#requests-card');
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        });
    </script>
{/block}