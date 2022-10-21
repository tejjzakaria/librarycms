{extends file='public.tpl'}
{block name=metaTitle}Member Registration{/block}
{block name=metaDescription}{/block}
{block name=metaKeywords}{/block}
{block name=headerCss append}{/block}
{block name=content}
    <section class="registration">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title mb-4">
                        <h2 class="text-uppercase text-center">{t}Registration{/t}</h2>
                    </div>
                </div>
                <div class="col-lg-12">
                    {if $siteViewOptions->getOptionValue("enableRegistration")}
                        {*<form action="{$routes->getRouteString("userRegistration")}" method="post" class="validate user-registration mx-auto">

                            <div class="registration-form p-5">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="control-label">{t}Email{/t}</label>
                                            <input type="text" class="form-control" autocomplete="off" id="email" name="email" placeholder="{t}Login{/t}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="control-label">{t}Password{/t}</label>
                                            <input type="password" class="form-control" autocomplete="off" name="password" id="mainPassword">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="control-label">{t}Confirm Password{/t}</label>
                                            <input type="password" class="form-control confirmPassword" autocomplete="off" name="confirmPassword">
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="firstName" class="control-label">{t}First Name{/t}</label>
                                            <input type="text" class="form-control" autocomplete="off" name="firstName">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="middleName" class="control-label">{t}Middle Name{/t}</label>
                                            <input type="text" class="form-control" autocomplete="off" name="middleName">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="lastName" class="control-label">{t}Last Name{/t}</label>
                                            <input type="text" class="form-control" autocomplete="off" name="lastName">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="gender" class="control-label">{t}Gender{/t}</label>
                                            <select name="gender" class="form-control custom-select">
                                                <option value="Male" selected>{t}Male{/t}</option>
                                                <option value="Female">{t}Female{/t}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="phone" class="control-label">{t}Phone{/t}</label>
                                            <input type="text" class="form-control" autocomplete="off" name="phone">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="address" class="control-label">{t}Address{/t}</label>
                                            <input type="text" class="form-control" autocomplete="off" name="address">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary shadow pull-right save-user">
                                                {t}Register{/t}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>*}

                        <form action="{$routes->getRouteString("userRegistration")}" method="post" class="validate user-registration mx-auto">
                            <div class="registration-successfully d-none">
                                <svg viewBox="-10 -10 500 500">
                                    <path class="circle" d="M877.28,335.72a203.17,203.17,0,0,1,37.86,118.1C915.14,565.26,823.44,657,712,657s-203.14-91.7-203.14-203.14S600.56,250.68,712,250.68a203.21,203.21,0,0,1,144.67,60.53" transform="translate(-508.86 -250.68)"></path>
                                    <polyline class="check" points="78.54 229.94 179.32 300.74 347.98 60.67"></polyline>
                                </svg>
                                <h2 class="thanks">{t}Thank you!{/t}</h2>
                                <p>{t}Your e-mail has been sent a letter with a link to activate your account.{/t}</p>
                            </div>
                            <div class="registration-form">
                                <div class="social-login">
                                    {if $socialNetworkSettings->getProvider('facebook')->isActive()}
                                        <a href="{$routes->getRouteString("socialAuth",["providerId"=>"facebook"])}" class="btn auth-btn btn-block facebook text-white mb-2"><i class="ti-facebook float-left"></i> <span>{t}Sign up with Facebook{/t}</span></a>
                                    {/if}
                                    {if $socialNetworkSettings->getProvider('google')->isActive()}
                                        <a href="{$routes->getRouteString("socialAuth",["providerId"=>"google"])}" class="btn auth-btn btn-block google-plus text-white"><i class="ti-google float-left"></i> <span>{t}Sign up with Google+{/t}</span></a>
                                    {/if}
                                    {if $socialNetworkSettings->getProvider('twitter')->isActive()}
                                        <a href="{$routes->getRouteString("socialAuth",["providerId"=>"twitter"])}" class="btn auth-btn btn-block twitter text-white"><i class="ti-twitter-alt float-left"></i> <span>{t}Sign up with Twitter{/t}</span></a>
                                    {/if}
                                </div>
                                {if $socialNetworkSettings->getProvider('facebook')->isActive() or $socialNetworkSettings->getProvider('google')->isActive() or $socialNetworkSettings->getProvider('twitter')->isActive()}
                                    <div class="my-3 text-sm text-center">{t}OR{/t}</div>
                                {/if}
                                <div class="form-group">
                                    <input type="text" class="form-control" autocomplete="off" id="email" name="email" placeholder="{t}Email{/t}">
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control" autocomplete="off" name="password" id="mainPassword" placeholder="{t}Password{/t}">
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control confirmPassword" autocomplete="off" name="confirmPassword" placeholder="{t}Confirm Password{/t}">
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" autocomplete="off" name="firstName" placeholder="{t}First Name{/t}">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" autocomplete="off" name="lastName" placeholder="{t}Last Name{/t}">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <select name="gender" class="form-control custom-select">
                                        <option value="Male" selected>{t}Male{/t}</option>
                                        <option value="Female">{t}Female{/t}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" autocomplete="off" name="phone" placeholder="{t}Phone{/t}">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" autocomplete="off" name="address" placeholder="{t}Address{/t}">
                                </div>

                                <button type="submit" class="btn btn-primary btn-block shadow reg-user">{t}Register{/t}</button>
                            </div>
                        </form>
                    {else}
                        <h2 class="text-center mt-5">{t}Registration is disabled by admin{/t}</h2>
                    {/if}
                </div>
            </div>
        </div>
    </section>
{/block}
{block name=footerJs append}
    <script type='text/javascript' src="{$themePath}resources/js/jquery.validate.js"></script>
{/block}
{block name=customJs append}
    <script>
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
                        $(form).append('<div class="form-message"><i class="fa fa-spinner fa-spin"></i><span class="sr-only">{t}Loading...{/t}</span> {t}Sending, Please Wait..{/t} </div>');
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
                        $(form).find('.form-message').fadeOut().remove();
                    }
                });
            }
        });
    </script>
{/block}