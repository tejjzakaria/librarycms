<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment;

    use KAASoft\Database\Entity\General\Book;
    use KAASoft\Database\Entity\Util\FieldOptions;
    use KAASoft\Util\FileHelper;

    /**
     * Class BookFieldSettings
     * @package KAASoft\Environment
     */
    class BookFieldSettings extends AbstractSettings {
        const BookFieldSettingsFileNameJSON = '/KAASoft/Config/BookFieldSettings.json';

        const ROOT_SETTINGS_ELEMENT = "bookFields";

        /**
         * @var array
         */
        private $bookFields;

        /**
         * copy data from assoc array to object fields
         * @param $settings BookLayoutSettings
         */
        public function copySettings($settings) {
            $this->setDefaultSettings();

            $bookFields = $settings[BookFieldSettings::ROOT_SETTINGS_ELEMENT];

            foreach ($bookFields as $bookFieldName => $bookFieldOptions) {
                if (array_key_exists($bookFieldName,
                                     $this->bookFields)) {
                    $fieldOptions = FieldOptions::getObjectInstance($bookFieldOptions);
                    $this->addBookField($bookFieldName,
                                        $fieldOptions);
                }
            }
        }

        /**
         * @param $name
         * @return mixed|null
         */
        public function getBookFieldOptions($name) {
            foreach ($this->bookFields as $bookFieldName => $bookFieldOptions) {
                if (strcmp($bookFieldName,
                           $name) === 0
                ) {
                    return $bookFieldOptions;
                }
            }

            return null;
        }

        /**
         * copy data from another Settings object
         * @param $settings BookFieldSettings
         */
        public function cloneSettings($settings) {
            $this->setBookFields($settings->getBookFields());
        }

        /**
         * Returns config file to load/store settings
         * @return string
         */
        public function getConfigFileName() {
            return realpath(FileHelper::getPrivateFolderLocation()) . BookFieldSettings::BookFieldSettingsFileNameJSON;
        }

        /**
         * Sets default settings
         */
        public function setDefaultSettings() {
            $this->setBookFields(Book::getVisibleFieldListPublic());
        }

        /**
         * @return array
         */
        function jsonSerialize() {
            return [ BookFieldSettings::ROOT_SETTINGS_ELEMENT => $this->bookFields ];
        }

        /**
         * @return array
         */
        public function getBookFields() {
            return $this->bookFields;
        }

        /**
         * @param array $bookFields
         */
        public function setBookFields($bookFields) {
            $this->bookFields = $bookFields;
        }

        /**
         * @param $name
         * @param $fieldOptions FieldOptions
         */
        public function addBookField($name, $fieldOptions) {
            if ($this->bookFields === null) {
                $this->bookFields = [];
            }
            $this->bookFields[$name] = $fieldOptions;
        }
    }