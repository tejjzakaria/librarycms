<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment\Routes\Admin;

    use KAASoft\Environment\Routes\AdminRoute;
    use KAASoft\Environment\Routes\RoutesInterface;

    /**
     * Class SeriesRoutes
     * @package KAASoft\Environment\Routes\Admin
     */
    class SeriesRoutes implements RoutesInterface {

        const SERIES_LIST_VIEW_ROUTE = "seriesListView";
        const SERIES_CREATE_ROUTE    = "seriesCreate";
        const SERIES_EDIT_ROUTE      = "seriesEdit";
        const SERIES_DELETE_ROUTE    = "seriesDelete";

        public static function getRoutes() {
            $routes = [];
            /*************************************  SERIES  **********************************************************/
            $routes[SeriesRoutes::SERIES_LIST_VIEW_ROUTE] = new AdminRoute(_("Series List View"),
                                                                           "/series(?:/page/(\\d+))?[/]??",
                                                                           "Admin\\Series\\SeriesAction",
                                                                           "/series",
                                                                           [ "page" ]);

            $routes[SeriesRoutes::SERIES_CREATE_ROUTE] = new AdminRoute(_("Series Create"),
                                                                        "/series/create[/]??",
                                                                        "Admin\\Series\\SeriesCreateAction",
                                                                        "/series/create/");

            $routes[SeriesRoutes::SERIES_EDIT_ROUTE] = new AdminRoute(_("Series Edit"),
                                                                      "/series/(\\d+)/edit[/]??",
                                                                      "Admin\\Series\\SeriesEditAction",
                                                                      "/series/[seriesId]/edit",
                                                                      [ "seriesId" ]);

            $routes[SeriesRoutes::SERIES_DELETE_ROUTE] = new AdminRoute(_("Series Delete"),
                                                                        "/series/(\\d+)/delete[/]??",
                                                                        "Admin\\Series\\SeriesDeleteAction",
                                                                        "/series/[seriesId]/delete",
                                                                        [ "seriesId" ]);

            /*************************************  SERIES END  ******************************************************/

            return $routes;
        }
    }