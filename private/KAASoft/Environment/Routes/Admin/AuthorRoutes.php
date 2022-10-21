<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment\Routes\Admin;

    use KAASoft\Environment\Routes\AdminRoute;
    use KAASoft\Environment\Routes\RoutesInterface;

    /**
     * Class AuthorRoutes
     * @package KAASoft\Environment\Routes\Admin
     */
    class AuthorRoutes implements RoutesInterface {

        const AUTHOR_LIST_VIEW_ROUTE = "authorListView";
        const AUTHOR_CREATE_ROUTE    = "authorCreate";
        const AUTHOR_EDIT_ROUTE      = "authorEdit";
        const AUTHOR_DELETE_ROUTE    = "authorDelete";

        public static function getRoutes() {
            $routes = [];
            /*************************************  AUTHOR  **********************************************************/
            $routes[AuthorRoutes::AUTHOR_LIST_VIEW_ROUTE] = new AdminRoute(_("Author List View"),
                                                                           "/authors(?:/page/(\\d+))?[/]??",
                                                                           "Admin\\Author\\AuthorsAction",
                                                                           "/authors",
                                                                           [ "page" ]);

            $routes[AuthorRoutes::AUTHOR_CREATE_ROUTE] = new AdminRoute(_("Author Create"),
                                                                        "/author/create[/]??",
                                                                        "Admin\\Author\\AuthorCreateAction",
                                                                        "/author/create/");

            $routes[AuthorRoutes::AUTHOR_EDIT_ROUTE] = new AdminRoute(_("Author Edit"),
                                                                      "/author/(\\d+)/edit[/]??",
                                                                      "Admin\\Author\\AuthorEditAction",
                                                                      "/author/[authorId]/edit",
                                                                      [ "authorId" ]);

            $routes[AuthorRoutes::AUTHOR_DELETE_ROUTE] = new AdminRoute(_("Author Delete"),
                                                                        "/author/(\\d+)/delete[/]??",
                                                                        "Admin\\Author\\AuthorDeleteAction",
                                                                        "/author/[authorId]/delete",
                                                                        [ "authorId" ]);

            /*************************************  AUTHOR END  ******************************************************/

            return $routes;
        }
    }