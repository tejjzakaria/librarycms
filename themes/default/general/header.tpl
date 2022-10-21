<header class="header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-6 col-lg-3 col-md-6">
                <div class="logo">
                    <a href="{$routes->getRouteString("publicIndex")}">{if $colorSchemas[$theme->getActiveColorSchema()]->isDark()}<img src="{$siteViewOptions->getOptionValue("lightLogoFilePath")}" alt="Logo">{else}<img src="{$siteViewOptions->getOptionValue("logoFilePath")}" alt="Logo">{/if}</a>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <nav class="navbar navbar-expand-lg">
                    <div class="collapse navbar-collapse justify-content-center" id="header-menu">
                        {function printMenu node=null}
                            {if isset($node) and $node->getValue() !== null}
                                {assign var="menuItem" value=$node->getValue()}
                                <li class="{if $node->getParent() == null}nav-item{/if}{if $node->getChildren() != null and $node->getParent() == null} dropdown{elseif $node->getParent() != null and $node->getChildren() != null} dropdown-submenu{/if} {if $menuItem->getClass() != null}{$menuItem->getClass()}{/if}">
                                    <a href="{if $menuItem->getPageId() !== null}{$pageUrls[$menuItem->getPageId()]}{elseif  $menuItem->getPostId() !== null}{$postUrls[$menuItem->getPostId()]}{elseif $menuItem->getLink() !== null}{$menuItem->getLink()}{/if}" class="{if $node->getParent() != null}dropdown-item{/if}" {if $node->getChildren() != null} data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"{/if}>
                                        {$menuItem->getTitle()} {if $node->getChildren() != null and $node->getParent() == null}
                                            <i class="ti-angle-down"></i>
                                        {elseif $node->getParent() != null and $node->getChildren() != null}
                                            <i class="ti-angle-right ml-auto"></i>
                                        {/if}
                                    </a>
                                    {if $node->getChildren() != null}
                                        <ul class="dropdown-menu{if $node->getParent() != null} menu-left{/if}">
                                            {foreach $node->getChildren() as $subNode}
                                                {printMenu node=$subNode}
                                            {/foreach}
                                        </ul>
                                    {/if}
                                </li>
                            {/if}
                        {/function}
                        <ul class="navbar-nav primary-menu">
                            {if isset($menu1)}
                                {foreach from=$menu1->getRootNodes() item=rootNode}
                                    {printMenu node=$rootNode}
                                {/foreach}
                            {/if}
                        </ul>
                    </div>
                </nav>
            </div>
            <div class="col-6 col-lg-3 col-md-6">
                <div class="menu-icons d-flex text-right align-items-center justify-content-end">
                    <div class="search-box">
                        <a href="#" class="search-open"><i class="ti-search"></i></a>
                    </div>
                    <div class="user-dropdown">
                        {if isset($user) and $user != null}
                            <a href="#" class="user-menu" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="ti-user"></i></a>
                            <ul class="dropdown-menu">
                                {if $user->getRole() != null and $user->getRole()->getPriority() <= 100}
                                    <li>
                                        <a class="dropdown-item" href="{$routes->getRouteString("userProfile")}">{t}Profile{/t}</a>
                                    </li>
                                    {if $siteViewOptions->getOptionValue("enableBookIssue")}
                                        <li>
                                            <a class="dropdown-item" href="{$routes->getRouteString("userBooksView")}">{t}My Books{/t}</a>
                                        </li>
                                    {/if}
                                    {if $siteViewOptions->getOptionValue("enableBookRequest") and !$siteViewOptions->getOptionValue("enableBookIssueByUser")}
                                        <li>
                                            <a class="dropdown-item" href="{$routes->getRouteString("userRequestListView")}">{t}Requested Books{/t}</a>
                                        </li>
                                    {/if}
                                    {if $siteViewOptions->getOptionValue("enableBookIssue")}
                                        <li>
                                            <a class="dropdown-item" href="{$routes->getRouteString("userIssuedBooksView")}">{t}Issued Books History{/t}</a>
                                        </li>
                                    {/if}
                                {else}
                                    <li>
                                        <a class="dropdown-item" href="{$routes->getRouteString("adminIndex")}">{t}Dashboard{/t}</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{$routes->getRouteString("userEdit",["userId"=>$user->getId()])}">{t}Profile{/t}</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{$routes->getRouteString("bookListView")}">{t}Books{/t}</a>
                                    </li>
                                    {if $siteViewOptions->getOptionValue("enableBookRequest")}
                                        <li>
                                            <a class="dropdown-item" href="{$routes->getRouteString("requestListView")}">{t}Requested Books{/t}</a>
                                        </li>
                                    {/if}
                                    {if $siteViewOptions->getOptionValue("enableBookIssue")}
                                        <li>
                                            <a class="dropdown-item" href="{$routes->getRouteString("issueListView")}">{t}Issued Books{/t}</a>
                                        </li>
                                    {/if}
                                {/if}
                                <li class="divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{$routes->getRouteString("publicLogout")}">{t}Logout{/t}</a>
                                </li>
                            </ul>
                        {else}
                            <a href="#" data-toggle="modal" data-target="#login-box" class="open-login-box">
                                <i class="ti-lock"></i>
                            </a>
                        {/if}
                    </div>
                    {if isset($languages)}
                        <div class="languages">
                            <a href="#" class="lang-select" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="ti-world"></i></a>
                            <ul class="dropdown-menu">
                                {foreach from=$languages item=language}
                                    {if $language->isActive()}
                                        <li>
                                            <a class="dropdown-item {if $language->getCode()==$activeLanguage->getCode()}active{/if}" href="{$routes->getRouteString("languageChange",["languageCode"=>$language->getCode()])}"><i class="flag flag-{$language->getShortCode()}"></i> {$language->getName()}
                                            </a>
                                        </li>
                                    {/if}
                                {/foreach}
                            </ul>
                        </div>
                    {/if}
                </div>
                <div class="menu-toggler" data-toggle="collapse" data-target="#header-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <div class="bar"></div>
                </div>
            </div>

            <div class="col-12">
                <div class="search-header hide">
                    <form action="{$routes->getRouteString("bookSearchPublic")}" method="{if $siteViewOptions->getOptionValue("useGetRequestBookSearch")}get{else}post{/if}">
                        <input class="form-control search-input" name="{if $siteViewOptions->getOptionValue("useGetRequestBookSearch")}q{else}searchText{/if}" aria-describedby="search" type="search">
                        <button type="submit" class="btn" id="header-search"><i class="ti-search"></i></button>
                        <span class="search-close">
                        <i class="ti-close"></i>
                    </span>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<div class="modal login-box" id="login-box" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="card card-no-border">
                    <div class="card-body">
                        {if $colorSchemas[$theme->getActiveColorSchema()]->isDark()}
                            <img src="{$siteViewOptions->getOptionValue("lightLogoFilePath")}" class="d-flex ml-auto mr-auto mb-4 mt-2 img-fluid" alt="Login">
                        {else}
                            <img src="{$siteViewOptions->getOptionValue("logoFilePath")}" class="d-flex ml-auto mr-auto mb-4 mt-2 img-fluid" alt="Login">
                        {/if}
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
                        <form action="{$routes->getRouteString("publicLogin")}" method="post" class="form-horizontal">
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <input name="login" class="form-control" placeholder="{t}Login{/t}" value="" type="text" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <input name="password" class="form-control" placeholder="{t}Password{/t}" type="password" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-8 col-xs-8 text-left">
                                        <div class="custom-control custom-checkbox mt-2">
                                            <input type="checkbox" name="rememberMe" class="custom-control-input" id="rememberMe">
                                            <label class="custom-control-label" for="rememberMe">{t}Remember me{/t}</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-xs-4">
                                        <button class="btn btn-primary shadow btn-block">{t}Login{/t}</button>
                                    </div>
                                    <div class="col-md-12 mt-3 additional-text text-center">
                                        <a href="{$routes->getRouteString("passwordRecovery")}">{t}Forgot password?{/t}</a>
                                    </div>
                                </div>
                                {if $siteViewOptions->getOptionValue("enableRegistration")}
                                    <div class="text-black-50 mt-3 additional-text text-center">Do not have an account? <a href="{$routes->getRouteString("userRegistration")}">{t}Sign Up{/t}</a></div>
                                {/if}
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>