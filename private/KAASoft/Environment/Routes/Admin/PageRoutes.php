<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment\Routes\Admin;


    use KAASoft\Environment\Routes\AdminRoute;
    use KAASoft\Environment\Routes\RoutesInterface;

    class PageRoutes implements RoutesInterface {
        const PAGE_LIST_VIEW_ROUTE = "pageListView";
        const PAGE_CREATE_ROUTE    = "pageCreate";
        const PAGE_EDIT_ROUTE      = "pageEdit";
        const PAGE_DELETE_ROUTE    = "pageDelete";

        public static function getRoutes() {
            $routes = [];

            $routes[PageRoutes::PAGE_LIST_VIEW_ROUTE] = new AdminRoute(_("Page List View"),
                                                                       "/pages(?:/page/(\\d+))?[/]??",
                                                                       "Admin\\Page\\PagesAction",
                                                                       "/pages",
                                                                       [ "page" ]);

            $routes[PageRoutes::PAGE_CREATE_ROUTE] = new AdminRoute(_("Page Create"),
                                                                    "/page/create[/]??",
                                                                    "Admin\\Page\\PageCreateAction",
                                                                    "/page/create");

            $routes[PageRoutes::PAGE_EDIT_ROUTE] = new AdminRoute(_("Page Edit"),
                                                                  "/page/edit/(\\d+)[/]??",
                                                                  "Admin\\Page\\PageEditAction",
                                                                  "/page/edit/[pageId]",
                                                                  [ "pageId" ]);

            $routes[PageRoutes::PAGE_DELETE_ROUTE] = new AdminRoute(_("Page Delete"),
                                                                    "/page/delete/(\\d+)[/]??",
                                                                    "Admin\\Page\\PageDeleteAction",
                                                                    "/page/delete/[pageId]",
                                                                    [ "pageId" ]);

            return $routes;
        }
    }
