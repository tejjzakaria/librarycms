<!DOCTYPE html>
<html lang="en" dir="{if $activeLanguage->isRTL()}rtl{else}ltr{/if}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="KAASoft">
    <meta name="robots" content="noindex,nofollow">
    <title>{if $siteViewOptions->getOptionValue("siteName") != null}{$siteViewOptions->getOptionValue("siteName")}{else}{$pageTitle}{/if}</title>
    <link rel="icon" type="image/png" href="{$siteViewOptions->getOptionValue("favIconFilePath")}"/>
    <link rel="stylesheet" href="{$themePath}resources/css/bootstrap.min.css">
    <link rel="stylesheet" href="{$themePath}resources/css/plugins.css">
    {if $activeLanguage->isRTL()}
        <link rel="stylesheet" href="{$themePath}resources/css/style.rtl.css">
    {else}
        <link rel="stylesheet" href="{$themePath}resources/css/style.css">
    {/if}
    {if $theme != null}
        {assign var=colorSchemas value=$theme->getColorSchemas()}
        {if $theme->getActiveColorSchema() == 'dark'}
            <link rel="stylesheet" href="{$themePath}resources/css/themes/dark.css">
        {/if}
    {/if}
</head>
<body>
<div class="d-flex flex-column flex login-register align-middle" {*style="background-image : url({$themePath}resources/img/library-bg.jpg);"*}>
    <div id="wrapper">
        <div class="py-5 text-center w-100">
            <div class="login-box card p-3 mx-auto w-auto-xs">
                <div class="px-3">
                    {if $theme != null and $colorSchemas[$theme->getActiveColorSchema()]->isDark()}
                        <img src="{$siteViewOptions->getOptionValue("lightLogoFilePath")}" class="d-flex ml-auto mr-auto mb-4 mt-2 img-fluid" alt="Login">
                    {else}
                        <img src="{$siteViewOptions->getOptionValue("logoFilePath")}" class="d-flex ml-auto mr-auto mb-4 mt-2 img-fluid" alt="Login">
                    {/if}
                    {include 'errors.tpl'}
                    <form action="{$routes->getRouteString("passwordChange")}" class="form-horizontal validate" method="post">
                        <div class="form-group ">
                            <h3 class="text-center mb-3">{t}Change Password{/t}</h3>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="icon-key"></i>
                                    </span>
                                <input type="password" name="password" class="form-control" id="mainPassword" placeholder="{t}New Password{/t}" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="icon-key"></i>
                                    </span>
                                <input type="password" name="confirmPassword" class="form-control" id="confirmPassword" placeholder="{t}Confirm Password{/t}" autocomplete="off">
                            </div>
                        </div>
                        <div class="login-message"></div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <a href="{$routes->getRouteString("adminLogin")}" class="btn btn-info pull-left">{t}Go Back{/t}</a>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-block changePassword">{t}Change{/t}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{$themePath}resources/js/jquery.min.js"></script>
<script src="{$themePath}resources/js/popper.min.js"></script>
<script src="{$themePath}resources/js/bootstrap.min.js"></script>
<script src="{$themePath}resources/js/plugins.js"></script>
<script src="{$themePath}resources/js/jquery.validate.js"></script>
<script src="{$themePath}resources/js/custom.js"></script>
<script>
    $(".validate").validate({
        rules: {
            password: {
                required: true
            },
            confirmPassword: {
                required: true,
                equalTo: '#mainPassword'
            }
        }
    });
    $('.changePassword').on('click', function (e) {
        e.preventDefault();
        var form = $(this).closest('form');
        if (form.valid()) {
            $.ajax({
                dataType: 'json',
                method: 'POST',
                data: $('#mainPassword').serialize(),
                url: $(form).attr('action').replace('[token]', '{$token}'),
                beforeSend: function (data) {
                    app.card.loading.start($('.login-box'));
                },
                success: function (data) {
                    if (data.error == undefined) {
                        $(form).find('.login-message').addClass('alert alert-success alert-dismissable').text(data.success).fadeIn();
                        $(form).find("input[type=text], textarea").val("");
                        setTimeout(function () {
                            $(form).find('.login-message').removeClass('success').fadeOut();
                        }, 5000);
                    } else {
                        $(form).find('.login-message').addClass('alert alert-success alert-dismissable').text(data.success).fadeIn();
                        setTimeout(function () {
                            $(form).find('.login-message').removeClass('error').fadeOut();
                        }, 5000);
                    }
                },
                error: function (jqXHR) {
                    $(form).find('.login-message').addClass('alert alert-danger alert-dismissable').text('Failed to send your message. Please try later or contact the administrator by another method.').fadeIn();
                    setTimeout(function () {
                        $(form).find('.login-message').removeClass('error').fadeOut();
                    }, 5000);
                },
                complete: function (data) {
                    app.card.loading.finish($('.login-box'));
                }
            });
        } else {
            $(form).find('.login-message').addClass('alert alert-danger alert-dismissable').text('Validation errors occurred. Please confirm the fields and submit it again.').fadeIn();
            setTimeout(function () {
                $(form).find('.login-message').removeClass('error').fadeOut();
            }, 5000);
        }
    });
</script>
</body>
</html>