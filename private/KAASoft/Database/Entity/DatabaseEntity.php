<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Database\Entity;
    /**
     * Class DatabaseEntity
     * @package KAASoft\Database\Entity
     */
    abstract class DatabaseEntity implements DatabaseSerializable {
        /**
         * @var integer|string
         */
        protected $id;

        /**
         * DatabaseEntity constructor.
         * @param $id
         */
        protected function __construct($id) {
            if (empty( $id ) and $id !== 0) {
                $id = null;
            }
            $this->id = $id;
        }

        /**
         * @return integer|string
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

        public function getDatabaseArray() {
            return isset( $this->id ) ? [ "id" => $this->id ] : [];
        }

        /**
         * @return array
         */
        public static function getDatabaseFieldNames() {
            return [];
        }

        /**
         * @param array $databaseArray
         * @return DatabaseEntity
         */
        public static function getObjectInstance(array $databaseArray) {
            return null;
        }
    }