{extends file='admin/admin.tpl'}
{block name=title}{if $action == "create"}{t}Add Request{/t}{else}{t}Edit Request{/t}{/if}{/block}
{block name=headerCss append}
    <link href="{$resourcePath}assets/css/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet"/>
{/block}
{block name=content}
    <div class="row">
        <div class="col-md-12">
            <div class="card" id="request-block">
                <div class="card-body">
                    {if $action == "create"}
                        {assign var=route value=$routes->getRouteString("requestCreate")}
                    {elseif $action == "edit" and isset($request)}
                        {assign var=route value=$routes->getRouteString("requestEdit",["requestId"=>$request->getId()])}
                    {elseif $action == "delete"}
                        {assign var=route value=""}
                    {/if}
                    <form action="{$route}" method="post">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="hidden" name="userId" value="{if $action == "edit"}{$request->getUserId()}{/if}">
                                    <label for="bookId" class="control-label">{t}Book{/t}</label>
                                    <select name="bookId" id="bookId" class="form-control">
                                        {if $action == "edit" and $request->getBook()}
                                            <option value="{$request->getBook()->getId()}" selected>{$request->getBook()->getTitle()}</option>
                                        {/if}
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status" class="control-label">{t}Status{/t}</label>
                                    <select name="status" id="status" class="form-control select-picker">
                                        <option value="Pending" {if $action == "edit" and $request->getStatus() == 'Pending'} selected{/if}>{t}Pending{/t}</option>
                                        <option value="Accepted" {if $action == "edit" and $request->getStatus() == 'Accepted'} selected{/if}>{t}Accepted{/t}</option>
                                        <option value="Rejected" {if $action == "edit" and $request->getStatus() == 'Rejected'} selected{/if}>{t}Rejected{/t}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="notes" class="control-label">{t}Notes{/t}</label>
                                    <textarea name="notes" id="notes" cols="30" rows="5" class="form-control">{if $action == "edit"}{$request->getNotes()}{/if}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-outline-secondary disabled pull-right save-request">
                                        <span class="far fa-save"></span> {t}Save{/t}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
{/block}
{block name=footerPageJs append}
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/select2/select2.full.min.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/bootstrap-select/bootstrap-select.js"></script>
{/block}
{block name=footerJs append}{/block}
{block name=footerCustomJs append}
    <script>
        $(document).ready(function () {
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

            $(document).on('change', 'input,textarea,select', function () {
                $(this).closest('form').attr('data-changed', true);
                $('.save-request').removeClass('btn-outline-secondary disabled').addClass('btn-outline-success');
            });
            var requestEditUrl = '{$routes->getRouteString("requestEdit",[])}';
            $('.save-request').on('click', function (e) {
                e.preventDefault();
                var form = $(this).closest('form');
                var dataChanged = form.attr('data-changed');
                if (dataChanged == 'true') {
                    $.ajax({
                        dataType: 'json',
                        method: 'POST',
                        data: form.serialize(),
                        url: form.attr('action'),
                        beforeSend: function (data) {
                            app.card.loading.start('#request-block');
                        },
                        success: function (data) {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                if (data.error) {
                                    app.notification('error', data.error);
                                } else {
                                    form.attr('action', requestEditUrl.replace("[requestId]", data.requestId)).attr('data-changed', false);
                                    app.notification('success', '{t}Data has been saved successfully{/t}');
                                    $('.save-request').removeClass('btn-outline-success').addClass('btn-outline-secondary disabled');
                                }
                            }
                        },
                        error: function (jqXHR, exception) {
                            app.notification('error', app.getErrorMessage(jqXHR, exception));
                        },
                        complete: function (data) {
                            app.card.loading.finish('#request-block');
                        }
                    });
                } else {
                    app.notification('information', '{t}There are no changes{/t}');
                }

            });
        });
    </script>
{/block}