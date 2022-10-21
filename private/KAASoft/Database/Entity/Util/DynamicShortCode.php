<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Database\Entity\Util;

    use KAASoft\Database\Entity\DatabaseEntity;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class DynamicShortCode
     * @package KAASoft\Database\Entity\Util
     */
    class DynamicShortCode extends DatabaseEntity {
        private $code;
        private $columnName;

        /**
         * DynamicShortCode constructor.
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
                                 'columnName' => $this->columnName ]);
        }

        /**
         * @param array $databaseArray
         * @return DynamicShortCode to restore form databaseArray
         */
        public static function getObjectInstance(array $databaseArray) {
            $test = new DynamicShortCode(ValidationHelper::getNullableInt($databaseArray["id"]));
            $test->setCode(ValidationHelper::getString($databaseArray['code']));
            $test->setColumnName(ValidationHelper::getString($databaseArray['columnName']));

            return $test;
        }

        /**
         * @return array
         */
        public static function getDatabaseFieldNames() {
            return array_merge(parent::getDatabaseFieldNames(),
                               [ KAASoftDatabase::$DYNAMIC_SHORT_CODES_TABLE_NAME . ".code",
                                 KAASoftDatabase::$DYNAMIC_SHORT_CODES_TABLE_NAME . ".columnName" ]);
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
        public function getColumnName() {
            return $this->columnName;
        }

        /**
         * @param mixed $columnName
         */
        public function setColumnName($columnName) {
            $this->columnName = $columnName;
        }
    }