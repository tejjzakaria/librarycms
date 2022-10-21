<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment\Routes\Admin;

    use KAASoft\Environment\Routes\AdminRoute;
    use KAASoft\Environment\Routes\PublicRoute;
    use KAASoft\Environment\Routes\RoutesInterface;

    /**
     * Class ElectronicBookRoutes
     * @package KAASoft\Environment\Routes\Admin
     */
    class ElectronicBookRoutes implements RoutesInterface {

        const ELECTRONIC_BOOK_UPLOAD_ROUTE     = "electronicBookUpload";
        const ELECTRONIC_BOOK_GET_ROUTE        = "electronicBookGet";
        const ELECTRONIC_BOOK_VIEW_ROUTE       = "electronicBookView";
        const ELECTRONIC_BOOK_VIEW_ADMIN_ROUTE = "electronicBookViewAdmin";
        const ELECTRONIC_BOOK_DELETE_ROUTE     = "electronicBookDelete";
        const ELECTRONIC_BOOK_LIST_VIEW_ROUTE  = "electronicBookListView";


        public static function getRoutes() {
            $routes = [];
            /*************************************  ELECTRONIC_BOOK  **********************************************************/
            $routes[ElectronicBookRoutes::ELECTRONIC_BOOK_UPLOAD_ROUTE] = new AdminRoute(_("eBook Upload"),
                                                                                         "/electronic-book/upload[/]??",
                                                                                         "Admin\\ElectronicBook\\ElectronicBookUploadAction",
                                                                                         "/electronic-book/upload");

            $routes[ElectronicBookRoutes::ELECTRONIC_BOOK_GET_ROUTE] = new PublicRoute(_("eBook Get"),
                                                                                       "/electronic-book/(\\d+)[/]??",
                                                                                       "Admin\\ElectronicBook\\ElectronicBookGetAction",
                                                                                       "/electronic-book/[electronicBookId]",
                                                                                       [ "electronicBookId" ]);

            $routes[ElectronicBookRoutes::ELECTRONIC_BOOK_VIEW_ROUTE] = new PublicRoute(_("eBook View"),
                                                                                        "/book/(\\d+)/read[/]??",
                                                                                        "Admin\\ElectronicBook\\ElectronicBookViewAction",
                                                                                        "/book/[bookId]/read",
                                                                                        [ "bookId" ]);

            $routes[ElectronicBookRoutes::ELECTRONIC_BOOK_VIEW_ADMIN_ROUTE] = new AdminRoute(_("eBook View"),
                                                                                             "/book/(\\d+)/read[/]??",
                                                                                             "Admin\\ElectronicBook\\ElectronicBookViewAdminAction",
                                                                                             "/book/[bookId]/read",
                                                                                             [ "bookId" ]);

            $routes[ElectronicBookRoutes::ELECTRONIC_BOOK_DELETE_ROUTE] = new AdminRoute(_("eBook Delete"),
                                                                                         "/electronic-book/(\\d+)/delete[/]??",
                                                                                         "Admin\\ElectronicBook\\ElectronicBookDeleteAction",
                                                                                         "/electronic-book/[electronicBookId]/delete",
                                                                                         [ "electronicBookId" ]);


            $routes[ElectronicBookRoutes::ELECTRONIC_BOOK_LIST_VIEW_ROUTE] = new AdminRoute(_("eBook List View"),
                                                                                            "/electronic-books(?:/page/(\\d+))?[/]??",
                                                                                            "Admin\\ElectronicBook\\ElectronicBooksViewAction",
                                                                                            "/electronic-books",
                                                                                            [ "page" ]);

            /*************************************  ELECTRONIC_BOOK END  ******************************************************/
            return $routes;
        }
    }