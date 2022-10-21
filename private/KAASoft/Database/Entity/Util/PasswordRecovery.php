<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Database\Entity\Util;


    use KAASoft\Database\Entity\DatabaseEntity;
    use KAASoft\Util\ValidationHelper;

    class PasswordRecovery extends DatabaseEntity {
        private $hash;
        private $email;
        private $validDateTime;

        function __construct($id = null) {
            parent::__construct($id);
        }

        /**
         * @return array
         */
        public function getDatabaseArray() {
            return array_merge(parent::getDatabaseArray(),
                               [ "email"         => $this->email,
                                 "hash"          => $this->hash,
                                 "validDateTime" => $this->validDateTime ]);
        }

        /**
         * @param array $databaseArray
         * @return PasswordRecovery to restore form databaseArray
         */
        public static function getObjectInstance(array $databaseArray) {
            $user = new PasswordRecovery();
            $user->setEmail(ValidationHelper::getString($databaseArray["email"]));
            $user->setHash(ValidationHelper::getString($databaseArray["hash"]));
            $user->setValidDateTime(ValidationHelper::getString($databaseArray["validDateTime"]));

            return $user;
        }

        /**
         * @return mixed
         */
        public function getEmail() {
            return $this->email;
        }

        /**
         * @param mixed $email
         */
        public function setEmail($email) {
            $this->email = $email;
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
        public function getValidDateTime() {
            return $this->validDateTime;
        }

        /**
         * @param mixed $validDateTime
         */
        public function setValidDateTime($validDateTime) {
            $this->validDateTime = $validDateTime;
        }
    }