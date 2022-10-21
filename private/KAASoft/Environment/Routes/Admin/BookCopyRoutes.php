<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment\Routes\Admin;


    use KAASoft\Environment\Routes\AdminRoute;
    use KAASoft\Environment\Routes\RoutesInterface;

    /**
     * Class BookCopyRoutes
     * @package KAASoft\Environment\Routes\Admin
     */
    class BookCopyRoutes implements RoutesInterface {
        const BOOK_COPY_CREATE_ROUTE = "bookCopyCreate";
        const BOOK_COPY_EDIT_ROUTE   = "bookCopyEdit";
        const BOOK_COPY_DELETE_ROUTE = "bookCopyDelete";
        const BOOK_SN_CHECK_ROUTE    = "bookSNCheck";

        public static function getRoutes() {
            $routes = [];


            $routes[BookCopyRoutes::BOOK_COPY_CREATE_ROUTE] = new AdminRoute(_("Book Copy Create"),
                                                                             "/book-copy/create[/]??",
                                                                             "Admin\\BookCopy\\BookCopyCreateAction",
                                                                             "/book-copy/create");

            $routes[BookCopyRoutes::BOOK_COPY_EDIT_ROUTE] = new AdminRoute(_("Book Copy Edit"),
                                                                           "/book-copy/edit/(\\d+)[/]??",
                                                                           "Admin\\BookCopy\\BookCopyEditAction",
                                                                           "/book-copy/edit/[bookCopyId]",
                                                                           [ "bookCopyId" ]);

            $routes[BookCopyRoutes::BOOK_COPY_DELETE_ROUTE] = new AdminRoute(_("Book Copy Delete"),
                                                                             "/book-copy/delete/(\\d+)[/]??",
                                                                             "Admin\\BookCopy\\BookCopyDeleteAction",
                                                                             "/book-copy/delete/[bookCopyId]",
                                                                             [ "bookCopyId" ]);

            $routes[BookCopyRoutes::BOOK_SN_CHECK_ROUTE] = new AdminRoute(_("BookSN Check"),
                                                                          "/book-copy/check-book-id[/]??",
                                                                          "Admin\\BookCopy\\CheckBookSNAction",
                                                                          "/book-copy/check-book-id");

            return $routes;
        }
    }
