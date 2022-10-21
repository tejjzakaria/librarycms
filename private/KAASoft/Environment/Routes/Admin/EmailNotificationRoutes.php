<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment\Routes\Admin;


    use KAASoft\Environment\Routes\AdminRoute;
    use KAASoft\Environment\Routes\RoutesInterface;

    class EmailNotificationRoutes implements RoutesInterface {

        const EMAIL_NOTIFICATION_LIST_VIEW_ROUTE = "emailNotificationListView";
        const EMAIL_NOTIFICATION_CREATE_ROUTE    = "emailNotificationCreate";
        const EMAIL_NOTIFICATION_EDIT_ROUTE      = "emailNotificationEdit";
        const EMAIL_NOTIFICATION_ACTIVATE_ROUTE  = "emailNotificationEnable";
        const EMAIL_NOTIFICATION_TEST_ROUTE      = "emailNotificationTest";
        const SMTP_SETTINGS_ROUTE                = "smtpSettings";
        const EMAIL_SETTINGS_ROUTE               = "emailSettings";
        const EMAIL_SEND_ROUTE                   = "emailSend";
        const USER_SEND_EMAIL_ROUTE              = "userSendEmail";

        public static function getRoutes() {
            $routes = array();

            $routes[EmailNotificationRoutes::EMAIL_SETTINGS_ROUTE] = new AdminRoute(_("Email Settings"),
                                                                                    "/email-settings[/]??",
                                                                                    "Admin\\Util\\EmailSettingsEditAction",
                                                                                    "/email-settings");

            $routes[EmailNotificationRoutes::SMTP_SETTINGS_ROUTE] = new AdminRoute(_("SMTP Settings"),
                                                                                   "/smtp-settings[/]??",
                                                                                   "Admin\\Util\\SMTPSettingsEditAction",
                                                                                   "/smtp-settings");

            $routes[EmailNotificationRoutes::EMAIL_NOTIFICATION_LIST_VIEW_ROUTE] = new AdminRoute(_("Email Notification List View"),
                                                                                                  "/email-notifications[/]??",
                                                                                                  "Admin\\Util\\EmailNotification\\EmailNotificationsViewAction",
                                                                                                  "/email-notifications");

            $routes[EmailNotificationRoutes::EMAIL_NOTIFICATION_CREATE_ROUTE] = new AdminRoute(_("Email Notification Create"),
                                                                                               "/email-notification/create[/]??",
                                                                                               "Admin\\Util\\EmailNotification\\EmailNotificationCreateAction",
                                                                                               "/email-notification/create/");


            $routes[EmailNotificationRoutes::EMAIL_NOTIFICATION_EDIT_ROUTE] = new AdminRoute(_("Email Notification Edit"),
                                                                                             "/email-notification/edit/([^/]*)[/]??",
                                                                                             "Admin\\Util\\EmailNotification\\EmailNotificationEditAction",
                                                                                             "/email-notification/edit/[route]",
                                                                                             [ "route" ]);

            $routes[EmailNotificationRoutes::EMAIL_NOTIFICATION_TEST_ROUTE] = new AdminRoute(_("Email Notification Test"),
                                                                                             "/email-notification/test[/]??",
                                                                                             "Admin\\Util\\EmailNotification\\EmailNotificationTestAction",
                                                                                             "/email-notification/test");

            $routes[EmailNotificationRoutes::EMAIL_NOTIFICATION_ACTIVATE_ROUTE] = new AdminRoute(_("Email Notification Activate"),
                                                                                                 "/email-notification/([^/]*)/(enable|disable)[/]??",
                                                                                                 "Admin\\Util\\EmailNotification\\EmailNotificationEnableAction",
                                                                                                 "/email-notification/[route]/[enable]",
                                                                                                 [ "route",
                                                                                                   "enable" ]);

            $routes[EmailNotificationRoutes::EMAIL_SEND_ROUTE] = new AdminRoute(_("Email Send"),
                                                                                "/email/send[/]??",
                                                                                "Admin\\Util\\EmailNotification\\EmailSendAction",
                                                                                "/email/send");

            $routes[EmailNotificationRoutes::USER_SEND_EMAIL_ROUTE] = new AdminRoute(_("User Email Send"),
                                                                                     "/user/(\\d+)/email/send[/]??",
                                                                                     "Admin\\Util\\EmailNotification\\UserSendEmailAction",
                                                                                     "/user/[userId]/email/send",
                                                                                     [ "userId" ]);

            return $routes;
        }
    }