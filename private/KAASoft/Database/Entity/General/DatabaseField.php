<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Database\Entity\General;

    use KAASoft\Database\Entity\DatabaseEntity;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class DatabaseField
     * @package KAASoft\Util\Database
     */
    class DatabaseField extends DatabaseEntity {
        const SQL_TYPE_STRING   = "STRING";
        const SQL_TYPE_INTEGER  = "INTEGER";
        const SQL_TYPE_FLOAT    = "FLOAT";
        const SQL_TYPE_LONGTEXT = "LONGTEXT";
        const SQL_TYPE_BOOL     = "BOOL";

        const CONTROL_TYPE_INPUT    = "INPUT";
        const CONTROL_TYPE_TEXTAREA = "TEXTAREA";
        const CONTROL_TYPE_CHECKBOX = "CHECKBOX";
        const CONTROL_TYPE_SELECT   = "SELECT";

        const SQL_DATABASE_TYPES = [ DatabaseField::SQL_TYPE_STRING   => "VARCHAR(255)",
                                     DatabaseField::SQL_TYPE_INTEGER  => "INT(10)",
                                     DatabaseField::SQL_TYPE_FLOAT    => "FLOAT(10,6)",
                                     DatabaseField::SQL_TYPE_LONGTEXT => "LONGTEXT",
                                     DatabaseField::SQL_TYPE_BOOL     => "BIT(1)" ];

        const CONTROL_TYPES = [ DatabaseField::CONTROL_TYPE_INPUT,
                                DatabaseField::CONTROL_TYPE_TEXTAREA,
                                DatabaseField::CONTROL_TYPE_CHECKBOX,
                                DatabaseField::CONTROL_TYPE_SELECT ];

        private $name;
        private $type;
        private $control;
        private $title;
        private $isFilterable;
        /**
         * @var array - list of values for select
         */
        private $listValues;

        /**
         * DatabaseField constructor.
         * @param null $id
         */
        public function __construct($id = null) {
            parent::__construct($id);
        }

        /**
         * @return array
         */
        public function getDatabaseArray() {
            return [ "name"    => $this->name,
                     "type"    => $this->type,
                     "control" => $this->control,
                     "title"   => $this->title ,
                     "isFilterable"   => $this->isFilterable ];
        }

        /**
         * @param array $databaseArray
         * @return DatabaseField to restore form databaseArray
         */
        public static function getObjectInstance(array $databaseArray) {
            $databaseField = new DatabaseField(ValidationHelper::getNullableInt($databaseArray["id"]));
            $databaseField->setName(ValidationHelper::getString($databaseArray["name"]));
            $databaseField->setType(ValidationHelper::getSqlType($databaseArray["type"]));
            $databaseField->setControl(ValidationHelper::getControlType($databaseArray["control"]));
            $databaseField->setTitle(ValidationHelper::getString($databaseArray["title"]));
            $databaseField->setIsFilterable(ValidationHelper::getBool($databaseArray["isFilterable"]));

            return $databaseField;
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
            return [ KAASoftDatabase::$BOOK_FIELDS_TABLE_NAME . ".name",
                     KAASoftDatabase::$BOOK_FIELDS_TABLE_NAME . ".type",
                     KAASoftDatabase::$BOOK_FIELDS_TABLE_NAME . ".control",
                     KAASoftDatabase::$BOOK_FIELDS_TABLE_NAME . ".title",
                     KAASoftDatabase::$BOOK_FIELDS_TABLE_NAME . ".isFilterable"];
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
        public function getType() {
            return $this->type;
        }

        /**
         * @param mixed $type
         */
        public function setType($type) {
            $this->type = $type;
        }

        /**
         * @return mixed
         */
        public function getControl() {
            return $this->control;
        }

        /**
         * @param mixed $control
         */
        public function setControl($control) {
            $this->control = $control;
        }

        /**
         * @return mixed
         */
        public function getListValues() {
            return $this->listValues;
        }

        /**
         * @param mixed $listValues
         */
        public function setListValues($listValues) {
            $this->listValues = $listValues;
        }

        /**
         * @param $value
         */
        public function addListValue($value) {
            if ($this->listValues === null) {
                $this->listValues = [];
            }
            $this->listValues[] = $value;
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
        public function isFilterable() {
            return $this->isFilterable;
        }

        /**
         * @param mixed $isFilterable
         */
        public function setIsFilterable($isFilterable) {
            $this->isFilterable = $isFilterable;
        }
    }