<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Image;

    use KAASoft\Util\FileHelper;

    /**
     * Class PageImageUploadAction
     * @package KAASoft\Controller\Admin\Image
     */
    class PageImageUploadAction extends ImageUploadBaseAction {


        /**
         * PageImageUploadAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute);
            $this->setImagesLocation(FileHelper::getPageImagesLocation());
            $this->setResolutions(ImageUploadBaseAction::getPageImageResolutions());
            //$this->setResolutions(ImageUploadBaseAction::getAvatarResolutions());
        }
    }