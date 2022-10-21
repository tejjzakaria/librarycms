<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Image;


    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Util\DisplaySwitch;

    /**
     * Class ImageOptionsAction
     * @package KAASoft\Controller\Admin\Image
     */
    class ImageOptionsAction extends AdminActionBase {

        /**
         * ImageOptionsAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         */
        protected function action($args) {

            $imageDatabaseHelper = new ImageDatabaseHelper($this);

            $this->smarty->assign("imageResolutions",
                                  $imageDatabaseHelper->getImageResolutions());

            return new DisplaySwitch("admin/imagesOptions.tpl");
        }
    }