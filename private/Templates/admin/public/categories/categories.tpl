{extends file='admin/admin.tpl'}
{block name=title}{t}Categories{/t}{/block}
{block name=content}
    <div class="row">
        <div class="col-sm-12">
            <div class="card" id="categories">
                <div class="table-responsive-sm">
                    <table class="table table-hover table-striped table-hover">
                    <thead>
                        <tr>
                            <th>{t}Category Name{/t}</th>
                            <th>{t}URL{/t}</th>
                            <th>{t}Title{/t}</th>
                            <th>{t}Meta Title{/t}</th>
                            <th>{t}Meta Keywords{/t}</th>
                            <th>{t}Meta Description{/t}</th>
                            <th style="width: 70px;"></th>
                        </tr>
                    </thead>
                    <tbody class="repeat-container">
                        {if isset($categories) and categories != null}
                            {foreach from=$categories item=category name=category}
                                <tr class="category" data-action="{$routes->getRouteString("categoryEdit",['categoryId'=>{$category->getId()}])}" data-changed="false">
                                    <td>
                                        <input name="id" class="category-id" type="hidden" value="{$category->getId()}"/>
                                        <input class="form-control" type="text" name="name" value="{$category->getName()}">
                                    </td>
                                    <td>
                                        <input class="form-control" type="text" name="url" value="{$category->getUrl()}">
                                    </td>
                                    <td>
                                        <input class="form-control" type="text" name="title" value="{$category->getTitle()}">
                                    </td>
                                    <td>
                                        <input class="form-control" type="text" name="metaTitle" value="{$category->getMetaTitle()}">
                                    </td>
                                    <td>
                                        <input class="form-control" type="text" name="metaKeywords" value="{$category->getMetaKeywords()}">
                                    </td>
                                    <td>
                                        <input class="form-control" type="text" name="metaDescription" value="{$category->getMetaDescription()}">
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown" data-trigger="hover" data-toggle="tooltip" title="{t}Delete{/t}">
                                            <button class="btn btn-outline-info no-border" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="far fa-trash-alt"></i>
                                            </button>
                                            <ul class="dropdown-menu delete-dropdown dropdown-menu-right">
                                                <li class="text-center">{t}Do you really want to delete?{/t}</li>
                                                <li class="divider"></li>
                                                <li class="text-center">
                                                    <button class="btn btn-outline-danger delete-category" data-delete="{$routes->getRouteString("categoryDelete",['categoryId'=>{$category->getId()}])}">
                                                        <span class="btn-icon"><i class="far fa-trash-alt"></i></span> {t}Delete{/t}
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            {/foreach}
                        {/if}
                        <tr class="copy-template category" data-action="{$routes->getRouteString("categoryCreate")}" data-changed="">
                            <td>
                                <input name="id" class="category-id" type="hidden" value=""/>
                                <input class="form-control category-name" type="text" name="name">
                            </td>
                            <td>
                                <input class="form-control" type="text" name="url">
                            </td>
                            <td>
                                <input class="form-control" type="text" name="title">
                            </td>
                            <td>
                                <input class="form-control" type="text" name="metaTitle">
                            </td>
                            <td>
                                <input class="form-control" type="text" name="metaKeywords">
                            </td>
                            <td>
                                <input class="form-control" type="text" name="metaDescription">
                            </td>
                            <td class="text-center">
                                <div class="dropdown" data-trigger="hover" data-toggle="tooltip" title="{t}Delete{/t}">
                                    <button class="btn btn-outline-info no-border" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                    <ul class="dropdown-menu delete-dropdown dropdown-menu-right">
                                        <li class="text-center">{t}Do you really want to delete?{/t}</li>
                                        <li class="divider"></li>
                                        <li class="text-center">
                                            <button class="btn btn-outline-danger delete-category">
                                                <span class="btn-icon"><i class="far fa-trash-alt"></i></span> {t}Delete{/t}
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6"></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-outline-success no-border category-add" data-trigger="hover" data-toggle="tooltip" data-title="{t}Add Category{/t}">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-success mb-3 {if $activeLanguage->isRTL()}ml-3{else}mr-3{/if} pull-right save-category">
                            <span class="btn-icon"><i class="far fa-save"></i></span> {t}Save{/t}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/block}
{block name=footerPageJs append}{/block}
{block name=footerCustomJs append}
    <script>
        $(document).ready(function () {
            $('.category-add').on('click', function (e) {
                e.preventDefault();
                var template = $('.copy-template');
                var container = template.closest('.repeat-container');
                var newRow = template.clone();
                newRow.insertBefore(container);
                newRow.removeClass('copy-template');
                app.tooltip_popover();
                return false;
            });
            $(document).on('change', 'input,textarea,select', function () {
                $(this).closest('tr').attr('data-changed', true);
            });
            var categoryEditUrl = '{$routes->getRouteString("categoryEdit",[])}';
            var categoryDeleteUrl = '{$routes->getRouteString("categoryDelete",[])}';
            $('.save-category').on('click', function (e) {
                e.preventDefault();
                var rowData;
                $('tr').each(function (index, element) {
                    var name, dataChanged = $(element).attr('data-changed');
                    if (dataChanged == 'true') {
                        rowData = $(element).find('input,textarea,select').serialize();
                        $.ajax({
                            dataType: 'json',
                            method: 'POST',
                            data: rowData,
                            url: $(element).attr('data-action'),
                            beforeSend: function () {
                                app.card.loading.start($("#categories"));
                            },
                            success: function (data) {
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                } else {
                                    if (data.error) {
                                        app.notification('error', data.error);
                                    } else {
                                        $(element).attr('data-action', categoryEditUrl.replace("[categoryId]", data.categoryId)).attr('data-changed', false).find('.category-id').val(data.categoryId);
                                        $(element).find('.delete-category').attr('data-delete', categoryDeleteUrl.replace("[categoryId]", data.categoryId));
                                        name = $(element).find('.category-name').val();
                                        app.notification('success', '{t}Data has been saved successfully{/t}');
                                    }
                                }
                            },
                            error: function (jqXHR, exception) {
                                app.notification('error', app.getErrorMessage(jqXHR, exception));
                            },
                            complete: function () {
                                app.card.loading.finish($("#categories"));
                            }
                        });
                    }
                });
            });
            $(document).on('click', '.delete-category', function () {
                var url = $(this).attr('data-delete');
                var row = $(this).closest('tr');
                if (url) {
                    $.ajax({
                        dataType: 'json',
                        method: 'POST',
                        url: $(this).attr('data-delete'),
                        beforeSend: function () {
                            app.card.loading.start($("#categories"));
                        },
                        success: function (data) {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                if (data.error) {
                                    app.notification('error', data.error);
                                } else {
                                    row.remove();
                                    app.notification('success', data.success);
                                }
                            }
                        },
                        error: function (jqXHR, exception) {
                            app.notification('error', app.getErrorMessage(jqXHR, exception));
                        },
                        complete: function () {
                            app.card.loading.finish($("#categories"));
                        }
                    });
                } else {
                    row.remove();
                    $('.tooltip.show').remove();
                }
            });
        });
    </script>
{/block}