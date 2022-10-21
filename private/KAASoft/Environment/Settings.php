<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment;

    use JsonSerializable;

    /**
     * Interface Settings
     * @package KAASoft\Environment
     */
    interface Settings extends JsonSerializable {
        /**
         * Loads data from config file
         */
        public function loadSettings();

        /**
         * Saves data to config file
         */
        public function saveSettings();

        /**
         * Returns config file to load/store settings
         * @return string
         */
        public function getConfigFileName();

        /**
         * Sets default settings
         */
        public function setDefaultSettings();
    }