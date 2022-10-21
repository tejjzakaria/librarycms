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
                            {include file='auth/login_form.tpl'}
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
            $('#sign-up-tab').click(function (e) {
                e.preventDefault();
                $('#sign-in-form').removeClass('show').addClass('d-none');
                $('#sign-up-form').removeClass('d-none').addClass('show');

                $('.social-login .facebook span').text('{t}Sign up with Facebook{/t}');
                $('.social-login .google span').text('{t}Sign up with Google+{/t}');
                $('.social-login .twitter span').text('{t}Sign up with Twitter{/t}');
            });
            $('#sign-in-tab').click(function (e) {
                e.preventDefault();
                $('#sign-up-form').removeClass('show').addClass('d-none');
                $('#sign-in-form').removeClass('d-none').addClass('show');

                $('.social-login .facebook span').text('{t}Sign in with Facebook{/t}');
                $('.social-login .google span').text('{t}Sign in with Google+{/t}');
                $('.social-login .twitter span').text('{t}Sign in with Twitter{/t}');
            });

            $('.validate').validate({
                ignore: null,
                messages: {
                    email: {
                        {literal}remote: jQuery.validator.format("<strong>{0}</strong> is already exist. Please use another email."){/literal}
                    }
                },
                rules: {
                    email: {
                        required: true,
                        email: true,
                        remote: {
                            param: {
                                url: '{$routes->getRouteString("userEmailCheck",[])}',
                                type: "post",
                                data: {
                                    email: function () {
                                        return $("#email").val();
                                    }
                                }
                            }
                        }
                    },
                    firstName: {
                        required: true
                    },
                    lastName: {
                        required: true
                    },
                    password: {
                        required: true
                    },
                    confirmPassword: {
                        equalTo: "#mainPassword"
                    }
                }
            });
            $('.reg-user').on('click', function (e) {
                e.preventDefault();
                var form = $(this).closest('form');
                if ($(form).valid()) {
                    $('.confirmPassword').attr('disabled', true);
                    $.ajax({
                        dataType: 'json',
                        method: 'POST',
                        data: form.serialize(),
                        url: form.attr('action'),
                        beforeSend: function (data) {
                            app.card.loading.start('.login-box');
                        },
                        success: function (data) {
                            app.ajax_redirect(data);
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                $('.registration-form').remove();
                                $('.registration-successfully').removeClass('d-none');
                            }
                        },
                        error: function (jqXHR, exception) {
                            app.notification('error', app.getErrorMessage(jqXHR, exception));
                        },
                        complete: function (data) {
                            app.card.loading.finish('.login-box');
                        }
                    });
                }
            });
        </script>
    </body>
</html>