<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment;

    use KAASoft\Util\FileHelper;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class SMTPSettings
     * @package KAASoft\Environment
     */
    class SMTPSettings extends AbstractSettings {
        const SMTPSettingsFileNameJSON = '/KAASoft/Config/SMTPSettings.json';

        private $server;
        private $port;
        private $security;
        private $userName;
        private $password;

        function __construct() {
        }

        /**
         * copy data from assoc array to object fields
         * @param $settings mixed
         */
        public function copySettings($settings) {
            $this->setUserName(ValidationHelper::getString($settings["userName"]));
            $this->setPassword(ValidationHelper::getString($settings["password"]));
            $this->setServer(ValidationHelper::getString($settings["server"]));
            $this->setPort(ValidationHelper::getInt($settings["port"]));
            $this->setSecurity(ValidationHelper::getString($settings["security"]));

        }

        /**
         * copy data from another Settings object
         * @param $settings SMTPSettings
         */
        public function cloneSettings($settings) {
            $this->setUserName($settings->getUserName());
            $this->setPassword($settings->getPassword());
            $this->setServer($settings->getServer());
            $this->setPort($settings->getPort());
            $this->setSecurity($settings->getSecurity());
        }

        /**
         * Returns config file to load/store settings
         * @return string
         */
        public function getConfigFileName() {
            return realpath(FileHelper::getPrivateFolderLocation()) . SMTPSettings::SMTPSettingsFileNameJSON;
        }

        /**
         * Sets default settings
         */
        public function setDefaultSettings() {
        }


        /**
         * @return array
         */
        function jsonSerialize() {
            return [ "userName" => $this->userName,
                     "password" => $this->password,
                     "server"   => $this->server,
                     "port"     => $this->port,
                     "security" => $this->security ];
        }

        /**
         * @return mixed
         */
        public function getUserName() {
            return $this->userName;
        }

        /**
         * @param mixed $userName
         */
        public function setUserName($userName) {
            $this->userName = $userName;
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
        public function getServer() {
            return $this->server;
        }

        /**
         * @param mixed $server
         */
        public function setServer($server) {
            $this->server = $server;
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

        /**
         * @return mixed
         */
        public function getSecurity() {
            return $this->security;
        }

        /**
         * @param mixed $security
         */
        public function setSecurity($security) {
            $this->security = $security;
        }
    }