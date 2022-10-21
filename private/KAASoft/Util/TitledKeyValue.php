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
     * Class TitledKeyValue
     * @package KAASoft\Util
     */
    class TitledKeyValue extends KeyValue {
        protected $title;

        /**
         * TitledKeyValue constructor.
         * @param $title
         * @param $key
         * @param $value
         */
        public function __construct($title, $key, $value) {
            parent::__construct($key,
                                $value);
            $this->title = $title;
        }

        /**
         * @return mixed
         */
        public function getTitle() {
            return $this->title;
        }

        /**
         * @param mixed $title
         */
        public function setTitle($title) {
            $this->title = $title;
        }
    }