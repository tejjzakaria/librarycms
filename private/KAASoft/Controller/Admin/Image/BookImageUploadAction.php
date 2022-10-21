<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Image;

    use KAASoft\Controller\Controller;
    use KAASoft\Util\FileHelper;

    /**
     * Class BookImageUploadAction
     * @package KAASoft\Controller\Admin\Image
     */
    class BookImageUploadAction extends ImageUploadBaseAction {

        /**
         * BookImageUploadAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute);
            $this->setImagesLocation(FileHelper::getBookImageLocation());
            $this->setResolutions(ImageUploadBaseAction::getBookImageResolutions());

            if (isset( $_POST["imageId"] )) {
                $imageDatabaseHelper = new ImageDatabaseHelper($this);

                $this->setImageToDelete($imageDatabaseHelper->getImage($_POST["imageId"]));
                if ($imageDatabaseHelper->deleteImage($_POST["imageId"]) === false) {
                    $this->rollbackDatabaseChanges();
                    $errorMessage = _("Couldn't delete old book image.");
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                }
            }
        }
    }