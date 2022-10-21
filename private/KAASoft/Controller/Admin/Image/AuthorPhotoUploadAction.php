<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Image;

    use KAASoft\Controller\Controller;
    use KAASoft\Util\FileHelper;

    /**
     * Class AuthorPhotoUploadAction
     * @package KAASoft\Controller\Admin\Image
     */
    class AuthorPhotoUploadAction extends ImageUploadBaseAction {

        /**
         * AuthorPhotoUploadAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute);
            $this->setImagesLocation(FileHelper::getAuthorPhotoLocation());
            $this->setResolutions(ImageUploadBaseAction::getAuthorPhotoResolutions());

            if (isset( $_POST["photoId"] )) {
                $imageId = $_POST["photoId"];
                $imageDatabaseHelper = new ImageDatabaseHelper($this);

                $this->setImageToDelete($imageDatabaseHelper->getImage($imageId));
                if ($imageDatabaseHelper->deleteImage($imageId) === false) {
                    $this->rollbackDatabaseChanges();
                    $errorMessage = _("Couldn't delete author's photo.");
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                }
            }
        }
    }