<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Database\Entity\Util;

    use KAASoft\Database\Entity\DatabaseEntity;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class DatabaseVersion
     * @package KAASoft\Database\Entity\Util
     */
    class DatabaseVersion extends DatabaseEntity {
        /**
         * @var integer
         */
        private $version;

        /**
         * DatabaseVersion constructor.
         */
        public function __construct() {
            parent::__construct(null);
        }

        /**
         * @return array
         */
        public function getDatabaseArray() {
            return [ 'version' => $this->version ];
        }

        /**
         * @param array $databaseArray
         * @return DatabaseVersion to restore form databaseArray
         */
        public static function getObjectInstance(array $databaseArray) {
            $databaseVersion = new DatabaseVersion();
            $databaseVersion->setVersion(ValidationHelper::getString($databaseArray['version']));

            return $databaseVersion;
        }

        /**
         * @return array
         */
        public static function getDatabaseFieldNames() {
            return [ KAASoftDatabase::$DATABASE_VERSION_TABLE_NAME . ".version" ];
        }

        /**
         * @return int
         */
        public function getVersion() {
            return $this->version;
        }

        /**
         * @param $version
         */
        public function setVersion($version) {
            $this->version = $version;
        }
    }