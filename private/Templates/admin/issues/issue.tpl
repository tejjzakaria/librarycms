{extends file='admin/admin.tpl'}
{block name=title}{if $action == "create"}{t}Add Issue{/t}{else}{t}Edit Issue{/t}{/if}{/block}
{block name=toolbar}
{/block}
{block name=headerCss append}
    <link href="{$resourcePath}assets/css/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet"/>
{/block}
{block name=content}
    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="issue-block">
                {if $action == "create"}
                    {assign var=route value=$routes->getRouteString("issueCreate")}
                {elseif $action == "edit" and isset($issue)}
                    {assign var=route value=$routes->getRouteString("issueEdit",["issueId"=>$issue->getId()])}
                {elseif $action == "delete"}
                    {assign var=route value=""}
                {/if}
                <form action="{$route}" method="post" class="card-body issue-form validate" data-edit="{if $action == "create"}false{else}true{/if}">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="bookId" class="control-label">{t}Book{/t}</label>
                                {if $action == "edit" and $issue->getBook() != null}
                                    <p class="font-weight-bold">{$issue->getBook()->getTitle()}</p>
                                    <input type="hidden" name="bookId" value="{$issue->getBook()->getId()}">
                                {/if}
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="bookId" class="control-label">{t}Book Id{/t}</label>
                                {if $action == "edit" and $issue->getBookCopy() != null}
                                    <p class="font-weight-bold">{$issue->getBookCopy()->getBookSN()}</p>
                                    <input type="hidden" name="bookCopyId" value="{$issue->getBookCopy()->getId()}">
                                {/if}
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="userId" class="control-label">{t}User{/t}</label>
                                {if $action == "edit" and $issue->getUser() != null}
                                    <p class="font-weight-bold">{$issue->getUser()->getFirstName()} {$issue->getUser()->getLastName()}</p>
                                    <input type="hidden" name="userId" value="{$issue->getUser()->getId()}">
                                {/if}
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="issueDate" class="control-label">{t}Issue Date{/t}</label>
                                {if $action == "edit"}
                                    <p class="font-weight-bold">{$issue->getIssueDate()}</p>
                                    <input type="hidden" name="issueDate" value="{$issue->getIssueDate()}">
                                {/if}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="expiryDate" class="control-label">{t}Expiry Date{/t}</label>
                                <input type="text" class="form-control date-picker" autocomplete="off" name="expiryDate" value="{if $action == "edit"}{$issue->getExpiryDate()}{/if}">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="returnDate" class="control-label">{t}Return Date{/t}</label>
                                <input type="text" class="form-control date-picker" autocomplete="off" name="returnDate" value="{if $action == "edit"}{$issue->getReturnDate()}{/if}">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="penalty" class="control-label">{t}Penalty{/t}</label>
                                <input type="text" class="form-control" autocomplete="off" name="penalty" value="{if $action == "edit"}{$issue->getPenalty()}{/if}">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="isLost" class="control-label d-block">{t}Book is Lost{/t}</label>
                                <label class="switch switch-sm">
                                    <input type="checkbox" name="isLost" value="1"{if $action == "edit" and $issue->isLost()} checked{/if}>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn-outline-secondary disabled pull-right save-issue">
                                    <span class="btn-icon"><i class="far fa-save"></i></span> {t}Save{/t}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
{/block}
{block name=footerPageJs append}
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/select2/select2.full.min.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/jquery-validate/jquery.validate.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/bootstrap-select/bootstrap-select.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>
{/block}
{block name=footerCustomJs append}
    <script>
        $(document).ready(function () {
            $('.date-picker').datepicker({
                format: "{$siteViewOptions->getOptionValue("inputDateFormatJS")}",
                keepOpen: true
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
                        markup += book.authors[i].firstName + " " + book.authors[i].lastName;
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
            });

            $('.validate input,.validate select,.validate textarea').tooltipster({
                trigger: 'custom',
                onlyOne: false,
                position: 'bottom',
                offsetY: -5,
                theme: 'tooltipster-kaa'
            });

            $('.validate').validate({
                errorPlacement: function (error, element) {
                    if (element != undefined) {
                        $(element).tooltipster('update', $(error).text());
                        $(element).tooltipster('show');
                    }
                },
                success: function (label, element) {
                    $(element).tooltipster('hide');
                },
                rules: {
                    bookId: {
                        required: true
                    },
                    userId: {
                        required: true
                    }
                }
            });

            $(document).on('change', 'input,textarea,select', function () {
                $(this).closest('form').attr('data-changed', true);
                $('.save-issue').removeClass('btn-outline-secondary disabled').addClass('btn-outline-success');
            });

            var issueEditUrl = '{$routes->getRouteString("issueEdit",[])}';
            $('.save-issue').on('click', function (e) {
                e.preventDefault();
                var form = $(this).closest('form');
                var dataEdit = form.attr('data-edit');
                var dataChanged = form.attr('data-changed');
                if (dataChanged == 'true') {
                    if ($(form).valid()) {
                        $.ajax({
                            dataType: 'json',
                            method: 'POST',
                            data: form.serialize(),
                            url: form.attr('action'),
                            beforeSend: function (data) {
                                app.card.loading.start('.card');
                            },
                            success: function (data) {
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                } else {
                                    if (data.error) {
                                        app.notification('error', data.error);
                                    } else {
                                        form.attr('action', issueEditUrl.replace("[issueId]", data.issueId)).attr('data-changed', false);
                                        app.notification('success', '{t}Data has been saved successfully{/t}');
                                        $('.save-issue').removeClass('btn-success').addClass('btn-outline-secondary disabled');
                                        if (dataEdit == 'false') {
                                            $('.page-title h3').text('{t}Edit Publisher{/t}');
                                            history.pushState(null, '', issueEditUrl.replace("[issueId]", data.issueId));
                                        }
                                        $(form).attr('data-edit', true);
                                    }
                                }
                            },
                            error: function (jqXHR, exception) {
                                app.notification('error', app.getErrorMessage(jqXHR, exception));
                            },
                            complete: function (data) {
                                app.card.loading.finish('.card');
                            }
                        });
                    } else {
                        app.notification('information', '{t}Validation errors occurred. Please confirm the fields and submit it again.{/t}');
                    }
                } else {
                    app.notification('information', '{t}There are no changes{/t}');
                }
            });
        });
    </script>
{/block}