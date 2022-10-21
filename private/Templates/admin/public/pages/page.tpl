{extends file='admin/admin.tpl'}
{block name=title}{if $action == "create"}{t}Add Page{/t}{else}{t}Edit Page{/t}{/if}{/block}
{block name=toolbar}
    {if $action == "edit"}
        <div class="heading-elements">
            <a href="{$routes->getRouteString("pageViewPublic",["pageUrl"=>$page->getUrl()])}" class="btn btn-outline-info btn-icon-fixed" target="_blank">
                <span class="btn-icon"><i class="far fa-eye"></i></span> {t}View Page{/t}
            </a>
        </div>
    {/if}
{/block}
{block name=headerCss append}
    <link href="{$resourcePath}assets/css/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet"/>
    <link href="{$resourcePath}assets/js/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.css" rel="stylesheet" type="text/css" media="screen">
    <link href="{$resourcePath}assets/js/plugins/summernote/summernote-bs4.css" rel="stylesheet"/>
{/block}
{block name=content}
    {if $action == "create"}
        {assign var=route value=$routes->getRouteString("pageCreate")}
    {elseif $action == "edit" and isset($page)}
        {assign var=route value=$routes->getRouteString("pageEdit",["pageId"=>$page->getId()])}
    {elseif $action == "delete"}
        {assign var=route value=""}
    {/if}
    <form action="{$route}" method="post" class="row validate page-form" data-edit="{if $action == "create"}false{else}true{/if}">
        <div class="col-lg-8 col-xlg-9 col-md-7">
            <div class="card">
                <div class="card-body">
                    <input type="hidden" name="id" value="{if $action == "edit" and isset($page)}{$page->getId()}{/if}" class="pageId">
                    <input type="hidden" name="imageId" value="{if $action == "edit" and isset($page)}{$page->getImageId()}{/if}" class="imageId">
                    <div class="form-group">
                        <label for="title">{t}Title{/t}</label>
                        <input type="text" name="title" class="form-control" value="{if $action == "edit" and isset($page)}{$page->getTitle()}{/if}">
                    </div>
                    {if $action == "edit" and isset($page) and ($page->getId() == 0 or $page->getId() == 1000)}
                        <input type="hidden" name="partialUrl" value="{if $action == "edit" and isset($page)}{$page->getPartialUrl()}{/if}">
                    {else}
                        <div class="form-group">
                            <label for="partialUrl">{t}URL{/t}</label>
                            <input type="text" name="partialUrl" class="form-control input-sm" value="{if $action == "edit" and isset($page)}{$page->getPartialUrl()}{/if}" id="urlPath">
                        </div>
                    {/if}
                    <a data-toggle="modal" href="#pageGallery" class="btn btn-outline-info btn-icon-fixed mb-2" id="openGallery">
                        <i class="far fa-images mr-1"></i> {t}Gallery{/t}
                    </a>
                    <div class="form-group">
                        <label for="content">{t}Content{/t}</label>
                        <textarea class="" name="content" id="content-box">{if isset($page)}{$page->getContent()}{/if}</textarea>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="metaTitle">{t}Meta Title{/t}</label>
                        <input type="text" name="metaTitle" class="form-control" value="{if $action == "edit"}{$page->getMetaTitle()}{/if}">
                    </div>
                    <div class="form-group">
                        <label for="shortDescription">{t}Meta Keywords{/t}</label>
                        <select name="metaKeySelect" id="meta-key" class="form-control" multiple>
                            {if $action == "edit"}
                                {assign var="tagList" value=","|explode:$page->getMetaKeywords()}
                                {foreach from=$tagList item=tag}
                                    {if $tag != null}
                                        <option value="{$tag}" selected>{$tag}</option>
                                    {/if}
                                {/foreach}
                            {/if}
                        </select>
                        <input type="hidden" name="metaKeywords" id="metaKeyList" value="{if $action == "edit"}{$page->getMetaKeywords()}{/if}">
                    </div>
                    <div class="form-group">
                        <label for="shortDescription">{t}Meta Description{/t}</label>
                        <textarea name="metaDescription" cols="30" rows="5" style="width:100% !important" class="form-control">{if $action == "edit"}{$page->getMetaDescription()}{/if}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-xlg-3 col-md-5">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="status">{t}Status{/t}</label>
                        {if isset($postStatuses)}
                            <select name="status" class="form-control select-picker">
                                {foreach $postStatuses as $pageStatusKey => $pageStatusValue}
                                    <option value="{$pageStatusKey}"{if isset($page) and $page->getStatus() !== null and $page->getStatus() ===$pageStatusValue} selected{/if}>{$pageStatusValue}</option>
                                {/foreach}
                            </select>
                        {/if}
                    </div>
                    <div class="form-group">
                        <label for="parentId">{t}Parent{/t}</label>
                        <select class="form-control select-picker" name="parentId" id="parentId">
                            {function printSelectValues node=null offset=""}
                                {if isset($node) and $node->getValue() !== null}
                                    <option value="{$node->getValue()->getId()}"{if isset($page) and $page->getParentId() === $node->getValue()->getId()} selected{/if}>{$offset}{$node->getValue()->getTitle()}</option>
                                {/if}
                                {foreach $node->getChildren() as $subNode}
                                    {printSelectValues node=$subNode offset = $offset|cat:"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"}
                                {/foreach}
                            {/function}

                            {if isset($pageTree)}
                                <option value="null">{t}Please select parent page:{/t}</option>
                                {foreach from=$pageTree->getRootNodes() item=rootNode}
                                    {printSelectValues node=$rootNode offset = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"}
                                {/foreach}
                            {/if}
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="indexTitle">{t}Published on{/t}</label>
                        <input class="form-control date-time-picker" type="text" name="publishDateTime" value="{if $action == "edit" and isset($page)}{$page->getPublishDateTime()}{/if}">
                    </div>
                    {if isset($customTemplates)}
                        <div class="form-group">
                            <label for="customTemplate" class="control-label">{t}Custom Template{/t}</label>

                            <select name="customTemplate" class="select-picker" style="width: 100% !important;">
                                <option value="">Select custom template</option>
                                {foreach from=$customTemplates item=template name=template}
                                    <option value="{$template->getFullTemplateFileName()}"{if isset($page) and strcmp($template->getFullTemplateFileName(),$page->getCustomTemplate())===0 } selected{/if}>{$template->getDisplayName()}</option>
                                {/foreach}
                            </select>
                        </div>
                    {/if}
                    <div class="form-group">
                        <button type="submit" class="btn btn-outline-secondary disabled pull-right save-page">
                            <span class="btn-icon"><i class="far fa-save"></i></span> {t}Save{/t}
                        </button>
                    </div>
                </div>
            </div>

            <div class="card" id="image-block">
                <div class="card-body">
                    <div class="drop-zone cover-drop-zone{if $action == "edit" and $page->getImageId() != null} cover-exist{/if}">
                        <label>{t escape=no}Drag & Drop your file or <span>Browse</span>{/t}</label>
                        <input type="file" accept="image/png, image/jpeg, image/gif" id="page-image" class="disabledIt" />
                        <button type="button" class="btn btn-info remove-page-image{if $action == "edit" and $page->getImageId() == null or $action == "create"} d-none{/if}" data-id="{if $action == "edit"}{$page->getImageId()}{/if}"><i class="far fa-trash-alt"></i></button>
                        {if $action == "edit" and $page->getImage() != null}
                            <img src="{$page->getImage()->getWebPath('small')}" class="img-fluid">
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="modal fade" id="pageGallery" tabindex="-1" role="dialog" aria-labelledby="pageGalleryLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 80%;">
            <div class="modal-content card">
                <div class="modal-body" id="galleryBlock">
                </div>
                <div class="modal-footer gallery-image-upload">
                    <div class="fileinput fileinput-new" data-provides="fileinput">
                        <span class="btn btn-sm btn-outline-secondary btn-file">
                            <span class="fileinput-new">{t}Select file{/t}</span>
                            <span class="fileinput-exists">{t}Change{/t}</span>
                            <input type="file" name="file">
                        </span>
                        <span class="fileinput-filename"></span>
                        <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">&times;</a>
                    </div>
                    <a href="#" class="btn btn-sm btn-outline-secondary uploadGalleryImage">
                        <i class="fa fa-upload"></i> {t}Upload{/t}
                    </a>
                </div>
            </div>
        </div>
    </div>
{/block}
{block name=footerPageJs append}
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/select2/select2.full.min.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/jasnyupload/fileinput.min.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/jquery-validate/jquery.validate.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/bootstrap-select/bootstrap-select.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/inputmask/jquery.inputmask.bundle.min.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/moment/moment.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/summernote/summernote-bs4.min.js"></script>
{/block}
{block name=footerCustomJs append}
    <script>
        $(document).ready(function () {
            $('.date-time-picker').datetimepicker({
                format: '{$siteViewOptions->getOptionValue("inputDateTimeFormatJS")}',
                keepOpen: true
            }).on('dp.change', function () {
                $(this).closest('form').attr('data-changed', true);
                $('.save-page').removeClass('btn-outline-secondary disabled').addClass('btn-outline-success');
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
                rules: {
                    title: {
                        required: true
                    }{if $action == "create" or ($action == "edit" and isset($page) and ($page->getId() != 0 or $page->getId() != 1000))},
                    partialUrl: {
                        required: true,
                        urlpath: true
                    }{/if}
                }
            });
            $('#urlPath').on('change', function (e) {
                var val = $(this).val();
                $(this).val(val.replace(/^\//, ''));
            });
            $('#meta-key').select2({
                multiple: true,
                tags: true,
                allowClear: true,
                language: {
                    noResults: function () {
                        return "{t}Please enter keywords{/t}";
                    }
                }
            }).on('change.select2', function () {
                $("#metaKeyList").val($(this).val());
            });
            $(document).on('click', '.ajax-page', function (e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('href'),
                    dataType: 'json',
                    beforeSend: function () {
                        app.card.loading.start($("#galleryBlock").parents('.modal-content'));
                    },
                    success: function (data) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                $("#galleryBlock").html(data.html);
                            }
                        }
                    },
                    complete: function () {
                        app.card.loading.finish($("#galleryBlock").parents('.modal-content'));
                    },
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    }
                });
            });
            var imagesUrl = '{$routes->getRouteString("imageListView",[])}';
            $('#openGallery').on('click', function (e) {
                e.preventDefault();
                $.ajax({
                    url: imagesUrl,
                    dataType: 'json',
                    beforeSend: function () {
                        app.card.loading.start($("#galleryBlock").parents('.modal-content'));
                    },
                    success: function (data) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                $("#galleryBlock").html(data.html);
                            }
                        }
                    },
                    complete: function () {
                        app.card.loading.finish($("#galleryBlock").parents('.modal-content'));
                    },
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    }
                });
            });

            $('#content-box').summernote().on('summernote.change', function () {
                $('.page-form').attr('data-changed', true);
                $('.save-page').removeClass('btn-outline-secondary disabled').addClass('btn-outline-success');
            });

            $(document).on('change', 'input,textarea,select', function () {
                $(this).closest('form').attr('data-changed', true);
                $('.save-page').removeClass('btn-outline-secondary disabled').addClass('btn-outline-success');
            });
            var pageEditUrl = '{$routes->getRouteString("pageEdit",[])}';
            $('.save-page').on('click', function (e) {
                e.preventDefault();
                var form = $(this).closest('form');
                var dataEdit = form.attr('data-edit');
                var dataChanged = form.attr('data-changed');
                if (dataChanged == 'true') {
                    if (form.valid()) {
                        $.ajax({
                            dataType: 'json',
                            method: 'POST',
                            data: form.serialize(),
                            url: form.attr('action'),
                            beforeSend: function () {
                                app.card.loading.start('.card');
                            },
                            success: function (data) {
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                } else {
                                    if (data.error) {
                                        app.notification('error', data.error);
                                    } else {
                                        form.attr('action', pageEditUrl.replace("[pageId]", data.pageId)).attr('data-changed', false);
                                        $('.pageId').val(data.pageId);
                                        app.notification('success', '{t}Data has been saved successfully{/t}');
                                        $('.save-page').removeClass('btn-outline-success').addClass('btn-outline-secondary disabled');
                                        if (dataEdit == 'false') {
                                            $('.page-title h3').text('{t}Edit Page{/t}');
                                            history.pushState(null, '', pageEditUrl.replace("[pageId]", data.pageId));
                                        }
                                        $(form).attr('data-edit', true);
                                    }
                                }
                            },
                            error: function (jqXHR, exception) {
                                app.notification('error', app.getErrorMessage(jqXHR, exception));
                            },
                            complete: function () {
                                app.card.loading.finish('.card');
                            }
                        });
                    } else {
                        app.notification('error', '{t}Validation errors occurred. Please confirm the fields and submit it again.{/t}');
                    }
                } else {
                    app.notification('information', '{t}There are no changes{/t}');
                }
            });
            $(document).on('click', '.close', function () {
                $(this).closest('.fileinput').find('input:file').removeClass('file-exists');
            });

            var imageUploadUrl = '{$routes->getRouteString("pageImageUpload",[])}';
            var imageDeleteUrl = '{$routes->getRouteString("imageDelete",[])}';
            $('#page-image').on('change', pageImageUpload);
            function pageImageUpload(event) {
                $('.page-form').attr('data-changed', true);
                var dropZone = $('.cover-drop-zone');
                function upload() {
                    var imageId = $('.imageId').val();
                    var imgData = new FormData();
                    imgData.set('file', file);
                    if (imageId) {
                        imgData.set('imageId', imageId);
                    }
                    $.ajax({
                        dataType: 'json',
                        method: 'POST',
                        processData: false,
                        contentType: false,
                        data: imgData,
                        url: imageUploadUrl,
                        beforeSend: function (data) {
                            app.card.loading.start('#image-block');
                        },
                        success: function (data) {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                if (data.error) {
                                    app.notification('error', data.error);
                                } else {
                                    $('.remove-post-image').attr('data-id', data.imageId);
                                    $('.imageId').val(data.imageId);
                                    app.notification('success', '{t}Image successfully uploaded{/t}');
                                    $('.save-page').click();
                                }
                            }
                        },
                        error: function (jqXHR, exception) {
                            app.notification('error', app.getErrorMessage(jqXHR, exception));
                        },
                        complete: function (data) {
                            app.card.loading.finish('#image-block');
                        }
                    });
                }
                if (window.File && window.FileReader && window.FileList && window.Blob) {
                    var file = event.target.files[0];
                    if ((/^image\/(gif|png|jpeg)$/i).test(file.type)) {
                        var reader = new FileReader();
                        reader.readAsDataURL(file);
                        reader.onload = function (e) {
                            var img = '<img src="' + e.target.result + '" class="img-fluid">';
                            $(dropZone).find('img').remove();
                            $(dropZone).addClass('cover-exist').append(img);
                            $('.remove-page-image').removeClass('d-none');
                            upload();
                        };
                    } else {
                        app.notification('error', '{t}Uploaded file is not a valid image. Only JPG, PNG and GIF files are allowed.{/t}');
                    }
                } else {
                    app.notification('error', '{t}The File APIs are not fully supported in this browser.{/t}');
                }
            }
            $(document).on('click', '.remove-page-image', function () {
                $('.page-form').attr('data-changed', true);
                var imgId = $(this).attr('data-id');
                if (imgId != undefined && imgId != null && imgId > 0) {
                    $.ajax({
                        dataType: 'json',
                        method: 'POST',
                        url: imageDeleteUrl.replace("[imageId]", imgId),
                        beforeSend: function (data) {
                            app.card.loading.start('#image-block');
                        },
                        success: function (data) {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                if (data.error) {
                                    app.notification('error', data.error);
                                } else {
                                    $('.cover-drop-zone').removeClass('cover-exist').find('img').remove();
                                    $('.remove-page-image').addClass('d-none');
                                    $('.imageId').val('');
                                    app.notification('success', data.success);
                                    $('.save-page').click();
                                }
                            }
                        },
                        error: function (jqXHR, exception) {
                            app.notification('error', app.getErrorMessage(jqXHR, exception));
                        },
                        complete: function (data) {
                            app.card.loading.finish('#image-block');
                        }
                    });
                }
            });

{*
            var imageDeleteUrl = '{$routes->getRouteString("imageDelete",[])}';
            $(document).on('clear.bs.fileinput', '.main-image-upload .fileinput', function () {
                var imgId = $(this).find('.delete-main-img').attr('data-id');
                if (imgId != undefined && imgId != null && imgId > 0) {
                    $.ajax({
                        dataType: 'json',
                        method: 'POST',
                        url: imageDeleteUrl.replace("[imageId]", imgId),
                        beforeSend: function (data) {
                            app.card.loading.start('#image-block');
                        },
                        success: function (data) {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                if (data.error) {
                                    app.notification('error', data.error);
                                } else {
                                    $('.imageId').val('');
                                    $('.save-page').click();
                                }
                            }
                        },
                        error: function (jqXHR, exception) {
                            app.notification('error', app.getErrorMessage(jqXHR, exception));
                        },
                        complete: function (data) {
                            app.card.loading.finish('#image-block');
                        }
                    });
                }
            });
            $(document).on('change.bs.fileinput', '.main-image-upload .fileinput, .gallery-image-upload .fileinput', function () {
                $(this).attr('data-changed', true);
                $(this).find('input:file').addClass('file-exists');
                $('.save-page').removeClass('btn-outline-secondary disabled').addClass('btn-outline-success');
            });
            var imageUploadUrl = '{$routes->getRouteString("pageImageUpload",[])}';
            $('.uploadMainImage').on('click', function (e) {
                e.preventDefault();
                var data, fileValue, file;

                var dataChanged = $('.main-image-upload .fileinput').attr('data-changed');
                var imageUploadElement = $('.main-image-upload');

                if (dataChanged == 'true') {
                    data = new FormData();
                    file = $(imageUploadElement).find('input:file');
                    fileValue = $(imageUploadElement).find('input:file').val();
                    var imageId = $('.imageId').val();
                    if ($(file)[0].files[0] != null) {
                        data.append('file', $(file)[0].files[0], fileValue);
                        if (imageId) {
                            data.append('imageId', imageId);
                        }
                    } else if ($(file).closest('.fileinput').hasClass('fileinput-new')) {
                        data.append('file', null);
                        if (imageId) {
                            data.append('imageId', imageId);
                        }
                    } else {
                        data.append('file', '');
                        if (imageId) {
                            data.append('imageId', imageId);
                        }
                    }
                    $.ajax({
                        dataType: 'json',
                        method: 'POST',
                        processData: false,
                        contentType: false,
                        data: data,
                        url: imageUploadUrl,
                        beforeSend: function () {
                            app.card.loading.start('#image-block');
                        },
                        success: function (data) {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                if (data.error) {
                                    app.notification('error', data.error);
                                } else {
                                    $('.imageId').val(data.imageId);
                                    $('.delete-main-img').attr('data-id', data.imageId);
                                    $('.save-page').click();
                                }
                            }
                        },
                        error: function (jqXHR, exception) {
                            app.notification('error', app.getErrorMessage(jqXHR, exception));
                        },
                        complete: function () {
                            app.card.loading.finish('#image-block');
                        }
                    });
                } else {
                    app.notification('information', '{t}Please Choose Image{/t}');
                }
            });
*}




            function dirname(path) {
                return path.replace(/\\/g, '/').replace(/\/[^\/]*$/, '');
            }

            function basename(path) {
                return path.replace(/\\/g, '/').replace(/.*\//, '');
            }

            $('.uploadGalleryImage').on('click', function (e) {
                e.preventDefault();
                var data, fileValue, file;
                var elem = $(this);
                var dataChanged = $('.gallery-image-upload .fileinput').attr('data-changed');
                var imageUploadElement = $('.gallery-image-upload');
                if (dataChanged == 'true') {
                    data = new FormData();
                    file = $(imageUploadElement).find('input:file');
                    fileValue = $(imageUploadElement).find('input:file').val();
                    if ($(file)[0].files[0] != null) {
                        data.append('file', $(file)[0].files[0], fileValue);
                    } else if ($(file).closest('.fileinput').hasClass('fileinput-new')) {
                        data.append('file', null);
                    } else {
                        data.append('file', '');
                    }
                    $.ajax({
                        dataType: 'json',
                        method: 'POST',
                        processData: false,
                        contentType: false,
                        data: data,
                        url: imageUploadUrl,
                        beforeSend: function () {
                            app.card.loading.start($("#galleryBlock").parents('.modal-content'));
                        },
                        success: function (data) {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                if (data.error) {
                                    app.notification('error', data.error);
                                } else {
                                    var row_template = $('.gallery-clone').clone();
                                    $(row_template).removeClass('gallery-clone').prependTo("#image-list");
                                    var path = dirname(data.url);
                                    var filename = basename(data.url);
                                    var smallImg = path + '/small/' + filename;
                                    var originalImg = data.url;
                                    $(row_template).find('.tile-image-title p').text(filename);
                                    $(row_template).find('.thumb-img').attr('src', smallImg);
                                    $(row_template).find('button').attr('data-id', data.imageId);
                                    $(row_template).find('.original-path').attr('data-clipboard-text', originalImg);
                                    $(elem).closest('.gallery-image-upload').find('.close').click();
                                }
                            }
                        },
                        error: function (jqXHR, exception) {
                            app.notification('error', app.getErrorMessage(jqXHR, exception));
                        },
                        complete: function () {
                            app.card.loading.finish($("#galleryBlock").parents('.modal-content'));
                        }
                    });
                } else {
                    app.notification('information', '{t}Please Choose Image{/t}');
                }

            });
            (function () {
                function createNode(text) {
                    var node = document.createElement('pre');
                    node.style.width = '1px';
                    node.style.height = '1px';
                    node.style.position = 'fixed';
                    node.style.top = '5px';
                    node.textContent = text;
                    return node;
                }

                function copyNode(node) {
                    var selection = getSelection();
                    selection.removeAllRanges();

                    var range = document.createRange();
                    range.selectNodeContents(node);
                    selection.addRange(range);

                    document.execCommand('copy');
                    selection.removeAllRanges();
                }

                function copyText(text) {
                    var node = createNode(text);
                    document.body.appendChild(node);
                    copyNode(node);
                    document.body.removeChild(node);
                }

                function copyInput(node) {
                    node.select();
                    document.execCommand('copy');
                    getSelection().removeAllRanges();
                }

                function isFormInput(element) {
                    return element.nodeName === 'INPUT' || element.nodeName === 'TEXTAREA';
                }

                $(document).on('click', '.copy-path', function () {
                    var text;
                    if (text = this.getAttribute('data-clipboard-text')) {
                        copyText(text);
                    } else {
                        var container = this.closest('.js-copy-container');
                        var content = container.querySelector('.js-copy-target');
                        if (isFormInput(content)) {
                            if (content.type === 'hidden') {
                                copyText(content.value);
                            } else {
                                copyInput(content);
                            }
                        } else {
                            copyNode(content);
                        }
                    }
                    this.blur();
                });
            }).call(this);
            $(document).on('click', '.delete-img', function (e) {
                e.preventDefault();
                var imgId = $(this).attr('data-id');
                var elem = $(this).closest('.gallery-img');
                if (imgId != undefined && imgId != null) {
                    $.ajax({
                        dataType: 'json',
                        method: 'POST',
                        url: imageDeleteUrl.replace("[imageId]", imgId),
                        beforeSend: function () {
                            app.card.loading.start($("#galleryBlock").parents('.modal-content'));
                        },
                        success: function (data) {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                if (data.error) {
                                    app.notification('error', data.error);
                                } else {
                                    $(elem).remove();
                                }
                            }
                        },
                        error: function (jqXHR, exception) {
                            app.notification('error', app.getErrorMessage(jqXHR, exception));
                        },
                        complete: function () {
                            app.card.loading.finish($("#galleryBlock").parents('.modal-content'));
                        }
                    });
                }
            });
        });
    </script>
{/block}