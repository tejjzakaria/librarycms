<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Util;


    use JsonSerializable;

    /**
     * Class EmailAddress
     * @package KAASoft\Util
     */
    class EmailAddress implements JsonSerializable {
        private $email;
        private $name;

        /**
         * EmailAddress constructor.
         * @param $email
         * @param $name
         */
        public function __construct($email = null, $name = null) {
            $this->email = $email;
            $this->name = $name;
        }

        /**
         * @return array
         */
        function jsonSerialize() {
            return [ "email" => $this->email,
                     "name"  => $this->name ];
        }

        /**
         * @return null
         */
        public function getEmail() {
            return $this->email;
        }

        /**
         * @param null $email
         */
        public function setEmail($email) {
            $this->email = $email;
        }

        /**
         * @return null
         */
        public function getName() {
            return $this->name;
        }

        /**
         * @param null $name
         */
        public function setName($name) {
            $this->name = $name;
        }
    }