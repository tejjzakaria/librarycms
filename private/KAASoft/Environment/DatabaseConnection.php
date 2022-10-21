<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment;

    use JsonSerializable;

    /**
     * Class DatabaseConnection
     * @package KAASoft\Environment
     */
    class DatabaseConnection implements JsonSerializable {
        private $name;
        private $databaseType;
        private $databaseName;
        private $host;
        private $port;
        private $username;
        private $password;
        private $charset;

        /**
         * DatabaseConnection constructor.
         */
        public function __construct() {
        }

        public function getDatabaseArray() {
            return [ "name"         => $this->name,
                     "databaseType" => $this->databaseType,
                     "databaseName" => $this->databaseName,
                     "host"         => $this->host,
                     "port"         => $this->port,
                     "username"     => $this->username,
                     "password"     => $this->password,
                     "charset"      => $this->charset ];
        }

        /**
         * @param array $databaseArray
         * @return DatabaseConnection to restore form databaseArray
         */
        public static function getObjectInstance(array $databaseArray) {
            $databaseConnection = new DatabaseConnection();
            $databaseConnection->setName(isset( $databaseArray["name"] ) ? $databaseArray["name"] : null);
            $databaseConnection->setDatabaseType(isset( $databaseArray["databaseType"] ) ? $databaseArray["databaseType"] : null);
            $databaseConnection->setDatabaseName(isset( $databaseArray["databaseName"] ) ? $databaseArray["databaseName"] : null);
            $databaseConnection->setHost(isset( $databaseArray["host"] ) ? $databaseArray["host"] : null);
            $databaseConnection->setPort(isset( $databaseArray["port"] ) ? $databaseArray["port"] : null);
            $databaseConnection->setUserName(isset( $databaseArray["username"] ) ? $databaseArray["username"] : null);
            $databaseConnection->setPassword(isset( $databaseArray["password"] ) ? $databaseArray["password"] : null);
            $databaseConnection->setCharset(isset( $databaseArray["charset"] ) ? $databaseArray["charset"] : null);

            return $databaseConnection;
        }

        /**
         * @return array
         */
        function jsonSerialize() {
            return $this->getDatabaseArray();
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
        public function getDatabaseType() {
            return $this->databaseType;
        }

        /**
         * @param mixed $databaseType
         */
        public function setDatabaseType($databaseType) {
            $this->databaseType = $databaseType;
        }

        /**
         * @return mixed
         */
        public function getDatabaseName() {
            return $this->databaseName;
        }

        /**
         * @param mixed $databaseName
         */
        public function setDatabaseName($databaseName) {
            $this->databaseName = $databaseName;
        }

        /**
         * @return mixed
         */
        public function getHost() {
            return $this->host;
        }

        /**
         * @param mixed $host
         */
        public function setHost($host) {
            $this->host = $host;
        }

        /**
         * @return mixed
         */
        public function getUsername() {
            return $this->username;
        }

        /**
         * @param mixed $username
         */
        public function setUserName($username) {
            $this->username = $username;
        }

        /**
         * @return mixed
         */
        public function getPassword() {
            return $this->password;
        }

        /**
         * @param mixed $password
         */
        public function setPassword($password) {
            $this->password = $password;
        }

        /**
         * @return mixed
         */
        public function getCharset() {
            return $this->charset;
        }

        /**
         * @param mixed $charset
         */
        public function setCharset($charset) {
            $this->charset = $charset;
        }

        /**
         * @return mixed
         */
        public function getPort() {
            return $this->port;
        }

        /**
         * @param mixed $port
         */
        public function setPort($port) {
            $this->port = $port;
        }
    }