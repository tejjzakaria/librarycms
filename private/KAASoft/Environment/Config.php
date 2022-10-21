<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment;

    use KAASoft\Util\FileHelper;
    use KAASoft\Util\HTTP\HttpClient;

    /**
     * Class Config
     * @package KAASoft\Environment
     */
    class Config {

        const DEBUG_MODE           = false;
        const DEBUG_DATABASE_MODE  = false;
        const DEMO_MODE            = false;
        const AJAX_TIMEOUT_SECONDS = 10;

        const VERSION_FILE_NAME = "version.txt";

        private static $version;

        function __construct() {
            // todo: import setting from file
        }

        public static function getSiteDomain() {
            return $_SERVER['HTTP_HOST'];
        }

        public static function getSiteURL() {
            // http or https
            $protocolName = ( !empty( $_SERVER['HTTPS'] ) && 'off' !== strtolower($_SERVER['HTTPS']) ? HttpClient::HTTPS_PROTOCOL : HttpClient::HTTP_PROTOCOL );
            // http:// or https://
            $requestScheme = $protocolName . HttpClient::HTTP_PROTOCOL_SEPARATOR;
            // kaasoft.pro
            $domainName = Config::getSiteDomain();

            // http://kaasoft.pro or https://kaasoft.pro
            return $requestScheme . $domainName;
        }

        /**
         * @var DatabaseConnection
         */
        private static $databaseConnection = null;

        /**
         * @return DatabaseConnection
         */
        public static function getDatabaseConnection() {
            if (Config::$databaseConnection === null) {

                // read stored connections
                $databaseSettings = new DatabaseSettings();
                $databaseSettings->loadSettings();
                if ($databaseSettings !== null) {
                    Config::$databaseConnection = $databaseSettings->getActiveDatabaseConnection();
                }
            }

            return Config::$databaseConnection;
        }

        /**
         * @param $databaseConnection DatabaseConnection
         */
        public static function setDatabaseConnection($databaseConnection) {
            self::$databaseConnection = $databaseConnection;
        }

        /**
         * @return string
         */
        public static function getVersion() {
            if (self::$version === null) {
                $versionFileName = FileHelper::getSiteRoot() . DIRECTORY_SEPARATOR . Config::VERSION_FILE_NAME;

                self::$version = trim(file_get_contents($versionFileName));
            }

            return self::$version;
        }
    }
