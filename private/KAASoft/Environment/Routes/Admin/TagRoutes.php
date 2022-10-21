<?php
/**
 * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
 */

    /**
     * Created by KAA Soft.
     * Date: 2018-05-13
     */


    namespace KAASoft\Environment\Routes\Admin;


    use KAASoft\Environment\Routes\AdminRoute;
    use KAASoft\Environment\Routes\RoutesInterface;

    class TagRoutes  implements RoutesInterface {

        const TAG_LIST_VIEW_ROUTE = "tagListView";
        const TAG_CREATE_ROUTE    = "tagCreate";
        const TAG_EDIT_ROUTE      = "tagEdit";
        const TAG_DELETE_ROUTE    = "tagDelete";

        public static function getRoutes() {
            $routes = [];

            $routes[TagRoutes::TAG_LIST_VIEW_ROUTE] = new AdminRoute(_("Tag List View"),
                                                                         "/tags(?:/page/(\\d+))?[/]??",
                                                                         "Admin\\Tag\\TagsAction",
                                                                         "/tags",
                                                                         [ "page" ]);

            $routes[TagRoutes::TAG_CREATE_ROUTE] = new AdminRoute(_("Tag Create"),
                                                                      "/tag/create[/]??",
                                                                      "Admin\\Tag\\TagCreateAction",
                                                                  "/tag/create");

            $routes[TagRoutes::TAG_EDIT_ROUTE] = new AdminRoute(_("Tag Edit"),
                                                                    "/tag/(\\d+)/edit[/]??",
                                                                    "Admin\\Tag\\TagEditAction",
                                                                    "/tag/[tagId]/edit",
                                                                    [ "tagId" ]);

            $routes[TagRoutes::TAG_DELETE_ROUTE] = new AdminRoute(_("Tag Delete"),
                                                                      "/tag/(\\d+)/delete[/]??",
                                                                      "Admin\\Tag\\TagDeleteAction",
                                                                      "/tag/[tagId]/delete",
                                                                      [ "tagId" ]);

            return $routes;
        }
    }