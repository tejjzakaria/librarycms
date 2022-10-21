<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment\Routes\Admin;

    use KAASoft\Environment\Routes\AdminRoute;
    use KAASoft\Environment\Routes\RoutesInterface;

    /**
     * Class StoreRoutes
     * @package KAASoft\Environment\Routes\Admin
     */
    class StoreRoutes implements RoutesInterface {

        const STORE_LIST_VIEW_ROUTE = "storeListView";
        const STORE_CREATE_ROUTE    = "storeCreate";
        const STORE_EDIT_ROUTE      = "storeEdit";
        const STORE_DELETE_ROUTE    = "storeDelete";

        public static function getRoutes() {
            $routes = [];
            /*************************************  AUTHOR  **********************************************************/
            $routes[StoreRoutes::STORE_LIST_VIEW_ROUTE] = new AdminRoute(_("Store List View"),
                                                                         "/stores(?:/page/(\\d+))?[/]??",
                                                                         "Admin\\Store\\StoresAction",
                                                                         "/stores",
                                                                         [ "page" ]);

            $routes[StoreRoutes::STORE_CREATE_ROUTE] = new AdminRoute(_("Store Create"),
                                                                      "/store/create[/]??",
                                                                      "Admin\\Store\\StoreCreateAction",
                                                                      "/store/create/");

            $routes[StoreRoutes::STORE_EDIT_ROUTE] = new AdminRoute(_("Store Edit"),
                                                                    "/store/(\\d+)/edit[/]??",
                                                                    "Admin\\Store\\StoreEditAction",
                                                                    "/store/[storeId]/edit",
                                                                    [ "storeId" ]);

            $routes[StoreRoutes::STORE_DELETE_ROUTE] = new AdminRoute(_("Store Delete"),
                                                                      "/store/(\\d+)/delete[/]??",
                                                                      "Admin\\Store\\StoreDeleteAction",
                                                                      "/store/[storeId]/delete",
                                                                      [ "storeId" ]);

            /*************************************  AUTHOR END  ******************************************************/

            return $routes;
        }
    }