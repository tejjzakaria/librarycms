<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Database\Entity\General;

    use KAASoft\Database\Entity\DatabaseEntity;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class ListValue
     * @package KAASoft\Util\Database
     */
    class ListValue extends DatabaseEntity {

        private $value;
        private $fieldId;

        /**
         * ListValue constructor.
         * @param null $id
         */
        public function __construct($id = null) {
            parent::__construct($id);
        }

        /**
         * @return array
         */
        public function getDatabaseArray() {
            return [ "value"    => $this->value,
                     "fieldId" => $this->fieldId ];
        }

        /**
         * @param array $databaseArray
         * @return ListValue to restore form databaseArray
         */
        public static function getObjectInstance(array $databaseArray) {
            $listValue = new ListValue(ValidationHelper::getNullableInt($databaseArray["id"]));
            $listValue->setValue(ValidationHelper::getString($databaseArray["value"]));
            $listValue->setFieldId(ValidationHelper::getString($databaseArray["fieldId"]));

            return $listValue;
        }

        /**
         * (PHP 5 &gt;= 5.4.0)<br/>
         * Specify data which should be serialized to JSON
         * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
         * @return mixed data which can be serialized by <b>json_encode</b>,
         * which is a value of any type other than a resource.
         */
        function jsonSerialize() {
            return $this->getDatabaseArray();
        }

        public static function getDatabaseFieldNames() {
            return [ KAASoftDatabase::$LIST_VALUES_TABLE_NAME . ".value",
                     KAASoftDatabase::$LIST_VALUES_TABLE_NAME . ".fieldId" ];
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
        public function getFieldId() {
            return $this->fieldId;
        }

        /**
         * @param mixed $fieldId
         */
        public function setFieldId($fieldId) {
            $this->fieldId = $fieldId;
        }
    }