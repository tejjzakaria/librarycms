<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Image;

    use KAASoft\Controller\Controller;
    use KAASoft\Util\FileHelper;

    /**
     * Class CoverUploadAction
     * @package KAASoft\Controller\Admin\Image
     */
    class CoverUploadAction extends ImageUploadBaseAction {

        /**
         * CoverUploadAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute);
            $this->setImagesLocation(FileHelper::getCoverLocation());
            $this->setResolutions(ImageUploadBaseAction::getCoverResolutions());

            if (isset( $_POST["coverId"] )) {
                $imageDatabaseHelper = new ImageDatabaseHelper($this);

                $this->setImageToDelete($imageDatabaseHelper->getImage($_POST["coverId"]));
                if ($imageDatabaseHelper->deleteImage($_POST["coverId"]) === false) {
                    $this->rollbackDatabaseChanges();
                    $errorMessage = _("Couldn't delete old Cover.");
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                }
            }
        }
    }