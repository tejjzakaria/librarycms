<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment\Routes\Admin;

    use KAASoft\Environment\Routes\AdminRoute;
    use KAASoft\Environment\Routes\PublicRoute;
    use KAASoft\Environment\Routes\RoutesInterface;

    /**
     * Class ReviewRoutes
     * @package KAASoft\Environment\Routes\Admin
     */
    class ReviewRoutes implements RoutesInterface {

        const REVIEW_LIST_VIEW_ROUTE = "reviewListView";
        const REVIEW_CREATE_ROUTE    = "reviewCreate";
        const REVIEW_EDIT_ROUTE      = "reviewEdit";
        const REVIEW_DELETE_ROUTE    = "reviewDelete";
        const REVIEW_CREATE_PUBLIC_ROUTE    = "reviewCreatePublic";

        public static function getRoutes() {
            $routes = [];
            /*************************************  REVIEW  **********************************************************/
            $routes[ReviewRoutes::REVIEW_LIST_VIEW_ROUTE] = new AdminRoute(_("Review List View"),
                                                                           "/reviews(?:/page/(\\d+))?[/]??",
                                                                           "Admin\\Review\\ReviewsAction",
                                                                           "/reviews",
                                                                           [ "page" ]);

            $routes[ReviewRoutes::REVIEW_CREATE_ROUTE] = new AdminRoute(_("Review Create"),
                                                                        "/review/create[/]??",
                                                                        "Admin\\Review\\ReviewCreateAction",
                                                                        "/review/create/");

            $routes[ReviewRoutes::REVIEW_EDIT_ROUTE] = new AdminRoute(_("Review Edit"),
                                                                      "/review/(\\d+)/edit[/]??",
                                                                      "Admin\\Review\\ReviewEditAction",
                                                                      "/review/[reviewId]/edit",
                                                                      [ "reviewId" ]);

            $routes[ReviewRoutes::REVIEW_DELETE_ROUTE] = new AdminRoute(_("Review Delete"),
                                                                        "/review/(\\d+)/delete[/]??",
                                                                        "Admin\\Review\\ReviewDeleteAction",
                                                                        "/review/[reviewId]/delete",
                                                                        [ "reviewId" ]);

            $routes[ReviewRoutes::REVIEW_CREATE_PUBLIC_ROUTE] = new PublicRoute(_("Review Create"),
                                                                        "/review/create[/]??",
                                                                        "Admin\\Review\\ReviewCreatePublicAction",
                                                                        "/review/create/");

            /*************************************  REVIEW END  ******************************************************/

            return $routes;
        }
    }