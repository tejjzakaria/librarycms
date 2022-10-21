<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */


    namespace KAASoft\Util;

    /**
     * Class Captcha
     * @package KAASoft\Util
     */
    class Captcha {

        /**
         * @var string - captcha code
         */
        private $code;
        /**
         * @var $imageFile - file name
         */
        private $imageFile;

        function __construct($code = "", $image = null) {
            $this->code = $code;
            $this->imageFile = $image;
        }


        /**
         * @return mixed
         */
        public function getCode() {
            return $this->code;
        }

        /**
         * @param mixed $code
         */
        public function setCode($code) {
            $this->code = $code;
        }

        /**
         * @return mixed
         */
        public function getImageFile() {
            return $this->imageFile;
        }

        /**
         * @param mixed $imageFile
         */
        public function setImageFile($imageFile) {
            $this->imageFile = $imageFile;
        }


    }