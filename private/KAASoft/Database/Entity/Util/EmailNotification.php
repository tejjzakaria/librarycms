<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Database\Entity\Util;

    use KAASoft\Database\Entity\DatabaseEntity;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\EmailAddress;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class EmailNotification
     * @package KAASoft\Database\Entity\Util
     */
    class EmailNotification extends DatabaseEntity {
        private $route;
        private $userId;
        private $subject;
        private $content;
        private $templateName;
        /**
         * @var EmailAddress
         */
        private $from;
        /**
         * @var array
         */
        private $to;
        private $creationDateTime;
        private $updateDateTime;
        private $isEnabled;

        /**
         * EmailNotification constructor.
         * @param null $id
         */
        public function __construct($id = null) {
            parent::__construct($id);
        }

        /**
         * @return array
         */
        public function getDatabaseArray() {
            return array_merge(parent::getDatabaseArray(),
                               [ 'userId'           => $this->userId,
                                 'route'            => $this->route,
                                 'content'          => $this->content,
                                 'subject'          => $this->subject,
                                 'templateName'     => $this->templateName,
                                 'from'             => $this->from,
                                 'to'               => $this->to,
                                 'updateDateTime'   => $this->updateDateTime,
                                 'creationDateTime' => $this->creationDateTime,
                                 'isEnabled'        => $this->isEnabled ]);
        }

        /**
         * @param array $databaseArray
         * @return EmailNotification to restore form databaseArray
         */
        public static function getObjectInstance(array $databaseArray) {
            $emailNotification = new EmailNotification(ValidationHelper::getNullableInt($databaseArray["id"]));
            $emailNotification->setUserId(ValidationHelper::getString($databaseArray['userId']));
            $emailNotification->setRoute(ValidationHelper::getString($databaseArray['route']));
            $emailNotification->setSubject(ValidationHelper::getString($databaseArray['subject']));
            $emailNotification->setContent(ValidationHelper::getString($databaseArray['content']));
            $emailNotification->setTemplateName(ValidationHelper::getString($databaseArray['templateName']));
            $emailNotification->setFrom(ValidationHelper::getString($databaseArray['from']));
            $emailNotification->setTo(ValidationHelper::getString($databaseArray['to']));
            $emailNotification->setCreationDateTime(ValidationHelper::getString($databaseArray['creationDateTime']));
            $emailNotification->setUpdateDateTime(ValidationHelper::getString($databaseArray['updateDateTime']));
            $emailNotification->setIsEnabled(ValidationHelper::getBool($databaseArray['isEnabled']));

            return $emailNotification;
        }

        public static function getDatabaseFieldNames() {
            return [ KAASoftDatabase::$EMAIL_NOTIFICATIONS_TABLE_NAME . ".userId",
                     KAASoftDatabase::$EMAIL_NOTIFICATIONS_TABLE_NAME . ".route",
                     KAASoftDatabase::$EMAIL_NOTIFICATIONS_TABLE_NAME . ".subject",
                     KAASoftDatabase::$EMAIL_NOTIFICATIONS_TABLE_NAME . ".content",
                     KAASoftDatabase::$EMAIL_NOTIFICATIONS_TABLE_NAME . ".templateName",
                     KAASoftDatabase::$EMAIL_NOTIFICATIONS_TABLE_NAME . ".from",
                     KAASoftDatabase::$EMAIL_NOTIFICATIONS_TABLE_NAME . ".creationDateTime",
                     KAASoftDatabase::$EMAIL_NOTIFICATIONS_TABLE_NAME . ".updateDateTime",
                     KAASoftDatabase::$EMAIL_NOTIFICATIONS_TABLE_NAME . ".to",
                     KAASoftDatabase::$EMAIL_NOTIFICATIONS_TABLE_NAME . ".isEnabled" ];
        }

        /**
         * @return mixed
         */
        public function getTemplateName() {
            return $this->templateName;
        }

        /**
         * @param mixed $templateName
         */
        public function setTemplateName($templateName) {
            $this->templateName = $templateName;
        }

        /**
         * @return mixed
         */
        public function getUserId() {
            return $this->userId;
        }

        /**
         * @param mixed $userId
         */
        public function setUserId($userId) {
            $this->userId = $userId;
        }

        /**
         * @return mixed
         */
        public function getContent() {
            return $this->content;
        }

        /**
         * @param mixed $content
         */
        public function setContent($content) {
            $this->content = $content;
        }

        /**
         * @return mixed
         */
        public function getCreationDateTime() {
            return $this->creationDateTime;
        }

        /**
         * @param mixed $creationDateTime
         */
        public function setCreationDateTime($creationDateTime) {
            $this->creationDateTime = $creationDateTime;
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
        public function getFrom() {
            return $this->from;
        }

        /**
         * @param mixed $from
         */
        public function setFrom($from) {
            $this->from = $from;
        }

        /**
         * @return mixed
         */
        public function getTo() {
            return $this->to;
        }

        /**
         * @param mixed $to
         */
        public function setTo($to) {
            $this->to = $to;
        }

        /**
         * @return mixed
         */
        public function getUpdateDateTime() {
            return $this->updateDateTime;
        }

        /**
         * @param mixed $updateDateTime
         */
        public function setUpdateDateTime($updateDateTime) {
            $this->updateDateTime = $updateDateTime;
        }

        /**
         * @return mixed
         */
        public function isEnabled() {
            return $this->isEnabled;
        }

        /**
         * @param mixed $isEnabled
         */
        public function setIsEnabled($isEnabled) {
            $this->isEnabled = $isEnabled;
        }

        /**
         * @return mixed
         */
        public function getRoute() {
            return $this->route;
        }

        /**
         * @param mixed $route
         */
        public function setRoute($route) {
            $this->route = $route;
        }
    }