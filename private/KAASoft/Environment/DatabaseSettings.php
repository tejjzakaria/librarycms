<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment;

    use KAASoft\Util\FileHelper;

    /**
     * Class DatabaseSettings
     * @package KAASoft\Environment
     */
    class DatabaseSettings extends AbstractSettings {
        const DatabaseSettingsFileNameJSON = '/KAASoft/Config/DatabaseSettings.json';

        /**
         * @var array
         */
        private $databaseConnections;
        private $activeConnectionName;

        function __construct() {
        }

        /**
         * copy data from assoc array to object fields
         * @param $settings mixed
         */
        public function copySettings($settings) {
            $databaseConnections = [];
            foreach ($settings["databaseConnections"] as $dbConnection) {
                $databaseConnections [] = DatabaseConnection::getObjectInstance($dbConnection);

            }
            $this->setDatabaseConnections($databaseConnections);
            $this->setActiveConnectionName($settings["activeConnectionName"]);
        }

        /**
         * copy data from another Settings object
         * @param $settings DatabaseSettings
         */
        public function cloneSettings($settings) {
            $this->setDatabaseConnections($settings->getDatabaseConnections());
            $this->setActiveConnectionName($settings->getActiveConnectionName());
        }

        /**
         * Returns config file to load/store settings
         * @return string
         */
        public function getConfigFileName() {
            return realpath(FileHelper::getPrivateFolderLocation()) . DatabaseSettings::DatabaseSettingsFileNameJSON;
        }

        /**
         * Sets default settings
         */
        public function setDefaultSettings() {

        }

        /**
         * @return DatabaseConnection|null
         */
        public function getActiveDatabaseConnection() {
            if (isset( $this->activeConnectionName ) and isset( $this->databaseConnections ) and count($this->databaseConnections) > 0) {
                foreach ($this->databaseConnections as $connection) {
                    if ($connection instanceof DatabaseConnection) {
                        if (strcmp($connection->getName(),
                                   $this->activeConnectionName) == 0
                        ) {
                            return $connection;
                        }
                    }
                }
            }

            return null;
        }

        /**
         * @return array
         */
        function jsonSerialize() {
            return [ "databaseConnections"  => $this->databaseConnections,
                     "activeConnectionName" => $this->activeConnectionName ];
        }

        /**
         * @return array
         */
        public function getDatabaseConnections() {
            return $this->databaseConnections;
        }

        /**
         * @param array $databaseConnections
         */
        public function setDatabaseConnections($databaseConnections) {
            $this->databaseConnections = $databaseConnections;
        }

        /**
         * @param DatabaseConnection $databaseConnection
         */
        public function addDatabaseConnection($databaseConnection) {
            if (!isset( $this->databaseConnections ) or $this->databaseConnections == null) {
                $this->databaseConnections = [];
            }
            for ($i = 0; $i < count($this->databaseConnections); $i++) {
                $dbConnection = $this->databaseConnections [$i];
                if ($dbConnection instanceof DatabaseConnection) {
                    if (strcmp($databaseConnection->getName(),
                               $dbConnection->getName()) === 0
                    ) {
                        // replace settings with existing name
                        $this->databaseConnections [$i] = $databaseConnection;

                        return;
                    }
                }
            }
            $this->databaseConnections[] = $databaseConnection;
        }

        /**
         * @return mixed
         */
        public function getActiveConnectionName() {
            return $this->activeConnectionName;
        }

        /**
         * @param mixed $activeConnectionName
         */
        public function setActiveConnectionName($activeConnectionName) {
            $this->activeConnectionName = $activeConnectionName;
        }


    }