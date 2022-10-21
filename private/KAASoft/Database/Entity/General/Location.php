<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Database\Entity\General;

    use JsonSerializable;
    use KAASoft\Database\Entity\DatabaseEntity;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\Comparable;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class Location
     * @package KAASoft\Database\Entity\General
     */
    class Location extends DatabaseEntity implements JsonSerializable, Comparable {
        /**
         * @var Store
         */
        private $store;

        private $name;
        private $storeId;

        /**
         * Location constructor.
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
                               [ "name"    => $this->name,
                                 "storeId" => $this->storeId ]);
        }

        /**
         * @param array $databaseArray
         * @return Location to relocation form databaseArray
         */
        public static function getObjectInstance(array $databaseArray) {
            $location = new Location(ValidationHelper::getNullableInt($databaseArray["id"]));
            $location->setName(ValidationHelper::getString($databaseArray["name"]));
            $location->setStoreId(ValidationHelper::getInt($databaseArray["storeId"]));

            return $location;
        }


        /**
         * @return array
         */
        public static function getDatabaseFieldNames() {
            return array_merge(parent::getDatabaseFieldNames(),
                               [ KAASoftDatabase::$LOCATIONS_TABLE_NAME . ".name",
                                 KAASoftDatabase::$LOCATIONS_TABLE_NAME . ".storeId" ]);
        }

        /**
         * (PHP 5 &gt;= 5.4.0)<br/>
         * Specify data which should be serialized to JSON
         * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
         * @return mixed data which can be serialized by <b>json_encode</b>,
         * which is a value of any type other than a resource.
         */
        function jsonSerialize() {
            return array_merge($this->getDatabaseArray(),
                               [ "store" => $this->store ]);
        }

        /**
         * @param $other
         * @return bool
         */
        public function compareTo($other) {
            return $other instanceof Location and strcasecmp($other->getName(),
                                                             $this->name) === 0 and $other->getStoreId() === $this->storeId;
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
        public function getStoreId() {
            return $this->storeId;
        }

        /**
         * @param mixed $storeId
         */
        public function setStoreId($storeId) {
            $this->storeId = $storeId;
        }

        /**
         * @return Store
         */
        public function getStore() {
            return $this->store;
        }

        /**
         * @param Store $store
         */
        public function setStore($store) {
            $this->store = $store;
        }
    }