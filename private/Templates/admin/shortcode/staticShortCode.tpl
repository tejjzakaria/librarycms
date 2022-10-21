{extends file='admin/admin.tpl'}
{block name=title}{t}Static ShortCodes{/t}{/block}
{block name=headerCss append}
    <link href="{$resourcePath}assets/css/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet"/>
{/block}
{block name=content}
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    {assign var=route value=$routes->getRouteString("staticShortCodeListView")}
                    <form action="{$route}" method="post" class="validate" id="form">
                        <div class="shortcode-container">
                            {if isset($shortCodes) and $shortCodes != null and !empty($shortCodes)}
                                {foreach from=$shortCodes item=shortCode name=shortCode}
                                    <div class="shortcode">
                                        <div class="input-group">
                                            <input class="form-control code-field" type="text" name="code[{$smarty.foreach.shortCode.index}]" placeholder="{t}Code Name{/t}" value="{$shortCode->getCode()}">
                                            <div class="input-group-btn">
                                                <button class="btn btn-outline-info" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="far fa-trash-alt"></i>
                                                </button>
                                                <ul class="dropdown-menu delete-dropdown dropdown-menu-right p-2">
                                                    <li class="text-center">{t}Do you really want to delete?{/t}</li>
                                                    <li class="divider"></li>
                                                    <li class="text-center">
                                                        <button class="btn btn-outline-danger delete-shortcode">
                                                            <span class="btn-icon"><i class="far fa-trash-alt"></i></span> {t}Delete{/t}
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <textarea name="code" cols="30" rows="5" id="code{$smarty.foreach.shortCode.index}" class="form-control editor-field">{$shortCode->getValue()}</textarea>
                                        <textarea name="value[{$smarty.foreach.shortCode.index}]" class="form-control value-field d-none">{$shortCode->getValue()}</textarea>
                                    </div>
                                {/foreach}
                            {/if}
                        </div>
                        <a href="#" class="btn btn-outline-info mt-3 pull-left" id="add-shortcode">{t}Add ShortCode{/t}</a>
                        <button type="submit" class="btn btn-success pull-right mt-3">
                            <span class="btn-icon"><i class="far fa-save"></i></span> {t}Save{/t}
                        </button>
                    </form>
                    <div class="shortcode copy-template">
                        <div class="input-group">
                            <input class="form-control code-field" type="text" name="code[]" placeholder="{t}Code Name{/t}">
                            <div class="input-group-btn">
                                <button class="btn btn-outline-info" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="far fa-trash-alt"></i>
                                </button>
                                <ul class="dropdown-menu delete-dropdown dropdown-menu-right p-2">
                                    <li class="text-center">{t}Do you really want to delete?{/t}</li>
                                    <li class="divider"></li>
                                    <li class="text-center">
                                        <button class="btn btn-outline-danger delete-shortcode">
                                            <span class="btn-icon"><i class="far fa-trash-alt"></i></span> {t}Delete{/t}
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <textarea name="code" cols="30" rows="5" id="" class="form-control editor-field"></textarea>
                        <textarea name="value[]" class="form-control value-field d-none"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/block}
{block name=footerPageJs append}
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/ace/ace.js" charset="utf-8"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/bootstrap-select/bootstrap-select.js"></script>
{/block}
{block name=footerCustomJs append}
    <script>
        $(document).ready(function () {
            function editor(element) {
                var code = $(element).find('.editor-field');
                var codeValue = $(element).find('.value-field');
                var editor = ace.edit(code.attr('id'));
                editor.setTheme("ace/theme/monokai");
                editor.session.setMode("ace/mode/smarty");
                editor.setOptions({
                    maxLines: 15,
                    showPrintMargin: false,
                    fontSize: '14px'
                });
                editor.getSession().on('change', function(){
                    codeValue.val(editor.getSession().getValue());
                });
            }

            $('.shortcode:not(.copy-template)').each(function (index, element) {
                editor(element);
            });

            $('#add-shortcode').on('click', function (e) {
                e.preventDefault();
                var container = $('.shortcode-container'),
                        template = $('.copy-template').clone(),
                        count = 0;
                container.append(template.removeClass('copy-template'));
                $('.shortcode:not(.copy-template)').each(function (index, element) {
                    $(element).find('.editor-field').attr('id', 'code' + count);
                    $(element).find('.code-field').attr('name', 'code[' + count + ']');
                    $(element).find('.value-field').attr('name', 'value[' + count + ']');
                    count++;
                });
                editor(template);
            });

            $(document).on('click', '.delete-shortcode', function (e) {
                e.preventDefault();
                $(this).closest('.shortcode').remove();
            });
            $('#form').submit(function() {
                var count = 0;
                $('.shortcode:not(.copy-template)').each(function (index, element) {
                    $(element).find('.code-field').attr('name', 'code[' + count + ']');
                    $(element).find('.value-field').attr('name', 'value[' + count + ']');
                    count++;
                });
                return true;
            });
        });
    </script>
{/block}