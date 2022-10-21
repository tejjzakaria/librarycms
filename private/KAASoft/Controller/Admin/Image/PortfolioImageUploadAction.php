<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Image;

    use KAASoft\Util\FileHelper;

    /**
     * Class PortfolioImageUploadAction
     * @package KAASoft\Controller\Admin\Image
     */
    class PortfolioImageUploadAction extends ImageUploadBaseAction {


        /**
         * PortfolioImageUploadAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute);
            $this->setImagesLocation(FileHelper::getPortfolioImagesLocation());
            $this->setResolutions(ImageUploadBaseAction::getPortfolioImageResolutions());
        }
    }