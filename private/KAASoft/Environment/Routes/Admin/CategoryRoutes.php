<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment\Routes\Admin;


    use KAASoft\Environment\Routes\AdminRoute;
    use KAASoft\Environment\Routes\RoutesInterface;

    class CategoryRoutes implements RoutesInterface {
        const CATEGORY_LIST_VIEW_ROUTE = "categoryListView";
        const CATEGORY_CREATE_ROUTE    = "categoryCreate";
        const CATEGORY_EDIT_ROUTE      = "categoryEdit";
        const CATEGORY_DELETE_ROUTE    = "categoryDelete";

        public static function getRoutes() {
            $routes = [];

            $routes[CategoryRoutes::CATEGORY_LIST_VIEW_ROUTE] = new AdminRoute(_("Category List View"),
                                                                               "/categories[/]??",
                                                                               "Admin\\Category\\CategoriesAction",
                                                                               "/categories");

            $routes[CategoryRoutes::CATEGORY_CREATE_ROUTE] = new AdminRoute(_("Category Create"),
                                                                            "/category/create[/]??",
                                                                            "Admin\\Category\\CategoryCreateAction",
                                                                            "/category/create");

            $routes[CategoryRoutes::CATEGORY_EDIT_ROUTE] = new AdminRoute(_("Category Edit"),
                                                                          "/category/edit/(\\d+)[/]??",
                                                                          "Admin\\Category\\CategoryEditAction",
                                                                          "/category/edit/[categoryId]",
                                                                          [ "categoryId" ]);

            $routes[CategoryRoutes::CATEGORY_DELETE_ROUTE] = new AdminRoute(_("Category Delete"),
                                                                            "/category/delete/(\\d+)[/]??",
                                                                            "Admin\\Category\\CategoryDeleteAction",
                                                                            "/category/delete/[categoryId]",
                                                                            [ "categoryId" ]);

            return $routes;
        }
    }
