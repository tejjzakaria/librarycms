<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    /**
     * Created by KAA Soft.
     * Date: 2018-06-15
     */


    namespace SocialConnect\SMS\Entity;

    /**
     * Class TextLocalResult
     * @package SocialConnect\SMS\Entity
     */
    class TextLocalResult extends SmsResult {

        private $balance;
        private $cost;
        private $numberOfMessages;
        private $batchId;
        private $message;
        private $messages;
        private $isSuccess;

        private $errorCode;
        private $errorMessage;

        /**
         * TextLocalResult constructor.
         * @param int|string $response
         */
        public function __construct($response) {
            parent::__construct($response["batch_id"]);
            $this->balance = $response["balance"];
            $this->cost = $response["cost"];
            $this->numberOfMessages = $response["num_message"];
            $this->batchId = $response["batch_id"];
            $this->message = $response["message"];
            $this->messages = $response["messages"];
            $this->isSuccess = strcmp($response["status"],
                                      "success") == 0;
        }

        /**
         * @return mixed
         */
        public function getBalance() {
            return $this->balance;
        }

        /**
         * @param mixed $balance
         */
        public function setBalance($balance) {
            $this->balance = $balance;
        }

        /**
         * @return mixed
         */
        public function getCost() {
            return $this->cost;
        }

        /**
         * @param mixed $cost
         */
        public function setCost($cost) {
            $this->cost = $cost;
        }

        /**
         * @return mixed
         */
        public function getNumberOfMessages() {
            return $this->numberOfMessages;
        }

        /**
         * @param mixed $numberOfMessages
         */
        public function setNumberOfMessages($numberOfMessages) {
            $this->numberOfMessages = $numberOfMessages;
        }

        /**
         * @return mixed
         */
        public function getBatchId() {
            return $this->batchId;
        }

        /**
         * @param mixed $batchId
         */
        public function setBatchId($batchId) {
            $this->batchId = $batchId;
        }

        /**
         * @return mixed
         */
        public function getMessage() {
            return $this->message;
        }

        /**
         * @param mixed $message
         */
        public function setMessage($message) {
            $this->message = $message;
        }

        /**
         * @return mixed
         */
        public function getMessages() {
            return $this->messages;
        }

        /**
         * @param mixed $messages
         */
        public function setMessages($messages) {
            $this->messages = $messages;
        }

        /**
         * @return boolean
         */
        public function isSuccess() {
            return $this->isSuccess;
        }

        /**
         * @param boolean $isSuccess
         */
        public function setIsSuccess($isSuccess) {
            $this->isSuccess = $isSuccess;
        }

        /**
         * @return mixed
         */
        public function getErrorCode() {
            return $this->errorCode;
        }

        /**
         * @param mixed $errorCode
         */
        public function setErrorCode($errorCode) {
            $this->errorCode = $errorCode;
        }

        /**
         * @return mixed
         */
        public function getErrorMessage() {
            return $this->errorMessage;
        }

        /**
         * @param mixed $errorMessage
         */
        public function setErrorMessage($errorMessage) {
            $this->errorMessage = $errorMessage;
        }
    }