<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment\Routes\Admin;

    use KAASoft\Environment\Routes\AdminRoute;
    use KAASoft\Environment\Routes\PublicRoute;
    use KAASoft\Environment\Routes\RoutesInterface;

    /**
     * Class SessionRoutes
     * @package KAASoft\Environment\Routes\Admin
     */
    class SessionRoutes implements RoutesInterface {

        const INDEX_ADMIN_ROUTE        = "adminIndex";
        const LOGIN_ROUTE              = "adminLogin";
        const LOGOUT_ROUTE             = "adminLogout";
        const SESSION_RESET_ROUTE      = "sessionReset";
        const EMAIL_CONFIRMATION_ROUTE = "emailConfirmation";
        const PASSWORD_RECOVERY_ROUTE  = "passwordRecovery";
        const PASSWORD_CHANGE_ROUTE    = "passwordChange";

        public static function getRoutes() {
            $routes = [];
            /*************************************  SESSION  ********************************************************/
            $routes[SessionRoutes::INDEX_ADMIN_ROUTE] = new AdminRoute(_("Admin Index"),
                                                                       "[/]??",
                                                                       "Admin\\General\\IndexAction",
                                                                       "/");

            $routes[SessionRoutes::LOGIN_ROUTE] = new AdminRoute(_("Admin Login"),
                                                                 "/login[/]??",
                                                                 "Admin\\General\\LoginAction",
                                                                 "/login");

            $routes[SessionRoutes::LOGOUT_ROUTE] = new AdminRoute(_("Admin Logout"),
                                                                  "/logout[/]??",
                                                                  "Admin\\General\\LogoutAction",
                                                                  "/logout");

            $routes[SessionRoutes::SESSION_RESET_ROUTE] = new AdminRoute(_("Session Reset"),
                                                                          "/session-reset[/]??",
                                                                          "Admin\\General\\SessionResetAction",
                                                                          "/session-reset");

            $routes[SessionRoutes::PASSWORD_RECOVERY_ROUTE] = new PublicRoute(_("Password Recovery"),
                                                                              "/password-recovery[/]??",
                                                                              "Admin\\General\\PasswordRecoveryAction",
                                                                              "/password-recovery");

            $routes[SessionRoutes::PASSWORD_CHANGE_ROUTE] = new PublicRoute(_("Password Change"),
                                                                            "/password-change/(.*)[/]??",
                                                                            "Admin\\General\\PasswordChangeAction",
                                                                            "/password-change/[token]",
                                                                            [ "token" ]);

            $routes[SessionRoutes::EMAIL_CONFIRMATION_ROUTE] = new PublicRoute(_("Email Confirm"),
                                                                               "/email-confirm/(.*)[/]??",
                                                                               "Pub\\EmailConfirmAction",
                                                                               "/email-confirm/[token]",
                                                                               [ "token" ]);

            /*************************************  SESSION END *****************************************************/

            return $routes;
        }
    }