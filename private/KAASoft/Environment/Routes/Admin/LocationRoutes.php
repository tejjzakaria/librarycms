<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment\Routes\Admin;

    use KAASoft\Environment\Routes\AdminRoute;
    use KAASoft\Environment\Routes\RoutesInterface;

    /**
     * Class LocationRoutes
     * @package KAASoft\Environment\Routes\Admin
     */
    class LocationRoutes implements RoutesInterface {

        const LOCATION_LIST_VIEW_ROUTE = "locationListView";
        const LOCATION_CREATE_ROUTE    = "locationCreate";
        const LOCATION_EDIT_ROUTE      = "locationEdit";
        const LOCATION_DELETE_ROUTE    = "locationDelete";

        public static function getRoutes() {
            $routes = [];
            /*************************************  AUTHOR  **********************************************************/
            $routes[LocationRoutes::LOCATION_LIST_VIEW_ROUTE] = new AdminRoute(_("Location List View"),
                                                                               "/locations(?:/page/(\\d+))?[/]??",
                                                                               "Admin\\Location\\LocationsAction",
                                                                               "/locations",
                                                                               [ "page" ]);

            $routes[LocationRoutes::LOCATION_CREATE_ROUTE] = new AdminRoute(_("Location Create"),
                                                                            "/location/create[/]??",
                                                                            "Admin\\Location\\LocationCreateAction",
                                                                            "/location/create/");

            $routes[LocationRoutes::LOCATION_EDIT_ROUTE] = new AdminRoute(_("Location Edit"),
                                                                          "/location/(\\d+)/edit[/]??",
                                                                          "Admin\\Location\\LocationEditAction",
                                                                          "/location/[locationId]/edit",
                                                                          [ "locationId" ]);

            $routes[LocationRoutes::LOCATION_DELETE_ROUTE] = new AdminRoute(_("Location Delete"),
                                                                            "/location/(\\d+)/delete[/]??",
                                                                            "Admin\\Location\\LocationDeleteAction",
                                                                            "/location/[locationId]/delete",
                                                                            [ "locationId" ]);

            /*************************************  AUTHOR END  ******************************************************/

            return $routes;
        }
    }