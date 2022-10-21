{extends file='admin/admin.tpl'}
{block name=title}{t}Book Barcode Generation{/t}{/block}
{block name=toolbar}{/block}
{block name=headerCss append}
    <link href="{$resourcePath}assets/css/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet"/>
    <link href="{$resourcePath}assets/css/barcode.css" rel="stylesheet" media="print"/>
{/block}
{block name=content}
    <div class="row print-hide">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td class="border-top-0">
                                    <select name="bookIds[]" id="bookId" class="form-control"></select>
                                </td>
                                <td class="border-top-0" style="width: 65px;">
                                    <a href="#" class="btn btn-outline-success no-border" id="add-book" data-container="body" data-toggle="tooltip" title="{t}Add Book{/t}"><i class="fas fa-plus" aria-hidden="true"></i></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <form id="books" class="validate-books {if isset($books) and $books != null}{else}d-none{/if}">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{t}Book{/t}</th>
                                    <th style="width: 160px;" class="text-center">{t}Quantity{/t}</th>
                                    <th style="width: 65px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                {if isset($books) and $books != null}
                                    {foreach from=$books item=book name=book}
                                        <tr data-id="{$smarty.foreach.book.index}">
                                            <td>{$book->getTitle()}</td>
                                            <td>
                                                <input type="hidden" class="form-control" name="bookIds[{$smarty.foreach.book.index}]" value="{$book->getId()}">
                                                <input type="text" class="form-control form-control-sm" name="barCodeQuantity[{$smarty.foreach.book.index}]" value="1">
                                            </td>
                                            <td class="text-center">
                                                <a href='#' class='btn btn-outline-info no-border remove-book' title='{t}Delete Book{/t}'><i class='fa fa-times'></i></a>
                                            </td>
                                        </tr>
                                    {/foreach}
                                {/if}
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <form class="validate card" id="barcode-settings">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="preset" class="control-label">{t}Preset{/t}</label>
                                <select name="preset" class="form-control select-picker" id="preset">
                                    <option value="8">8 {t}Per Page{/t} (97.1 mm x 67.1 mm)</option>
                                    <option value="12">12 {t}Per Page{/t} (95 mm x 40 mm)</option>
                                    <option value="16">16 {t}Per Page{/t} (99.1 mm x 33.9 mm)</option>
                                    <option value="24">24 {t}Per Page{/t} (63 mm x 33.9 mm)</option>
                                    <option value="30">30 {t}Per Page{/t} (60 mm x 25 mm)</option>
                                    <option value="40">40 {t}Per Page{/t} (45.7 mm x 25.5 mm)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label for="labelWidth" class="control-label">{t}Label Width{/t} (mm)</label>
                                <input type="text" class="form-control width-barcode" name="labelWidth" id="labelWidth" value="99.1">
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label for="labelHeight" class="control-label">{t}Label Height{/t} (mm)</label>
                                <input type="text" class="form-control height-barcode" name="labelHeight" id="labelHeight" value="67.7">
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label for="cornerRadius" class="control-label">{t}Corner Radius{/t} (mm)</label>
                                <input type="text" class="form-control height-barcode" name="cornerRadius" id="cornerRadius" value="1.5">
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label for="primaryFontSize" class="control-label">{t}Primary Font Size{/t} (em)</label>
                                <input type="text" class="form-control height-barcode" name="primaryFontSize" id="primaryFontSize" value="1">
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label for="secondaryFontSize" class="control-label">{t}Secondary Font Size{/t} (em)</label>
                                <input type="text" class="form-control height-barcode" name="secondaryFontSize" id="secondaryFontSize" value="0.750">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-4">
                            <div class="form-group">
                                <label for="pageMarginTop" class="control-label">{t}Page Margin Top{/t} (mm)</label>
                                <input type="text" class="form-control height-barcode" name="pageMarginTop" id="pageMarginTop" value="13.1">
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <div class="form-group">
                                <label for="pageMarginBottom" class="control-label">{t}Page Margin Bottom{/t} (mm)</label>
                                <input type="text" class="form-control height-barcode" name="pageMarginBottom" id="pageMarginBottom" value="13.1">
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <div class="form-group">
                                <label for="pageMarginLeft" class="control-label">{t}Page Margin Left{/t} (mm)</label>
                                <input type="text" class="form-control height-barcode" name="pageMarginLeft" id="pageMarginLeft" value="4.4">
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <div class="form-group">
                                <label for="pageMarginRight" class="control-label">{t}Page Margin Right{/t} (mm)</label>
                                <input type="text" class="form-control height-barcode" name="pageMarginRight" id="pageMarginRight" value="4.4">
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <div class="form-group">
                                <label for="gapAcross" class="control-label">{t}Gap Across{/t} (mm)</label>
                                <input type="text" class="form-control height-barcode" name="gapAcross" id="gapAcross" value="3">
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <div class="form-group">
                                <label for="gapDown" class="control-label">{t}Gap Down{/t} (mm)</label>
                                <input type="text" class="form-control height-barcode" name="gapDown" id="gapDown" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-1 col-md-2">
                            <div class="form-group">
                                <label for="isPrintBookTitle" class="control-label">{t}Title{/t}</label><br>
                                <label class="switch switch-sm">
                                    <input type="checkbox" name="isPrintBookTitle" id="isPrintBookTitle" value="1" checked>
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-2">
                            <div class="form-group">
                                <label for="isPrintBookCover" class="control-label">{t}Cover{/t}</label><br>
                                <label class="switch switch-sm">
                                    <input type="checkbox" name="isPrintBookCover" id="isPrintBookCover" value="1" checked>
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-2">
                            <div class="form-group">
                                <label for="isPrintBookISBN" class="control-label">{t}ISBN{/t}</label><br>
                                <label class="switch switch-sm">
                                    <input type="checkbox" name="isPrintBookISBN" id="isPrintBookISBN" value="1" checked>
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-2">
                            <div class="form-group">
                                <label for="isPrintBookPublisher" class="control-label">{t}Publisher{/t}</label><br>
                                <label class="switch switch-sm">
                                    <input type="checkbox" name="isPrintBookPublisher" id="isPrintBookPublisher" value="1" checked>
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-2">
                            <div class="form-group">
                                <label for="isPrintBookGenre" class="control-label">{t}Genre{/t}</label><br>
                                <label class="switch switch-sm">
                                    <input type="checkbox" name="isPrintBookGenre" id="isPrintBookGenre" value="1" checked>
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-2">
                            <div class="form-group">
                                <label for="isPrintBookAuthor" class="control-label">{t}Author{/t}</label><br>
                                <label class="switch switch-sm">
                                    <input type="checkbox" name="isPrintBookAuthor" id="isPrintBookAuthor" value="1" checked>
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-2">
                            <div class="form-group">
                                <label for="isPrintBookPrice" class="control-label">{t}Price{/t}</label><br>
                                <label class="switch switch-sm">
                                    <input type="checkbox" name="isPrintBookPrice" id="isPrintBookPrice" value="1" checked>
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-5 col-md-4">
                            <div class="form-group">
                                <label for="customText" class="control-label">{t}Custom Text{/t}</label>
                                <input type="text" class="form-control height-barcode" name="customText" placeholder="{t}e.g. Library Name{/t}" value="Library CMS">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <button class="btn btn-primary pull-right" id="generate">
                                <span class="btn-icon"><i class="icon-magic-wand"></i></span> {t}Generate{/t}
                            </button>
                            <button type="button" class="btn btn-primary pull-left" id="print">
                                <span class="btn-icon"><i class="icon-printer"></i></span> {t}Print{/t}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div id="pages"></div>
{/block}
{block name=footerPageJs append}
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/JsBarcode/JsBarcode.all.min.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/bootstrap-select/bootstrap-select.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/jquery-validate/jquery.validate.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/select2/select2.full.min.js"></script>
{/block}
{block name=footerCustomJs append}
    <script>
        $('.validate input,.validate select,.validate textarea,.validate-books input').tooltipster({
            trigger: 'custom',
            onlyOne: false,
            position: 'bottom',
            offsetY: -5,
            theme: 'tooltipster-kaa'
        });
        $('.validate-books').validate({
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
                'barCodeQuantity[]': {
                    required: true,
                    digits: true,
                    min: 1
                }
            }
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
                labelWidth: {
                    required: true,
                    number: true,
                    min: 1
                },
                labelHeight: {
                    required: true,
                    number: true,
                    min: 1
                },
                cornerRadius: {
                    required: true,
                    number: true
                },
                primaryFontSize: {
                    required: true,
                    number: true
                },
                secondaryFontSize: {
                    required: true,
                    number: true
                },
                pageMarginTop: {
                    required: true,
                    number: true
                },
                pageMarginBottom: {
                    required: true,
                    number: true
                },
                pageMarginLeft: {
                    required: true,
                    number: true
                },
                pageMarginRight: {
                    required: true,
                    number: true
                },
                gapAcross: {
                    required: true,
                    number: true
                },
                gapDown: {
                    required: true,
                    number: true
                }
            }
        });
        window.onbeforeprint = function () {
            $(".fix-header .header").trigger("sticky_kit:detach");
        };
        window.onafterprint = function () {
            app.sticky_header();
        };
        $(document).on('click', '#print', function (e) {
            e.preventDefault();
            window.print();
        });
        var sizes = {
            size8: {
                labelWidth: 99.1,
                labelHeight: 67.7,
                cornerRadius: 1.5,
                primaryFontSize: 1,
                secondaryFontSize: 0.750,
                pageMarginTop: 13.1,
                pageMarginBottom: 13.1,
                pageMarginLeft: 4.4,
                pageMarginRight: 4.4,
                gapAcross: 3,
                gapDown: 0,
                isPrintBookTitle: true,
                isPrintBookCover: true,
                isPrintBookISBN: true,
                isPrintBookPublisher: true,
                isPrintBookGenre: true,
                isPrintBookAuthor: true,
                isPrintBookPrice: true
            },
            size12: {
                labelWidth: 63.5,
                labelHeight: 72,
                cornerRadius: 1.5,
                primaryFontSize: 1,
                secondaryFontSize: 0.750,
                pageMarginTop: 4.5,
                pageMarginBottom: 4.5,
                pageMarginLeft: 7.75,
                pageMarginRight: 7.75,
                gapAcross: 2,
                gapDown: 0,
                isPrintBookTitle: true,
                isPrintBookCover: true,
                isPrintBookISBN: true,
                isPrintBookPublisher: true,
                isPrintBookGenre: true,
                isPrintBookAuthor: true,
                isPrintBookPrice: true
            },
            size16: {
                labelWidth: 99.1,
                labelHeight: 33.9,
                cornerRadius: 1.5,
                primaryFontSize: 0.750,
                secondaryFontSize: 0.750,
                pageMarginTop: 12.9,
                pageMarginBottom: 12.9,
                pageMarginLeft: 4.9,
                pageMarginRight: 4.9,
                gapAcross: 2,
                gapDown: 0,
                isPrintBookTitle: true,
                isPrintBookCover: true,
                isPrintBookISBN: false,
                isPrintBookPublisher: false,
                isPrintBookGenre: false,
                isPrintBookAuthor: false,
                isPrintBookPrice: false
            },
            size24: {
                labelWidth: 63,
                labelHeight: 33.9,
                cornerRadius: 1.5,
                primaryFontSize: 1,
                secondaryFontSize: 0.750,
                pageMarginTop: 12.9,
                pageMarginBottom: 12.9,
                pageMarginLeft: 7,
                pageMarginRight: 7,
                gapAcross: 2,
                gapDown: 0,
                isPrintBookTitle: true,
                isPrintBookCover: false,
                isPrintBookISBN: false,
                isPrintBookPublisher: false,
                isPrintBookGenre: false,
                isPrintBookAuthor: false,
                isPrintBookPrice: false
            },
            size30: {
                labelWidth: 60,
                labelHeight: 25,
                cornerRadius: 1.5,
                primaryFontSize: 0.550,
                secondaryFontSize: 0.750,
                pageMarginTop: 13,
                pageMarginBottom: 13,
                pageMarginLeft: 9,
                pageMarginRight: 9,
                gapAcross: 6,
                gapDown: 3,
                isPrintBookTitle: true,
                isPrintBookCover: false,
                isPrintBookISBN: false,
                isPrintBookPublisher: false,
                isPrintBookGenre: false,
                isPrintBookAuthor: false,
                isPrintBookPrice: false
            },
            size40: {
                labelWidth: 45.7,
                labelHeight: 25.5,
                cornerRadius: 1.5,
                primaryFontSize: 0.750,
                secondaryFontSize: 0.750,
                pageMarginTop: 21,
                pageMarginBottom: 21,
                pageMarginLeft: 10.6,
                pageMarginRight: 10.6,
                gapAcross: 2,
                gapDown: 0,
                isPrintBookTitle: true,
                isPrintBookCover: false,
                isPrintBookISBN: false,
                isPrintBookPublisher: false,
                isPrintBookGenre: false,
                isPrintBookAuthor: false,
                isPrintBookPrice: false
            }
        };
        $('#preset').on('change', function (e) {
            e.preventDefault();
            var value = $(this).val();
            $('#labelWidth').val(sizes['size' + value].labelWidth);
            $('#labelHeight').val(sizes['size' + value].labelHeight);
            $('#cornerRadius').val(sizes['size' + value].cornerRadius);
            $('#primaryFontSize').val(sizes['size' + value].primaryFontSize);
            $('#secondaryFontSize').val(sizes['size' + value].secondaryFontSize);
            $('#pageMarginTop').val(sizes['size' + value].pageMarginTop);
            $('#pageMarginBottom').val(sizes['size' + value].pageMarginBottom);
            $('#pageMarginLeft').val(sizes['size' + value].pageMarginLeft);
            $('#pageMarginRight').val(sizes['size' + value].pageMarginRight);
            $('#gapAcross').val(sizes['size' + value].gapAcross);
            $('#gapDown').val(sizes['size' + value].gapDown);

            if (sizes['size' + value].isPrintBookTitle == true) {
                $('#isPrintBookTitle').attr("checked", true);
            } else {
                $('#isPrintBookTitle').attr("checked", false);
            }
            if (sizes['size' + value].isPrintBookCover == true) {
                $('#isPrintBookCover').attr("checked", true);
            } else {
                $('#isPrintBookCover').attr("checked", false);
            }
            if (sizes['size' + value].isPrintBookISBN == true) {
                $('#isPrintBookISBN').attr("checked", true);
            } else {
                $('#isPrintBookISBN').attr("checked", false);
            }
            if (sizes['size' + value].isPrintBookPublisher == true) {
                $('#isPrintBookPublisher').attr("checked", true);
            } else {
                $('#isPrintBookPublisher').attr("checked", false);
            }
            if (sizes['size' + value].isPrintBookGenre == true) {
                $('#isPrintBookGenre').attr("checked", true);
            } else {
                $('#isPrintBookGenre').attr("checked", false);
            }
            if (sizes['size' + value].isPrintBookAuthor == true) {
                $('#isPrintBookAuthor').attr("checked", true);
            } else {
                $('#isPrintBookAuthor').attr("checked", false);
            }
            if (sizes['size' + value].isPrintBookPrice == true) {
                $('#isPrintBookPrice').attr("checked", true);
            } else {
                $('#isPrintBookPrice').attr("checked", false);
            }
        });

        function extractText(str) {
            var ret = "";

            if (/"/.test(str)) {
                ret = str.match(/"(.*?)"/)[1];
            } else {
                ret = str;
            }

            return ret;
        }

        var genURL = '{$routes->getRouteString("bookBulkBarCodeGenerate",[])}';
        $('#generate').on('click', function (e) {
            e.preventDefault();
            var form = $('#barcode-settings');
            if (form.valid()) {
                $.ajax({
                    dataType: 'json',
                    method: 'POST',
                    data: $('#barcode-settings, #books').serialize(),
                    url: genURL,
                    beforeSend: function (data) {
                        app.card.loading.start('#barcode-settings');
                    },
                    success: function (data) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                $('#pages').html(data.html);
                                try {
                                    JsBarcode(".barcode").init();
                                } catch (err) {
                                    app.notification('error', extractText(err) + ' {t}is not a valid ISBN{/t}');
                                }
                            }
                        }
                    },
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    },
                    complete: function (data) {
                        app.card.loading.finish('#barcode-settings');
                    }
                });
            }
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
            if (book.genres != null && book.genres.length > 0) {
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
            if (book.authors != null && book.authors.length > 0) {
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
            if (!bookVal) {
                app.notification('error', '{t}Book is required{/t}');
                return false;
            }
            var count = 0;
            if (bookVal) {
                if ($('#books tbody').find('tr:visible').length > 0) {
                    var langLength = $('#books tbody').find('tr:visible:last-child').attr('data-id');
                } else {
                    langLength = 0;
                }
                count = parseInt(langLength) + 1;
                $('#books').removeClass('d-none').slideDown();
                var data = $(book).select2('data');
                var bookId = data[0].id;
                var title = data[0].title;

                var markup = "<tr data-id='" + count + "'>";
                markup += "<td>" + title + "</td>";
                markup += "<td>";
                markup += "<input type='hidden' class='form-control' name='bookIds[" + count + "]' value='" + bookId + "'>";
                markup += "<input type='text' class='form-control form-control-sm' data-rule-min='1' data-rule-required='true' data-rule-digits='true' name='barCodeQuantity[" + count + "]' value='1'>";
                markup += "</td>";
                markup += "<td class='text-center'>";
                markup += "<a href='#' class='btn btn-outline-info no-border remove-book' title='{t}Delete Book{/t}'><i class='fa fa-times'></i></a>";
                markup += "</td>";
                markup += "</tr>";
                $('#books tbody').append(markup);
                $('#bookId').empty().trigger('change');
                $('.validate-books input').tooltipster({
                    trigger: 'custom',
                    onlyOne: false,
                    position: 'bottom',
                    offsetY: -5,
                    theme: 'tooltipster-kaa'
                });
                $('.validate-books').validate({
                    errorPlacement: function (error, element) {
                        if (element != undefined) {
                            $(element).tooltipster('update', $(error).text());
                            $(element).tooltipster('show');
                        }
                    },
                    success: function (label, element) {
                        $(element).tooltipster('hide');
                    }
                });
            }
        });
        $(document).on('click', '.remove-book', function (e) {
            e.preventDefault();
            $(this).closest('tr').remove();
            if ($('#books tbody tr').length < 1) {
                $('#books').addClass('d-none');
            }
        });
    </script>
{/block}