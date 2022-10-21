<!DOCTYPE html>
<html lang="en" class="">
    <head>
        <meta charset="UTF-8">
        <title>{t}500 Internal server error{/t}</title>
        <link rel="icon" type="image/png" href="{$siteViewOptions->getOptionValue("favIconFilePath")}"/>
        <meta name=viewport content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="{$themePath}resources/css/bootstrap.min.css">

        <link rel="stylesheet" href="{$themePath}resources/css/plugins.css">
        <link rel="stylesheet" href="{$themePath}resources/css/style.css">
    </head>
    <body>
        <section class="error-page">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <h1 class="text-center">500</h1>
                        <div class="error-messages">
                            <h4 class="not-found text-center mb-3">{t}Internal server error{/t}</h4>
                            <p class="text-center">{t}Looks like we have an internal issue, please try again in couple of minutes.{/t}</p>
                            {include 'errors.tpl'}
                            <div class="text-center">
                                <a href="#" class="btn btn-primary shadow" onclick="history.back(); return false;">{t}Go back!{/t}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </body>
</html>