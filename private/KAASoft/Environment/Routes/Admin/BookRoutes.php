<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */


    namespace KAASoft\Environment\Routes\Admin;

    use KAASoft\Environment\Routes\AdminRoute;
    use KAASoft\Environment\Routes\PublicRoute;
    use KAASoft\Environment\Routes\RoutesInterface;

    /**
     * Class BookRoutes
     * @package KAASoft\Environment\Routes\Admin
     */
    class BookRoutes implements RoutesInterface {

        const BOOK_LIST_VIEW_ROUTE                 = "bookListView";
        const BOOK_CREATE_ROUTE                    = "bookCreate";
        const BOOK_EDIT_ROUTE                      = "bookEdit";
        const BOOK_VIEW_ROUTE                      = "bookView";
        const BOOK_DELETE_ROUTE                    = "bookDelete";
        const BOOK_BULK_DELETE_ROUTE               = "bookBulkDelete";
        const BOOK_BULK_EDIT_ROUTE                 = "bookBulkEdit";
        const BOOK_CLONE_ROUTE                     = "bookClone";
        const BOOK_SEARCH_ROUTE                    = "bookSearch";
        const USER_BOOKS_VIEW_ROUTE                = "userBooksView";
        const USER_BOOKS_ADMIN_ROUTE               = "userBooksViewAdmin";
        const STORE_BOOKS_VIEW_ROUTE               = "storeBooksView";
        const LOCATION_BOOKS_VIEW_ROUTE            = "locationBooksView";
        const BOOK_BULK_BAR_CODE_GENERATE_ROUTE    = "bookBulkBarCodeGenerate";
        const BOOK_RATING_SET_ROUTE                = "bookRatingSet";
        const BOOK_URL_GENERATE_ROUTE              = "bookUrlGenerate";
        const BOOK_URL_CHECK_ROUTE                 = "bookUrlCheck";
        const BOOK_LAYOUT_ROUTE                    = "bookLayout";
        const BOOK_VISIBLE_FIELDS_FOR_PUBLIC_ROUTE = "bookVisibleFieldsForPublic";


        public static function getRoutes() {
            $routes = [];
            /*************************************  BOOK  **********************************************************/
            $routes[BookRoutes::BOOK_LIST_VIEW_ROUTE] = new AdminRoute(_("Books List View"),
                                                                       "/books(?:/page/(\\d+))?[/]??",
                                                                       "Admin\\Book\\BooksAction",
                                                                       "/books",
                                                                       [ "page" ]);

            $routes[BookRoutes::BOOK_CREATE_ROUTE] = new AdminRoute(_("Book Create"),
                                                                    "/book/create[/]??",
                                                                    "Admin\\Book\\BookCreateAction",
                                                                    "/book/create/");

            $routes[BookRoutes::BOOK_EDIT_ROUTE] = new AdminRoute(_("Book Edit"),
                                                                  "/book/(\\d+)/edit[/]??",
                                                                  "Admin\\Book\\BookEditAction",
                                                                  "/book/[bookId]/edit",
                                                                  [ "bookId" ]);

            $routes[BookRoutes::BOOK_VIEW_ROUTE] = new AdminRoute(_("Book View"),
                                                                  "/book/(\\d+)/view[/]??",
                                                                  "Admin\\Book\\BookViewAction",
                                                                  "/book/[bookId]/view",
                                                                  [ "bookId" ]);

            $routes[BookRoutes::BOOK_CLONE_ROUTE] = new AdminRoute(_("Book Clone"),
                                                                   "/book/(\\d+)/clone[/]??",
                                                                   "Admin\\Book\\BookCloneAction",
                                                                   "/book/[bookId]/clone",
                                                                   [ "bookId" ]);


            $routes[BookRoutes::BOOK_DELETE_ROUTE] = new AdminRoute(_("Book Delete"),
                                                                    "/book/(\\d+)/delete[/]??",
                                                                    "Admin\\Book\\BookDeleteAction",
                                                                    "/book/[bookId]/delete",
                                                                    [ "bookId" ]);

            $routes[BookRoutes::BOOK_BULK_DELETE_ROUTE] = new AdminRoute(_("Book Bulk Delete"),
                                                                         "/books/delete[/]??",
                                                                         "Admin\\Book\\BookBulkDeleteAction",
                                                                         "/books/delete");

            $routes[BookRoutes::BOOK_BULK_EDIT_ROUTE] = new AdminRoute(_("Book Bulk Edit"),
                                                                       "/books/edit[/]??",
                                                                       "Admin\\Book\\BookBulkEditAction",
                                                                       "/books/edit");

            $routes[BookRoutes::BOOK_SEARCH_ROUTE] = new PublicRoute(_("Book Search"),
                                                                     "/books/search(?:/page/(\\d+))?[/]??",
                                                                     "Admin\\Book\\BookSearchAction",
                                                                     "/books/search",
                                                                     [ "page" ]);

            $routes[BookRoutes::USER_BOOKS_VIEW_ROUTE] = new PublicRoute(_("User Books View"),
                                                                         "/user/books(?:/page/(\\d+))?[/]??",
                                                                         "Admin\\Book\\UserBooksViewAction",
                                                                         "/user/books",
                                                                         [ "page" ]);

            $routes[BookRoutes::USER_BOOKS_ADMIN_ROUTE] = new AdminRoute(_("User Books Admin"),
                                                                        "/user/(\\d+)/books(?:/page/(\\d+))?[/]??",
                                                                        "Admin\\Book\\UserBooksAction",
                                                                        "/user/[userId]/books",
                                                                        [ "userId",
                                                                          "page" ]);

            $routes[BookRoutes::STORE_BOOKS_VIEW_ROUTE] = new AdminRoute(_("Store Books View"),
                                                                         "/store/(\\d+)/books(?:/page/(\\d+))?[/]??",
                                                                         "Admin\\Book\\StoreBooksViewAction",
                                                                         "/store/[storeId]/books",
                                                                         [ "storeId",
                                                                           "page" ]);

            $routes[BookRoutes::LOCATION_BOOKS_VIEW_ROUTE] = new AdminRoute(_("Location Books View"),
                                                                            "/location/(\\d+)/books(?:/page/(\\d+))?[/]??",
                                                                            "Admin\\Book\\LocationBooksViewAction",
                                                                            "/location/[locationId]/books",
                                                                            [ "locationId",
                                                                              "page" ]);

            $routes[BookRoutes::BOOK_BULK_BAR_CODE_GENERATE_ROUTE] = new AdminRoute(_("Book Bulk Bar Code Generate"),
                                                                                    "/books/barcode-generate[/]??",
                                                                                    "Admin\\Book\\BookBulkBarCodeGenerateAction",
                                                                                    "/books/barcode-generate");

            $routes[BookRoutes::BOOK_RATING_SET_ROUTE] = new PublicRoute(_("Book Rating Set"),
                                                                         "/book/(\\d+)/set-rating/(1|2|3|4|5)[/]??",
                                                                         "Admin\\Book\\BookRatingSetAction",
                                                                         "/book/[bookId]/set-rating/[rating]",
                                                                         [ "bookId",
                                                                           "rating" ]);

            $routes[BookRoutes::BOOK_URL_GENERATE_ROUTE] = new AdminRoute(_("Book Url Generate"),
                                                                          "/book/generate-url[/]??",
                                                                          "Admin\\Book\\BookUrlGenerateAction",
                                                                          "/book/generate-url");

            $routes[BookRoutes::BOOK_URL_CHECK_ROUTE] = new PublicRoute(_("Book Url Check"),
                                                                        "/book/check-url[/]??",
                                                                        "Admin\\Book\\BookCheckUrlAction",
                                                                        "/book/check-url");

            $routes[BookRoutes::BOOK_LAYOUT_ROUTE] = new AdminRoute(_("Book Layout"),
                                                                    "/book/layout[/]??",
                                                                    "Admin\\Book\\BookLayoutAction",
                                                                    "/book/layout");

            $routes[BookRoutes::BOOK_VISIBLE_FIELDS_FOR_PUBLIC_ROUTE] = new AdminRoute(_("Book Visible Fields For Public"),
                                                                                       "/book/visible-fields[/]??",
                                                                                       "Admin\\Book\\BookVisibleFieldsAction",
                                                                                       "/book/visible-fields");

            /*************************************  BOOK END  ******************************************************/

            return $routes;
        }
    }