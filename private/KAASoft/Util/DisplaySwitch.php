<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Util;

    /**
     * Class DisplaySwitch
     * @package KAASoft\Util
     */
    class DisplaySwitch {
        /**
         * @var array
         */
        private $ajaxResult = null;

        /**
         * @var string
         */
        private $smartyTemplate;

        /**
         * @var string
         */
        private $redirectURL;

        /**
         * @var bool
         */
        private $isErrorOccurred;

        /**
         * DisplaySwitch constructor.
         * @param      $smartyTemplate
         * @param      $redirect
         * @param bool $isErrorOccurred
         */
        public function __construct($smartyTemplate = null, $redirect = null, $isErrorOccurred = false) {
            $this->smartyTemplate = $smartyTemplate;
            $this->redirectURL = $redirect;
            $this->isErrorOccurred = $isErrorOccurred;
        }

        /**
         * @return string
         */
        public function getSmartyTemplate() {
            return $this->smartyTemplate;
        }

        /**
         * @param string $smartyTemplate
         */
        public function setSmartyTemplate($smartyTemplate) {
            $this->smartyTemplate = $smartyTemplate;
        }

        /**
         * @return string
         */
        public function getRedirectURL() {
            return $this->redirectURL;
        }

        /**
         * @param string $redirectURL
         */
        public function setRedirectURL($redirectURL) {
            $this->redirectURL = $redirectURL;
        }

        /**
         * @return boolean
         */
        public function isErrorOccurred() {
            return $this->isErrorOccurred;
        }

        /**
         * @param boolean $isErrorOccurred
         */
        public function setIsErrorOccurred($isErrorOccurred) {
            $this->isErrorOccurred = $isErrorOccurred;
        }

        public function addAjaxResult($key, $value) {
            if ($this->ajaxResult == null) {
                $this->ajaxResult = [];
            }
            $this->ajaxResult[$key] = $value;
        }

        /**
         * @return array
         */
        public function getAjaxResult() {
            return $this->ajaxResult;
        }
    }