<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Database\Entity\Util;


    use KAASoft\Database\Entity\DatabaseEntity;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class ImageResolution
     * @package KAASoft\Database\Entity\Util
     */
    class ImageResolution extends DatabaseEntity {

        private $name;
        private $width;
        private $height;


        public function __construct($id = null) {
            parent::__construct($id);
        }

        /**
         * @return array
         */
        public function getDatabaseArray() {
            return array_merge(parent::getDatabaseArray(),
                               [ 'name'   => $this->name,
                                 'width'  => $this->width,
                                 'height' => $this->height ]);
        }

        /**
         * @param array $databaseArray
         * @return ImageResolution to restore form databaseArray
         */
        public static function getObjectInstance(array $databaseArray) {
            $image = new ImageResolution(ValidationHelper::getNullableInt($databaseArray["id"]));
            $image->setName(ValidationHelper::getString($databaseArray['name']));
            $image->setWidth(ValidationHelper::getNullableInt($databaseArray['width']));
            $image->setHeight(ValidationHelper::getNullableInt($databaseArray['height']));

            return $image;
        }

        /**
         * @return array
         */
        public static function getDatabaseFieldNames() {
            return [ KAASoftDatabase::$IMAGE_RESOLUTIONS_TABLE_NAME . ".name",
                     KAASoftDatabase::$IMAGE_RESOLUTIONS_TABLE_NAME . ".width",
                     KAASoftDatabase::$IMAGE_RESOLUTIONS_TABLE_NAME . ".height" ];
        }

        /**
         * @return mixed
         */
        public function getName() {
            return $this->name;
        }

        /**
         * @param mixed $name
         */
        public function setName($name) {
            $this->name = $name;
        }

        /**
         * @return mixed
         */
        public function getWidth() {
            return $this->width;
        }

        /**
         * @param mixed $width
         */
        public function setWidth($width) {
            $this->width = $width;
        }

        /**
         * @return mixed
         */
        public function getHeight() {
            return $this->height;
        }

        /**
         * @param mixed $height
         */
        public function setHeight($height) {
            $this->height = $height;
        }
    }