{extends file='admin/admin.tpl'}
{block name=title}{t}Books{/t}{/block}
{block name=toolbar}
    <div class="btn-group">
        <a href="{$routes->getRouteString("bookCreate")}" class="btn btn-success">
            <span class="btn-icon"><i class="fas fa-plus"></i></span> {t}Add Book{/t}
        </a>
        <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div class="dropdown-menu dropdown-menu-right">
            <a href="{$routes->getRouteString("bookCreate")}" class="dropdown-item "><i class="far fa-hand-paper mr-1"></i> {t}Manually{/t}</a>
            <a class="dropdown-item" data-toggle="collapse" href="#add-book-block" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-magic mr-1" aria-hidden="true"></i> {t}Google Books{/t}</a>
        </div>
    </div>
{/block}
{block name=headerCss append}
    <link href="{$resourcePath}assets/css/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet"/>
{/block}
{block name=content}
    <div class="modal fade" id="bulkEditBooks" role="dialog" aria-labelledby="bulkEditBooksLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{t}Edit Books{/t}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bulk-edit card">
                    {if $isDemoMode === true}
                        <div class="alert alert-info text-center">In the demo version you can't bulk book edit.</div>
                    {/if}
                    <div class="row">
                        <div class="col-lg-6">
                            <label for="seriesId" class="control-label">{t}Series{/t}</label>
                            <div class="input-group input-select">
                                <select name="seriesId" id="seriesId" class="form-control"></select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary first-btn delete-data" type="button" data-container="body" data-toggle="tooltip" title="{t}Clear Value{/t}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary edit-data" type="button" data-container="body" data-toggle="tooltip" title="{t}Confirm Value{/t}">
                                        <i class="fa fa-check"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label for="publisherId" class="control-label">{t}Publisher{/t}</label>
                            <div class="input-group input-select">
                                <select name="publisherId" id="publisherId" class="form-control"></select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary first-btn delete-data" type="button" data-container="body" data-toggle="tooltip" title="{t}Clear Value{/t}">
                                        <i class="fas fa-times"></i></button>
                                </div>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary edit-data" type="button" data-container="body" data-toggle="tooltip" title="{t}Confirm Value{/t}">
                                        <i class="fa fa-check"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <label for="authors[]" class="control-label">{t}Authors{/t}</label>
                            <div class="input-group input-select">
                                <select class="form-control" name="authors[]" id="authors" multiple="multiple"></select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary first-btn delete-data" type="button" data-container="body" data-toggle="tooltip" title="{t}Clear Value{/t}">
                                        <i class="fas fa-times"></i></button>
                                </div>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary edit-data" type="button" data-container="body" data-toggle="tooltip" title="{t}Confirm Value{/t}">
                                        <i class="fa fa-check"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <label for="genres[]" class="control-label">{t}Genres{/t}</label>
                            <div class="input-group input-select">
                                <select class="form-control" name="genres[]" id="genres" multiple="multiple"></select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary first-btn delete-data" type="button" data-container="body" data-toggle="tooltip" title="{t}Clear Value{/t}">
                                        <i class="fas fa-times"></i></button>
                                </div>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary edit-data" type="button" data-container="body" data-toggle="tooltip" title="{t}Confirm Value{/t}">
                                        <i class="fa fa-check"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <label for="publishingYear" class="control-label">{t}Published Year{/t}</label>
                            <div class="input-group">
                                <input type="text" class="form-control year-picker" autocomplete="off" name="publishingYear">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary edit-data" type="button" data-container="body" data-toggle="tooltip" title="{t}Confirm Value{/t}">
                                        <i class="fa fa-check"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label for="pages" class="control-label">{t}Pages{/t}</label>
                            <div class="input-group">
                                <input type="text" class="form-control" autocomplete="off" name="pages">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary edit-data" type="button" data-container="body" data-toggle="tooltip" title="{t}Confirm Value{/t}">
                                        <i class="fa fa-check"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <label for="type" class="control-label">{t}Type{/t}</label>
                            <div class="input-group input-select">
                                <select name="type" class="form-control select-picker">
                                    <option value="Standard">{t}Standard{/t}</option>
                                    <option value="Digital">{t}Digital{/t}</option>
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary edit-data" type="button" data-container="body" data-toggle="tooltip" title="{t}Confirm Value{/t}">
                                        <i class="fa fa-check"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label for="physicalForm" class="control-label">{t}Physical Form{/t}</label>
                            <div class="input-group input-select">
                                <select name="physicalForm" class="form-control select-picker">
                                    <option value="Book">{t}Book{/t}</option>
                                    <option value="Manuscript">{t}Manuscript{/t}</option>
                                    <option value="Journal">{t}Journal{/t}</option>
                                    <option value="CD/DVD">{t}CD/DVD{/t}</option>
                                    <option value="Other">{t}Other{/t}</option>
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary edit-data" type="button" data-container="body" data-toggle="tooltip" title="{t}Confirm Value{/t}">
                                        <i class="fa fa-check"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <label for="size" class="control-label">{t}Size{/t}</label>
                            <div class="input-group input-select">
                                <select name="size" class="form-control select-picker">
                                    <option value="Medium">{t}Medium{/t}</option>
                                    <option value="Large">{t}Large{/t}</option>
                                    <option value="Huge">{t}Huge{/t}</option>
                                    <option value="Small">{t}Small{/t}</option>
                                    <option value="Tiny">{t}Tiny{/t}</option>
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary edit-data" type="button" data-container="body" data-toggle="tooltip" title="{t}Confirm Value{/t}">
                                        <i class="fa fa-check"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label for="binding" class="control-label">{t}Binding{/t}</label>
                            <div class="input-group input-select">
                                <select name="binding" id="bindingId" class="form-control select-picker">
                                    <option value="Softcover">{t}Softcover{/t}</option>
                                    <option value="Hardcover">{t}Hardcover{/t}</option>
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary edit-data" type="button" data-container="body" data-toggle="tooltip" title="{t}Confirm Value{/t}">
                                        <i class="fa fa-check"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <label for="stores[]" class="control-label">{t}Store{/t}</label>
                            <div class="input-group input-select">
                                <select class="form-control" name="stores[]" id="stores" multiple="multiple"></select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary first-btn delete-data" type="button" data-container="body" data-toggle="tooltip" title="{t}Clear Value{/t}">
                                        <i class="fas fa-times"></i></button>
                                </div>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary edit-data" type="button" data-container="body" data-toggle="tooltip" title="{t}Confirm Value{/t}">
                                        <i class="fa fa-check"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <label for="locations[]" class="control-label">{t}Location{/t}</label>
                            <div class="input-group input-select">
                                <select class="form-control" name="locations[]" id="locations" multiple="multiple"></select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary first-btn delete-data" type="button" data-container="body" data-toggle="tooltip" title="{t}Clear Value{/t}">
                                        <i class="fas fa-times"></i></button>
                                </div>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary edit-data" type="button" data-container="body" data-toggle="tooltip" title="{t}Confirm Value{/t}">
                                        <i class="fa fa-check"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <label for="price" class="control-label mr-1">{t}Price{/t}</label>
                            <span>({$siteViewOptions->getOptionValue("priceCurrency")})</span>
                            <div class="input-group">
                                <input type="text" class="form-control" autocomplete="off" name="price">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary edit-data" type="button" data-container="body" data-toggle="tooltip" title="{t}Confirm Value{/t}">
                                        <i class="fa fa-check"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label for="language" class="control-label">{t}Language{/t}</label>
                            <div class="input-group">
                                <input type="text" class="form-control" autocomplete="off" name="language">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary edit-data" type="button" data-container="body" data-toggle="tooltip" title="{t}Confirm Value{/t}">
                                        <i class="fa fa-check"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="bulk-edit" {if $isDemoMode === true}disabled{/if}>{t}Save Changes{/t}</button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="collapse" id="add-book-block">
                <div class="card">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{t}Book{/t}</th>
                                <th style="width: 65px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select name="bookId" id="bookId" class="form-control"></select>
                                </td>
                                <td class="text-center">
                                    <a href="#" class="btn btn-success" id="add-book" data-container="body" data-placement="left" data-toggle="tooltip" title="{t}Add Book With Google Books{/t}"><i class="fas fa-plus" aria-hidden="true"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="google-result text-center"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card" id="books-block">
                {include 'admin/books/books-list.tpl'}
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
        var bookBulkBarCodeGenerateUrl = '{$routes->getRouteString("bookBulkBarCodeGenerate",[])}';
        $(document).on('click', '#gen-barcode', function (e) {
            e.preventDefault();
            if ($('.books-id:checked').length > 0) {
                var form = document.createElement("form");
                form.setAttribute("method", "post");
                form.setAttribute("action", bookBulkBarCodeGenerateUrl);
                $(".books-id:checked").each(function (index, el) {
                    var hiddenField = document.createElement("input");
                    hiddenField.setAttribute("type", "hidden");
                    hiddenField.setAttribute("name", $(el).attr('name'));
                    hiddenField.setAttribute("value", $(el).val());
                    form.appendChild(hiddenField);
                });
                document.body.appendChild(form);
                form.submit();
            }
        });
        $.fn.modal.Constructor.prototype._enforceFocus = function () {
        };
        $('.edit-data').on('click', function (e) {
            e.preventDefault();
            if ($(this).closest('.input-group').attr('data-changed') !== 'true') {
                $(this).addClass('change-data');
                $(this).closest('.input-group').attr('data-changed', true);
            } else {
                $(this).removeClass('change-data');
                $(this).closest('.input-group').attr('data-changed', false);
            }
        });
        $('.delete-data').on('click', function (e) {
            e.preventDefault();
            $(this).closest('.input-group').find('select').empty().append('<option value="0" selected>{t}Clear{/t}</option>').val("0").trigger('change.select2');
        });
        var bulkEditBooksUrl = '{$routes->getRouteString("bookBulkEdit",[])}';
        $('#bulk-edit').on('click', function (e) {
            e.preventDefault();
            var ids = $('.books-id:checked').serialize();
            if ($('.books-id:checked').length > 0) {
                if ($('.bulk-edit .input-group[data-changed="true"]').length > 0) {
                    var data = $('.bulk-edit .input-group[data-changed="true"]').find('select, textarea, input').serialize();
                    $.ajax({
                        dataType: 'json',
                        type: 'POST',
                        url: bulkEditBooksUrl,
                        data: data + '&' + ids,
                        beforeSend: function () {
                            app.card.loading.start('.bulk-edit');
                        },
                        success: function (data) {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                if (data.error) {
                                    app.notification('error', data.error);
                                } else {
                                    app.notification('success', data.success);
                                    $('.bulk-edit').find('select, textarea, input').val("");
                                    $('.bulk-edit .input-group').removeAttr('data-changed').find('.edit-data').removeClass('change-data');
                                    $('.bulk-edit').find('select').empty().trigger('change');
                                    //$('.books-id:checked').prop('checked', false);
                                }
                            }
                        },
                        complete: function () {
                            app.card.loading.finish('.bulk-edit');
                        },
                        error: function (jqXHR, exception) {
                            app.notification('error', app.getErrorMessage(jqXHR, exception));
                        }
                    });
                }
            }
        });
        $('.year-picker').datepicker({
            format: "yyyy",
            startView: 2,
            minViewMode: 2,
            maxViewMode: 2,
            keepOpen: true
        });
        var genreSearchUrl = '{$routes->getRouteString("genreSearchPublic",[])}';
        $("#genres").select2({
            ajax: {
                url: genreSearchUrl,
                dataType: 'json',
                type: 'POST',
                data: function (params) {
                    return {
                        searchText: params.term
                    };
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
                                        text: item.name,
                                        id: item.id,
                                        term: params.term
                                    }
                                })
                            };
                        }
                    }
                },
                cache: false
            },
            templateResult: function (item) {
                if (item.loading) {
                    return item.text;
                }
                return app.markMatch(item.text, item.term);
            },
            minimumInputLength: 2,
            allowClear: true,
            placeholder: " "
        });
        var storeSearchUrl = '{$routes->getRouteString("storeSearchPublic",[])}';
        $("#stores").select2({
            ajax: {
                url: storeSearchUrl,
                dataType: 'json',
                type: 'POST',
                data: function (params) {
                    return {
                        searchText: params.term
                    };
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
                                        text: item.name,
                                        id: item.id,
                                        term: params.term
                                    }
                                })
                            };
                        }
                    }
                },
                cache: false
            },
            templateResult: function (item) {
                if (item.loading) {
                    return item.text;
                }
                return app.markMatch(item.text, item.term);
            },
            minimumInputLength: 2,
            allowClear: true,
            placeholder: " "
        });
        var locationSearchUrl = '{$routes->getRouteString("locationSearchPublic",[])}';
        $("#locations").select2({
            ajax: {
                url: locationSearchUrl,
                dataType: 'json',
                type: 'POST',
                data: function (params) {
                    var datas = $("#stores").serialize() + '&searchText=' + params.term;
                    return datas;
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
                                        text: item.name + ' (' + item.store.name + ')',
                                        id: item.id,
                                        term: params.term
                                    }
                                })
                            };
                        }
                    }
                },
                cache: false
            },
            templateResult: function (item) {
                if (item.loading) {
                    return item.text;
                }
                return app.markMatch(item.text, item.term);
            },
            minimumInputLength: 2,
            allowClear: true,
            placeholder: " "
        });
        var authorSearchUrl = '{$routes->getRouteString("authorSearchPublic",[])}';
        $("#authors").select2({
            ajax: {
                url: authorSearchUrl,
                dataType: 'json',
                type: 'POST',
                data: function (params) {
                    return {
                        searchText: params.term
                    };
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
                                    if (item.firstName) {
                                        var text = item.firstName + ' ' + item.lastName;
                                    } else {
                                        text = item.lastName;
                                    }
                                    return {
                                        text: text,
                                        id: item.id,
                                        term: params.term
                                    }
                                })
                            };
                        }
                    }
                },
                cache: false
            },
            templateResult: function (item) {
                if (item.loading) {
                    return item.text;
                }
                return app.markMatch(item.text, item.term);
            },
            minimumInputLength: 2,
            allowClear: true,
            placeholder: " "
        });
        var publisherSearchUrl = '{$routes->getRouteString("publisherSearchPublic",[])}';
        $('#publisherId').select2({
            ajax: {
                url: function () {
                    return publisherSearchUrl;
                },
                dataType: 'json',
                type: 'POST',
                data: function (params) {
                    return {
                        searchText: params.term
                    };
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
                                        text: item.name,
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
            minimumInputLength: 2,
            allowClear: true,
            placeholder: " "
        });
        var seriesSearchUrl = '{$routes->getRouteString("seriesSearchPublic",[])}';
        $('#seriesId').select2({
            ajax: {
                url: function () {
                    return seriesSearchUrl;
                },
                dataType: 'json',
                type: 'POST',
                data: function (params) {
                    return {
                        searchText: params.term
                    };
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
                                        text: item.name,
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
            minimumInputLength: 2,
            allowClear: true,
            placeholder: " "
        });
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
        var bookSearchUrl = '{$routes->getRouteString("bookGoogleSearchPublic",[])}';
        $('#bookId').select2({
            ajax: {
                url: function () {
                    return bookSearchUrl;
                },
                delay: 500,
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
                            var books = $.parseJSON(data.books);
                            return {
                                results: books.items
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
            var markup = "<div class='select-book'>";
            markup += "<div class='select-book-cover'>";
            if (book.volumeInfo.imageLinks && book.volumeInfo.imageLinks.smallThumbnail) {
                markup += "<img src='" + book.volumeInfo.imageLinks.smallThumbnail + "' />";
            } else {
                markup += "<img src='{$siteViewOptions->getOptionValue("noImagePath")}' />";
            }
            markup += "</div>";
            markup += "<div class='select-book-info'>";
            markup += "<div class='select-book-title'>" + book.volumeInfo.title + "";
            if (book.volumeInfo.publishedDate) {
                markup += " <span>(" + book.volumeInfo.publishedDate + ")</span>";
            }
            markup += "</div>";
            if (book.volumeInfo.publisher) {
                markup += "<div class='select-book-publisher'><strong>{t}Publisher:{/t}</strong> " + book.volumeInfo.publisher + "</div>";
            }
            var isbnLength = $(book.volumeInfo.industryIdentifiers).length;
            if (isbnLength > 0) {
                for (var i = 0; i < isbnLength; i++) {
                    if (book.volumeInfo.industryIdentifiers[i].type == "ISBN_13") {
                        markup += "<div class='select-book-isbn'><strong>{t}ISBN13:{/t}</strong> " + book.volumeInfo.industryIdentifiers[i].identifier + "</div>";
                    }
                    if (book.volumeInfo.industryIdentifiers[i].type == "ISBN_10") {
                        markup += "<div class='select-book-isbn'><strong>{t}ISBN10:{/t}</strong> " + book.volumeInfo.industryIdentifiers[i].identifier + "</div>";
                    }
                }
            }
            var authorsLength = $(book.volumeInfo.authors).length;
            if (authorsLength > 0) {
                markup += "<div class='select-book-author'><strong>{t}Authors:{/t}</strong> ";
                var lastIndex = authorsLength - 1;
                for (i = 0; i < authorsLength; i++) {
                    markup += book.volumeInfo.authors[i];
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
            return book.volumeInfo.title || book.text;
        }
        $(document).on('click', '#filter-books', function (e) {
            e.preventDefault();
            var searchText = $(this).closest('.input-group').find('input').val();
            var url = '{$routes->getRouteString("bookListView")}';
            if (searchText) {
                $.ajax({
                    dataType: 'json',
                    type: 'POST',
                    url: url,
                    data: $('#searchText').serialize(),
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
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    },
                    complete: function () {
                        app.card.loading.finish('#books-block');
                    }
                });
            }
        });
        $(document).on('click', '#add-book', function (e) {
            e.preventDefault();
            var bookEditUrl = '{$routes->getRouteString("bookEdit",[])}',
                    url = '{$routes->getRouteString("bookByGoogleDataCreate",[])}',
                    id = $('#bookId').val();
            if (id) {
                $.ajax({
                    dataType: 'json',
                    type: 'POST',
                    url: url.replace("[googleBookId]", id),
                    beforeSend: function () {
                        app.card.loading.start('#add-book-block .card');
                    },
                    success: function (data) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                app.notification('success', data.success);
                                var markup = "<a href='" + bookEditUrl.replace("[bookId]", data.bookId) + "' target='_blank' class='btn btn-outline-info'><i class='fa fa-eye' aria-hidden='true'></i> {t}View Book{/t}</a>";
                                $('.google-result').html(markup);
                            }
                        }
                    },
                    complete: function () {
                        app.card.loading.finish('#add-book-block .card');
                    },
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    }
                });
            } else {
                app.notification('error', '{t}Book is required{/t}');
                return false;
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
            var url = '{$routes->getRouteString("bookListView")}';
            $.ajax({
                type: "POST",
                url: url,
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
            var url = '{$routes->getRouteString("bookListView")}';
            $.ajax({
                type: "POST",
                url: url,
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