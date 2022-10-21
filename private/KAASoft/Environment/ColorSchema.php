<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment;

    use JsonSerializable;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class ColorSchema
     * @package KAASoft\Environment
     */
    class ColorSchema implements JsonSerializable {
        private $title;
        private $isDark;

        /**
         * ColorSchema constructor.
         */
        public function __construct() {
        }


        public function getDatabaseArray() {
            return [ "title"   => $this->title,
                     "isDark" => $this->isDark ];
        }

        public function copySettings($settings) {
            $this->setTitle(ValidationHelper::getString($settings["title"]));
            $this->setIsDark(ValidationHelper::getBool($settings["isDark"]));
        }

        /**
         * @return array
         */
        function jsonSerialize() {
            return $this->getDatabaseArray();
        }

        /**
         * @return mixed
         */
        public function getTitle() {
            return $this->title;
        }

        /**
         * @param mixed $title
         */
        public function setTitle($title) {
            $this->title = $title;
        }


        /**
         * @return mixed
         */
        public function isDark() {
            return $this->isDark;
        }

        /**
         * @param mixed $isDark
         */
        public function setIsDark($isDark) {
            $this->isDark = $isDark;
        }
    }