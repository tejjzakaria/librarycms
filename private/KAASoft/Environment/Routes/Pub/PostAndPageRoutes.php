<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment\Routes\Pub;


    use KAASoft\Environment\Routes\PostRoute;
    use KAASoft\Environment\Routes\PublicRoute;
    use KAASoft\Environment\Routes\RoutesInterface;

    class PostAndPageRoutes implements RoutesInterface {
        const POST_LIST_VIEW_PUBLIC_ROUTE       = "postListViewPublic";
        const POST_LIST_BY_CATEGORY_VIEW_ROUTE  = "postListByCategoryViewPublic";
        const POST_SEARCH_PUBLIC_ROUTE          = "postSearchPublic";
        const PAGE_SEARCH_PUBLIC_ROUTE          = "pageSearchPublic";
        const POST_AND_PAGE_SEARCH_PUBLIC_ROUTE = "postAndPageSearchPublic";
        const POST_VIEW_PUBLIC_ROUTE            = "postViewPublic";
        const PAGE_VIEW_PUBLIC_ROUTE            = "pageViewPublic";

        public static function getRoutes() {
            $routes = [];

            $routes[PostAndPageRoutes::POST_LIST_VIEW_PUBLIC_ROUTE] = new PostRoute(_("Post List View Public"),
                                                                                    "(?:/page/(\\d+))?[/]??",
                                                                                    "Pub\\Post\\PostsViewAction",
                                                                                    "",
                                                                                    [ "page" ]);

            $routes[PostAndPageRoutes::POST_LIST_BY_CATEGORY_VIEW_ROUTE] = new PostRoute(_("Post List View By Category"),
                                                                                         "/category/([^/!*\"()\[\];:@&=+$,?#]*)(?:/page/(\\d+))?[/]??",
                                                                                         "Pub\\Post\\PostCategoryViewAction",
                                                                                         "/category/[categoryUrl]",
                                                                                         [ "categoryUrl",
                                                                                           "page" ]);

            $routes[PostAndPageRoutes::POST_SEARCH_PUBLIC_ROUTE] = new PublicRoute(_("Post Search Public"),
                                                                                   "/post/search[/]??",
                                                                                   "Pub\\Post\\PostSearchAction",
                                                                                   "/post/search");


            $routes[PostAndPageRoutes::POST_AND_PAGE_SEARCH_PUBLIC_ROUTE] = new PublicRoute(_("Posts And Pages Search Public"),
                                                                                            "/page-post/search[/]??",
                                                                                            "Pub\\PageAndPostSearchAction",
                                                                                            "/page-post/search");

            $routes[PostAndPageRoutes::PAGE_SEARCH_PUBLIC_ROUTE] = new PublicRoute(_("Pages Search Public"),
                                                                                   "/page/search[/]??",
                                                                                   "Pub\\Page\\PageSearchAction",
                                                                                   "/page/search");

            // This route should be before last in this list because has too hungry regular expression
            $routes[PostAndPageRoutes::POST_VIEW_PUBLIC_ROUTE] = new PostRoute(_("Post View Public"),
                                                                               "/([^/!*'\"()\[\];:@&=+$,?#]*)[/]??",
                                                                               "Pub\\Post\\PostViewAction",
                                                                               "/[postUrl]",
                                                                               [ "postUrl" ]);

            $routes[BookPublicRoutes::BOOK_VIEW_VIA_URL_PUBLIC_ROUTE] = new PublicRoute(_("Book View Public via URL"),
                                                                                        "/book/([^/!*'\"()\[\];:@&=+$,?#]*)[/]??",
                                                                                        "Pub\\Book\\BookPublicViewViaUrlAction",
                                                                                        "/book/[bookUrl]",
                                                                                        ["bookUrl"]);

            // This route should be last in this list because has too hungry regular expression
            $routes[PostAndPageRoutes::PAGE_VIEW_PUBLIC_ROUTE] = new PublicRoute(_("Page View Public"),
                                                                                 "/([^!*'\"()\[\];:@&=+$,?#]*)[/]??",
                                                                                 "Pub\\Page\\PageViewAction",
                                                                                 "/[pageUrl]",
                                                                                 [ "pageUrl" ]);

            return $routes;
        }
    }
