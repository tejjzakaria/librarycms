<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment\Routes\Pub;

    use KAASoft\Environment\Routes\PublicRoute;
    use KAASoft\Environment\Routes\RoutesInterface;

    /**
     * Class GeneralPublicRoutes
     * @package KAASoft\Environment\Routes\Pub
     */
    class GeneralPublicRoutes implements RoutesInterface {

        const PUBLIC_INDEX_ROUTE           = "publicIndex";
        const PUBLIC_LOGIN_ROUTE           = "publicLogin";
        const PUBLIC_LOGOUT_ROUTE          = "publicLogout";
        const PUBLIC_SESSION_RESET_ROUTE   = "publicSessionReset";
        const PAGE_IS_FORBIDDEN_ROUTE      = "pageIsForbidden";
        const PAGE_IS_NOT_FOUND_ROUTE      = "pageIsNotFound";
        const INTERNAL_SERVER_ERROR_ROUTE  = "internalServerError";
        const EPIC_FAIL_ROUTE_NAME         = "epicFail";
        const USER_REGISTRATION_ROUTE_NAME = "userRegistration";
        const USER_PROFILE_ROUTE_NAME      = "userProfile";
        const SOCIAL_AUTH_ROUTE_NAME       = "socialAuth";

        public static function getRoutes() {
            $routes = [];
            $routes[GeneralPublicRoutes::PUBLIC_INDEX_ROUTE] = new PublicRoute(_("Public Index"),
                                                                               "[/]??(?:/index.(?:html|php|htm))?",
                                                                               "Pub\\IndexAction",
                                                                               "/");

            $routes[GeneralPublicRoutes::INTERNAL_SERVER_ERROR_ROUTE] = new PublicRoute(_("InternalServerError"),
                                                                                        "/500[/]??",
                                                                                        "Pub\\InternalServerErrorAction",
                                                                                        "/500");

            $routes[GeneralPublicRoutes::PAGE_IS_NOT_FOUND_ROUTE] = new PublicRoute(_("Page Is Not Found"),
                                                                                    "/404[/]??",
                                                                                    "Pub\\PageNotFoundAction",
                                                                                    "/404");

            $routes[GeneralPublicRoutes::PAGE_IS_FORBIDDEN_ROUTE] = new PublicRoute(_("Page Is Forbidden"),
                                                                                    "/403[/]??",
                                                                                    "Pub\\PageForbiddenAction",
                                                                                    "/403");

            $routes[GeneralPublicRoutes::EPIC_FAIL_ROUTE_NAME] = new PublicRoute(_("Epic Fail"),
                                                                                 "/epicFail[/]??",
                                                                                 "Pub\\EpicFailAction",
                                                                                 "/epicFail");

            $routes[GeneralPublicRoutes::PUBLIC_LOGIN_ROUTE] = new PublicRoute(_("Public Login"),
                                                                               "/login[/]??",
                                                                               "Pub\\LoginAction",
                                                                               "/login");

            $routes[GeneralPublicRoutes::PUBLIC_LOGOUT_ROUTE] = new PublicRoute(_("Public Logout"),
                                                                                "/logout[/]??",
                                                                                "Pub\\LogoutAction",
                                                                                "/logout");

            $routes[GeneralPublicRoutes::PUBLIC_SESSION_RESET_ROUTE] = new PublicRoute(_("Session Reset"),
                                                                                       "/session-reset[/]??",
                                                                                       "Pub\\SessionResetAction",
                                                                                       "/session-reset");

            $routes[GeneralPublicRoutes::USER_REGISTRATION_ROUTE_NAME] = new PublicRoute(_("Public User Registration"),
                                                                                         "/registration[/]??",
                                                                                         "Pub\\RegistrationAction",
                                                                                         "/registration");

            $routes[GeneralPublicRoutes::USER_PROFILE_ROUTE_NAME] = new PublicRoute(_("Public User Profile"),
                                                                                    "/profile[/]??",
                                                                                    "Pub\\User\\UserProfileAction",
                                                                                    "/profile");

            $routes[GeneralPublicRoutes::SOCIAL_AUTH_ROUTE_NAME] = new PublicRoute(_("Social Auth"),
                                                                                   "/auth/(facebook|google|twitter)[/]?",
                                                                                   "Pub\\SocialAuthAction",
                                                                                   "/auth/[providerId]",
                                                                                   [ "providerId" ]);

            return $routes;
        }
    }
