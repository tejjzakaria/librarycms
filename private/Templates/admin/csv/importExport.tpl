{extends file='admin/admin.tpl'}
{block name=title}{t}Import & Export CSV{/t}{/block}
{block name=toolbar}{/block}
{block name=headerCss append}
    <link href="{$resourcePath}assets/css/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet"/>
{/block}
{block name=content}
    <div class="row">
        <div class="col-md-12">
            <form enctype="multipart/form-data" action="{$routes->getRouteString("importCSV")}" method="POST" class="card import-csv">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="">{t}CSV File{/t}
                                <i class="fa fa-exclamation-circle ml-2" aria-hidden="true" data-container="body" data-toggle="tooltip" title="{t}It is recommended to make imports into a clean database to avoid duplication of information (in particular authors, genres, etc.){/t}"></i>
                            </th>
                            <th style="">{t}Column Delimiter{/t}</th>
                            <th style="">{t}Multiple Values Delimiter{/t}</th>
                            <th style="width: 65px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <label class="custom-file d-block">
                                    <input type="file" class="custom-file-input" id="files" name="file">
                                    <span class="custom-file-label"></span>
                                </label>
                            </td>
                            <td>
                                <input name="columnDelimiter" placeholder="{t}Default: ,{/t}" id="columnDelimiter" class="form-control" type="text" value="">
                            </td>
                            <td>
                                <input name="multipleValuesDelimiter" placeholder="{t}Default: |{/t}" id="multipleValuesDelimiter" class="form-control" type="text" value="">
                            </td>
                            <td class='text-center'>
                                <a href="#" class="btn btn-outline-success" id="import">
                                    <span class="btn-icon"><i class="far fa-file-excel"></i></span> {t}Import{/t}
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="card-body">
                    <div class="row" id="csv-config"></div>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form action="{$routes->getRouteString("exportCSV")}" method="POST" class="card export-csv">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="">{t}Column Delimiter{/t}</th>
                            <th style="">{t}Multiple Values Delimiter{/t}</th>
                            <th style="width: 65px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <input name="columnDelimiter" placeholder="{t}Default: ,{/t}" class="form-control" type="text" value="">
                            </td>
                            <td>
                                <input name="multipleValuesDelimiter" placeholder="{t}Default: |{/t}" class="form-control" type="text" value="">
                            </td>
                            <td class="text-center">
                                <a href="#" class="btn btn-outline-success" id="export">
                                    <span class="btn-icon"><i class="far fa-file-excel"></i></span> {t}Export{/t}
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
    <div class="modal fade" id="logs" role="dialog" data-backdrop="static" aria-labelledby="logsLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
                <div class="modal-body console" id="console">
                    <div class="mCustomScrollBox" id="consoleMessages"></div>
                </div>
            </div>
        </div>
    </div>
{/block}
{block name=footerPageJs append}
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/bootstrap-select/bootstrap-select.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/papaparse/papaparse.min.js"></script>
{/block}
{block name=footerCustomJs append}
    <script>
        function compare(a, b) {
            if (a.priority < b.priority)
                return -1;
            if (a.priority > b.priority)
                return 1;
            return 0;
        }

        $("input[type=file]").change(function () {
            var fieldVal = $(this).val();
            if (fieldVal != undefined || fieldVal != "") {
                fieldVal = fieldVal.substring(12);
                $(this).next(".custom-file-label").html(fieldVal);
            }
        });
        $('#files').on('change', function (e) {
            e.preventDefault();
            $('#files').parse({
                config: {
                    preview: 1,
                    delimiter: $('#columnDelimiter').val(),
                    complete: function (results, file) {
                        var a1 = [
                            {
                                regex: /(metatit|meta_tit|meta tit)/i,
                                title: '{t}Meta Title{/t}',
                                value: 'Books.metaTitle',
                                isMapped: false,
                                priority: 25,
                                csvColumn: null
                            },
                            {
                                regex: /(title|book_name|bookname)/i,
                                title: '{t}Title{/t}',
                                value: 'Books.title',
                                isMapped: false,
                                priority: 1,
                                csvColumn: null
                            },
                            {
                                regex: /(subtitle|sub_title|sub-title|sub title)/i,
                                title: '{t}Subtitle{/t}',
                                value: 'Books.subtitle',
                                isMapped: false,
                                priority: 2,
                                csvColumn: null
                            },
                            {
                                regex: /(bookid|book_id|serial|bookSN)/i,
                                title: '{t}Book ID{/t}',
                                value: 'Books.bookSN',
                                isMapped: false,
                                priority: 3,
                                csvColumn: null
                            },
                            {
                                regex: /(isbn10|isbn_10|isbn-10|isbn 10)/i,
                                title: '{t}ISBN 10{/t}',
                                value: 'Books.ISBN10',
                                isMapped: false,
                                priority: 5,
                                csvColumn: null
                            },
                            {
                                regex: /(isbn13|isbn_13|isbn-13|isbn 13|isbn)/i,
                                title: '{t}ISBN 13{/t}',
                                value: 'Books.ISBN13',
                                isMapped: false,
                                priority: 4,
                                csvColumn: null
                            },
                            {
                                regex: /(cover|image)/i,
                                title: '{t}Cover{/t}',
                                value: 'Images.path',
                                isMapped: false,
                                priority: 6,
                                csvColumn: null
                            },
                            {
                                regex: /(publisher)/i,
                                title: '{t}Publisher{/t}',
                                value: 'Publishers.name',
                                isMapped: false,
                                priority: 7,
                                csvColumn: null
                            },
                            {
                                regex: /(series)/i,
                                title: '{t}Series{/t}',
                                value: 'Series.name',
                                isMapped: false,
                                priority: 8,
                                csvColumn: null
                            },
                            {
                                regex: /(author)/i,
                                title: '{t}Authors{/t}',
                                value: 'Authors.lastName',
                                isMapped: false,
                                priority: 9,
                                csvColumn: null
                            },
                            {
                                regex: /(genre)/i,
                                title: '{t}Genres{/t}',
                                value: 'Genres.name',
                                isMapped: false,
                                priority: 10,
                                csvColumn: null
                            },
                            {
                                regex: /(store)/i,
                                title: '{t}Stores{/t}',
                                value: 'Stores.name',
                                isMapped: false,
                                priority: 11,
                                csvColumn: null
                            },
                            {
                                regex: /(loc)/i,
                                title: '{t}Locations{/t}',
                                value: 'Locations.name',
                                isMapped: false,
                                priority: 12,
                                csvColumn: null
                            },
                            {
                                regex: /(edit)/i,
                                title: '{t}Edition{/t}',
                                value: 'Books.edition',
                                isMapped: false,
                                priority: 13,
                                csvColumn: null
                            },
                            {
                                regex: /(year|date)/i,
                                title: '{t}Published Year{/t}',
                                value: 'Books.publishingYear',
                                isMapped: false,
                                priority: 14,
                                csvColumn: null
                            },
                            {
                                regex: /(page)/i,
                                title: '{t}Pages{/t}',
                                value: 'Books.pages',
                                isMapped: false,
                                priority: 15,
                                csvColumn: null
                            },
                            {
                                regex: /(type)/i,
                                title: '{t}Type{/t}',
                                value: 'Books.type',
                                isMapped: false,
                                priority: 16,
                                csvColumn: null
                            },
                            {
                                regex: /(phys|form)/i,
                                title: '{t}Physical Form{/t}',
                                value: 'Books.physicalForm',
                                isMapped: false,
                                priority: 17,
                                csvColumn: null
                            },
                            {
                                regex: /(size)/i,
                                title: '{t}Size{/t}',
                                value: 'Books.size',
                                isMapped: false,
                                priority: 18,
                                csvColumn: null
                            },
                            {
                                regex: /(bind)/i,
                                title: '{t}Binding{/t}',
                                value: 'Books.binding',
                                isMapped: false,
                                priority: 19,
                                csvColumn: null
                            },
                            {
                                regex: /(quant)/i,
                                title: '{t}Quantity{/t}',
                                value: 'Books.quantity',
                                isMapped: false,
                                priority: 20,
                                csvColumn: null
                            },
                            {
                                regex: /(price)/i,
                                title: '{t}Price{/t}',
                                value: 'Books.price',
                                isMapped: false,
                                priority: 21,
                                csvColumn: null
                            },
                            {
                                regex: /(lang)/i,
                                title: '{t}Language{/t}',
                                value: 'Books.language',
                                isMapped: false,
                                priority: 22,
                                csvColumn: null
                            },
                            {
                                regex: /(metadesc|meta_desc|meta desc)/i,
                                title: '{t}Meta Description{/t}',
                                value: 'Books.metaDescription',
                                isMapped: false,
                                priority: 27,
                                csvColumn: null
                            },
                            {
                                regex: /(desc)/i,
                                title: '{t}Description{/t}',
                                value: 'Books.description',
                                isMapped: false,
                                priority: 23,
                                csvColumn: null
                            },
                            {
                                regex: /(note)/i,
                                title: '{t}Notes{/t}',
                                value: 'Books.notes',
                                isMapped: false,
                                priority: 24,
                                csvColumn: null
                            },
                            {
                                regex: /(metakey|meta_key|meta key)/i,
                                title: '{t}Meta Keywords{/t}',
                                value: 'Books.metaKeywords',
                                isMapped: false,
                                priority: 26,
                                csvColumn: null
                            }
                        ];
                        if (file.type == 'application/vnd.ms-excel' || file.type == 'text/plain' || file.type == 'text/x-csv' || file.type == 'application/csv' || file.type == 'application/x-csv' || file.type == 'text/csv' || file.type == 'text/comma-separated-values' || file.type == 'text/x-comma-separated-values' || file.type == 'text/tab-separated-values') {
                            var markup = "";
                            var index;
                            for (var i = 0; i < results.data[0].length; i++) {
                                //console.log(results.data[0][i]);
                                for (index = 0; index < a1.length; ++index) {
                                    if (a1[index].isMapped == false) {
                                        if (a1[index].regex.test(results.data[0][i])) {
                                            a1[index].csvColumn = results.data[0][i];
                                            a1[index].isMapped = true;
                                            break;
                                        }
                                    }
                                }
                            }
                            a1.sort(compare);
                            for (i = 0; i < results.data[0].length; i++) {
                                markup += "<div class='col-lg-2 col-md-3 mb-3  card text-center'>";
                                markup += "<div class='card-header csv-header'>" + results.data[0][i] + "</div>";
                                markup += "<input type='hidden' name='keys[" + i + "]' value='" + results.data[0][i] + "'>";
                                markup += "<div class='card-body type'>";
                                markup += "<select name='values[" + i + "]' class='form-control select-picker'>";
                                markup += "<option value=''></option>";

                                for (index = 0; index < a1.length; ++index) {
                                    var isSelected = (a1[index].csvColumn == results.data[0][i]);
                                    markup += "<option value='" + a1[index].value + "'" + (isSelected ? " selected" : "") + ">" + a1[index].title + "</option>";
                                }
                                {if $customFields != null}
                                {foreach from=$customFields item=customField name=customField}
                                markup += "<option value='Books.{$customField->getName()}'>{$customField->getTitle()}</option>";
                                {/foreach}
                                {/if}
                                markup += "</select>";
                                markup += "</div>";
                                markup += "</div>";
                            }
                            $('#csv-config').empty().append(markup);
                            app.bootstrap_select();
                        } else {
                            app.notification('error', '{t}CSV files are accepted only{/t}');
                        }
                    }
                },
                before: function (file, inputElem) {
                    /*for (var index = 0; index < a1.length; ++index) {
                     a1[index].isMapped = false;
                     }*/
                },
                complete: function () {
                    console.log("Done with all files.");
                }
            });
        });
        var importCSVUrl = '{$routes->getRouteString("importCSV",[])}';
        $(document).on('click', '#import', function (e) {
            e.preventDefault();
            var formData = new FormData();
            var file = $('input:file');
            var fileValue = $(file).val();
            if ($(file)[0].files[0].type == 'application/vnd.ms-excel' || $(file)[0].files[0].type == 'text/plain' || $(file)[0].files[0].type == 'text/x-csv' || $(file)[0].files[0].type == 'application/csv' || $(file)[0].files[0].type == 'application/x-csv' || $(file)[0].files[0].type == 'text/csv' || $(file)[0].files[0].type == 'text/comma-separated-values' || $(file)[0].files[0].type == 'text/x-comma-separated-values' || $(file)[0].files[0].type == 'text/tab-separated-values') {
                if ($(file)[0].files[0] != null) {
                    formData.append('file', $(file)[0].files[0], fileValue);
                    $("#csv-config input, #csv-config select").each(function (index, element) {
                        formData.append($(element).attr('name'), $(element).val());
                    });
                    if ($('#columnDelimiter').val()) {
                        formData.append('columnDelimiter', $('#columnDelimiter').val());
                    }
                    if ($('#multipleValuesDelimiter').val()) {
                        formData.append('multipleValuesDelimiter', $('#multipleValuesDelimiter').val());
                    }
                } else {
                    app.notification('error', '{t}Please choose file{/t}');
                    return false;
                }
                $.ajax({
                    type: "POST",
                    data: formData,
                    url: importCSVUrl,
                    processData: false,
                    contentType: false,
                    dataType: 'html',
                    xhr: function () {
                        var xhr = new XMLHttpRequest();
                        //var xhr = new XMLHttpRequest();
                        xhr.addEventListener("progress", function (evt) {
                            var lines = evt.currentTarget.response.split("\n");
                            if (lines.length)
                                var progress = lines[lines.length - 1];
                            else
                                var progress = 0;
                            $(".console .mCSB_container").html(progress);
                            $("#console").mCustomScrollbar("scrollTo", 'bottom');
                        }, false);

                        return xhr;
                    },
                    beforeSend: function () {
                        app.card.loading.start('.card.import-csv');
                        $('#logs').modal('show');
                        $('#consoleMessages').html('');
                    },
                    success: function (data) {
                        app.notification('success', '{t}Books Successfully Imported{/t}');
                    },
                    complete: function () {
                        app.card.loading.finish('.card.import-csv');
                    },
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    }
                });
            } else {
                app.notification('error', '{t}Please choose a valid CSV file{/t}');
                return false;
            }
        });
        $('#logs').on('show.bs.modal', function (e) {
            $("#console").mCustomScrollbar({
                axis: "y",
                autoHideScrollbar: true,
                scrollInertia: 0,
                advanced: {
                    autoScrollOnFocus: false,
                    updateOnContentResize: true,
                    autoUpdateTimeout: 60
                }
            });
        });


        var exportCSVUrl = '{$routes->getRouteString("exportCSV",[])}';
        $('#export').on('click', function (e) {
            e.preventDefault();
            var formData = $('.export-csv').serialize();
            $.ajax({
                type: 'POST',
                processData: false,
                data: formData,
                url: exportCSVUrl,
                beforeSend: function () {
                    app.card.loading.start('.export-csv');
                },
                success: function (data, textStatus, jqXHR) {
                    function isJson(str) {
                        try {
                            JSON.parse(str);
                        } catch (e) {
                            return false;
                        }
                        return true;
                    }

                    if (isJson(data)) {
                        var json = JSON.parse(data);
                        if (json.redirect) {
                            window.location.href = json.redirect;
                        } else {
                            app.notification('error', json.error);
                        }
                    } else {
                        var blob = new Blob([data], {
                            type: 'application/vnd.ms-excel'
                        });
                        var rawFileName = jqXHR.getResponseHeader('Content-Disposition');
                        var re = /filename[^;=\n]*="((['"]).*?\2|[^;\n]*)"/i;
                        var m;
                        if ((m = re.exec(rawFileName)) !== null) {
                            if (m.index === re.lastIndex) {
                                re.lastIndex++;
                            }
                        }
                        var downloadUrl = URL.createObjectURL(blob);
                        var a = document.createElement("a");
                        a.href = downloadUrl;
                        a.download = m[1];
                        document.body.appendChild(a);
                        a.click();
                    }
                },
                complete: function () {
                    app.card.loading.finish('.export-csv');
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        });
    </script>
{/block}