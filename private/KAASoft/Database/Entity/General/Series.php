<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Database\Entity\General;

    use JsonSerializable;
    use KAASoft\Database\Entity\DatabaseEntity;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\Comparable;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class Series
     * @package KAASoft\Database\Entity\General
     */
    class Series extends DatabaseEntity implements JsonSerializable, Comparable {
        private $name;
        /**
         * @var bool
         */
        private $isComplete;

        /**
         * Series constructor.
         * @param null $id
         */
        function __construct($id = null) {
            parent::__construct($id);
        }

        /**
         * @return array
         */
        public function getDatabaseArray() {
            return array_merge(parent::getDatabaseArray(),
                               [ "name"       => $this->name,
                                 "isComplete" => $this->isComplete ]);
        }

        /**
         * @param array $databaseArray
         * @return Series to restore form databaseArray
         */
        public static function getObjectInstance(array $databaseArray) {
            $author = new Series(ValidationHelper::getNullableInt($databaseArray["id"]));
            $author->setName(ValidationHelper::getString($databaseArray["name"]));
            $author->setIsComplete(ValidationHelper::getBool($databaseArray["isComplete"]));

            return $author;
        }


        /**
         * @return array
         */
        public static function getDatabaseFieldNames() {
            return array_merge(parent::getDatabaseFieldNames(),
                               [ KAASoftDatabase::$SERIES_TABLE_NAME . ".name",
                                 KAASoftDatabase::$SERIES_TABLE_NAME . ".isComplete" ]);
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

        /**
         * @param $other
         * @return bool
         */
        public function compareTo($other) {
            return $other instanceof Series and strcasecmp($other->getName(),
                                                           $this->name) === 0;
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
         * @return bool
         */
        public function isComplete() {
            return $this->isComplete;
        }

        /**
         * @param bool $isComplete
         */
        public function setIsComplete($isComplete) {
            $this->isComplete = $isComplete;
        }
    }