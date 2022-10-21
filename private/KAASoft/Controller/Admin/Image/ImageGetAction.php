<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Image;


    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\FileHelper;

    /**
     * Class ImageGetAction
     * @package KAASoft\Controller\Admin\Image
     */
    class ImageGetAction extends PublicActionBase {
        /**
         * ImageGetAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute,
                                true);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         */
        protected function action($args) {
            $imageId = $args["imageId"];
            $imageType = isset( $args["imageType"] ) ? $args["imageType"] : null;
            $imageDatabaseHelper = new ImageDatabaseHelper($this);
            $image = $imageDatabaseHelper->getImage($imageId);
            if ($image !== null) {
                $imagePath = $image->getPath();
                if ($imageType !== null) {
                    $imageName = basename($imagePath);
                    $parentDir = dirname($imagePath);

                    $imagePath = $parentDir . DIRECTORY_SEPARATOR . $imageType . DIRECTORY_SEPARATOR . $imageName;
                }
                $fileName = FileHelper::getImageRootLocation() . $imagePath;
                if (file_exists($fileName)) {

                    $mimeType = FileHelper::getFileMimeType($fileName);
                    if ($mimeType !== null) {
                        header('Content-Type: ' . $mimeType);
                    }

                    header('Content-Disposition: attachment; filename="' . basename($image->getPath()) . '"');
                    readfile($fileName);
                }
                else {
                    $fileName = FileHelper::getSiteRoot() . "/assets/img/imageNotFound.png";
                    if (file_exists($fileName)) {
                        $mimeType = FileHelper::getFileMimeType($fileName);
                        if ($mimeType !== null) {
                            header('Content-Type: ' . $mimeType);
                        }
                        header('Content-Disposition: attachment; filename="' . basename($fileName) . '"');
                        readfile($fileName);
                    }

                }
            }

            return new DisplaySwitch();
        }
    }