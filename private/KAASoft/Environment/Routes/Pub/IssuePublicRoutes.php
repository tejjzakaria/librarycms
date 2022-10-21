<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment\Routes\Pub;

    use KAASoft\Environment\Routes\PublicRoute;
    use KAASoft\Environment\Routes\RoutesInterface;

    /**
     * Class IssueRoutes
     * @package KAASoft\Environment\Routes\Admin
     */
    class IssuePublicRoutes implements RoutesInterface {

        const ISSUE_CREATE_PUBLIC_ROUTE             = "issueCreatePublic";
        const ISSUED_BOOK_SET_LOST_PUBLIC_ROUTE     = "bookSetLostPublic";
        const ISSUED_BOOK_SET_RETURNED_PUBLIC_ROUTE = "bookSetReturnedPublic";

        public static function getRoutes() {
            $routes = [];
            /*************************************  ISSUE  **********************************************************/
            $routes[IssuePublicRoutes::ISSUE_CREATE_PUBLIC_ROUTE] = new PublicRoute(_("Issue Create Public"),
                                                                                    "/issue/create[/]??",
                                                                                    "Pub\\Issue\\IssueCreatePublicAction",
                                                                                    "/issue/create/");

            $routes[IssuePublicRoutes::ISSUED_BOOK_SET_LOST_PUBLIC_ROUTE] = new PublicRoute(_("Issued Book Set Lost Public"),
                                                                                            "/issue/(\\d+)/book-lost/(false|true)[/]??",
                                                                                            "Pub\\Issue\\BookLostSetPublicAction",
                                                                                            "/issue/[issueId]/book-lost/[isLost]",
                                                                                            [ "issueId",
                                                                                              "isLost" ]);

            $routes[IssuePublicRoutes::ISSUED_BOOK_SET_RETURNED_PUBLIC_ROUTE] = new PublicRoute(_("Issued Book Set Returned Public"),
                                                                                                "/issue/(\\d+)/book-returned[/]??",
                                                                                                "Pub\\Issue\\BookReturnedSetPublicAction",
                                                                                                "/issue/[issueId]/book-returned",
                                                                                                [ "issueId" ]);

            /*************************************  ISSUE END  ******************************************************/

            return $routes;
        }
    }