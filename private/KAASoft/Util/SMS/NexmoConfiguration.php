<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    /**
     * Created by KAA Soft.
     * Date: 2018-06-15
     */


    namespace KAASoft\Util\SMS;


    use KAASoft\Environment\SmsSettings;
    use KAASoft\Util\TitledKeyValue;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class NexmoConfiguration
     * @package KAASoft\Util\SMS
     */
    class NexmoConfiguration extends SmsProviderConfiguration {
        private $apiKey;
        private $apiSecret;

        /**
         * NexmoConfiguration constructor.
         */
        public function __construct() {
            $this->name = SmsSettings::NEXMO_NAME;
            $this->title = SmsSettings::SMS_PROVIDERS[SmsSettings::NEXMO_NAME];
        }


        /**
         * @param array $databaseArray
         */
        public function populateInstance(array $databaseArray) {
            $this->setApiKey(ValidationHelper::getString($databaseArray["apiKey"]));
            $this->setApiSecret(ValidationHelper::getString($databaseArray["apiSecret"]));
        }

        /**
         * Specify data which should be serialized to JSON
         * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
         * @return mixed data which can be serialized by <b>json_encode</b>,
         * which is a value of any type other than a resource.
         * @since 5.4.0
         */
        function jsonSerialize() {
            return array_merge(parent::jsonSerialize(),
                               [ "apiKey"    => $this->apiKey,
                                 "apiSecret" => $this->apiSecret ]);
        }

        public function getConfig() {
            return [ "apiKey"    => $this->apiKey,
                     "apiSecret" => $this->apiSecret ];
        }

        public function getTitledConfig() {
            return [ new TitledKeyValue("API Key",
                                        "apiKey",
                                        $this->apiKey),
                     new TitledKeyValue("API Secret",
                                        "apiSecret",
                                        $this->apiSecret) ];
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
        public function getApiSecret() {
            return $this->apiSecret;
        }

        /**
         * @param mixed $apiSecret
         */
        public function setApiSecret($apiSecret) {
            $this->apiSecret = $apiSecret;
        }
    }