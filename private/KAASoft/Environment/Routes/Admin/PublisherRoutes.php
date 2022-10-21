<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment\Routes\Admin;

    use KAASoft\Environment\Routes\AdminRoute;
    use KAASoft\Environment\Routes\RoutesInterface;

    /**
     * Class PublisherRoutes
     * @package KAASoft\Environment\Routes\Admin
     */
    class PublisherRoutes implements RoutesInterface {

        const PUBLISHER_LIST_VIEW_ROUTE = "publisherListView";
        const PUBLISHER_CREATE_ROUTE    = "publisherCreate";
        const PUBLISHER_EDIT_ROUTE      = "publisherEdit";
        const PUBLISHER_DELETE_ROUTE    = "publisherDelete";

        public static function getRoutes() {
            $routes = [];
            /*************************************  PUBLISHER  ********************************************************/
            $routes[PublisherRoutes::PUBLISHER_LIST_VIEW_ROUTE] = new AdminRoute(_("Publisher List View"),
                                                                                 "/publishers(?:/page/(\\d+))?[/]??",
                                                                                 "Admin\\Publisher\\PublishersAction",
                                                                                 "/publishers",
                                                                                 [ "page" ]);

            $routes[PublisherRoutes::PUBLISHER_CREATE_ROUTE] = new AdminRoute(_("Publisher Create"),
                                                                              "/publisher/create[/]??",
                                                                              "Admin\\Publisher\\PublisherCreateAction",
                                                                              "/publisher/create/");

            $routes[PublisherRoutes::PUBLISHER_EDIT_ROUTE] = new AdminRoute(_("Publisher Edit"),
                                                                            "/publisher/(\\d+)/edit[/]??",
                                                                            "Admin\\Publisher\\PublisherEditAction",
                                                                            "/publisher/[publisherId]/edit",
                                                                            [ "publisherId" ]);

            $routes[PublisherRoutes::PUBLISHER_DELETE_ROUTE] = new AdminRoute(_("Publisher Delete"),
                                                                              "/publisher/(\\d+)/delete[/]??",
                                                                              "Admin\\Publisher\\PublisherDeleteAction",
                                                                              "/publisher/[publisherId]/delete",
                                                                              [ "publisherId" ]);

            /*************************************  PUBLISHER END  ****************************************************/

            return $routes;
        }
    }