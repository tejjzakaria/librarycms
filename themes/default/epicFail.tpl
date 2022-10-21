<!DOCTYPE html>
<html lang="en" class="">
    <head>
        <meta charset="UTF-8">
        <title>{t}Epic Fail{/t}</title>
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
                        <h1 class="text-center">{t}Epic Fail{/t}</h1>
                        <div class="error-messages">
                            {if isset($smarty.session.messages) and count($smarty.session.messages) > 0}
                                <div class="alert alert-dismissable alert-danger">
                                    {foreach from=$smarty.session.messages item=message}
                                        {$message->getMessage()}
                                        <br/>
                                    {/foreach}
                                </div>
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </body>
</html>