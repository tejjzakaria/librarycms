<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment\Routes\Admin;


    use KAASoft\Environment\Routes\AdminRoute;
    use KAASoft\Environment\Routes\RoutesInterface;

    class PostRoutes implements RoutesInterface {
        const POST_LIST_VIEW_ROUTE = "postListView";
        const POST_CREATE_ROUTE    = "postCreate";
        const POST_EDIT_ROUTE      = "postEdit";
        const POST_DELETE_ROUTE    = "postDelete";

        public static function getRoutes() {
            $routes = [];

            $routes[PostRoutes::POST_LIST_VIEW_ROUTE] = new AdminRoute(_("Post List View"),
                                                                       "/posts(?:/page/(\\d+))?[/]??",
                                                                       "Admin\\Post\\PostsAction",
                                                                       "/posts",
                                                                       [ "page" ]);

            $routes[PostRoutes::POST_CREATE_ROUTE] = new AdminRoute(_("Post Create"),
                                                                    "/post/create[/]??",
                                                                    "Admin\\Post\\PostCreateAction",
                                                                    "/post/create");

            $routes[PostRoutes::POST_EDIT_ROUTE] = new AdminRoute(_("Post Edit"),
                                                                  "/post/edit/(\\d+)[/]??",
                                                                  "Admin\\Post\\PostEditAction",
                                                                  "/post/edit/[postId]",
                                                                  [ "postId" ]);

            $routes[PostRoutes::POST_DELETE_ROUTE] = new AdminRoute(_("Post Delete"),
                                                                    "/post/delete/(\\d+)[/]??",
                                                                    "Admin\\Post\\PostDeleteAction",
                                                                    "/post/delete/[postId]",
                                                                    [ "postId" ]);

            return $routes;
        }
    }
