<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment\Routes\Admin;

    use KAASoft\Environment\Routes\AdminRoute;
    use KAASoft\Environment\Routes\PublicRoute;
    use KAASoft\Environment\Routes\RoutesInterface;

    /**
     * Class RequestRoutes
     * @package KAASoft\Environment\Routes\Admin
     */
    class RequestRoutes implements RoutesInterface {

        const REQUEST_LIST_VIEW_ROUTE         = "requestListView";
        const USER_REQUEST_LIST_VIEW_ROUTE    = "userRequestListView";
        const REQUEST_CREATE_ROUTE            = "requestCreate";
        const REQUEST_EDIT_ROUTE              = "requestEdit";
        const REQUEST_DELETE_ROUTE            = "requestDelete";
        const REQUEST_SET_STATUS_ROUTE        = "requestSetStatus";
        const USER_REQUESTED_BOOKS_VIEW_ROUTE = "userRequestedBooksView";

        public static function getRoutes() {
            $routes = [];
            /*************************************  REQUEST  **********************************************************/
            $routes[RequestRoutes::REQUEST_LIST_VIEW_ROUTE] = new AdminRoute(_("Request List View"),
                                                                             "/requests(?:/page/(\\d+))?[/]??",
                                                                             "Admin\\Request\\RequestsAction",
                                                                             "/requests",
                                                                             [ "page" ]);

            $routes[RequestRoutes::USER_REQUEST_LIST_VIEW_ROUTE] = new PublicRoute(_("User\"s Requests View"),
                                                                                  "/user/requests(?:/page/(\\d+))?[/]??",
                                                                                  "Admin\\Request\\UserRequestsAction",
                                                                                  "/user/requests",
                                                                                  [ "page" ]);

            $routes[RequestRoutes::REQUEST_CREATE_ROUTE] = new PublicRoute(_("Request Create"),
                                                                          "/request/create[/]??",
                                                                          "Admin\\Request\\RequestCreateAction",
                                                                          "/request/create/");

            $routes[RequestRoutes::REQUEST_EDIT_ROUTE] = new AdminRoute(_("Request Edit"),
                                                                        "/request/(\\d+)/edit[/]??",
                                                                        "Admin\\Request\\RequestEditAction",
                                                                        "/request/[requestId]/edit",
                                                                        [ "requestId" ]);

            $routes[RequestRoutes::REQUEST_DELETE_ROUTE] = new AdminRoute(_("Request Delete"),
                                                                          "/request/(\\d+)/delete[/]??",
                                                                          "Admin\\Request\\RequestDeleteAction",
                                                                          "/request/[requestId]/delete",
                                                                          [ "requestId" ]);

            $routes[RequestRoutes::REQUEST_SET_STATUS_ROUTE] = new AdminRoute(_("Request Set Status"),
                                                                              "/request/(\\d+)/set-status/(Pending|Accepted|Rejected)[/]??",
                                                                              "Admin\\Request\\RequestStatusSetAction",
                                                                              "/request/[requestId]/set-status/[status]",
                                                                              [ "requestId",
                                                                                "status" ]);

            $routes[RequestRoutes::USER_REQUESTED_BOOKS_VIEW_ROUTE] = new PublicRoute(_("User Requested Books View"),
                                                                                     "/requested-books(?:/page/(\\d+))?[/]??",
                                                                                     "Admin\\Book\\UserRequestedBooksViewAction",
                                                                                     "/requested-books",
                                                                                     [ "page" ]);

            /*************************************  REQUEST END  ******************************************************/

            return $routes;
        }
    }