<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment\Routes\Admin;

    use KAASoft\Environment\Routes\AdminRoute;
    use KAASoft\Environment\Routes\PublicRoute;
    use KAASoft\Environment\Routes\RoutesInterface;

    /**
     * Class UserRoutes
     * @package KAASoft\Environment\Routes\Admin
     */
    class UserRoutes implements RoutesInterface {

        const USER_LIST_VIEW_ROUTE      = "userListView";
        const ROLE_USER_LIST_VIEW_ROUTE = "roleUserListView";
        const USER_CREATE_ROUTE         = "userCreate";
        const USER_EDIT_ROUTE           = "userEdit";
        const USER_DELETE_ROUTE         = "userDelete";
        const USER_EMAIL_CHECK_ROUTE    = "userEmailCheck";
        const USER_PHOTO_UPLOAD_ROUTE   = "userPhotoUpload";
        const USER_SEARCH_ROUTE         = "userSearch";

        public static function getRoutes() {
            $routes = [];
            /*************************************  USER  **********************************************************/
            $routes[UserRoutes::USER_LIST_VIEW_ROUTE] = new AdminRoute(_("User List View"),
                                                                       "/users(?:/page/(\\d+))?[/]??",
                                                                       "Admin\\User\\UsersAction",
                                                                       "/users",
                                                                       [ "page" ]);

            $routes[UserRoutes::ROLE_USER_LIST_VIEW_ROUTE] = new AdminRoute(_("Role User List View"),
                                                                            "/role/(\\d+)/users(?:/page/(\\d+))?[/]??",
                                                                            "Admin\\User\\RoleUsersAction",
                                                                            "/role/[roleId]/users",
                                                                            [ "roleId",
                                                                              "page" ]);

            $routes[UserRoutes::USER_CREATE_ROUTE] = new AdminRoute(_("User Create"),
                                                                    "/user/create[/]??",
                                                                    "Admin\\User\\UserCreateAction",
                                                                    "/user/create/");

            $routes[UserRoutes::USER_EDIT_ROUTE] = new AdminRoute(_("User Edit"),
                                                                  "/user/(\\d+)/edit[/]??",
                                                                  "Admin\\User\\UserEditAction",
                                                                  "/user/[userId]/edit",
                                                                  [ "userId" ]);


            $routes[UserRoutes::USER_DELETE_ROUTE] = new AdminRoute(_("User Delete"),
                                                                    "/user/(\\d+)/delete[/]??",
                                                                    "Admin\\User\\UserDeleteAction",
                                                                    "/user/[userId]/delete",
                                                                    [ "userId" ]);

            $routes[UserRoutes::USER_EMAIL_CHECK_ROUTE] = new PublicRoute(_("User Email Check"),
                                                                          "/user/check-email[/]??",
                                                                          "Admin\\User\\UserCheckEmailAction",
                                                                          "/user/check-email");

            $routes[UserRoutes::USER_PHOTO_UPLOAD_ROUTE] = new PublicRoute(_("User Photo Upload"),
                                                                           "/user/photo-upload/??",
                                                                           "Admin\\Image\\UserPhotoUploadAction",
                                                                           "/user/photo-upload");

            $routes[UserRoutes::USER_SEARCH_ROUTE] = new AdminRoute(_("User Search"),
                                                                    "/user/search[/]??",
                                                                    "Admin\\User\\UserSearchAction",
                                                                    "/user/search");

            /*************************************  USER END  ******************************************************/

            return $routes;
        }
    }