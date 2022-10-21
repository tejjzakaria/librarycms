<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    /**
     * Created by KAA Soft.
     * Date: 2018-06-08
     */


    namespace KAASoft\Database\Entity\Util;


    use JsonSerializable;
    use KAASoft\Util\ValidationHelper;

    class FieldOptions implements JsonSerializable {

        private $title;
        private $isEditable;
        private $isVisible;

        /**
         * FieldOption constructor.
         * @param      $title
         * @param      $isEditable
         * @param bool $isVisible
         */
        public function __construct($title = null, $isEditable = null, $isVisible = true) {
            $this->title = $title;
            $this->isEditable = $isEditable;
            $this->isVisible = $isVisible;
        }

        /**+
         * @return array
         */
        function jsonSerialize() {
            return [ "title"      => $this->title,
                     "isEditable" => $this->isEditable,
                     "isVisible"  => $this->isVisible ];
        }

        /**
         * @param $fieldOptionsArray
         * @return FieldOptions
         */
        public static function getObjectInstance($fieldOptionsArray) {
            $fieldOptions = new FieldOptions();
            $fieldOptions->setTitle(ValidationHelper::getString($fieldOptionsArray["title"]));
            $fieldOptions->setIsEditable(ValidationHelper::getBool($fieldOptionsArray["isEditable"]));
            $fieldOptions->setIsVisible(ValidationHelper::getBool($fieldOptionsArray["isVisible"]));

            return $fieldOptions;
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
        public function isEditable() {
            return $this->isEditable;
        }

        /**
         * @param mixed $isEditable
         */
        public function setIsEditable($isEditable) {
            $this->isEditable = $isEditable;
        }

        /**
         * @return mixed
         */
        public function isVisible() {
            return $this->isVisible;
        }

        /**
         * @param mixed $isVisible
         */
        public function setIsVisible($isVisible) {
            $this->isVisible = $isVisible;
        }
    }