<form action="{$routes->getRouteString("adminLogin")}" method="post" class="form-horizontal">
    <div class="form-group ">
        <div class="col-xs-12">
            <input type="text" name="login" class="form-control" placeholder="{t}Login{/t}" value="{if isset($login) and !empty($login) and $login != false  }{$login}{/if}">
        </div>
    </div>
    <div class="form-group">
        <div class="col-xs-12">
            <input type="password" name="password" class="form-control" placeholder="{t}Password{/t}">
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-md-8 col-xs-8">
                <div class="app-checkbox">
                    <label>
                        <input type="checkbox" name="rememberMe"> {t}Remember me{/t}
                    </label>
                </div>
            </div>
            <div class="col-md-4 col-xs-4">
                <button class="btn btn-success btn-block">{t}Login{/t}</button>
            </div>
            <div class="col-md-12 mt-2">
                <a href="{$routes->getRouteString("passwordRecovery")}" class="btn btn-secondary btn-block">{t}Forgot password?{/t}</a>
            </div>
            {if $siteViewOptions->getOptionValue("enableRegistration")}
                <div class="col-md-12 mt-2">
                    <a href="{$routes->getRouteString("userRegistration")}" class="btn btn-info btn-block">{t}Sign Up{/t}</a>
                </div>
            {/if}
        </div>
    </div>
</form>