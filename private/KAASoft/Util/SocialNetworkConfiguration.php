<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    /**
     * Created by KAA Soft.
     * Date: 2018-06-10
     */


    namespace KAASoft\Util;


    use JsonSerializable;

    /**
     * Class SocialNetwork
     * @package KAASoft\Util
     */
    class SocialNetworkConfiguration implements JsonSerializable {

        private $name;
        private $applicationId;
        private $applicationSecret;
        private $isActive;

        /**
         * SocialNetwork constructor.
         * @param null $name
         * @param null $applicationId
         * @param null $applicationSecret
         * @param bool $isActive
         */
        public function __construct($name = null, $applicationId = null, $applicationSecret = null, $isActive = false) {
            $this->name = $name;
            $this->applicationId = $applicationId;
            $this->applicationSecret = $applicationSecret;
            $this->isActive = $isActive;
        }

        /**
         * @param array $databaseArray
         * @return SocialNetworkConfiguration
         */
        public static function getObjectInstance(array $databaseArray) {
            $socialNetworkConfiguration = new SocialNetworkConfiguration();
            $socialNetworkConfiguration->setName(ValidationHelper::getString($databaseArray["name"]));
            $socialNetworkConfiguration->setApplicationId(ValidationHelper::getString($databaseArray["applicationId"]));
            $socialNetworkConfiguration->setApplicationSecret(ValidationHelper::getString($databaseArray["applicationSecret"]));
            $socialNetworkConfiguration->setIsActive(ValidationHelper::getBool($databaseArray["isActive"]));

            return $socialNetworkConfiguration;
        }

        /**
         * Specify data which should be serialized to JSON
         * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
         * @return mixed data which can be serialized by <b>json_encode</b>,
         * which is a value of any type other than a resource.
         * @since 5.4.0
         */
        function jsonSerialize() {
            return [ "name"              => $this->name,
                     "applicationId"     => $this->applicationId,
                     "applicationSecret" => $this->applicationSecret,
                     "isActive"          => $this->isActive ];
        }

        public function getConfig() {
            return [ "applicationId"     => $this->applicationId,
                     "applicationSecret" => $this->applicationSecret ];
        }

        /**
         * @return mixed
         */
        public function getApplicationId() {
            return $this->applicationId;
        }

        /**
         * @param mixed $applicationId
         */
        public function setApplicationId($applicationId) {
            $this->applicationId = $applicationId;
        }

        /**
         * @return mixed
         */
        public function getApplicationSecret() {
            return $this->applicationSecret;
        }

        /**
         * @param mixed $applicationSecret
         */
        public function setApplicationSecret($applicationSecret) {
            $this->applicationSecret = $applicationSecret;
        }

        /**
         * @return mixed
         */
        public function isActive() {
            return $this->isActive;
        }

        /**
         * @param mixed $isActive
         */
        public function setIsActive($isActive) {
            $this->isActive = $isActive;
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

    }