{extends file='admin/admin.tpl'}
{block name=title}{if $action == "create"}{t}Add User{/t}{else}{t}Edit User{/t}{/if}{/block}
{block name=headerCss append}
    <link href="{$resourcePath}assets/css/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet"/>
    <link href="{$resourcePath}assets/js/plugins/summernote/summernote-bs4.css" rel="stylesheet"/>
{/block}
{block name=content}
    {if $isDemoMode === true and $action == "edit" and $editedUser->getId() <= 3}
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-info text-center">In the demo version you cant change builtin users.</div>
            </div>
        </div>
    {/if}

    <ul class="nav nav-tabs special-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#general" role="tab">
                {t}General{/t}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#issuedBooks" role="tab">
                {t}Issued Books{/t}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#logs" role="tab">
                {t}Logs{/t}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane card p-20 active" id="general" role="tabpanel" aria-labelledby="general-tab">
            <div class="row">
                <div class="col-lg-8 col-xlg-9 col-md-7">
                    {if $action == "create"}
                        {assign var=route value=$routes->getRouteString("userCreate")}
                    {elseif $action == "edit" and isset($editedUser)}
                        {assign var=route value=$routes->getRouteString("userEdit",["userId"=>$editedUser->getId()])}
                    {elseif $action == "delete"}
                        {assign var=route value=""}
                    {/if}
                    <form action="{$route}" method="post" class="card form-horizontal validate user-form" data-edit="{if $action == "create"}false{else}true{/if}">
                        <div class="card-body p-0">
                            <div class="row">
                                <div class="col-lg-12">
                                    {if isset($user) and $user->getRole() != null and $user->getRole()->getPriority() >= 255}
                                        <div class="pull-right">
                                            {if $isDemoMode === true and $action == "edit" and $editedUser->getId() <= 3}
                                                <input type="hidden" name="isActive" value="{if $action == "edit" and $editedUser->isActive()}1{else}0{/if}">
                                            {else}
                                                <label class="switch switch-sm" data-container="body" data-toggle="tooltip" title="{t}Account Status (Active/Inactive){/t}">
                                                    <input type="checkbox" name="isActive" value="1"{if $action == "edit" and $editedUser->isActive()} checked{/if}>
                                                </label>
                                            {/if}
                                            <input type="hidden" name="userId" class="userId" value="{if $action == "edit" and isset($editedUser)}{$editedUser->getId()}{/if}">
                                            <input type="hidden" name="photoId" class="photoId" value="{if $action == "edit"}{$editedUser->getPhotoId()}{/if}">
                                        </div>
                                    {else}
                                        <input type="hidden" name="isActive" value="{if $action == "edit" and $editedUser->isActive()}1{elseif $action == "create"}1{else}0{/if}">
                                        <input type="hidden" name="userId" class="userId" value="{if $action == "edit" and isset($editedUser)}{$editedUser->getId()}{/if}">
                                        <input type="hidden" name="photoId" class="photoId" value="{if $action == "edit"}{$editedUser->getPhotoId()}{/if}">
                                    {/if}
                                </div>
                            </div>
                            <div class="row">
                                <div class="{if isset($user) and $user->getRole() != null and $user->getRole()->getPriority() >= 255}col-lg-6{else}col-lg-12{/if}">
                                    <div class="form-group">
                                        <label class="control-label">{t}Email{/t}
                                            {if $action == "create"}
                                                <i class="fa fa-info-circle text-warning" data-container="body" data-toggle="tooltip" title="{t}required{/t}"></i>
                                            {/if}
                                        </label>
                                        <input type="text" class="form-control" autocomplete="off" data-email="{if $action == "edit"}{$editedUser->getEmail()}{/if}" id="email" name="email" placeholder="{t}Login{/t}" value="{if $action == "edit"}{$editedUser->getEmail()}{/if}">
                                    </div>
                                </div>
                                {if isset($user) and $user->getRole() != null and $user->getRole()->getPriority() >= 255}
                                    <div class="col-lg-6">
                                        {if isset($roles)}
                                            <div class="form-group">
                                                <label for="roleId">{t}User Role{/t}</label>
                                                <select name="roleId" id="roleId" class="form-control">
                                                    {foreach from=$roles item=role name=role}
                                                        <option value="{$role->getId()}" {if isset($editedUser) and $editedUser->getRole() !== null and $editedUser->getRole()->getId() == $role->getId()}selected{/if}>{$role->getName()}</option>
                                                    {/foreach}
                                                </select>
                                            </div>
                                        {/if}
                                    </div>
                                {else}
                                    <input type="hidden" name="roleId" value="{if isset($editedUser) and $editedUser->getRole() !== null}{$editedUser->getRole()->getId()}{else}3{/if}" readonly>
                                {/if}
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="control-label">{if $action == "edit"}{t}New Password{/t}{else}{t}Password{/t}{/if}
                                            {if $action == "create"}
                                                <i class="fa fa-info-circle text-warning" data-container="body" data-toggle="tooltip" title="{t}required{/t}"></i>
                                            {/if}
                                            {if $action == "edit"}
                                                <i class="fa fa-exclamation-circle" data-toggle="tooltip" data-trigger="hover" data-original-title="{t}If you do not want to change your password, leave blank.{/t}"></i>
                                            {/if}
                                        </label>
                                        <input type="password" class="form-control" autocomplete="off" name="password" id="mainPassword">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="control-label">{if $action == "edit"}{t}Confirm New Password{/t}{else}{t}Confirm Password{/t}{/if}
                                            {if $action == "create"}
                                                <i class="fa fa-info-circle text-warning" data-container="body" data-toggle="tooltip" title="{t}required{/t}"></i>
                                            {/if}
                                        </label>
                                        <input type="password" class="form-control confirmPassword" autocomplete="off" name="confirmPassword">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="firstName" class="control-label">{t}First Name{/t}</label>
                                        <input type="text" class="form-control" autocomplete="off" name="firstName" value="{if $action == "edit"}{$editedUser->getFirstName()}{/if}">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="middleName" class="control-label">{t}Middle Name{/t}</label>
                                        <input type="text" class="form-control" autocomplete="off" name="middleName" value="{if $action == "edit"}{$editedUser->getMiddleName()}{/if}">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="lastName" class="control-label">{t}Last Name{/t}</label>
                                        <input type="text" class="form-control" autocomplete="off" name="lastName" value="{if $action == "edit"}{$editedUser->getLastName()}{/if}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="gender" class="control-label">{t}Gender{/t}</label>
                                        <select name="gender" class="form-control select-picker">
                                            <option value="Male"{if $action == "edit" and $editedUser->getGender() == 'Male'} selected{/if}>{t}Male{/t}</option>
                                            <option value="Female"{if $action == "edit" and $editedUser->getGender() == 'Female'} selected{/if}>{t}Female{/t}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="phone" class="control-label">{t}Phone{/t}</label>
                                        <input type="text" class="form-control" autocomplete="off" name="phone" value="{if $action == "edit"}{$editedUser->getPhone()}{/if}">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="address" class="control-label">{t}Address{/t}</label>
                                        <input type="text" class="form-control" autocomplete="off" name="address" value="{if $action == "edit"}{$editedUser->getAddress()}{/if}">
                                    </div>
                                </div>
                            </div>
                            {if $isDemoMode === true and $action == "edit" and $editedUser->getId() <= 3}
                            {else}
                                <div class="row mt-3">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-outline-secondary disabled btn-icon-fixed pull-right save-user"{if $isDemoMode === true and $action == "edit" and $editedUser->getId() <= 3} disabled{/if}>
                                                <span class="btn-icon"><i class="far fa-save"></i></span> {t}Save{/t}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            {/if}
                        </div>
                    </form>
                </div>
                <div class="col-lg-4 col-xlg-3 col-md-5">
                    <div class="card" id="photo-block">
                        <div class="card-body p-0">
                            <div class="drop-zone cover-drop-zone{if $action == "edit" and $editedUser->getPhotoId() != null} cover-exist{/if}">
                                <label>{t escape=no}Drag & Drop your photo or <span>Browse</span>{/t}</label>
                                <input type="file" accept="image/png, image/jpeg, image/gif" id="user-photo" class="disabledIt" />
                                <button type="button" class="btn btn-info remove-user-photo{if $action == "edit" and $editedUser->getPhotoId() == null or $action == "create"} d-none{/if}" data-id="{if $action == "edit"}{$editedUser->getPhotoId()}{/if}"><i class="far fa-trash-alt"></i></button>
                                {if $action == "edit" and $editedUser->getPhoto() != null}
                                    <img src="{$editedUser->getPhoto()->getWebPath()}" class="img-fluid">
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane card" id="issuedBooks" role="tabpanel" aria-labelledby="issuedBooks-tab">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card" id="userBooks">
                        {include "admin/users/userBooks-list.tpl"}
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane card" id="logs" role="tabpanel" aria-labelledby="logs-tab">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card" id="bookIssueLogs">
                        {include "admin/users/bookIssueLogs-list.tpl" pages=$issuePages}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="userSendSMSModal" tabindex="-1" role="dialog" aria-labelledby="userSendSMSModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title m-auto">{t}Send Notification{/t}</h5>
                </div>
                <div class="modal-body">
                    <form action="{$routes->getRouteString("smsSend",[])}" class="card p-3 mb-0">
                        <div class="form-group">
                            <label for="subject">{t}Sender{/t}</label>
                            <input type="hidden" name="bookId" id="smsBookId">
                            <input type="text" class="form-control" id="messageSender" name="sender">
                        </div>
                        <div class="form-group">
                            <label for="smsContent">{t}Message{/t}
                                <a class="text-muted" data-toggle="collapse" href="#shortcode-block-sms" aria-expanded="false" aria-controls="shortcode-block-sms"><i class="fa fa-info-circle"></i></a></label>
                            <textarea class="form-control" id="smsContent" name="content"></textarea>
                        </div>
                        <div class="alert alert-default text-center collapse" id="shortcode-block-sms">
                            {t}ShortCodes For Use{/t}: <br><code>[USER_FIRST_NAME]</code>, <code>[USER_LAST_NAME]</code>, <code>[BOOK]</code>, <code>[BOOKS]</code>
                        </div>
                        <div class="text-center">
                            <button type="button" class="btn btn-outline-primary" id="sendSMSToDelayedUser" data-url="">
                                <span class="btn-icon"><i class="far fa-paper-plane"></i></span> {t}Send SMS{/t}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="userSendEmailModal" tabindex="-1" role="dialog" aria-labelledby="userSendEmailModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title m-auto">{t}Send Notification{/t}</h5>
                </div>
                <div class="modal-body">
                    <form action="{$routes->getRouteString("userSendEmail",[])}" class="card p-3 mb-0">
                        <div class="form-group">
                            <label for="subject">{t}Subject{/t}</label>
                            <input type="hidden" name="bookId" id="emailBookId">
                            <input type="text" class="form-control" id="messageSubject" name="subject">
                        </div>
                        <div class="form-group">
                            <label for="emailContent">{t}Message{/t}
                                <a class="text-muted" data-toggle="collapse" href="#shortcode-block-email" aria-expanded="false" aria-controls="shortcode-block-email"><i class="fa fa-info-circle"></i></a></label>
                            <textarea class="form-control" id="emailContent" name="content"></textarea>
                        </div>
                        <div class="alert alert-default text-center collapse" id="shortcode-block-email">
                            {t}ShortCodes For Use{/t}: <br><code>[USER_FIRST_NAME]</code>, <code>[USER_LAST_NAME]</code>,
                            <code>[BOOK]</code>
                        </div>
                        <div class="text-center">
                            <button type="button" class="btn btn-outline-primary" id="sendEmailToDelayedUser" data-url="">
                                <span class="btn-icon"><i class="far fa-paper-plane"></i></span> {t}Send Email{/t}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
{/block}
{block name=footerPageJs append}
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/jquery-validate/jquery.validate.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/select2/select2.full.min.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/bootstrap-select/bootstrap-select.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/summernote/summernote-bs4.min.js"></script>
{/block}
{block name=footerCustomJs append}
    <script>
        $(document).ready(function () {
            $("#roleId").select2({
                maximumSelectionLength: 6
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
                ignore: null,
                messages: {
                    email: {
                        remote: jQuery.validator.format("<strong>{literal}{0}{/literal}</strong> {t}is already exist. Please use another email{/t}.")
                    }
                },
                rules: {
                    {if (isset($editedUser) and !$editedUser->isLdapUser()) or $action == "create"}
                    email: {
                        required: true,
                        email: true,
                        remote: {
                            param: {
                                delay: 500,
                                url: '{$routes->getRouteString("userEmailCheck",[])}',
                                type: "post",
                                data: {
                                    email: function () {
                                        return $("#email").val();
                                    }
                                },
                                error: function (jqXHR, exception) {
                                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                                }
                            },
                            depends: function (element) {
                                return ($(element).val() !== $("#email").attr('data-email'));
                            }
                        }
                    },
                    {/if}
                    {if $action == "create"}password: "required",{/if}
                    confirmPassword: {
                        equalTo: "#mainPassword"
                    }
                }
            });
            $(document).on('change', 'input,textarea,select', function () {
                $(this).closest('form').attr('data-changed', true);
                $('.save-user').removeClass('btn-outline-secondary disabled').addClass('btn-outline-success');
            });
            var photoUploadUrl = '{$routes->getRouteString("userPhotoUpload",[])}';
            var imageDeleteUrl = '{$routes->getRouteString("imageDelete",[])}';
            {if $isDemoMode === true and $action == "edit" and $editedUser->getId() <= 3}
            $('#user-photo').on('change', app.notification('information', '{t}In the demo version you cant change builtin users.{/t}'));
            {else}
            $('#user-photo').on('change', userPhotoUpload);
            {/if}
            function userPhotoUpload(event) {
                $('.user-form').attr('data-changed', true);
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
                                    $('.remove-user-photo').attr('data-id', data.imageId);
                                    $('.photoId').val(data.imageId);
                                    app.notification('success', '{t}Photo successfully uploaded{/t}');
                                    $('.save-user').click();
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
                            $('.remove-user-photo').removeClass('d-none');
                            upload();
                        };
                    } else {
                        app.notification('error', '{t}Uploaded file is not a valid image. Only JPG, PNG and GIF files are allowed.{/t}');
                    }
                } else {
                    app.notification('error', '{t}The File APIs are not fully supported in this browser.{/t}');
                }
            }
            $(document).on('click', '.remove-user-photo', function () {
                $('.user-form').attr('data-changed', true);
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
                                    $('.remove-user-photo').addClass('d-none');
                                    $('.photoId').val('');
                                    app.notification('success', data.success);
                                    $('.save-user').click();
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
            {if $isDemoMode === true and $action == "edit" and $editedUser->getId() <= 3}
            $('.save-user').on('click', function (e) {
                e.preventDefault();
                app.notification('information', '{t}In the demo version you cant change builtin users.{/t}');
            });
            {else}
            var userEditUrl = '{$routes->getRouteString("userEdit",[])}';
            $('.save-user').on('click', function (e) {
                e.preventDefault();
                var form = $(this).closest('form');
                var dataEdit = form.attr('data-edit');
                var dataChanged = form.attr('data-changed');
                if (dataChanged == 'true') {
                    if ($(form).valid()) {
                        $('.confirmPassword').attr('disabled', true);
                        $.ajax({
                            dataType: 'json',
                            method: 'POST',
                            data: form.serialize(),
                            url: form.attr('action'),
                            beforeSend: function (data) {
                                app.card.loading.start('.card');
                            },
                            success: function (data) {
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                } else {
                                    if (data.error) {
                                        app.notification('error', data.error);
                                    } else {
                                        form.attr('action', userEditUrl.replace("[userId]", data.userId)).attr('data-changed', false).find('.userId').val(data.userId);
                                        $('.save-user').removeClass('btn-outline-success').addClass('btn-outline-secondary disabled');
                                        app.notification('success', '{t}Data has been saved successfully{/t}');
                                        if (dataEdit == 'false') {
                                            $('.page-title h3').text('{t}Edit User{/t}');
                                            history.pushState(null, '', userEditUrl.replace("[userId]", data.userId));
                                        }
                                        $(form).attr('data-edit', true);
                                    }
                                }
                            },
                            error: function (jqXHR, exception) {
                                app.notification('error', app.getErrorMessage(jqXHR, exception));
                            },
                            complete: function (data) {
                                $('.confirmPassword').attr('disabled', false);
                                app.card.loading.finish('.card');
                            }
                        });
                    }
                } else {
                    app.notification('information', '{t}There are no changes{/t}');
                }
            });
            {/if}
            $(document).on('click', '#issuedBooks .ajax-page', function (e) {
                e.preventDefault();
                $.ajax({
                    dataType: 'json',
                    url: $(this).attr('href'),
                    beforeSend: function () {
                        app.card.loading.start($("#userBooks"));
                    },
                    success: function (data) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                $("#userBooks").html(data.html);
                            }
                        }
                    },
                    complete: function () {
                        app.card.loading.finish($("#userBooks"));
                    },
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    }
                });
            });
            $(document).on('click', '#logs .ajax-page', function (e) {
                e.preventDefault();
                $.ajax({
                    dataType: 'json',
                    url: $(this).attr('href'),
                    beforeSend: function () {
                        app.card.loading.start($("#bookIssueLogs"));
                    },
                    success: function (data) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                $("#bookIssueLogs").html(data.html);
                            }
                        }
                    },
                    complete: function () {
                        app.card.loading.finish($("#bookIssueLogs"));
                    },
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    }
                });
            });

            var userSendEmailURL = '{$routes->getRouteString("userSendEmail",[])}';
            var userSMSSendURL = '{$routes->getRouteString("smsSend",[])}';
            $('#userSendSMSModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var userId = button.data('user');
                var bookId = button.data('book');
                var modal = $(this);
                $('#smsBookId').val(bookId);
                $('#sendSMSToDelayedUser').attr('data-url', userSMSSendURL.replace('[userId]', userId));
                $.ajax({
                    type: "GET",
                    dataType: 'json',
                    url: userSMSSendURL.replace('[userId]', userId),
                    beforeSend: function () {
                        app.card.loading.start($(modal).find('.card'));
                    },
                    success: function (data) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                $('#messageSender').val(data.sender);
                                $('#smsContent').val(data.smsTemplate);
                            }
                        }
                    },
                    complete: function () {
                        app.card.loading.finish($(modal).find('.card'));
                    },
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    }
                });
            });
            $('#userSendEmailModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var userId = button.data('user');
                var bookId = button.data('book');
                var modal = $(this);
                $('#emailBookId').val(bookId);
                $('#sendEmailToDelayedUser').attr('data-url', userSendEmailURL.replace('[userId]', userId));
                $.ajax({
                    type: "GET",
                    dataType: 'json',
                    url: userSendEmailURL.replace('[userId]', userId),
                    beforeSend: function () {
                        app.card.loading.start($(modal).find('.card'));
                    },
                    success: function (data) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                $('#emailContent').val(data.dynamicEmailTemplate).summernote({
                                    toolbar: [
                                        ['style', ['bold', 'italic', 'underline', 'clear']],
                                        ['font', ['strikethrough']],
                                        ['fontsize', ['fontsize']],
                                        ['color', ['color']],
                                        ['para', ['ul', 'ol', 'paragraph']],
                                        ['height', ['height']],
                                        ['misc', ['codeview']]
                                    ]
                                });
                            }
                        }
                    },
                    complete: function () {
                        app.card.loading.finish($(modal).find('.card'));
                    },
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    }
                });
            });
            $('#sendSMSToDelayedUser').on('click', function (e) {
                e.preventDefault();
                var form = $(this).closest('form');
                var url = $(this).attr('data-url');
                $.ajax({
                    type: "POST",
                    url: url,
                    data: $(form).serialize(),
                    dataType: 'json',
                    beforeSend: function () {
                        app.card.loading.start($(form));
                    },
                    success: function (data) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                app.notification('success', data.success);
                                $('#userSendSMSModal').modal('hide');
                                $('.tooltip.show').remove();
                            }
                        }
                    },
                    complete: function () {
                        app.card.loading.finish($(form));
                    },
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    }
                });
            });
            $('#sendEmailToDelayedUser').on('click', function (e) {
                e.preventDefault();
                var form = $(this).closest('form');
                var url = $(this).attr('data-url');
                $.ajax({
                    type: "POST",
                    url: url,
                    data: $(form).serialize(),
                    dataType: 'json',
                    beforeSend: function () {
                        app.card.loading.start($(form));
                    },
                    success: function (data) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                app.notification('success', data.success);
                                $('#emailContent').summernote('destroy');
                                $('#userSendEmailModal').modal('hide');
                                $('.tooltip.show').remove();
                            }
                        }
                    },
                    complete: function () {
                        app.card.loading.finish($(form));
                    },
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    }
                });
            });

            $(document).on('click', '.lost-book', function (e) {
                e.preventDefault();
                var className, url, $this = $(this);
                var bookLost = $this.attr('data-lost');
                if (bookLost == 'true') {
                    url = $this.attr('href').replace("[isLost]", 'false');
                } else {
                    url = $this.attr('href').replace("[isLost]", 'true');
                }
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: url,
                    beforeSend: function () {
                        app.card.loading.start('#userBooks');
                    },
                    success: function (data) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                if (bookLost == 'true') {
                                    $($this).tooltip('hide');
                                    $($this).closest('tr').remove();
                                } else {
                                    $($this).tooltip('hide');
                                    $($this).closest('tr').remove();
                                }
                            }
                        }
                    },
                    complete: function () {
                        app.card.loading.finish('#userBooks');
                    },
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    }
                });
            });
            $(document).on('click', '.return-book', function (e) {
                e.preventDefault();
                var $this = $(this);
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: $this.attr('href'),
                    beforeSend: function () {
                        app.card.loading.start('#userBooks');
                    },
                    success: function (data) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                app.notification('success', '{t}Book successfully returned{/t}');
                                $($this).tooltip('hide');
                                $($this).closest('tr').remove();
                            }
                        }
                    },
                    complete: function () {
                        app.card.loading.finish('#userBooks');
                    },
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    }
                });
            });
            $(document).on('click', '.delete-issue', function (e) {
                var url = $(this).attr('data-url');
                var row = $(this).closest('tr');
                $.ajax({
                    dataType: 'json',
                    type: 'POST',
                    url: url,
                    beforeSend: function () {
                        app.card.loading.start('#userBooks');
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
                        app.card.loading.finish('#userBooks');
                    },
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    }
                });
            });
        });
    </script>
{/block}