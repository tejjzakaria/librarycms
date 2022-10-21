<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment;

    use KAASoft\Util\FileHelper;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class EmailSettings
     * @package KAASoft\Environment
     */
    class EmailSettings extends AbstractSettings {
        const EmailSettingsFileNameJSON = '/KAASoft/Config/EmailSettings.json';

        const PHP_MAIL     = "mail";
        const SMTP         = "smtp";
        const SEND_METHODS = [ EmailSettings::PHP_MAIL,
                               EmailSettings::SMTP ];
        // Short codes
        const SHORT_CODE_DYNAMIC_CONTENT = "DYNAMIC_CONTENT";
        const SHORT_CODE_BOOK            = "BOOK";
        const SHORT_CODE_BOOKS           = "BOOKS";
        const SHORT_CODE_USER_FIRST_NAME = "USER_FIRST_NAME";
        const SHORT_CODE_USER_LAST_NAME  = "USER_LAST_NAME";
        // Short codes end

        // Parameters
        private $sendMethod;
        private $defaultFromEmailAddress;
        private $defaultFromEmailName;
        private $dynamicEmailTemplate;
        private $staticEmailTemplate;

        // Parameters end

        function __construct() {
        }

        /**
         * copy data from assoc array to object fields
         * @param $settings mixed
         */
        public function copySettings($settings) {
            $this->setDefaultFromEmailAddress(ValidationHelper::getString($settings["defaultFromEmailAddress"]));
            $this->setDefaultFromEmailName(ValidationHelper::getString($settings["defaultFromEmailName"]));
            $this->setSendMethod(ValidationHelper::getStringFromVariants($settings["sendMethod"],
                                                                         EmailSettings::SEND_METHODS,
                                                                         EmailSettings::PHP_MAIL));
            $this->setDynamicEmailTemplate(ValidationHelper::getString($settings["dynamicEmailTemplate"]));
            $this->setStaticEmailTemplate(ValidationHelper::getString($settings["staticEmailTemplate"]));
        }

        /**
         * @param EmailSettings $settings
         */
        public function cloneSettings($settings) {
            $this->setDefaultFromEmailAddress($settings->getDefaultFromEmailAddress());
            $this->setDefaultFromEmailName($settings->getDefaultFromEmailName());
            $this->setSendMethod($settings->getSendMethod());
            $this->setDynamicEmailTemplate($settings->getDynamicEmailTemplate());
            $this->setStaticEmailTemplate($settings->getStaticEmailTemplate());
        }

        /**
         * Returns config file to load/store settings
         * @return string
         */
        public function getConfigFileName() {
            return realpath(FileHelper::getPrivateFolderLocation()) . EmailSettings::EmailSettingsFileNameJSON;
        }

        /**
         * Sets default settings
         */
        public function setDefaultSettings() {
            $this->setSendMethod(EmailSettings::PHP_MAIL);
            $this->setDefaultFromEmailAddress("no-replay@" . Config::getSiteDomain());
            $this->setDefaultFromEmailName("Library CMS - No Replay");
            $this->setStaticEmailTemplate("<html><body>[DYNAMIC_CONTENT]</body></html>");
            $this->setDynamicEmailTemplate("<h1>Hi! This is email template</h1>");
        }

        /**
         * @return array
         */
        function jsonSerialize() {
            return [ "sendMethod"              => $this->sendMethod,
                     "defaultFromEmailAddress" => $this->defaultFromEmailAddress,
                     "defaultFromEmailName"    => $this->defaultFromEmailName,
                     "dynamicEmailTemplate"    => $this->dynamicEmailTemplate,
                     "staticEmailTemplate"     => $this->staticEmailTemplate ];
        }

        /**
         * @return mixed
         */
        public function getSendMethod() {
            return $this->sendMethod;
        }

        /**
         * @param mixed $sendMethod
         */
        public function setSendMethod($sendMethod) {
            $this->sendMethod = $sendMethod;
        }

        /**
         * @return mixed
         */
        public function getDefaultFromEmailAddress() {
            return $this->defaultFromEmailAddress;
        }

        /**
         * @param mixed $defaultFromEmailAddress
         */
        public function setDefaultFromEmailAddress($defaultFromEmailAddress) {
            $this->defaultFromEmailAddress = $defaultFromEmailAddress;
        }

        /**
         * @return mixed
         */
        public function getDefaultFromEmailName() {
            return $this->defaultFromEmailName;
        }

        /**
         * @param mixed $defaultFromEmailName
         */
        public function setDefaultFromEmailName($defaultFromEmailName) {
            $this->defaultFromEmailName = $defaultFromEmailName;
        }

        /**
         * @return mixed
         */
        public function getDynamicEmailTemplate() {
            return $this->dynamicEmailTemplate;
        }

        /**
         * @param mixed $dynamicEmailTemplate
         */
        public function setDynamicEmailTemplate($dynamicEmailTemplate) {
            $this->dynamicEmailTemplate = $dynamicEmailTemplate;
        }

        /**
         * @return mixed
         */
        public function getStaticEmailTemplate() {
            return $this->staticEmailTemplate;
        }

        /**
         * @param mixed $staticEmailTemplate
         */
        public function setStaticEmailTemplate($staticEmailTemplate) {
            $this->staticEmailTemplate = $staticEmailTemplate;
        }
    }