<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment;


    use KAASoft\Util\FileHelper;
    use KAASoft\Util\ValidationHelper;

    class GoogleSettings extends AbstractSettings {

        const GoogleSettingsFileNameJSON = '/KAASoft/Config/GoogleSettings.json';

        private $apiKey;
        private $maxSearchResults;
        private $country;

        /**
         * copy data from assoc array to object fields
         * @param $settings mixed
         */
        public function copySettings($settings) {
            $this->setApiKey(ValidationHelper::getString($settings["apiKey"]));
            $this->setMaxSearchResults(ValidationHelper::getInt($settings["maxSearchResults"]));
            $this->setCountry(ValidationHelper::getString($settings["country"]));
        }

        /**
         * copy data from another Settings object
         * @param $settings GoogleSettings
         */
        public function cloneSettings($settings) {
            $this->setApiKey($settings->getApiKey());
            $this->setMaxSearchResults($settings->getMaxSearchResults());
            $this->setCountry($settings->getCountry());
        }

        /**
         * Returns config file to load/store settings
         * @return string
         */
        public function getConfigFileName() {
            return realpath(FileHelper::getPrivateFolderLocation()) . GoogleSettings::GoogleSettingsFileNameJSON;
        }

        /**
         * Sets default settings
         */
        public function setDefaultSettings() {
            $this->setApiKey("123456789");
            $this->setMaxSearchResults(10);
            $this->setCountry("US");
        }

        /**
         * Specify data which should be serialized to JSON
         * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
         * @return mixed data which can be serialized by <b>json_encode</b>,
         * which is a value of any type other than a resource.
         * @since 5.4.0
         */
        function jsonSerialize() {
            return [ "apiKey"           => $this->apiKey,
                     "maxSearchResults" => $this->maxSearchResults,
                     "country"          => $this->country ];
        }

        /**
         * @return mixed
         */
        public function getApiKey() {
            return $this->apiKey;
        }

        /**
         * @param mixed $apiKey
         */
        public function setApiKey($apiKey) {
            $this->apiKey = $apiKey;
        }

        /**
         * @return mixed
         */
        public function getMaxSearchResults() {
            return $this->maxSearchResults;
        }

        /**
         * @param mixed $maxSearchResults
         */
        public function setMaxSearchResults($maxSearchResults) {
            $this->maxSearchResults = $maxSearchResults;
        }

        /**
         * @return mixed
         */
        public function getCountry() {
            return $this->country;
        }

        /**
         * @param mixed $country
         */
        public function setCountry($country) {
            $this->country = $country;
        }


    }