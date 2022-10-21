{extends file='admin/admin.tpl'}
{block name=title}{t}Sizes{/t}{/block}
{block name=content}
    <div class="row">
        <div class="col-sm-12">
            <div class="card" id="bookSize">
                <form action="{$routes->getRouteString("bookSizeListView")}" method="post">
                    <table class="table table-hover table-striped table-hover">
                        <thead>
                            <tr>
                                <th>{t}Name{/t}</th>
                                <th style="width: 70px"></th>
                            </tr>
                        </thead>
                        <tbody class="repeat-container">
                            {if isset($bookSizes) and bookSizes != null}
                                {foreach from=$bookSizes item=size name=size}
                                    <tr class="size">
                                        <td>
                                            <input class="form-control" type="text" name="names[{$smarty.foreach.size.index}]" value="{$size->getName()}">
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
                                                        <button class="btn btn-outline-danger delete-size">
                                                            <span class="btn-icon"><i class="far fa-trash-alt"></i></span> {t}Delete{/t}
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                {/foreach}
                            {/if}
                            <tr class="copy-template size">
                                <td>
                                    <input class="form-control" type="text" name="names[count]" disabled>
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
                                                <button class="btn btn-outline-danger delete-size">
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
                                    <button type="button" class="add btn btn-outline-success no-border size-add">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    <button type="submit" class="btn btn-success pull-right {if $activeLanguage->isRTL()}ml-3{else}mr-3{/if} mb-3">
                        <span class="btn-icon"><i class="far fa-save"></i></span> {t}Save{/t}
                    </button>
                </form>
            </div>
        </div>
    </div>
{/block}
{block name=footerCustomJs append}
    <script>
        $(document).ready(function () {
            $('.size-add').on('click', function (e) {
                e.preventDefault();
                var template = $('.copy-template');
                var container = template.closest('.repeat-container');
                var newRow = template.clone();
                var sizeLength = container.find('tr:visible').length;
                var count = sizeLength + 1;
                $('input', newRow).each(function () {
                    $.each(this.attributes, function (index, element) {
                        this.value = this.value.replace('[count]', '[' + count + ']');
                    });
                });
                newRow.removeClass('copy-template');
                newRow.find('input').removeAttr('disabled');
                newRow.appendTo(container);
                app.tooltip_popover();
                return false;
            });

            $(document).on('click', '.delete-size', function () {
                var row = $(this).closest('tr');
                row.remove();
                $('.tooltip.show').remove();
            });
        });
    </script>
{/block}