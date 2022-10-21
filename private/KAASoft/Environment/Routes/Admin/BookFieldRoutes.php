<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment\Routes\Admin;

    use KAASoft\Environment\Routes\AdminRoute;
    use KAASoft\Environment\Routes\RoutesInterface;

    /**
     * Class BookFieldRoutes
     * @package KAASoft\Environment\Routes\Admin
     */
    class BookFieldRoutes implements RoutesInterface {

        const BOOK_FIELD_LIST_VIEW_ROUTE = "bookFieldListView";
        const BOOK_FIELD_CREATE_ROUTE    = "bookFieldCreate";
        const BOOK_FIELD_EDIT_ROUTE      = "bookFieldEdit";
        const BOOK_FIELD_DELETE_ROUTE    = "bookFieldDelete";
        const BOOK_FIELD_CHECK_ROUTE            = "bookFieldCheck";

        public static function getRoutes() {
            $routes = [];
            /*************************************  BOOK_FIELD  ********************************************************/
            $routes[BookFieldRoutes::BOOK_FIELD_LIST_VIEW_ROUTE] = new AdminRoute(_("Book Field List View"),
                                                                                  "/book-fields(?:/page/(\\d+))?[/]??",
                                                                                  "Admin\\BookField\\BookFieldsAction",
                                                                                  "/book-fields",
                                                                                  [ "page" ]);

            $routes[BookFieldRoutes::BOOK_FIELD_CREATE_ROUTE] = new AdminRoute(_("Book Field Create"),
                                                                               "/book-field/create[/]??",
                                                                               "Admin\\BookField\\BookFieldCreateAction",
                                                                               "/book-field/create/");

            $routes[BookFieldRoutes::BOOK_FIELD_EDIT_ROUTE] = new AdminRoute(_("Book Field Edit"),
                                                                             "/book-field/(\\d+)/edit[/]??",
                                                                             "Admin\\BookField\\BookFieldEditAction",
                                                                             "/book-field/[bookFieldId]/edit",
                                                                             [ "bookFieldId" ]);

            $routes[BookFieldRoutes::BOOK_FIELD_DELETE_ROUTE] = new AdminRoute(_("Book Field Delete"),
                                                                               "/book-field/(\\d+)/delete[/]??",
                                                                               "Admin\\BookField\\BookFieldDeleteAction",
                                                                               "/book-field/[bookFieldId]/delete",
                                                                               [ "bookFieldId" ]);

            $routes[BookFieldRoutes::BOOK_FIELD_CHECK_ROUTE] = new AdminRoute(_("Book Field Check"),
                                                                         "/book-field/check[/]??",
                                                                         "Admin\\BookField\\BookFieldNameCheckAction",
                                                                         "/book-field/check");


            /*************************************  BOOK_FIELD END  ****************************************************/

            return $routes;
        }
    }