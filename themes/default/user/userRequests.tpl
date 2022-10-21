{extends file='public.tpl'}
{block name=metaTitle}{t}Requested Books{/t} | {$siteViewOptions->getOptionValue("siteName")}{/block}
{block name=metaDescription}{/block}
{block name=metaKeywords}{/block}
{block name=headerCss append}
    <link rel="stylesheet" href="{$themePath}resources/css/select2.min.css">
{/block}
{block name=content}
    <div class="page-heading">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title text-center">
                        <h1>{t}My Requested Book{/t}</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section class="user-books">
        <div class="container">
            <div class="row">
                <div class="col-md-12" id="requests-card">
                    {include 'user/userRequest-list.tpl'}
                </div>
            </div>
        </div>
    </section>
{/block}
{block name=footerJs append}
    <script src="{$themePath}resources/js/jquery.validate.js"></script>
    <script src="{$themePath}resources/js/select2.full.min.js"></script>
{/block}
{block name=customJs append}
    <script>
        var bookSearchUrl = '{$routes->getRouteString("bookSearch",[])}';
        function bookSearch() {
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
                        if (jqXHR.statusText=='abort') {
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
        }
        bookSearch();
        function formatBook(book) {
            if (book.loading) return book.text;
            var i,lastIndex,markup = "<div class='select-book'>";
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
            if (book.genres && book.genres.length > 0) {
                markup += "<div class='select-book-genre'><strong>{t}Genres:{/t}</strong> ";
                lastIndex = book.genres.length - 1;
                for(i=0; i<book.genres.length; i++){
                    markup +=  book.genres[i].name;
                    if(lastIndex != i) {
                        markup +=  ", ";
                    }
                }
                markup += "</div>";
            }
            if (book.authors && book.authors.length > 0) {
                markup += "<div class='select-book-author'><strong>{t}Authors:{/t}</strong> ";
                lastIndex = book.authors.length - 1;
                for(i=0; i<book.authors.length; i++){
                    if(book.authors[i].firstName) {
                        var text = book.authors[i].firstName + ' ' + book.authors[i].lastName;
                    } else {
                        text = book.authors[i].lastName;
                    }
                    markup +=  text;
                    if(lastIndex != i) {
                        markup +=  ", ";
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
        $(document).on('click', '#add-book', function (e) {
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
                if (data[0].publisher){
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
                if (publishingYear) {
                    markup += publishingYear;
                }
                markup += "</td>";
                markup += "<td>";
                if (publisherName) {
                    markup += publisherName;
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
                markup += "<a href='#' class='btn btn-outline-info btn-sm no-border remove-book' title='{t}Delete Book{/t}'><i class='far fa-trash-alt'></i></a>";
                markup += "</td>";
                markup += "</tr>";
                $('#request-result tbody').append(markup);
                $('#bookId').empty().trigger('change');
            }
        });
        $(document).on('click', '#request-book', function (e) {
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
                    app.preloader.start('#request-book-block');
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
                            var url = '{$routes->getRouteString("userRequestListView")}';
                            $.ajax({
                                type: "POST",
                                url: url,
                                data: $('#books-sort, #countPerPage').serialize() + '&sortOrder=' + $('option:selected', '#books-sort').attr('data-order'),
                                dataType: 'json',
                                beforeSend: function () {
                                    $('#request-book-block').collapse('hide');
                                    app.preloader.start('#requests-card');
                                },
                                success: function (data) {
                                    if (data.redirect) {
                                        window.location.href = data.redirect;
                                    } else {
                                        if (data.error) {
                                            app.notification('error', data.error);
                                        } else {
                                            $('#requests-card').html(data.html);
                                            bookSearch();
                                            app.bootstrap_tooltip();
                                        }
                                    }
                                },
                                complete: function () {
                                    app.preloader.finish('#requests-card');
                                },
                                error: function (jqXHR, exception) {
                                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                                }
                            });
                        }
                    }
                },
                complete: function () {
                    app.preloader.finish('#request-book-block');
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
        $(document).on('click', '.delete-request', function (e) {
            var url = $(this).attr('data-url');
            var row = $(this).closest('tr');
            $.ajax({
                dataType: 'json',
                type: 'POST',
                url: url,
                beforeSend: function () {
                    app.preloader.start('#requests-card');
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
                    app.preloader.finish('#requests-card');
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        });
        $(document).on('change', '#countPerPage', function (e) {
            var url = '{$routes->getRouteString("userRequestListView")}';
            $.ajax({
                type: "POST",
                url: url,
                data: $('#books-sort, #countPerPage').serialize() + '&sortOrder=' + $('option:selected', '#books-sort').attr('data-order'),
                dataType: 'json',
                beforeSend: function () {
                    app.preloader.start('#requests-card');
                },
                success: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else {
                            $('#requests-card').html(data.html);
                            app.bootstrap_tooltip();
                            bookSearch();
                        }
                    }
                },
                complete: function () {
                    app.preloader.finish('#requests-card');
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        });
        $(document).on('change', '#books-sort', function (e) {
            var url = '{$routes->getRouteString("userRequestListView")}';
            $.ajax({
                type: "POST",
                url: url,
                data: $('#books-sort, #countPerPage').serialize() + '&sortOrder=' + $('option:selected', '#books-sort').attr('data-order'),
                dataType: 'json',
                beforeSend: function () {
                    app.preloader.start('#requests-card');
                },
                success: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else {
                            $('#requests-card').html(data.html);
                            app.bootstrap_tooltip();
                            bookSearch();
                        }
                    }
                },
                complete: function () {
                    app.preloader.finish('#requests-card');
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
                    app.preloader.start('#requests-card');
                },
                success: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else {
                            $('#requests-card').html(data.html);
                            app.bootstrap_tooltip();
                            bookSearch();
                        }
                    }
                },
                complete: function () {
                    app.preloader.finish('#requests-card');
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        });
    </script>
{/block}