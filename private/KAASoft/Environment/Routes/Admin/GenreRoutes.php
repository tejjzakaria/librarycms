<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment\Routes\Admin;

    use KAASoft\Environment\Routes\AdminRoute;
    use KAASoft\Environment\Routes\RoutesInterface;

    /**
     * Class GenreRoutes
     * @package KAASoft\Environment\Routes\Admin
     */
    class GenreRoutes implements RoutesInterface {

        const GENRE_LIST_VIEW_ROUTE = "genreListView";
        const GENRE_CREATE_ROUTE    = "genreCreate";
        const GENRE_EDIT_ROUTE      = "genreEdit";
        const GENRE_DELETE_ROUTE    = "genreDelete";

        public static function getRoutes() {
            $routes = [];
            /*************************************  AUTHOR  **********************************************************/
            $routes[GenreRoutes::GENRE_LIST_VIEW_ROUTE] = new AdminRoute(_("Genre List View"),
                                                                         "/genres(?:/page/(\\d+))?[/]??",
                                                                         "Admin\\Genre\\GenresAction",
                                                                         "/genres",
                                                                         [ "page" ]);

            $routes[GenreRoutes::GENRE_CREATE_ROUTE] = new AdminRoute(_("Genre Create"),
                                                                      "/genre/create[/]??",
                                                                      "Admin\\Genre\\GenreCreateAction",
                                                                      "/genre/create/");

            $routes[GenreRoutes::GENRE_EDIT_ROUTE] = new AdminRoute(_("Genre Edit"),
                                                                    "/genre/(\\d+)/edit[/]??",
                                                                    "Admin\\Genre\\GenreEditAction",
                                                                    "/genre/[genreId]/edit",
                                                                    [ "genreId" ]);

            $routes[GenreRoutes::GENRE_DELETE_ROUTE] = new AdminRoute(_("Genre Delete"),
                                                                      "/genre/(\\d+)/delete[/]??",
                                                                      "Admin\\Genre\\GenreDeleteAction",
                                                                      "/genre/[genreId]/delete",
                                                                      [ "genreId" ]);

            /*************************************  AUTHOR END  ******************************************************/

            return $routes;
        }
    }