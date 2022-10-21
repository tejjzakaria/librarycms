<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Image;

    /**
     * Class ImageDeleteAction
     * @package KAASoft\Controller\Admin\Image
     */
    class ImageDeleteAction extends ImageDeleteBaseAction {
        /**
         * ImageDeleteAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute);
            $this->setResolutions(ImageUploadBaseAction::getGeneralImageResolutions());
        }
    }