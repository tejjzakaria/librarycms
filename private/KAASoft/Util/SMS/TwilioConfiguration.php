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
     * Class TwilioConfiguration
     * @package KAASoft\Util\SMS
     */
    class TwilioConfiguration extends SmsProviderConfiguration {
        private $accountSID;
        private $authToken;

        /**
         * TwilioConfiguration constructor.
         */
        public function __construct() {
            $this->name = SmsSettings::TWILIO_NAME;
            $this->title = SmsSettings::SMS_PROVIDERS[SmsSettings::TWILIO_NAME];
        }


        /**
         * @param array $databaseArray
         */
        public function populateInstance(array $databaseArray) {
            $this->setAccountSID(ValidationHelper::getString($databaseArray["accountSID"]));
            $this->setAuthToken(ValidationHelper::getString($databaseArray["authToken"]));
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
                               [ "accountSID" => $this->accountSID,
                                 "authToken"  => $this->authToken ]);
        }

        public function getConfig() {
            return [ "accountSID" => $this->accountSID,
                     "authToken"  => $this->authToken ];
        }

        public function getTitledConfig() {
            return [ new TitledKeyValue("Account SID",
                                        "accountSID",
                                        $this->accountSID),
                     new TitledKeyValue("Auth Token",
                                        "authToken",
                                        $this->authToken) ];
        }

        /**
         * @return mixed
         */
        public function getAccountSID() {
            return $this->accountSID;
        }

        /**
         * @param mixed $accountSID
         */
        public function setAccountSID($accountSID) {
            $this->accountSID = $accountSID;
        }

        /**
         * @return mixed
         */
        public function getAuthToken() {
            return $this->authToken;
        }

        /**
         * @param mixed $authToken
         */
        public function setAuthToken($authToken) {
            $this->authToken = $authToken;
        }
    }