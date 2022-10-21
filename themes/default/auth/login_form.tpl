<div class="social-login">
    {if $socialNetworkSettings->getProvider('facebook')->isActive()}
        <a href="{$routes->getRouteString("socialAuth",["providerId"=>"facebook"])}" class="btn auth-btn btn-block facebook text-white mb-2"><i class="ti-facebook float-left"></i> <span>{t}Sign in with Facebook{/t}</span></a>
    {/if}
    {if $socialNetworkSettings->getProvider('google')->isActive()}
        <a href="{$routes->getRouteString("socialAuth",["providerId"=>"google"])}" class="btn auth-btn btn-block google-plus text-white"><i class="ti-google float-left"></i> <span>{t}Sign in with Google+{/t}</span></a>
    {/if}
    {if $socialNetworkSettings->getProvider('twitter')->isActive()}
        <a href="{$routes->getRouteString("socialAuth",["providerId"=>"twitter"])}" class="btn auth-btn btn-block twitter text-white"><i class="ti-twitter-alt float-left"></i> <span>{t}Sign in with Twitter{/t}</span></a>
    {/if}
</div>
{if $socialNetworkSettings->getProvider('facebook')->isActive() or $socialNetworkSettings->getProvider('google')->isActive() or $socialNetworkSettings->getProvider('twitter')->isActive()}
    <div class="my-3 text-sm text-center">{t}OR{/t}</div>
{/if}
<form action="{$routes->getRouteString("adminLogin")}" method="post" id="sign-in-form" class="form-horizontal fade show">
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
            <div class="col-md-8 col-xs-8 text-left">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" name="rememberMe" id="rememberMe">
                    <label class="custom-control-label" for="rememberMe">{t}Remember me{/t}</label>
                </div>
            </div>
            <div class="col-md-4 col-xs-4">
                <button class="btn btn-primary shadow btn-block">{t}Login{/t}</button>
            </div>
            <div class="col-md-12 mt-3 additional-text">
                <a href="{$routes->getRouteString("passwordRecovery")}">{t}Forgot password?{/t}</a>
            </div>
        </div>
        {if $siteViewOptions->getOptionValue("enableRegistration")}
            <div class="text-black-50 mt-3 additional-text">Do not have an account? <a href="#" id="sign-up-tab">{t}Sign Up{/t}</a></div>
        {/if}
    </div>
</form>

<form action="{$routes->getRouteString("adminLogin")}" method="post" id="sign-up-form" class="form-horizontal validate fade d-none">
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
    {*<div class="form-group">
        <label for="gender" class="control-label">{t}Gender{/t}</label>
        <select name="gender" class="form-control custom-select">
            <option value="Male" selected>{t}Male{/t}</option>
            <option value="Female">{t}Female{/t}</option>
        </select>
    </div>*}
    <div class="form-group">
        <input type="text" class="form-control" autocomplete="off" name="phone" placeholder="{t}Phone{/t}">
    </div>
    <div class="form-group">
        <input type="text" class="form-control" autocomplete="off" name="address" placeholder="{t}Address{/t}">
    </div>

    <button type="submit" class="btn btn-primary btn-block shadow reg-user">{t}Register{/t}</button>

    <div class="text-black-50 mt-4 additional-text">Already have an account? <a href="#" id="sign-in-tab">{t}Sign In{/t}</a></div>
</form>