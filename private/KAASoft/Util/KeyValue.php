<?php
/**
 * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
 */

    /**
     * Created by KAA Soft.
     * Date: 2018-06-18
     */


    namespace KAASoft\Util;

    /**
     * Class KeyValue
     * @package KAASoft\Util
     */
    class KeyValue {

        protected $key;
        protected $value;

        /**
         * KeyValue constructor.
         * @param $key
         * @param $value
         */
        public function __construct($key, $value) {
            $this->key = $key;
            $this->value = $value;
        }

        /**
         * @return mixed
         */
        public function getKey() {
            return $this->key;
        }

        /**
         * @param mixed $key
         */
        public function setKey($key) {
            $this->key = $key;
        }

        /**
         * @return mixed
         */
        public function getValue() {
            return $this->value;
        }

        /**
         * @param mixed $value
         */
        public function setValue($value) {
            $this->value = $value;
        }
    }