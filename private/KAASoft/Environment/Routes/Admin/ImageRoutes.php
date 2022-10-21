<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment\Routes\Admin;

    use KAASoft\Environment\Routes\AdminRoute;
    use KAASoft\Environment\Routes\PublicRoute;
    use KAASoft\Environment\Routes\RoutesInterface;

    /**
     * Class ImageRoutes
     * @package KAASoft\Environment\Routes\Admin
     */
    class ImageRoutes implements RoutesInterface {

        const USER_AVATAR_UPLOAD_ROUTE  = "avatarUpload";
        const BOOK_COVER_UPLOAD_ROUTE   = "coverUpload";
        const BOOK_IMAGE_UPLOAD_ROUTE   = "bookImageUpload";
        const AUTHOR_PHOTO_UPLOAD_ROUTE = "authorPhotoUpload";
        const POST_IMAGE_UPLOAD_ROUTE   = "postImageUpload";
        const PAGE_IMAGE_UPLOAD_ROUTE   = "pageImageUpload";

        const IMAGE_GET_ROUTE           = "imageGet";
        const IMAGE_DELETE_ROUTE        = "imageDelete";
        const IMAGE_DELETE_PUBLIC_ROUTE = "imageDeletePublic";
        const IMAGE_LIST_VIEW_ROUTE     = "imageListView";
        const IMAGE_OPTIONS_VIEW_ROUTE  = "imageOptionsView";

        const IMAGE_RESOLUTION_CREATE_ROUTE = "imageResolutionCreate";
        const IMAGE_RESOLUTION_EDIT_ROUTE   = "imageResolutionEdit";
        const IMAGE_RESOLUTION_DELETE_ROUTE = "imageResolutionDelete";


        public static function getRoutes() {
            $routes = [];
            /*************************************  IMAGE  **********************************************************/
            $routes[ImageRoutes::USER_AVATAR_UPLOAD_ROUTE] = new AdminRoute(_("Avatar Upload"),
                                                                            "/avatar/upload[/]??",
                                                                            "Admin\\Image\\AvatarUploadAction",
                                                                            "/avatar/upload");

            $routes[ImageRoutes::BOOK_COVER_UPLOAD_ROUTE] = new AdminRoute(_("Cover Upload"),
                                                                           "/cover/upload[/]??",
                                                                           "Admin\\Image\\CoverUploadAction",
                                                                           "/cover/upload");

            $routes[ImageRoutes::BOOK_IMAGE_UPLOAD_ROUTE] = new AdminRoute(_("Book Image Upload"),
                                                                           "/book-image/upload[/]??",
                                                                           "Admin\\Image\\BookImageUploadAction",
                                                                           "/book-image/upload");

            $routes[ImageRoutes::AUTHOR_PHOTO_UPLOAD_ROUTE] = new AdminRoute(_("Author Photo Upload"),
                                                                             "/author-photo/upload[/]??",
                                                                             "Admin\\Image\\AuthorPhotoUploadAction",
                                                                             "/author-photo/upload");

            $routes[ImageRoutes::POST_IMAGE_UPLOAD_ROUTE] = new AdminRoute(_("Post Image Upload"),
                                                                           "/post-image/upload[/]??",
                                                                           "Admin\\Image\\PostImageUploadAction",
                                                                           "/post-image/upload");

            $routes[ImageRoutes::PAGE_IMAGE_UPLOAD_ROUTE] = new AdminRoute(_("Page Image Upload"),
                                                                           "/page-image/upload[/]??",
                                                                           "Admin\\Image\\PageImageUploadAction",
                                                                           "/page-image/upload");

            $routes[ImageRoutes::IMAGE_GET_ROUTE] = new PublicRoute(_("Image Get"),
                                                                    "/image/(\\d+)(?:/(small))?[/]??",
                                                                    "Admin\\Image\\ImageGetAction",
                                                                    "/image/[imageId]/[imageType]",
                                                                    [ "imageId",
                                                                      "imageType" ]);

            $routes[ImageRoutes::IMAGE_DELETE_ROUTE] = new AdminRoute(_("Image Delete"),
                                                                      "/image/(\\d+)/delete[/]??",
                                                                      "Admin\\Image\\ImageDeleteAction",
                                                                      "/image/[imageId]/delete",
                                                                      [ "imageId" ]);

            $routes[ImageRoutes::IMAGE_DELETE_PUBLIC_ROUTE] = new PublicRoute(_("Image Delete Public"),
                                                                              "/image/(\\d+)/delete[/]??",
                                                                              "Admin\\Image\\ImageDeleteAction",
                                                                              "/image/[imageId]/delete",
                                                                              [ "imageId" ]);

            $routes[ImageRoutes::IMAGE_LIST_VIEW_ROUTE] = new AdminRoute(_("Image List View"),
                                                                         "/images(?:/page/(\\d+))?[/]??",
                                                                         "Admin\\Image\\ImagesViewAction",
                                                                         "/images",
                                                                         [ "page" ]);

            $routes[ImageRoutes::IMAGE_OPTIONS_VIEW_ROUTE] = new AdminRoute(_("Image Options View"),
                                                                            "/image/options[/]??",
                                                                            "Admin\\Image\\ImageOptionsAction",
                                                                            "/image/options");

            $routes[ImageRoutes::IMAGE_RESOLUTION_CREATE_ROUTE] = new AdminRoute(_("Image Resolution Create"),
                                                                                 "/image/resolution/create[/]??",
                                                                                 "Admin\\Image\\ImageResolutionCreateAction",
                                                                                 "/image/resolution/create");

            $routes[ImageRoutes::IMAGE_RESOLUTION_EDIT_ROUTE] = new AdminRoute(_("Image Resolution Edit"),
                                                                               "/image/resolution/(\\d+)/edit[/]??",
                                                                               "Admin\\Image\\ImageResolutionEditAction",
                                                                               "/image/resolution/[resolutionId]/edit",
                                                                               [ "resolutionId" ]);

            $routes[ImageRoutes::IMAGE_RESOLUTION_DELETE_ROUTE] = new AdminRoute(_("Image Resolution Delete"),
                                                                                 "/image/resolution/(\\d+)/delete[/]??",
                                                                                 "Admin\\Image\\ImageResolutionDeleteAction",
                                                                                 "/image/resolution/[resolutionId]/delete",
                                                                                 [ "resolutionId" ]);

            /*************************************  IMAGE END  ******************************************************/
            return $routes;
        }
    }