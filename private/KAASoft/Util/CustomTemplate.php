<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Util;

    /**
     * Class CustomTemplate
     * @package KAASoft\Util
     */
    class CustomTemplate {
        const PAGE_GROUP = "pages";
        const POST_GROUP = "posts";

        private $group;
        private $fileName;
        private $displayName;

        public function __construct($group = null, $fileName = null, $displayName = null) {
            $this->group = $group;
            $this->fileName = $fileName;
            $this->displayName = $displayName;
        }

        public function getFullTemplateFileName() {
            return FileHelper::CUSTOM_TEMPLATE_FOLDER_NAME . DIRECTORY_SEPARATOR . $this->group . DIRECTORY_SEPARATOR . $this->fileName;
        }

        /**
         * @return null
         */
        public function getGroup() {
            return $this->group;
        }

        /**
         * @param null $group
         */
        public function setGroup($group) {
            $this->group = $group;
        }

        /**
         * @return null
         */
        public function getFileName() {
            return $this->fileName;
        }

        /**
         * @param null $fileName
         */
        public function setFileName($fileName) {
            $this->fileName = $fileName;
        }

        /**
         * @return null
         */
        public function getDisplayName() {
            return $this->displayName;
        }

        /**
         * @param null $displayName
         */
        public function setDisplayName($displayName) {
            $this->displayName = $displayName;
        }
    }