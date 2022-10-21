<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Image;

    use KAASoft\Util\FileHelper;

    /**
     * Class PostImageUploadAction
     * @package KAASoft\Controller\Admin\Image
     */
    class PostImageUploadAction extends ImageUploadBaseAction {


        /**
         * PostImageUploadAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute);
            $this->setImagesLocation(FileHelper::getPostImagesLocation());
            $this->setResolutions(ImageUploadBaseAction::getPostImageResolutions());
        }
    }