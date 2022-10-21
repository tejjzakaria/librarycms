<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Util;

    /**
     * Class Message
     * @package KAASoft\Util
     */
    class Message {

        const MESSAGE_STATUS_INFO    = "INFO";
        const MESSAGE_STATUS_ERROR   = "ERROR";
        const MESSAGE_STATUS_WARNING = "WARNING";
        const MESSAGE_STATUS_SUCCESS = "SUCCESS";

        private $message;
        private $status;
        /**
         * @var int
         */
        private $errorCode;

        /**
         * @param string $message
         * @param string $status
         */
        function __construct($message, $status = Message::MESSAGE_STATUS_INFO) {
            $this->message = $message;
            $this->status = $status;
        }

        /**
         * @return string
         */
        public function getMessage() {
            return $this->message;
        }

        /**
         * @return string
         */
        public function getStatus() {
            return $this->status;
        }

        /**
         * @return int
         */
        public function getErrorCode() {
            return $this->errorCode;
        }

        /**
         * @param int $errorCode
         */
        public function setErrorCode($errorCode) {
            $this->errorCode = $errorCode;
        }
    }