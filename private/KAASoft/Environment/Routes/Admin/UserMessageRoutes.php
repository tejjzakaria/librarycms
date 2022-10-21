<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment\Routes\Admin;


    use KAASoft\Environment\Routes\AdminRoute;
    use KAASoft\Environment\Routes\RoutesInterface;

    class UserMessageRoutes implements RoutesInterface {
        const USER_MESSAGE_LIST_VIEW_ROUTE  = "userMessageListView";
        const USER_MESSAGE_DELETE_ROUTE     = "userMessageDelete";
        const USER_MESSAGE_SET_VIEWED_ROUTE = "userMessageSetViewed";
        const USER_MESSAGE_CREATE_ROUTE     = "userMessageCreate";

        public static function getRoutes() {
            $routes = [];

            $routes[UserMessageRoutes::USER_MESSAGE_LIST_VIEW_ROUTE] = new AdminRoute(_('User Message List View'),
                                                                                      '/user-messages(?:/page/(\d+))?[/]??',
                                                                                      'Admin\\UserMessage\\UserMessagesViewAction',
                                                                                      '/user-messages',
                                                                                      [ 'page' ]);

            $routes[UserMessageRoutes::USER_MESSAGE_CREATE_ROUTE] = new AdminRoute(_('User Message Create'),
                                                                                   '/user-message/create[/]??',
                                                                                   'Pub\\UserMessage\\UserMessageCreateAction',
                                                                                   '/user-message/create');

            $routes[UserMessageRoutes::USER_MESSAGE_DELETE_ROUTE] = new AdminRoute(_('User Message Delete'),
                                                                                   '/user-message/(\d+)/delete[/]??',
                                                                                   'Admin\\UserMessage\\UserMessageDeleteAction',
                                                                                   '/user-message/[messageId]/delete',
                                                                                   [ 'messageId' ]);

            $routes[UserMessageRoutes::USER_MESSAGE_SET_VIEWED_ROUTE] = new AdminRoute(_('User Message Set Viewed'),
                                                                                       '/user-message/set-viewed[/]??',
                                                                                       'Admin\\UserMessage\\UserMessageSetViewedAction',
                                                                                       '/user-message/set-viewed');

            return $routes;
        }
    }
