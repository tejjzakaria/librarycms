{extends file='admin/admin.tpl'}
{block name=title}{t}Languages{/t}{/block}
{block name=content}
    <div class="row">
        <div class="col-sm-12">
            <div class="card" id="languages">
                <form action="{$routes->getRouteString("languageListView")}" method="post">
                    <table class="table table-hover table-striped table-hover">
                        <thead>
                            <tr>
                                <th>{t}Language Name{/t}</th>
                                <th>{t}Language Code{/t}</th>
                                <th>{t}Language Short Code{/t}</th>
                                <th class="text-center">{t}RTL{/t}</th>
                                <th class="text-center">{t}Status{/t}</th>
                                <th style="width: 70px"></th>
                            </tr>
                        </thead>
                        <tbody class="repeat-container">
                            {if isset($allLanguages) and allLanguages != null}
                                {foreach from=$allLanguages item=language name=language}
                                    <tr class="language">
                                        <td>
                                            <input class="form-control" type="text" name="name[{$smarty.foreach.language.index}]" value="{$language->getName()}">
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" name="code[{$smarty.foreach.language.index}]" value="{$language->getCode()}">
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" name="shortCode[{$smarty.foreach.language.index}]" value="{$language->getShortCode()}">
                                        </td>
                                        <td class="text-center">
                                            <label class="switch switch-sm">
                                                <input type="checkbox" name="RTL[{$smarty.foreach.language.index}]" value="1"{if $language->isRTL()} checked{/if}>
                                            </label>
                                        </td>
                                        <td class="text-center">
                                            <label class="switch switch-sm">
                                                <input type="checkbox" name="status[{$smarty.foreach.language.index}]" value="1"{if $language->isActive()} checked{/if}>
                                            </label>
                                        </td>
                                        <td class="text-center">
                                            <input type="hidden" name="id[{$smarty.foreach.language.index}]" value="{$language->getId()}"/>
                                            {if $language->getId() != 1}
                                                <div class="dropdown" data-trigger="hover" data-toggle="tooltip" title="{t}Delete{/t}">
                                                    <button class="btn btn-outline-info no-border" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="far fa-trash-alt"></i>
                                                    </button>
                                                    <ul class="dropdown-menu delete-dropdown dropdown-menu-right">
                                                        <li class="text-center">{t}Do you really want to delete?{/t}</li>
                                                        <li class="divider"></li>
                                                        <li class="text-center">
                                                            <button class="btn btn-outline-danger delete-lang">
                                                                <span class="btn-icon"><i class="far fa-trash-alt"></i></span> {t}Delete{/t}
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            {/if}
                                        </td>
                                    </tr>
                                {/foreach}
                            {/if}
                            <tr class="copy-template language">
                                <td>
                                    <input class="form-control" type="text" name="name[count]" disabled>
                                </td>
                                <td>
                                    <input class="form-control" type="text" name="code[count]" disabled>
                                </td>
                                <td>
                                    <input class="form-control" type="text" name="shortCode[count]" disabled>
                                    <input type="hidden" name="RTL[count]" value="0" disabled>
                                </td>
                                <td class="text-center">
                                    <label class="switch switch-sm">
                                        <input type="checkbox" name="RTL[count]" value="1" disabled>
                                    </label>
                                </td>
                                <td class="text-center">
                                    <label class="switch switch-sm">
                                        <input type="checkbox" name="status[count]" value="1" disabled>
                                    </label>
                                </td>
                                <td class="text-center">
                                    <input type="hidden" name="id[count]" disabled>
                                    <div class="dropdown" data-trigger="hover" data-toggle="tooltip" title="{t}Delete{/t}">
                                        <button class="btn btn-outline-info no-border" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="far fa-trash-alt"></i>
                                        </button>
                                        <ul class="dropdown-menu delete-dropdown dropdown-menu-right">
                                            <li class="text-center">{t}Do you really want to delete?{/t}</li>
                                            <li class="divider"></li>
                                            <li class="text-center">
                                                <button class="btn btn-outline-danger delete-lang">
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
                                <td colspan="5"></td>
                                <td class="text-center">
                                    <button type="button" class="add btn btn-outline-success no-border lang-add">
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
            $('.lang-add').on('click', function (e) {
                e.preventDefault();
                var template = $('.copy-template');
                var container = template.closest('.repeat-container');
                var newRow = template.clone();
                var langLength = container.find('tr:visible').length;
                var count = langLength + 1;
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

            $(document).on('click', '.delete-lang', function () {
                var row = $(this).closest('tr');
                row.remove();
                $('.tooltip.show').remove();
            });
        });
    </script>
{/block}