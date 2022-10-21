<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Database\Entity\Util;


    use JsonSerializable;
    use KAASoft\Database\Entity\DatabaseEntity;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class Language
     * @package KAASoft\Database\Entity\Util
     */
    class Language extends DatabaseEntity implements JsonSerializable {
        private $name;
        private $code;
        private $shortCode;
        private $isActive;
        private $isRTL;

        protected function __construct($id = null) {
            parent::__construct($id);
        }

        /**
         * @param array $databaseArray
         * @return Language to restore form databaseArray
         */
        public static function getObjectInstance(array $databaseArray) {
            $language = new Language(ValidationHelper::getNullableInt($databaseArray["id"]));

            $language->setActive(ValidationHelper::getBool($databaseArray["isActive"]));
            $language->setCode(ValidationHelper::getString($databaseArray["code"]));
            $language->setName(ValidationHelper::getString($databaseArray["name"]));
            $language->setShortCode(ValidationHelper::getString($databaseArray["shortCode"]));
            $language->setIsRTL(ValidationHelper::getBool($databaseArray["isRTL"]));

            return $language;
        }

        public function getDatabaseArray() {
            return array_merge(parent::getDatabaseArray(),
                               [ "code"      => $this->code,
                                 "isActive"  => $this->isActive,
                                 "name"      => $this->name,
                                 "shortCode" => $this->shortCode,
                                 "isRTL"     => $this->isRTL ]);
        }


        function jsonSerialize() {
            return $this->getDatabaseArray();
        }


        public static function getDatabaseFieldNames() {
            return [ KAASoftDatabase::$LANGUAGES_TABLE_NAME . ".code",
                     KAASoftDatabase::$LANGUAGES_TABLE_NAME . ".isActive",
                     KAASoftDatabase::$LANGUAGES_TABLE_NAME . ".name",
                     KAASoftDatabase::$LANGUAGES_TABLE_NAME . ".shortCode",
                     KAASoftDatabase::$LANGUAGES_TABLE_NAME . ".isRTL" ];
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
        public function getId() {
            return $this->id;
        }

        /**
         * @param mixed $id
         */
        public function setId($id) {
            $this->id = $id;
        }

        /**
         * @return mixed
         */
        public function isActive() {
            return $this->isActive;
        }

        /**
         * @param mixed $isActive
         */
        public function setActive($isActive) {
            $this->isActive = $isActive;
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
         * @return null
         */
        public function getShortCode() {
            return $this->shortCode;
        }

        /**
         * @param null $shortCode
         */
        public function setShortCode($shortCode) {
            $this->shortCode = $shortCode;
        }

        /**
         * @return mixed
         */
        public function isRTL() {
            return $this->isRTL;
        }

        /**
         * @param mixed $isRTL
         */
        public function setIsRTL($isRTL) {
            $this->isRTL = $isRTL;
        }
    }