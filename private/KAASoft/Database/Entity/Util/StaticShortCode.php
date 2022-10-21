<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Database\Entity\Util;


    use KAASoft\Database\Entity\DatabaseEntity;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class StaticShortCode
     * @package KAASoft\Database\Entity\Util
     */
    class StaticShortCode extends DatabaseEntity {
        private $code;
        private $value;
        private $isLongText;

        /**
         * StaticShortCode constructor.
         * @param null $id
         */
        public function __construct($id = null) {
            parent::__construct($id);
        }

        /**
         * @return array
         */
        public function getDatabaseArray() {
            return array_merge(parent::getDatabaseArray(),
                               [ 'code'       => $this->code,
                                 'value'      => $this->value,
                                 'isLongText' => $this->isLongText ]);
        }

        /**
         * @param array $databaseArray
         * @return StaticShortCode to restore form databaseArray
         */
        public static function getObjectInstance(array $databaseArray) {
            $test = new StaticShortCode(ValidationHelper::getNullableInt($databaseArray["id"]));
            $test->setCode(ValidationHelper::getString($databaseArray['code']));
            $test->setValue(ValidationHelper::getString($databaseArray['value']));
            $test->setIsLongText(ValidationHelper::getNullableInt($databaseArray['isLongText']));

            return $test;
        }

        /**
         * @return array
         */
        public static function getDatabaseFieldNames() {
            return array_merge(parent::getDatabaseFieldNames(),
                               [ KAASoftDatabase::$STATIC_SHORT_CODES_TABLE_NAME . ".code",
                                 KAASoftDatabase::$STATIC_SHORT_CODES_TABLE_NAME . ".value",
                                 KAASoftDatabase::$STATIC_SHORT_CODES_TABLE_NAME . ".isLongText" ]);
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
        public function getValue() {
            return $this->value;
        }

        /**
         * @param mixed $value
         */
        public function setValue($value) {
            $this->value = $value;
        }

        /**
         * @return mixed
         */
        public function isLongText() {
            return $this->isLongText;
        }

        /**
         * @param mixed $isLongText
         */
        public function setIsLongText($isLongText) {
            $this->isLongText = $isLongText;
        }


    }