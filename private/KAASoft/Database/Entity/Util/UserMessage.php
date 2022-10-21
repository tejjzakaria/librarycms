<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Database\Entity\Util;


    use KAASoft\Database\Entity\DatabaseEntity;
    use KAASoft\Database\KAASoftDatabase;

    /**
     * Class UserMessage
     * @package KAASoft\Database\Entity\Util
     */
    class UserMessage extends DatabaseEntity {
        private $name;
        private $email;
        private $subject;
        private $message;
        private $isViewed;
        private $creationDate;

        public function __construct($id = null) {
            parent::__construct($id);
        }

        /**
         * @return array
         */
        public function getDatabaseArray() {
            return array_merge(parent::getDatabaseArray(),
                               [ 'name'         => $this->name,
                                 'email'        => $this->email,
                                 'subject'      => $this->subject,
                                 'message'      => $this->message,
                                 'creationDate' => $this->creationDate,
                                 'isViewed'     => $this->isViewed ]);
        }

        /**
         * @param array $databaseArray
         * @return UserMessage to restore form databaseArray
         */
        public static function getObjectInstance(array $databaseArray) {
            $userMessage = new UserMessage(isset( $databaseArray["id"] ) ? $databaseArray["id"] : null);
            $userMessage->setName(isset( $databaseArray['name'] ) ? $databaseArray['name'] : null);
            $userMessage->setEmail(isset( $databaseArray['email'] ) ? $databaseArray['email'] : null);
            $userMessage->setSubject(isset( $databaseArray['subject'] ) ? $databaseArray['subject'] : null);
            $userMessage->setMessage(isset( $databaseArray['message'] ) ? $databaseArray['message'] : null);
            $userMessage->setCreationDate(isset( $databaseArray['creationDate'] ) ? $databaseArray['creationDate'] : null);
            $userMessage->setIsViewed(isset( $databaseArray['isViewed'] ) ? $databaseArray['isViewed'] : false);

            return $userMessage;
        }

        /**
         * @return array
         */
        public static function getDatabaseFieldNames() {
            return [ KAASoftDatabase::$USER_MESSAGES_TABLE_NAME . '.name',
                     KAASoftDatabase::$USER_MESSAGES_TABLE_NAME . '.email',
                     KAASoftDatabase::$USER_MESSAGES_TABLE_NAME . '.subject',
                     KAASoftDatabase::$USER_MESSAGES_TABLE_NAME . '.message',
                     KAASoftDatabase::$USER_MESSAGES_TABLE_NAME . '.creationDate',
                     KAASoftDatabase::$USER_MESSAGES_TABLE_NAME . '.isViewed' ];
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
        public function getCreationDate() {
            return $this->creationDate;
        }

        /**
         * @param mixed $creationDate
         */
        public function setCreationDate($creationDate) {
            $this->creationDate = $creationDate;
        }

        /**
         * @return mixed
         */
        public function getSubject() {
            return $this->subject;
        }

        /**
         * @param mixed $subject
         */
        public function setSubject($subject) {
            $this->subject = $subject;
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
        public function isViewed() {
            return $this->isViewed;
        }

        /**
         * @param mixed $isViewed
         */
        public function setIsViewed($isViewed) {
            $this->isViewed = $isViewed;
        }
    }