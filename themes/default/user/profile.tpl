{extends file='public.tpl'}
{block name=metaTitle}Profile | {$siteViewOptions->getOptionValue("siteName")}{/block}
{block name=metaDescription}{/block}
{block name=metaKeywords}{/block}
{block name=headerCss append}{/block}
{block name=content}
    <div class="page-heading">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title text-center">
                        <h1>{t}My Profile{/t}</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section class="profile">
        <div class="container">
            <div class="row position-relative profile-content">
                {*<div class="preloader hide" id="app-preloader">
                    <div class="overlay"></div>
                    <div class="loader"></div>
                </div>*}
                <div class="col-lg-9 col-md-7 order-column-2">
                    <form method="post" novalidate class="validate user-form" action="{$routes->getRouteString("userProfile")}">
                        <input type="hidden" name="photoId" class="photoId" value="{if $action == "edit"}{$editedUser->getPhotoId()}{/if}">
                        <h2>{t}Personal Information{/t}</h2>
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
                                    <select name="gender" class="form-control custom-select">
                                        <option value="Male"{if $action == "edit" and $editedUser->getGender() == 'Male'} selected{/if}>{t}Male{/t}</option>
                                        <option value="Female"{if $action == "edit" and $editedUser->getGender() == 'Female'} selected{/if}>{t}Female{/t}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div class="form-group">
                                    <label for="address" class="control-label">{t}Address{/t}</label>
                                    <input type="text" class="form-control" autocomplete="off" name="address" value="{if $action == "edit"}{$editedUser->getAddress()}{/if}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{t}Email{/t}</label>
                                    <input type="text" class="form-control" autocomplete="off" data-email="{if $action == "edit"}{$editedUser->getEmail()}{/if}" id="email" name="email" placeholder="{t}Login{/t}" value="{if $action == "edit"}{$editedUser->getEmail()}{/if}">

                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="phone" class="control-label">{t}Phone{/t}</label>
                                    <input type="text" class="form-control" autocomplete="off" name="phone" value="{if $action == "edit"}{$editedUser->getPhone()}{/if}">
                                </div>
                            </div>
                        </div>
                        {if $action == "edit" and $editedUser->getSocialId() == null}
                        <h2>{t}Change Password{/t}</h2>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{if $action == "edit"}{t}New Password{/t}{/if}
                                        {if $action == "edit"}
                                            <i class="ti-info" data-toggle="tooltip" data-trigger="hover" data-original-title="{t}If you do not want to change your password, leave blank.{/t}"></i>
                                        {/if}
                                    </label>
                                    <input type="password" class="form-control" autocomplete="off" name="password" id="mainPassword">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{if $action == "edit"}{t}Confirm New Password{/t}{/if}</label>
                                    <input type="password" class="form-control confirmPassword" autocomplete="off" name="confirmPassword">
                                </div>
                            </div>
                        </div>
                        {/if}
                        <div class="row mt-3">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary shadow save-user">
                                        <i class="fa fa-floppy-o"></i> {t}Save Changes{/t}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-lg-3 col-md-5 order-column-1">
                    <h2>{t}Photo{/t}</h2>

                    <div class="drop-zone photo-drop-zone{if $action == "edit" and $editedUser->getPhotoId() != null} photo-exist{/if}">
                        <label>{t escape=no}Drag & Drop your photo or <span>Browse</span>{/t}</label>
                        <input type="file" accept="image/png, image/jpeg, image/gif" id="user-photo" class="disabledIt" />
                        <button type="button" class="btn btn-info remove-photo{if $action == "edit" and $editedUser->getPhotoId() == null or $action == "create"} d-none{/if}" data-id="{if $action == "edit"}{$editedUser->getPhotoId()}{/if}"><i class="far fa-trash-alt"></i></button>
                        {if $action == "edit" and $editedUser->getPhoto() != null}
                            <img src="{$editedUser->getPhoto()->getWebPath()}" class="img-fluid">
                            {*else}
                                <img src="{$siteViewOptions->getOptionValue("noBookImageFilePath")}" class="img-fluid">*}
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </section>
{/block}
{block name=footerJs append}
    <script src="{$themePath}resources/js/jquery.validate.js"></script>
{/block}
{block name=customJs append}
    <script>
        $(document).ready(function () {
            $('.drop-zone').on('dragover', function() {
                $(this).addClass('hover');
            });
            $('.drop-zone').on('dragleave', function() {
                $(this).removeClass('hover');
            });
            var photoUploadUrl = '{$routes->getRouteString("userPhotoUpload",[])}';
            var photoDeleteUrl = '{$routes->getRouteString("imageDeletePublic",[])}';
            $('#user-photo').on('change', userPhotoUpload);
            function userPhotoUpload(event) {
                var dropZone = $('.photo-drop-zone');
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
                            app.preloader.start('.photo-drop-zone');
                        },
                        success: function (data) {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                if (data.error) {
                                    app.notification('error', data.error);
                                } else {
                                    $('.remove-photo').attr('data-id', data.imageId);
                                    $('.photoId').val(data.imageId);
                                    app.notification('success', '{t}Photo successfully uploaded{/t}');
                                    saveUser();
                                }
                            }
                        },
                        error: function (jqXHR, exception) {
                            app.notification('error', app.getErrorMessage(jqXHR, exception));
                        },
                        complete: function (data) {
                            app.preloader.finish('.photo-drop-zone');
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
                            $(dropZone).addClass('photo-exist').append(img);
                            $('.remove-photo').removeClass('d-none');
                            upload();
                        };
                    } else {
                        app.notification('error', '{t}Uploaded file is not a valid image. Only JPG, PNG and GIF files are allowed.{/t}');
                    }
                } else {
                    app.notification('error', '{t}The File APIs are not fully supported in this browser.{/t}');
                }
            }

            $(document).on('click', '.remove-photo', function () {
                var imgId = $(this).attr('data-id');
                if (imgId != undefined && imgId != null && imgId > 0) {
                    $.ajax({
                        dataType: 'json',
                        method: 'POST',
                        url: photoDeleteUrl.replace("[imageId]", imgId),
                        beforeSend: function (data) {
                            app.preloader.start('.photo-drop-zone');
                        },
                        success: function (data) {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                if (data.error) {
                                    app.notification('error', data.error);
                                } else {
                                    $('.photo-drop-zone').removeClass('photo-exist').find('img').remove();
                                    $('.remove-photo').addClass('d-none');
                                    $('.photoId').val('');
                                    app.notification('success', data.success);
                                    saveUser();
                                }
                            }
                        },
                        error: function (jqXHR, exception) {
                            app.notification('error', app.getErrorMessage(jqXHR, exception));
                        },
                        complete: function (data) {
                            app.preloader.finish('.photo-drop-zone');
                        }
                    });
                }
            });

            $('.validate').validate({
                ignore: null,
                messages: {
                    email: {
                        remote: jQuery.validator.format("<strong>{literal}{0}{/literal}</strong> {t}is already exist. Please use another email{/t}.")
                    }
                },
                rules: {
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
                    firstName: {
                        required: true
                    },
                    lastName: {
                        required: true
                    },
                    confirmPassword: {
                        equalTo: "#mainPassword"
                    }
                }
            });
            function saveUser() {
                var form = $('.user-form');
                if ($(form).valid()) {
                    $('.confirmPassword').attr('disabled', true);
                    $.ajax({
                        dataType: 'json',
                        method: 'POST',
                        data: form.serialize(),
                        url: form.attr('action'),
                        beforeSend: function (data) {
                            app.preloader.start('.profile-content');
                        },
                        success: function (data) {
                            app.ajax_redirect(data);
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                app.notification('success', '{t}Data has been saved successfully{/t}');
                            }
                        },
                        error: function (jqXHR, exception) {
                            app.notification('error', app.getErrorMessage(jqXHR, exception));
                        },
                        complete: function (data) {
                            $('.confirmPassword').attr('disabled', false);
                            app.preloader.finish('.profile-content');
                        }
                    });
                }
            }
            $('.save-user').on('click', function (e) {
                e.preventDefault();
                saveUser();
            });
        });
    </script>
{/block}