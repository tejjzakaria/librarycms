{extends file='admin/admin.tpl'}
{block name=title}{if $action == "create"}{t}Add Author{/t}{else}{t}Edit Author{/t}{/if}{/block}
{block name=headerCss append}
    <link href="{$resourcePath}assets/js/plugins/summernote/summernote-bs4.css" rel="stylesheet"/>
{/block}
{block name=content}
    <div class="row">
        <div class="col-md-9">
            <div class="card" id="author-block">
                <div class="card-body">
                    {if $action == "create"}
                        {assign var=route value=$routes->getRouteString("authorCreate")}
                    {elseif $action == "edit" and isset($author)}
                        {assign var=route value=$routes->getRouteString("authorEdit",["authorId"=>$author->getId()])}
                    {elseif $action == "delete"}
                        {assign var=route value=""}
                    {/if}
                    <form action="{$route}" method="post" class="author-form" data-edit="{if $action == "create"}false{else}true{/if}">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name" class="control-label">{t}First Name{/t}</label>
                                    <input type="text" class="form-control" autocomplete="off" name="firstName" value="{if $action == "edit"}{$author->getFirstName()}{/if}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name" class="control-label">{t}Middle Name{/t}</label>
                                    <input type="text" class="form-control" autocomplete="off" name="middleName" value="{if $action == "edit"}{$author->getMiddleName()}{/if}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name" class="control-label">{t}Last Name{/t}</label>
                                    <input type="text" class="form-control" autocomplete="off" name="lastName" value="{if $action == "edit"}{$author->getLastName()}{/if}">
                                    <input type="hidden" name="photoId" class="photoId" value="{if $action == "edit"}{$author->getPhotoId()}{/if}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description" class="control-label">{t}Description{/t}</label>
                                    <textarea type="text" class="form-control" autocomplete="off" name="description" id="content-box">{if $action == "edit"}{$author->getDescription()}{/if}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row margin-top-20">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-outline-secondary disabled pull-right save-author">
                                        <span class="btn-icon"><i class="far fa-save"></i></span> {t}Save{/t}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card" id="photo-block">
                <div class="card-body">
                    <div class="drop-zone cover-drop-zone{if $action == "edit" and $author->getPhotoId() != null} cover-exist{/if}">
                        <label>{t escape=no}Drag & Drop your file or <span>Browse</span>{/t}</label>
                        <input type="file" accept="image/png, image/jpeg, image/gif" id="author-photo" class="disabledIt" />
                        <button type="button" class="btn btn-info remove-author-photo{if $action == "edit" and $author->getPhotoId() == null or $action == "create"} d-none{/if}" data-id="{if $action == "edit"}{$author->getPhotoId()}{/if}"><i class="far fa-trash-alt"></i></button>
                        {if $action == "edit" and $author->getPhoto() != null}
                            <img src="{$author->getPhoto()->getWebPath()}" class="img-fluid">
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>
{/block}
{block name=footerPageJs append}
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/jasnyupload/fileinput.min.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/summernote/summernote-bs4.min.js"></script>
{/block}
{block name=footerCustomJs append}
    <script>
        $(document).ready(function () {
            $('#content-box').summernote().on('summernote.change', function () {
                $('.author-form').attr('data-changed', true);
                $('.save-author').removeClass('btn-outline-secondary disabled').addClass('btn-outline-success');
            });
            $(document).on('change', 'input,textarea,select', function () {
                $(this).closest('form').attr('data-changed', true);
                $('.save-author').removeClass('btn-outline-secondary disabled').addClass('btn-outline-success');
            });

            var photoUploadUrl = '{$routes->getRouteString("authorPhotoUpload",[])}';
            var imageDeleteUrl = '{$routes->getRouteString("imageDelete",[])}';
            $('#author-photo').on('change', authorPhotoUpload);
            function authorPhotoUpload(event) {
                $('.author-form').attr('data-changed', true);
                var dropZone = $('.cover-drop-zone');
                function upload() {
                    var photoId = $('.photoId').val();
                    var imgData = new FormData();
                    imgData.set('file', file);
                    if (photoId) {
                        imgData.set('photoId', photoId);
                    }
                    $.ajax({
                        dataType: 'json',
                        method: 'POST',
                        processData: false,
                        contentType: false,
                        data: imgData,
                        url: photoUploadUrl,
                        beforeSend: function (data) {
                            app.card.loading.start('#photo-block');
                        },
                        success: function (data) {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                if (data.error) {
                                    app.notification('error', data.error);
                                } else {
                                    $('.remove-author-photo').attr('data-id', data.imageId);
                                    $('.photoId').val(data.imageId);
                                    app.notification('success', '{t}Photo successfully uploaded{/t}');
                                    $('.save-author').click();
                                }
                            }
                        },
                        error: function (jqXHR, exception) {
                            app.notification('error', app.getErrorMessage(jqXHR, exception));
                        },
                        complete: function (data) {
                            app.card.loading.finish('#photo-block');
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
                            $('.remove-author-photo').removeClass('d-none');
                            upload();
                        };
                    } else {
                        app.notification('error', '{t}Uploaded file is not a valid image. Only JPG, PNG and GIF files are allowed.{/t}');
                    }
                } else {
                    app.notification('error', '{t}The File APIs are not fully supported in this browser.{/t}');
                }
            }
            $(document).on('click', '.remove-author-photo', function () {
                $('.author-form').attr('data-changed', true);
                var imgId = $(this).attr('data-id');
                if (imgId != undefined && imgId != null && imgId > 0) {
                    $.ajax({
                        dataType: 'json',
                        method: 'POST',
                        url: imageDeleteUrl.replace("[imageId]", imgId),
                        beforeSend: function (data) {
                            app.card.loading.start('#photo-block');
                        },
                        success: function (data) {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                if (data.error) {
                                    app.notification('error', data.error);
                                } else {
                                    $('.cover-drop-zone').removeClass('cover-exist').find('img').remove();
                                    $('.remove-author-photo').addClass('d-none');
                                    $('.photoId').val('');
                                    app.notification('success', data.success);
                                    $('.save-author').click();
                                }
                            }
                        },
                        error: function (jqXHR, exception) {
                            app.notification('error', app.getErrorMessage(jqXHR, exception));
                        },
                        complete: function (data) {
                            app.card.loading.finish('#photo-block');
                        }
                    });
                }
            });

            var authorEditUrl = '{$routes->getRouteString("authorEdit",[])}';
            $('.save-author').on('click', function (e) {
                e.preventDefault();
                var form = $(this).closest('form');
                var dataEdit = form.attr('data-edit');
                var dataChanged = form.attr('data-changed');
                if (dataChanged == 'true') {
                    $.ajax({
                        dataType: 'json',
                        method: 'POST',
                        data: form.serialize(),
                        url: form.attr('action'),
                        beforeSend: function (data) {
                            app.card.loading.start('#author-block');
                        },
                        success: function (data) {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                if (data.error) {
                                    app.notification('error', data.error);
                                } else {
                                    form.attr('action', authorEditUrl.replace("[authorId]", data.authorId)).attr('data-changed', false);
                                    app.notification('success', '{t}Data has been saved successfully{/t}');
                                    $('.save-author').removeClass('btn-outline-success').addClass('btn-outline-secondary disabled');
                                    if (dataEdit == 'false') {
                                        $('.page-title h3').text('{t}Edit Author{/t}');
                                        history.pushState(null, '', authorEditUrl.replace("[authorId]", data.authorId));
                                    }
                                    $(form).attr('data-edit', true);
                                }
                            }
                        },
                        error: function (jqXHR, exception) {
                            app.notification('error', app.getErrorMessage(jqXHR, exception));
                        },
                        complete: function (data) {
                            app.card.loading.finish('#author-block');
                        }
                    });
                } else {
                    app.notification('information', '{t}There are no changes{/t}');
                }
            });
        });
    </script>
{/block}