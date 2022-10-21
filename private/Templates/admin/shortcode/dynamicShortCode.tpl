{extends file='admin/admin.tpl'}
{block name=title}{t}Dynamic ShortCodes{/t}{/block}
{block name=content}
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                {assign var=route value=$routes->getRouteString("dynamicShortCodeListView")}
                <form action="{$route}" method="post" class="validate">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>{t}Code{/t}</th>
                                <th>{t}Column Name{/t}</th>
                                <th style="width: 60px">{t}Action{/t}</th>
                            </tr>
                        </thead>
                        <tbody class="shortcode-container">
                            {if isset($shortCodes) and $shortCodes != null and !empty($shortCodes)}
                                {foreach from=$shortCodes item=shortCode name=shortCode}
                                    <tr class="shortcode-row">
                                        <td>
                                            <input class="form-control code-field" type="text" name="code[{$smarty.foreach.shortCode.index}]" value="{$shortCode->getCode()}">
                                        </td>
                                        <td>
                                            <input class="form-control columnName-field" type="text" name="columnName[{$smarty.foreach.shortCode.index}]" value="{$shortCode->getColumnName()}">
                                        </td>
                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button class="btn btn-outline-info btn-sm" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="far fa-trash-alt"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    <li class="text-center">{t}Do you really want to delete?{/t}</li>
                                                    <li class="divider"></li>
                                                    <li class="text-center">
                                                        <button class="btn btn-outline-secondary delete-shortcode">
                                                            <i class="far fa-trash-alt"></i> {t}Delete{/t}
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                {/foreach}
                            {/if}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2"></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-success" id="add-shortcode" data-trigger="hover" data-toggle="tooltip" title="{t}Add{/t}">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn-success pull-right mb-3 mr-3">
                                    <i class="far fa-save"></i> {t}Save{/t}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
{/block}
{block name=footerCustomJs append}
    <script>
        $(document).ready(function () {
            var shortcode_markup = "<tr class='shortcode-row'>" +
                    "<td>" +
                    "<input class='form-control code-field' type='text' name='code[]'>" +
                    "</td>" +
                    "<td>" +
                    "<input class='form-control columnName-field' type='text' name='columnName[]'>" +
                    "</td>" +
                    "<td class='text-center'>" +
                    "<div class='dropdown'>" +
                    "<button class='btn btn-outline-info btn-sm' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>" +
                    "<i class='far fa-trash-alt'></i>" +
                    "</button>" +
                    "<ul class='dropdown-menu dropdown-menu-right'>" +
                    "<li class='text-center'>{t}Do you really want to delete?{/t}</li>" +
                    "<li class='divider'></li>" +
                    "<li class='text-center'>" +
                    "<button class='btn btn-outline-secondary delete-shortcode'>" +
                    "<i class='far fa-trash-alt'></i> {t}Delete{/t}" +
                    "</button>" +
                    "</li>" +
                    "</ul>" +
                    "</div>" +
                    "</td>" +
                    "</tr>";
            $('#add-shortcode').on('click', function (e) {
                e.preventDefault();
                var container = $('.shortcode-container');
                container.append(shortcode_markup);
            });
            function changedShortCode() {
                var count = 0;
                $('.shortcode-container tr').each(function (index, element) {
                    $(element).find('.code-field').attr('name', 'code[' + count + ']');
                    $(element).find('.columnName-field').attr('name', 'columnName[' + count + ']');
                    count++;
                });
            }
            $(document).on('click', '.delete-shortcode', function (e) {
                e.preventDefault();
                $(this).closest('tr').remove();
                changedShortCode();
            });
            $(document).on('change', 'input', function (e) {
                changedShortCode();
            });
        });
    </script>
{/block}