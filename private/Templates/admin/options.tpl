{extends file='admin/admin.tpl'}
{block name=title}{t}Settings{/t}{/block}
{block name=headerCss append}
    <link href="{$resourcePath}assets/css/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet"/>
{/block}
{block name=content}
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body p-0">
                    <form action="{$routes->getRouteString("optionListView")}" class="" id="options-form" method="post">
                        <ul class="nav nav-tabs customtab" role="tablist">
                            {if isset($options)}
                                {foreach from=$options item=option key=key name=group}
                                    <li class="nav-item">
                                        <a class="nav-link {if $smarty.foreach.group.first}active{/if}" data-toggle="tab" href="#{$key}" role="tab">
                                            {$siteViewOptions->getOptionGroup("$key")->getTitle()}
                                        </a>
                                    </li>
                                {/foreach}
                            {/if}
                        </ul>
                        <div class="tab-content">
                            {if isset($options)}
                                {foreach from=$options item=option key=key name=group}
                                    <div class="tab-pane {if $smarty.foreach.group.first}active{/if} p-20" id="{$key}" role="tabpanel">
                                        <div class="row">
                                            {foreach key=key item=item from=$option}
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="{$item->getName()}" class="control-label">{t}{$item->getTitle()}{/t} {if $item->getDescription() != null}
                                                            <i class="icon-info" data-toggle="tooltip" data-html="true" data-placement="top" title="{t}{$item->getDescription()}{/t}"></i>{/if}
                                                        </label>

                                                        {if $item->getControl() == "button"}
                                                            <br>
                                                            <button class="btn btn-outline-primary" data-value="{$item->getValue()}" id="{$item->getName()}">{t}{$item->getTitle()}{/t}</button>
                                                        {/if}
                                                        {if $item->getControl() == "input"}
                                                            <input type="text" class="form-control" autocomplete="off" name="{$item->getName()}" value="{$item->getValue()}">
                                                        {/if}
                                                        {if $item->getControl() == "checkbox"}
                                                            <br>
                                                            <label class="switch switch-sm">
                                                                <input type="checkbox" name="{$item->getName()}" value="{$item->getValue()}" {if $item->getValue()}checked{/if}>
                                                            </label>
                                                        {/if}
                                                        {if $item->getControl() == "select" and $item->getListValues() != null}
                                                            <select class="form-control select-picker" name="{$item->getName()}">
                                                                {foreach from=$item->getListValues() key=key item=value}
                                                                    <option value="{$key}"{if strcmp($key,$item->getValue()) === 0} selected{/if}>{t}{$value}{/t}</option>
                                                                {/foreach}
                                                            </select>
                                                        {/if}
                                                        {if $item->getControl() == "file"}
                                                            <div class="card fileinput {if $item->getValue() != null}fileinput-exists{else}fileinput-new{/if}" style="width: 100%;" data-provides="fileinput">
                                                                <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 100%; height: 200px;">
                                                                    {if $item->getValue() != null}
                                                                        <img src="{$item->getValue()}" alt="" class="img-fluid">
                                                                    {/if}
                                                                </div>
                                                                <div>
                                                                    <a href="#" class="btn btn-sm btn-outline-secondary mr-1 fileinput-exists delete-image" data-dismiss="fileinput">{t}Remove{/t}</a>
                                                                    <span class="btn btn-sm btn-outline-secondary mr-1 btn-file file-input">
                                                                    <span class="fileinput-new">{t}Select image{/t}</span>
                                                                        <span class="fileinput-exists">{t}Change{/t}</span>
                                                                        <input type="file" name="file" value="{if $item->getValue() != null}{$item->getValue()}{/if}" class="disabledIt">
                                                                        <input class="file-path" name="{$item->getName()}" type="hidden" value="{if $item->getValue() != null}{$item->getValue()}{/if}" data-default="{$item->getDefaultValue()}">
                                                                    </span>
                                                                    <a href="#" class="btn btn-sm btn-outline-secondary uploadImage fileinput-exists">
                                                                        <i class="fa fa-upload"></i> {t}Upload{/t}
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        {/if}
                                                    </div>
                                                </div>
                                            {/foreach}
                                        </div>
                                    </div>
                                {/foreach}
                            {/if}
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success pull-right mb-3 mr-3">
                                <span class="btn-icon"><i class="far fa-save"></i></span> {t}Save Options{/t}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="logs" role="dialog" aria-labelledby="logsLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body console" id="console">
                    <div class="mCustomScrollBox" id="consoleMessages"></div>
                </div>
            </div>
        </div>
    </div>
{/block}
{block name=footerPageJs append}
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/jasnyupload/fileinput.min.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/jquery-validate/jquery.validate.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/bootstrap-select/bootstrap-select.js"></script>
{/block}
{block name=footerCustomJs append}
    <script>
        var genSiteMapUrl = '{$routes->getRouteString("sitemapGenerate",[])}';
        $('#generateSiteMap').on('click', function (e) {
            e.preventDefault();
            $('#logs').modal('show');
            $('consoleMessages').html('');
            var xhr = new XMLHttpRequest();
            xhr.addEventListener("progress", function (evt) {
                var lines = evt.currentTarget.response.split("\n");
                if (lines.length)
                    var progress = lines[lines.length - 1];
                else
                    var progress = 0;
                $(".console .mCSB_container").html(progress);
                $("#console").mCustomScrollbar("scrollTo",'bottom');
            }, false);
            xhr.open('POST', genSiteMapUrl, true);
            xhr.send();
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

        var fileUploadUrl = '{$routes->getRouteString("siteViewOptionFileUpload",[])}';
        var fileDeleteUrl = '{$routes->getRouteString("siteViewOptionFileDelete",[])}';
        $('.uploadImage').on('click', function (e) {
            e.preventDefault();
            var imageData;
            var container = $(this).closest('.fileinput');
            imageData = new FormData();
            var image = $(container).find('input:file');
            var imageValue = $(container).find('input:file').val();
            var filePath = $(container).find('.file-path');
            var filePathValue = $(filePath).val();
            var optionName = $(filePath).attr('name');
            imageData.append('file', $(image)[0].files[0], imageValue);
            if (optionName) {
                if(filePathValue) {
                    imageData.append('path', filePathValue);
                }
                imageData.append('optionName', optionName);
            }
            $.ajax({
                dataType: 'json',
                method: 'POST',
                processData: false,
                contentType: false,
                data: imageData,
                url: fileUploadUrl,
                beforeSend: function (data) {
                    app.card.loading.start(container);
                },
                success: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else {
                            $(filePath).val(data.path);
                        }
                    }
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                },
                complete: function (data) {
                    app.card.loading.finish(container);
                }
            });

        });
        $('#options-form').submit(function (e) {
            $('.file-path').each(function (index, element) {
                var filePathValue = $(element).val();
                var filePathDefaultValue = $(element).attr('data-default');

                function isEmpty(str) {
                    return (!str || 0 === str.length);
                }

                if (isEmpty(filePathValue)) {
                    $(element).val(filePathDefaultValue);
                }
            });
            return true;
        });
        $(document).on('clear.bs.fileinput', '.fileinput', function () {
            var filePath = $(this).find('.file-path');
            var filePathValue = filePath.val();
            var filePathDefaultValue = filePath.attr('data-default');
            if (filePathValue !== filePathDefaultValue) {
                if (filePathValue != undefined && filePathValue != null) {
                    $.ajax({
                        dataType: 'json',
                        method: 'POST',
                        data: {
                            path: filePathValue
                        },
                        url: fileDeleteUrl,
                        success: function (data) {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                if (data.error) {
                                    app.notification('error', data.error);
                                } else {
                                    $(filePath).val('');
                                }
                            }
                        },
                        error: function (jqXHR, exception) {
                            app.notification('error', app.getErrorMessage(jqXHR, exception));
                        }
                    });
                } else {
                    $(filePath).val('');
                }
            }
        });
    </script>
{/block}