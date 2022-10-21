<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Database\Entity;

    /**
     * Interface DatabaseSerializable
     * @package KAASoft\Database\Entity
     */
    interface DatabaseSerializable {

        /**
         * @return array to save in database (fieldName => fieldValue)
         */
        public function getDatabaseArray();

        /**
         * @param array $databaseArray
         * @return object to restore form databaseArray
         */
        public static function getObjectInstance(array $databaseArray);
    }