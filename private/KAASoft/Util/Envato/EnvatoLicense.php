<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    /**
     * Created by KAA Soft.
     * Date: 2018-05-26
     */


    namespace KAASoft\Util\Envato;


    use JsonSerializable;
    use KAASoft\Environment\AbstractSettings;
    use KAASoft\Util\FileHelper;
    use KAASoft\Util\ValidationHelper;

    class EnvatoLicense extends AbstractSettings implements JsonSerializable {

        const EnvatoLicenseFileNameJSON = '/KAASoft/Config/License.json';

        private $licenseType;
        private $supportUntil;
        private $buyer;
        private $itemId;
        private $itemName;
        private $hash;
        private $lastValidation;
        private $purchaseCode;

        /**
         * EnvatoLicense constructor.
         */
        public function __construct() {
        }

        /**
         * Specify data which should be serialized to JSON
         * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
         * @return mixed data which can be serialized by <b>json_encode</b>,
         * which is a value of any type other than a resource.
         * @since 5.4.0
         */
        function jsonSerialize() {
            return [ "licenseType"    => $this->licenseType,
                     "supportUntil"   => $this->supportUntil,
                     "buyer"          => $this->buyer,
                     "itemId"         => $this->itemId,
                     "itemName"       => $this->itemName,
                     "hash"           => $this->hash,
                     "lastValidation" => $this->lastValidation,
                     "purchaseCode"   => $this->purchaseCode ];
        }

        /**
         * copy data from assoc array to object fields
         * @param $settings mixed
         */
        public function copySettings($settings) {
            $this->setItemId(ValidationHelper::getString($settings["itemId"]));
            $this->setItemName(ValidationHelper::getString($settings["itemName"]));
            $this->setSupportUntil(ValidationHelper::getString($settings["supportUntil"]));
            $this->setBuyer(ValidationHelper::getString($settings["buyer"]));
            $this->setLicenseType(ValidationHelper::getString($settings["licenseType"]));
            $this->setHash(ValidationHelper::getString($settings["hash"]));
            $this->setLastValidation(ValidationHelper::getString($settings["lastValidation"]));
            $this->setPurchaseCode(ValidationHelper::getString($settings["purchaseCode"]));
        }

        /**
         * copy data from another Settings object
         * @param $settings EnvatoLicense
         */
        public function cloneSettings($settings) {
            $this->setItemId(ValidationHelper::getString($settings->getItemId()));
            $this->setItemName(ValidationHelper::getString($settings->getItemName()));
            $this->setSupportUntil(ValidationHelper::getString($settings->getSupportUntil()));
            $this->setBuyer(ValidationHelper::getString($settings->getBuyer()));
            $this->setLicenseType(ValidationHelper::getString($settings->getLicenseType()));
            $this->setHash(ValidationHelper::getString($settings->getHash()));
            $this->setLastValidation(ValidationHelper::getString($settings->getLastValidation()));
            $this->setPurchaseCode(ValidationHelper::getString($settings->getPurchaseCode()));
        }

        /**
         * Returns config file to load/store settings
         * @return string
         */
        public function getConfigFileName() {
            return realpath(FileHelper::getPrivateFolderLocation()) . EnvatoLicense::EnvatoLicenseFileNameJSON;
        }

        /**
         * Sets default settings
         */
        public function setDefaultSettings() {
        }

        /**
         * @return mixed
         */
        public function getLicenseType() {
            return $this->licenseType;
        }

        /**
         * @param mixed $licenseType
         */
        public function setLicenseType($licenseType) {
            $this->licenseType = $licenseType;
        }

        /**
         * @return mixed
         */
        public function getSupportUntil() {
            return $this->supportUntil;
        }

        /**
         * @param mixed $supportUntil
         */
        public function setSupportUntil($supportUntil) {
            $this->supportUntil = $supportUntil;
        }

        /**
         * @return mixed
         */
        public function getBuyer() {
            return $this->buyer;
        }

        /**
         * @param mixed $buyer
         */
        public function setBuyer($buyer) {
            $this->buyer = $buyer;
        }

        /**
         * @return mixed
         */
        public function getItemId() {
            return $this->itemId;
        }

        /**
         * @param mixed $itemId
         */
        public function setItemId($itemId) {
            $this->itemId = $itemId;
        }

        /**
         * @return mixed
         */
        public function getItemName() {
            return $this->itemName;
        }

        /**
         * @param mixed $itemName
         */
        public function setItemName($itemName) {
            $this->itemName = $itemName;
        }

        /**
         * @return mixed
         */
        public function getHash() {
            return $this->hash;
        }

        /**
         * @param mixed $hash
         */
        public function setHash($hash) {
            $this->hash = $hash;
        }

        /**
         * @return mixed
         */
        public function getLastValidation() {
            return $this->lastValidation;
        }

        /**
         * @param mixed $lastValidation
         */
        public function setLastValidation($lastValidation) {
            $this->lastValidation = $lastValidation;
        }

        /**
         * @return mixed
         */
        public function getPurchaseCode() {
            return $this->purchaseCode;
        }

        /**
         * @param mixed $purchaseCode
         */
        public function setPurchaseCode($purchaseCode) {
            $this->purchaseCode = $purchaseCode;
        }
    }