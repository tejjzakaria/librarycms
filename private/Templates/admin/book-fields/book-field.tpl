{extends file='admin/admin.tpl'}
{block name=title}{if $action == "create"}{t}Add Field{/t}{else}{t}Edit Field{/t}{/if}{/block}
{block name=content}
    <div class="row">
        <div class="col-md-12">
            <div class="card" id="field-block">
                <div class="card-body">
                    {if $action == "create"}
                        {assign var=route value=$routes->getRouteString("bookFieldCreate")}
                    {elseif $action == "edit" and isset($bookField)}
                        {assign var=route value=$routes->getRouteString("bookFieldEdit",["bookFieldId"=>$bookField->getId()])}
                    {elseif $action == "delete"}
                        {assign var=route value=""}
                    {/if}
                    <form action="{$route}" method="post" class="validate" data-edit="{if $action == "create"}false{else}true{/if}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="control-label">{t}Name{/t}</label>
                                    <input type="text" class="form-control" autocomplete="off" data-name="{if $action == "edit"}{$bookField->getName()}{/if}" id="fieldName" name="name" value="{if $action == "edit"}{$bookField->getName()}{/if}" {if $action == "edit"}readonly{/if}>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sqlTypes" class="control-label">{t}Type{/t}</label>
                                    {if $action == "edit"}
                                        <input type="text" class="form-control" autocomplete="off" name="type" value="{$bookField->getType()}" readonly>
                                    {else}
                                        {if isset($sqlTypes) and $sqlTypes != null}
                                            <select class="form-control custom-select" id="sqlTypes" autocomplete="off" name="type">
                                                {foreach from=$sqlTypes key="key" item="type"}
                                                    <option value="{$type}"{if $action == "edit" and $bookField->getType() == $type} selected{/if}>{$key}</option>
                                                {/foreach}
                                            </select>
                                        {/if}
                                    {/if}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="control-label">{t}Title{/t}</label>
                                    <input type="text" class="form-control" autocomplete="off" name="title" value="{if $action == "edit"}{$bookField->getName()}{/if}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="controlTypes" class="control-label">{t}Control{/t}</label>
                                    {if isset($controlTypes) and $controlTypes != null}
                                        <select class="form-control custom-select" id="controlTypes" autocomplete="off" name="control">
                                            {foreach from=$controlTypes key="key" item="type"}
                                                <option value="{$type}"{if $action == "edit" and $bookField->getControl() == $type} selected{/if}>{$type}</option>
                                            {/foreach}
                                        </select>
                                    {/if}
                                </div>
                            </div>
                        </div>
                        <div class="row {if ($action == "edit" and $bookField->getListValues() == null) or $action == "create"}d-none{/if}" id="listValueBlock">
                            <div class="col-lg-12">
                                <div class="table-responsive-sm">
                                    <table class="table table-hover table-striped table-hover">
                                        <thead>
                                        <tr>
                                            <th>{t}List Values{/t}</th>
                                            <th style="width: 70px;"></th>
                                        </tr>
                                        </thead>
                                        <tbody class="repeat-container">
                                        {if $action == "edit" and $bookField->getListValues() !== null}
                                            {foreach from=$bookField->getListValues() item=listValue name=listValue}
                                                <tr class="category" data-changed="false">
                                                    <td>
                                                        <input class="form-control" type="text" name="listValues[]" value="{$listValue->getValue()}">
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="dropdown" data-trigger="hover" data-toggle="tooltip"
                                                             title="{t}Delete{/t}">
                                                            <button class="btn btn-outline-info no-border" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                <i class="far fa-trash-alt"></i>
                                                            </button>
                                                            <ul class="dropdown-menu delete-dropdown dropdown-menu-right">
                                                                <li class="text-center">{t}Do you really want to delete?{/t}</li>
                                                                <li class="divider"></li>
                                                                <li class="text-center">
                                                                    <button class="btn btn-outline-danger delete-value">
                                                                        <span class="btn-icon"><i class="far fa-trash-alt"></i></span> {t}Delete{/t}
                                                                    </button>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            {/foreach}
                                        {/if}
                                        <tr class="copy-template value" data-changed="">
                                            <td>
                                                <input class="form-control value-name" type="text" name="listValues[]" disabled>
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
                                                            <button class="btn btn-outline-danger delete-value">
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
                                            <td></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-outline-success no-border value-add" data-trigger="hover" data-toggle="tooltip" data-title="{t}Add Value{/t}">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row margin-top-20">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-outline-secondary disabled pull-right mr-2 save-field">
                                       <span class="btn-icon"> <i class="far fa-save"></i></span> {t}Save{/t}
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
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/jquery-validate/jquery.validate.js"></script>
{/block}
{block name=footerCustomJs append}
    <script>
        $(document).ready(function () {
            $('#controlTypes').on('change', function (e) {
                e.preventDefault();
                var value = $(this).val();
                if (value == 'SELECT') {
                    $('#listValueBlock').removeClass('d-none');
                    $('.value-add').click();
                }
            });
            $('.value-add').on('click', function (e) {
                e.preventDefault();
                var template = $('.copy-template');
                var container = template.closest('.repeat-container');
                var newRow = template.clone();
                newRow.find('input').removeAttr('disabled');
                newRow.appendTo(container);
                newRow.removeClass('copy-template');
                app.tooltip_popover();
                return false;
            });
            $(document).on('click', '.delete-value', function () {
                var row = $(this).closest('tr');
                $(this).closest('form').attr('data-changed', true);
                $('.save-field').removeClass('btn-outline-secondary  disabled').addClass('btn-outline-success');
                row.remove();
                $('.tooltip.show').remove();
            });
            $(document).on('change', 'input,textarea,select', function () {
                $(this).closest('form').attr('data-changed', true);
                $('.save-field').removeClass('btn-outline-secondary  disabled').addClass('btn-outline-success');
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
                messages: {
                    name: {
                        remote: jQuery.validator.format("<strong>{literal}{0}{/literal}</strong> {t}is already exist. Please use another Name{/t}.")
                    }
                },
                rules: {
                    title: {
                        required: true
                    },
                    type: {
                        required: true
                    },
                    control: {
                        required: true
                    }{if $action == "create"},
                    name: {
                        required: true,
                        remote: {
                            param: {
                                delay: 500,
                                url: '{$routes->getRouteString("bookFieldCheck",[])}',
                                type: "post",
                                data: {
                                    bookFieldName: function () {
                                        return $("#fieldName").val();
                                    }
                                },
                                error: function (jqXHR, exception) {
                                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                                }
                            },
                            depends: function (element) {
                                return ($(element).val() !== $("#fieldName").attr('data-name'));
                            }
                        }
                    }{/if}
                }
            });
            var bookFieldEditUrl = '{$routes->getRouteString("bookFieldEdit",[])}';
            $('.save-field').on('click', function (e) {
                e.preventDefault();
                var form = $(this).closest('form');
                var dataEdit = form.attr('data-edit');
                var dataChanged = form.attr('data-changed');
                var fieldName = $('#fieldName').val();
                if (dataChanged == 'true') {
                    if ($(form).valid()) {
                        $.ajax({
                            dataType: 'json',
                            method: 'POST',
                            data: form.serialize(),
                            url: form.attr('action'),
                            beforeSend: function (data) {
                                app.card.loading.start('#field-block');
                            },
                            success: function (data) {
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                } else {
                                    if (data.error) {
                                        app.notification('error', data.error);
                                    } else {
                                        form.attr('action', bookFieldEditUrl.replace("[bookFieldId]", data.bookFieldId)).attr('data-changed', false);
                                        app.notification('success', '{t}Data has been saved successfully{/t}');
                                        $('#fieldName').attr('data-name', fieldName);
                                        $('.save-field').removeClass('btn-outline-success').addClass('btn-outline-secondary disabled');
                                        if (dataEdit == 'false') {
                                            $('.page-title h3').text('{t}Edit Field{/t}');
                                            history.pushState(null, '', bookFieldEditUrl.replace("[bookFieldId]", data.bookFieldId));
                                        }
                                        $(form).attr('data-edit', true);
                                    }
                                }
                            },
                            error: function (jqXHR, exception) {
                                app.notification('error', app.getErrorMessage(jqXHR, exception));
                            },
                            complete: function (data) {
                                app.card.loading.finish('#field-block');
                            }
                        });
                    } else {
                        app.notification('information', '{t}Validation errors occurred. Please confirm the fields and submit it again.{/t}');
                    }
                } else {
                    app.notification('information', '{t}Nothing to save.{/t}');
                }
            });
        });
    </script>
{/block}