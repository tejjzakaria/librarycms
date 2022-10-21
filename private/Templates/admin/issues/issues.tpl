{extends file='admin/admin.tpl'}
{block name=title}{t}Issued Books{/t}{/block}
{block name=toolbar}
    <div class="heading-elements">
        <a href="#issue-book-block" class="btn btn-success" data-toggle="collapse" aria-expanded="false" aria-controls="collapseExample">
            <span class="btn-icon"><i class="fas fa-plus"></i></span> {t}Issue Book{/t}
        </a>
    </div>
{/block}
{block name=headerCss append}
    <link href="{$resourcePath}assets/css/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet"/>
    <link href="{$resourcePath}assets/js/plugins/summernote/summernote-bs4.css" rel="stylesheet"/>
{/block}
{block name=content}
    <div class="row">
        <div class="col-md-12">
            <div class="collapse" id="issue-book-block">
                <div class="card">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width: 33%;">{t}Book{/t}</th>
                                <th style="width: 33%;">{t}Copy{/t}</th>
                                <th style="width: 33%;">{t}User{/t}</th>
                                <th style="width: 65px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select name="bookIds[]" id="bookId" class="form-control"></select>
                                </td>
                                <td>
                                    <select name="bookCopyIds[]" id="bookCopyId" class="form-control custom-select"></select>
                                </td>
                                <td>
                                    <select name="userId" id="userId" class="form-control"></select>
                                </td>
                                <td class="text-center">
                                    <a href="#" class="btn btn-outline-success no-border" id="add-book" data-container="body" data-toggle="tooltip" title="{t}Add Another Book{/t}"><i class="fas fa-plus" aria-hidden="true"></i></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <form action="{$routes->getRouteString("issueCreate")}" id="issue-form">
                        <input type="hidden" name="userId" id="user-id">
                        <table class="table table-hover d-none" id="issue-result">
                            <thead>
                                <tr>
                                    <th>{t}Books for{/t} <span class="text-muted user-name"></span></th>
                                    <th style="width: 200px;">{t}Book Copy{/t}</th>
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
                                <a href="#" class="btn btn-success pull-right {if $activeLanguage->isRTL()}ml-2{else}mr-2{/if} mb-3" id="issue-book">{t}Issue{/t}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card" id="issues-card">
                {include 'admin/issues/issues-list.tpl'}
            </div>
        </div>
    </div>
    <div class="modal fade" id="userSendSMSModal" tabindex="-1" role="dialog" aria-labelledby="userSendSMSModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title m-auto">{t}Send Notification{/t}</h5>
                </div>
                <div class="modal-body">
                    <form action="{$routes->getRouteString("smsSend",[])}" class="card p-3 mb-0">
                        <div class="form-group">
                            <label for="subject">{t}Sender{/t}</label>
                            <input type="hidden" name="bookId" id="smsBookId">
                            <input type="text" class="form-control" id="messageSender" name="sender">
                        </div>
                        <div class="form-group">
                            <label for="smsContent">{t}Message{/t}
                                <a class="text-muted" data-toggle="collapse" href="#shortcode-block-sms" aria-expanded="false" aria-controls="shortcode-block-sms"><i class="fa fa-info-circle"></i></a></label>
                            <textarea class="form-control" id="smsContent" name="content"></textarea>
                        </div>
                        <div class="alert alert-default text-center collapse" id="shortcode-block-sms">
                            {t}ShortCodes For Use{/t}: <br><code>[USER_FIRST_NAME]</code>, <code>[USER_LAST_NAME]</code>, <code>[BOOK]</code>, <code>[BOOKS]</code>
                        </div>
                        <div class="text-center">
                            <button type="button" class="btn btn-outline-primary" id="sendSMSToDelayedUser" data-url="">
                                <span class="btn-icon"><i class="far fa-paper-plane"></i></span> {t}Send SMS{/t}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="userSendEmailModal" tabindex="-1" role="dialog" aria-labelledby="userSendEmailModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title m-auto">{t}Send Notification{/t}</h5>
                </div>
                <div class="modal-body">
                    <form action="{$routes->getRouteString("userSendEmail",[])}" class="card p-3 mb-0">
                        <div class="form-group">
                            <label for="subject">{t}Subject{/t}</label>
                            <input type="hidden" name="bookId" id="emailBookId">
                            <input type="text" class="form-control" id="messageSubject" name="subject">
                        </div>
                        <div class="form-group">
                            <label for="emailContent">{t}Message{/t}
                                <a class="text-muted" data-toggle="collapse" href="#shortcode-block-email" aria-expanded="false" aria-controls="shortcode-block-email"><i class="fa fa-info-circle"></i></a></label>
                            <textarea class="form-control" id="emailContent" name="content"></textarea>
                        </div>
                        <div class="alert alert-default text-center collapse" id="shortcode-block-email">
                            {t}ShortCodes For Use{/t}: <br><code>[USER_FIRST_NAME]</code>, <code>[USER_LAST_NAME]</code>,
                            <code>[BOOK]</code>
                        </div>
                        <div class="text-center">
                            <button type="button" class="btn btn-outline-primary" id="sendEmailToDelayedUser" data-url="">
                                <span class="btn-icon"><i class="far fa-paper-plane"></i></span> {t}Send Email{/t}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
{/block}
{block name=footerPageJs append}
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/jquery-validate/jquery.validate.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/select2/select2.full.min.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/bootstrap-select/bootstrap-select.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/summernote/summernote-bs4.min.js"></script>
{/block}
{block name=footerCustomJs append}
    <script>
        var userSendEmailURL = '{$routes->getRouteString("userSendEmail",[])}';
        var userSMSSendURL = '{$routes->getRouteString("smsSend",[])}';
        $('#userSendSMSModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var userId = button.data('user');
            var bookId = button.data('book');
            var modal = $(this);
            $('#smsBookId').val(bookId);
            $('#sendSMSToDelayedUser').attr('data-url', userSMSSendURL.replace('[userId]', userId));
            $.ajax({
                type: "GET",
                dataType: 'json',
                url: userSMSSendURL.replace('[userId]', userId),
                beforeSend: function () {
                    app.card.loading.start($(modal).find('.card'));
                },
                success: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else {
                            $('#messageSender').val(data.sender);
                            $('#smsContent').val(data.smsTemplate);
                        }
                    }
                },
                complete: function () {
                    app.card.loading.finish($(modal).find('.card'));
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        });
        $('#userSendEmailModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var userId = button.data('user');
            var bookId = button.data('book');
            var modal = $(this);
            $('#emailBookId').val(bookId);
            $('#sendEmailToDelayedUser').attr('data-url', userSendEmailURL.replace('[userId]', userId));
            $.ajax({
                type: "GET",
                dataType: 'json',
                url: userSendEmailURL.replace('[userId]', userId),
                beforeSend: function () {
                    app.card.loading.start($(modal).find('.card'));
                },
                success: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else {
                            $('#emailContent').val(data.dynamicEmailTemplate).summernote({
                                toolbar: [
                                    ['style', ['bold', 'italic', 'underline', 'clear']],
                                    ['font', ['strikethrough']],
                                    ['fontsize', ['fontsize']],
                                    ['color', ['color']],
                                    ['para', ['ul', 'ol', 'paragraph']],
                                    ['height', ['height']],
                                    ['misc', ['codeview']]
                                ]
                            });
                        }
                    }
                },
                complete: function () {
                    app.card.loading.finish($(modal).find('.card'));
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        });
        $('#sendSMSToDelayedUser').on('click', function (e) {
            e.preventDefault();
            var form = $(this).closest('form');
            var url = $(this).attr('data-url');
            $.ajax({
                type: "POST",
                url: url,
                data: $(form).serialize(),
                dataType: 'json',
                beforeSend: function () {
                    app.card.loading.start($(form));
                },
                success: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else {
                            app.notification('success', data.success);
                            $('#userSendSMSModal').modal('hide');
                            $('.tooltip.show').remove();
                        }
                    }
                },
                complete: function () {
                    app.card.loading.finish($(form));
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        });
        $('#sendEmailToDelayedUser').on('click', function (e) {
            e.preventDefault();
            var form = $(this).closest('form');
            var url = $(this).attr('data-url');
            $.ajax({
                type: "POST",
                url: url,
                data: $(form).serialize(),
                dataType: 'json',
                beforeSend: function () {
                    app.card.loading.start($(form));
                },
                success: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else {
                            app.notification('success', data.success);
                            $('#emailContent').summernote('destroy');
                            $('#userSendEmailModal').modal('hide');
                            $('.tooltip.show').remove();
                        }
                    }
                },
                complete: function () {
                    app.card.loading.finish($(form));
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        });
        var bookSearchUrl = '{$routes->getRouteString("bookSearch",[])}';
        $('#bookId').select2({
            ajax: {
                url: function () {
                    return bookSearchUrl;
                },
                dataType: 'json',
                type: 'POST',
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
        }).on('select2:select', function (e) {
            var data = $(this).select2('data');
            var bookCopies = data[0].bookCopies;
            var bookCopiesSelect = $('#bookCopyId');
            bookCopiesSelect.empty();
            if(bookCopies && bookCopies.length > 0) {
                $.each(bookCopies, function (i, item) {
                    if(item.issueStatus != 'ISSUED' && item.issueStatus != 'LOST') {
                        $(bookCopiesSelect).append($('<option>', {
                            value: item.id,
                            text: item.bookSN
                        }));
                    }
                });
                if($(bookCopiesSelect).find('option').length < 1) {
                    $(bookCopiesSelect).append($('<option>', {
                        value: 'NULL',
                        text: '{t}Not Available{/t}'
                    }));
                }
            } else {
                $(bookCopiesSelect).append($('<option>', {
                    value: 'NULL',
                    text: '{t}Not Available{/t}'
                }));
            }
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
            if (book.genres && book.genres.length > 0) {
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
            if (book.authors && book.authors.length > 0) {
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
        var searchUserUrl = '{$routes->getRouteString("userSearch",[])}';
        $('#userId').select2({
            ajax: {
                url: function () {
                    return searchUserUrl;
                },
                dataType: 'json',
                type: 'POST',
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
                processResults: function (data, params) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else {
                            return {
                                results: $.map(data, function (item) {
                                    return {
                                        text: item.firstName + ' ' + item.lastName + ' (' + item.email + ')',
                                        id: item.id,
                                        term: params.term
                                    }
                                })
                            };
                        }
                    }
                },
                cache: true
            },
            templateResult: function (item) {
                if (item.loading) {
                    return item.text;
                }
                return app.markMatch(item.text, item.term);
            },
            minimumInputLength: 2
        }).on("change", function (e) {
            var data = $(this).select2('data');
            $('#user-id').val($(this).val());
            if (data.length > 0) {
                $('#issue-form').find('.user-name').text(data[0].text);
            }
        });
        $('#add-book').on('click', function (e) {
            e.preventDefault();
            var book = $("#bookId");
            var bookVal = book.val();
            var bookCopy = $('#bookCopyId');
            var bookCopyVal = bookCopy.val();
            var user = $("#userId");
            var userVal = user.val();
            if (!bookVal) {
                app.notification('error', '{t}Book is required{/t}');
                return false;
            }
            if (!bookCopyVal || bookCopyVal == 'NULL') {
                app.notification('error', '{t}There is no available copies{/t}');
                return false;
            }
            if (!userVal) {
                app.notification('error', '{t}User is required{/t}');
                return false;
            }
            if (bookVal && userVal) {
                $('#issue-result').removeClass('d-none').slideDown();
                var data = $(book).select2('data');
                var bookId = data[0].id;
                var title = data[0].title;
                if (data[0].publisher) {
                    var publisherName = data[0].publisher.name;
                }
                var publishingYear = data[0].publishingYear;
                var ISBN10 = data[0].ISBN10;
                var ISBN13 = data[0].ISBN13;
                var bookCopies = data[0].bookCopies;

                var markup = "<tr>";
                markup += "<td>" + title + "</td>";
                if(bookCopies && bookCopies.length > 0) {
                    markup += "<td>";
                    markup += "<select name='bookCopyIds[]' class='form-control form-control-sm custom-select'>";
                    for (var i = 0; i < bookCopies.length; i++) {
                        if(bookCopies[i].issueStatus != 'ISSUED' && bookCopies[i].issueStatus != 'LOST') {
                            markup += "<option value='" + bookCopies[i].id + "'>" + bookCopies[i].bookSN + "</option>";
                        }
                    }
                    markup += "</select>";
                    markup += "</td>";
                }

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
                if (ISBN13) {
                    markup += ISBN13;
                } else if (ISBN10) {
                    markup += ISBN10;
                }
                markup += "</td>";
                markup += "<td class='text-center'>";
                //markup += "<input type='hidden' name='bookIds[]' value=" + bookId + ">";
                markup += "<a href='#' class='btn btn-outline-info btn-sm no-border remove-book' title='{t}Delete Book{/t}'><i class='far fa-trash-alt'></i></a>";
                markup += "</td>";
                markup += "</tr>";
                $('#issue-result tbody').append(markup);
                $('#bookId').empty().trigger('change');
                $('#bookCopyId').empty();
            }
        });
        $('#issue-book').on('click', function (e) {
            e.preventDefault();
            var formData, form = $('#issue-form');
            var bookVal = $("#bookId").val();
            var userVal = $("#userId").val();
            var bookCopyVal = $('#bookCopyId').val();
            if (!userVal) {
                app.notification('error', '{t}User is required{/t}');
                return false;
            }
            if ($('#issue-result tbody tr').length < 1) {
                if (!bookVal) {
                    app.notification('error', '{t}Book is required{/t}');
                    return false;
                }
                if (!bookCopyVal || bookCopyVal == 'NULL') {
                    app.notification('error', '{t}There is no available copies{/t}');
                    return false;
                }
                formData = $('#bookCopyId, #userId').serialize();
            } else {
                formData = $(form).serialize()
            }
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: formData,
                url: $(form).attr('action'),
                beforeSend: function () {
                    app.card.loading.start('#issue-book-block .card');
                },
                success: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else {

                            $('#userId').empty().trigger('change');
                            $('#bookId').empty().trigger('change');
                            $('#bookCopyId').empty();
                            $('#issue-result').addClass('d-none').slideUp();
                            $('#issue-result tbody tr').remove();
                            var url = '{$routes->getRouteString("issueListView")}';
                            $.ajax({
                                type: "POST",
                                url: url,
                                data: $('#books-sort, #countPerPage').serialize() + '&sortOrder=' + $('option:selected', '#books-sort').attr('data-order'),
                                dataType: 'json',
                                beforeSend: function () {
                                    app.card.loading.start('#issues-card');
                                },
                                success: function (data) {
                                    if (data.redirect) {
                                        window.location.href = data.redirect;
                                    } else {
                                        if (data.error) {
                                            app.notification('error', data.error);
                                        } else {
                                            $('#issues-card').html(data.html);
                                            app.bootstrap_select();
                                            app.tooltip_popover();
                                        }
                                    }
                                },
                                complete: function () {
                                    app.card.loading.finish('#issues-card');
                                },
                                error: function (jqXHR, exception) {
                                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                                }
                            });
                        }
                    }
                },
                complete: function () {
                    app.card.loading.finish('#issue-book-block .card');
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        });
        $(document).on('click', '.lost-book', function (e) {
            e.preventDefault();
            var className, url, $this = $(this);
            var bookLost = $this.attr('data-lost');
            if (bookLost == 'true') {
                url = $this.attr('href').replace("[isLost]", 'false');
            } else {
                url = $this.attr('href').replace("[isLost]", 'true');
            }
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: url,
                beforeSend: function () {
                    app.card.loading.start('#issues-card');
                },
                success: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else {
                            if (bookLost == 'true') {
                                $this.closest('tr').find('.book-status .badge').removeClass('badge-danger').text('');
                                $this.attr('data-lost', 'false');
                                $this.attr('data-original-title', '{t}Book Is Lost{/t}');
                            } else {
                                $this.closest('tr').find('.book-status .badge').addClass('badge-danger').text('{t}lost{/t}');
                                $this.attr('data-lost', 'true');
                                $this.attr('data-original-title', '{t}Book Not Lost{/t}');
                            }
                        }
                    }
                },
                complete: function () {
                    app.card.loading.finish('#issues-card');
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
                    app.card.loading.start('#issues-card');
                },
                success: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else {
                            $this.closest('tr').find('.book-returned-date').text(data.returnDate);
                            $this.closest('tr').find('.book-status .badge').addClass('badge-success').text('{t}returned{/t}');
                            $this.tooltip('hide').remove();
                        }
                    }
                },
                complete: function () {
                    app.card.loading.finish('#issues-card');
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        });
        $(document).on('click', '.remove-book', function (e) {
            e.preventDefault();
            $(this).closest('tr').remove();
            if ($('#issue-result tbody tr').length < 1) {
                $('#issue-result').addClass('d-none');
            }
        });
        $(document).on('click', '.delete-issue', function (e) {
            var url = $(this).attr('data-url');
            var row = $(this).closest('tr');
            $.ajax({
                dataType: 'json',
                type: 'POST',
                url: url,
                beforeSend: function () {
                    app.card.loading.start('#issues-card');
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
                    app.card.loading.finish('#issues-card');
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        });
        $(document).on('change', '#countPerPage', function (e) {
            var url = '{$routes->getRouteString("issueListView")}';
            $.ajax({
                type: "POST",
                url: url,
                data: $('#books-sort, #countPerPage').serialize() + '&sortOrder=' + $('option:selected', '#books-sort').attr('data-order'),
                dataType: 'json',
                beforeSend: function () {
                    app.card.loading.start('#issues-card');
                },
                success: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else {
                            $('#issues-card').html(data.html);
                            app.bootstrap_select();
                            app.tooltip_popover();
                        }
                    }
                },
                complete: function () {
                    app.card.loading.finish('#issues-card');
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        });
        $(document).on('change', '#books-sort', function (e) {
            var url = '{$routes->getRouteString("issueListView")}';
            $.ajax({
                type: "POST",
                url: url,
                data: $('#books-sort, #countPerPage').serialize() + '&sortOrder=' + $('option:selected', '#books-sort').attr('data-order'),
                dataType: 'json',
                beforeSend: function () {
                    app.card.loading.start('#issues-card');
                },
                success: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else {
                            $('#issues-card').html(data.html);
                            app.bootstrap_select();
                            app.tooltip_popover();
                        }
                    }
                },
                complete: function () {
                    app.card.loading.finish('#issues-card');
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
                dataType: 'json',
                data: $('#books-sort, #countPerPage').serialize() + '&sortOrder=' + $('option:selected', '#books-sort').attr('data-order'),
                url: $(this).attr('href'),
                beforeSend: function () {
                    app.card.loading.start('#issues-card');
                },
                success: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else {
                            $('#issues-card').html(data.html);
                            app.bootstrap_select();
                            app.tooltip_popover();
                        }
                    }
                },
                complete: function () {
                    app.card.loading.finish('#issues-card');
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        });
    </script>
{/block}