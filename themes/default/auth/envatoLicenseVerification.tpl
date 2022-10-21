<!DOCTYPE html>
<html lang="en" style="height: 100%;">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="KAASoft">
        <meta name="robots" content="noindex,nofollow">
        <meta http-equiv="cache-control" content="no-cache"/>
        <meta http-equiv="Pragma" content="no-cache"/>
        <meta http-equiv="Expires" content="-1"/>
        <title>Install Library CMS</title>
        <link href="{$resourcePath}assets/css/plugins/bootstrap/bootstrap.min.css" rel="stylesheet">
        <link href="{$resourcePath}assets/css/plugins.css" rel="stylesheet">
        <link href="{$resourcePath}assets/css/style.css" rel="stylesheet">
        <link rel="icon" type="image/png" sizes="32x32" href="{$resourcePath}assets/images/favicon.png">
    </head>
    <body style="min-height: 100%;background: linear-gradient(180deg,#f0f0f0 0,#dee1e3 100%) !important;">
        <section id="wrapper">
            <div class="login-register" style="">
                <div class="login-box card" style="max-width: 600px;width: auto;">
                    <div class="card-body">
                        <img src="{$resourcePath}assets/images/logo.png" class="d-flex ml-auto mr-auto mb-4 mt-2" alt="{t}Logo{/t}"/>
                        {include 'errors.tpl'}
                        <form action="{$routes->getRouteString("envatoLicenseVerificationPublic")}" method="post" class="form-horizontal validate">
                            <div class="form-group">
                                <h3 class="text-center mb-3">Library CMS License Verification</h3>
                            </div>
                            <div class="form-group">
                                <label class="form-control-label">Purchase Code</label>
                                <input type="text" name="purchaseCode" autocomplete="off" class="form-control purchase-code">
                            </div>

                            <div class="form-group mt-3">
                                <button type="submit" class="btn btn-primary btn-block" id="verify-code">{t}Verify{/t}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        <script src="{$resourcePath}assets/js/plugins/jquery/jquery-3.2.1.min.js"></script>
        <script src="{$resourcePath}assets/js/plugins/bootstrap/popper.min.js"></script>
        <script src="{$resourcePath}assets/js/plugins/bootstrap/bootstrap.min.js"></script>
        <script type="text/javascript" src="{$resourcePath}assets/js/plugins/jquery-validate/jquery.validate.js"></script>
        <script src="{$resourcePath}assets/js/plugins/tooltipster/jquery.tooltipster.min.js"></script>
        <script src="{$resourcePath}assets/js/plugins/noty/noty.min.js"></script>
        <script src="{$resourcePath}assets/js/main.js"></script>
        <script>
            $('#verify-code').on('click', function (e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    data: $('.purchase-code').serialize(),
                    url: '{$routes->getRouteString("envatoLicenseVerificationPublic")}',
                    beforeSend: function () {
                        app.card.loading.start('.login-box');
                    },
                    success: function (data) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else if (data.warning) {
                                app.notification('warning', data.warning);
                            } else {
                                app.notification('success', data.success);
                                window.location.href = '{$routes->getRouteString("publicIndex")}';
                            }
                        }
                    },
                    complete: function () {
                        app.card.loading.finish('.login-box');
                    },
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    }
                });
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
                    purchaseCode: {
                        required: true
                    }
                }
            });
        </script>
    </body>
</html>