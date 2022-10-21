{extends file='admin/admin.tpl'}
{block name=title}{if $action == "create"}{t}Add Review{/t}{else}{t}Edit Review{/t}{/if}{/block}
{block name=content}
    <div class="row">
        <div class="col-md-12">
            {if $action == "create"}
                {assign var=route value=$routes->getRouteString("reviewCreate")}
            {elseif $action == "edit" and isset($review)}
                {assign var=route value=$routes->getRouteString("reviewEdit",["reviewId"=>$review->getId()])}
            {elseif $action == "delete"}
                {assign var=route value=""}
            {/if}
            <form action="{$route}" method="post" class="validate" data-edit="{if $action == "create"}false{else}true{/if}">
                <div class="card" id="review-block">
                    <div class="card-header pt-0 pb-0">
                        <div class="heading-elements">
                            <label class="switch switch-sm" data-container="body" data-toggle="tooltip" title="{t}Review Status (Approved/Pending){/t}">
                                <input type="checkbox" name="isPublish" value="1"{if $action == "edit" and $review->isPublish()} checked{/if}>
                            </label>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="bookId" class="control-label">{t}Book{/t}</label>
                                    <select name="bookId" id="bookId" class="form-control">
                                        {if $action == "edit" and $review->getBook() and $review->getBookId() != null}
                                            <option value="{$review->getBookId()}" selected>{$review->getBook()->getTitle()}</option>
                                        {/if}
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="userId" class="control-label">{t}User{/t}</label>
                                    <select name="userId" id="userId" class="form-control">
                                        {if $action == "edit" and $review->getUser() and $review->getUserId() != null}
                                            <option value="{$review->getUserId()}" selected>{$review->getUser()->getLastName()} {$review->getUser()->getFirstName()}</option>
                                        {/if}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="name" class="control-label">{t}Name{/t}</label>
                                    <input type="text" name="name" class="form-control" value="{if $action == "edit"}{$review->getName()}{/if}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="email" class="control-label">{t}Email{/t}</label>
                                    <input type="text" name="email" class="form-control" value="{if $action == "edit"}{$review->getEmail()}{/if}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="text" class="control-label">{t}Review{/t}</label>
                                    <textarea name="text" cols="30" rows="10" class="form-control">{if $action == "edit"}{$review->getText()}{/if}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-outline-secondary disabled pull-right save-review">
                                        <span class="btn-icon"><i class="far fa-save"></i></span> {t}Save{/t}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
{/block}
{block name=footerPageJs append}
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/jquery-validate/jquery.validate.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/select2/select2.full.min.js"></script>
{/block}
{block name=footerCustomJs append}
    <script>
        $(document).ready(function () {
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
                    }
                }
            });
            $(document).on('change', 'input,textarea,select', function () {
                $(this).closest('form').attr('data-changed', true);
                $('.save-review').removeClass('btn-outline-secondary disabled').addClass('btn-outline-success');
            });
            var bookSearchUrl = '{$routes->getRouteString("bookSearch",[])}';
            $('#bookId').select2({
                ajax: {
                    url: function () {
                        return bookSearchUrl;
                    },
                    dataType: 'json',
                    type: 'POST',
                    delay: 400,
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
            var reviewEditUrl = '{$routes->getRouteString("reviewEdit",[])}';
            $('.save-review').on('click', function (e) {
                e.preventDefault();
                var form = $(this).closest('form');
                var dataEdit = form.attr('data-edit');
                var dataChanged = form.attr('data-changed');
                if (dataChanged == 'true') {
                    $.ajax({
                        dataType: 'json',
                        method: 'POST',
                        data: form.serialize(),
                        url: form.attr('action'),
                        beforeSend: function (data) {
                            app.card.loading.start('#review-block');
                        },
                        success: function (data) {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                if (data.error) {
                                    app.notification('error', data.error);
                                } else {
                                    form.attr('action', reviewEditUrl.replace("[reviewId]", data.reviewId)).attr('data-changed', false);
                                    app.notification('success', '{t}Data has been saved successfully{/t}');
                                    $('.save-review').removeClass('btn-outline-success').addClass('btn-outline-secondary disabled');
                                    if (dataEdit == 'false') {
                                        $('.page-title h3').text('{t}Edit Review{/t}');
                                        history.pushState(null, '', reviewEditUrl.replace("[reviewId]", data.reviewId));
                                    }
                                    $(form).attr('data-edit', true);
                                }
                            }
                        },
                        error: function (jqXHR, exception) {
                            app.notification('error', app.getErrorMessage(jqXHR, exception));
                        },
                        complete: function (data) {
                            app.card.loading.finish('#review-block');
                        }
                    });
                } else {
                    app.notification('information', '{t}There are no changes{/t}');
                }
            });
        });
    </script>
{/block}