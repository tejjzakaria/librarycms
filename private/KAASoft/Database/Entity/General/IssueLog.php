<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Database\Entity\General;


    use JsonSerializable;
    use KAASoft\Database\Entity\DatabaseEntity;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class Issue
     * @package KAASoft\Database\Entity\General
     */
    class IssueLog extends DatabaseEntity implements JsonSerializable {

        private $userId;
        private $userFullName;
        private $bookId;
        private $bookCopyId;
        private $bookTitle;
        private $bookISBN;
        private $bookSN;
        private $requestId;
        private $requestStatus;
        private $requestNotes;
        private $requestDateTime;
        private $requestAcceptRejectDateTime;
        private $issueId;
        private $issueDate;
        private $expiryDate;
        private $returnDate;
        private $isLost;
        private $isIssueDeleted;
        private $penalty;
        private $updateDateTime;

        /**
         * Issue constructor.
         * @param null $id
         */
        function __construct($id = null) {
            parent::__construct($id);
        }

        /**
         * @return array
         */
        public function getDatabaseArray() {
            return array_merge(parent::getDatabaseArray(),
                               [ "userId"                      => $this->userId,
                                 "userFullName"                => $this->userFullName,
                                 "bookId"                      => $this->bookId,
                                 "bookCopyId"                  => $this->bookCopyId,
                                 "bookTitle"                   => $this->bookTitle,
                                 "bookISBN"                    => $this->bookISBN,
                                 "bookSN"                      => $this->bookSN,
                                 "requestId"                   => $this->requestId,
                                 "requestStatus"               => $this->requestStatus,
                                 "requestNotes"                => $this->requestNotes,
                                 "requestDateTime"             => $this->requestDateTime,
                                 "requestAcceptRejectDateTime" => $this->requestAcceptRejectDateTime,
                                 "issueId"                     => $this->issueId,
                                 "issueDate"                   => $this->issueDate,
                                 "expiryDate"                  => $this->expiryDate,
                                 "returnDate"                  => $this->returnDate,
                                 "isLost"                      => $this->isLost,
                                 "isIssueDeleted"                      => $this->isIssueDeleted,
                                 "penalty"                     => $this->penalty,
                                 "updateDateTime"              => $this->updateDateTime ]);
        }

        /**
         * @param array $databaseArray
         * @return IssueLog to restore form databaseArray
         */
        public static function getObjectInstance(array $databaseArray) {
            $issueLog = new IssueLog(ValidationHelper::getNullableInt($databaseArray["id"]));
            $issueLog->setUserId(ValidationHelper::getNullableInt($databaseArray["userId"]));
            $issueLog->setUserFullName(ValidationHelper::getString($databaseArray["userFullName"]));
            $issueLog->setBookId(ValidationHelper::getNullableInt($databaseArray["bookId"]));
            $issueLog->setBookCopyId(ValidationHelper::getNullableInt($databaseArray["bookCopyId"]));
            $issueLog->setBookTitle(ValidationHelper::getString($databaseArray["bookTitle"]));
            $issueLog->setBookISBN(ValidationHelper::getString($databaseArray["bookISBN"]));
            $issueLog->setBookSN(ValidationHelper::getString($databaseArray["bookSN"]));
            $issueLog->setRequestId(ValidationHelper::getNullableInt($databaseArray["requestId"]));
            $issueLog->setRequestStatus(ValidationHelper::getString($databaseArray["requestStatus"]));
            $issueLog->setRequestNotes(ValidationHelper::getString($databaseArray["requestNotes"]));
            $issueLog->setRequestDateTime(ValidationHelper::getString($databaseArray["requestDateTime"]));
            $issueLog->setRequestAcceptRejectDateTime(ValidationHelper::getString($databaseArray["requestAcceptRejectDateTime"]));
            $issueLog->setIssueId(ValidationHelper::getString($databaseArray["issueId"]));
            $issueLog->setIssueDate(ValidationHelper::getString($databaseArray["issueDate"]));
            $issueLog->setExpiryDate(ValidationHelper::getString($databaseArray["expiryDate"]));
            $issueLog->setReturnDate(ValidationHelper::getString($databaseArray["returnDate"]));
            $issueLog->setIsLost(ValidationHelper::getBool($databaseArray["isLost"]));
            $issueLog->setIsIssueDeleted(ValidationHelper::getBool($databaseArray["isIssueDeleted"]));
            $issueLog->setPenalty(ValidationHelper::getFloat($databaseArray["penalty"]));
            $issueLog->setUpdateDateTime(ValidationHelper::getString($databaseArray["updateDateTime"]));

            return $issueLog;
        }


        /**
         * @return array
         */
        public static function getDatabaseFieldNames() {
            return array_merge(parent::getDatabaseFieldNames(),
                               [ KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME . ".bookId",
                                 KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME . ".bookCopyId",
                                 KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME . ".bookTitle",
                                 KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME . ".bookISBN",
                                 KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME . ".bookSN",
                                 KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME . ".userId",
                                 KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME . ".userFullName",
                                 KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME . ".requestId",
                                 KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME . ".requestStatus",
                                 KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME . ".requestNotes",
                                 KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME . ".requestDateTime",
                                 KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME . ".requestAcceptRejectDateTime",
                                 KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME . ".issueId",
                                 KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME . ".issueDate",
                                 KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME . ".expiryDate",
                                 KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME . ".returnDate",
                                 KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME . ".isLost",
                                 KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME . ".isIssueDeleted",
                                 KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME . ".penalty",
                                 KAASoftDatabase::$ISSUE_LOGS_TABLE_NAME . ".updateDateTime" ]);
        }

        /**
         * (PHP 5 &gt;= 5.4.0)<br/>
         * Specify data which should be serialized to JSON
         * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
         * @return mixed data which can be serialized by <b>json_encode</b>,
         * which is a value of any type other than a resource.
         */
        function jsonSerialize() {
            return $this->getDatabaseArray();
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
        public function getUserFullName() {
            return $this->userFullName;
        }

        /**
         * @param mixed $userFullName
         */
        public function setUserFullName($userFullName) {
            $this->userFullName = $userFullName;
        }

        /**
         * @return mixed
         */
        public function getBookId() {
            return $this->bookId;
        }

        /**
         * @param mixed $bookId
         */
        public function setBookId($bookId) {
            $this->bookId = $bookId;
        }

        /**
         * @return mixed
         */
        public function getBookTitle() {
            return $this->bookTitle;
        }

        /**
         * @param mixed $bookTitle
         */
        public function setBookTitle($bookTitle) {
            $this->bookTitle = $bookTitle;
        }

        /**
         * @return mixed
         */
        public function getBookISBN() {
            return $this->bookISBN;
        }

        /**
         * @param mixed $bookISBN
         */
        public function setBookISBN($bookISBN) {
            $this->bookISBN = $bookISBN;
        }

        /**
         * @return mixed
         */
        public function getBookSN() {
            return $this->bookSN;
        }

        /**
         * @param mixed $bookSN
         */
        public function setBookSN($bookSN) {
            $this->bookSN = $bookSN;
        }

        /**
         * @return mixed
         */
        public function getRequestId() {
            return $this->requestId;
        }

        /**
         * @param mixed $requestId
         */
        public function setRequestId($requestId) {
            $this->requestId = $requestId;
        }

        /**
         * @return mixed
         */
        public function getRequestStatus() {
            return $this->requestStatus;
        }

        /**
         * @param mixed $requestStatus
         */
        public function setRequestStatus($requestStatus) {
            $this->requestStatus = $requestStatus;
        }

        /**
         * @return mixed
         */
        public function getRequestNotes() {
            return $this->requestNotes;
        }

        /**
         * @param mixed $requestNotes
         */
        public function setRequestNotes($requestNotes) {
            $this->requestNotes = $requestNotes;
        }

        /**
         * @return mixed
         */
        public function getRequestDateTime() {
            return $this->requestDateTime;
        }

        /**
         * @param mixed $requestDateTime
         */
        public function setRequestDateTime($requestDateTime) {
            $this->requestDateTime = $requestDateTime;
        }

        /**
         * @return mixed
         */
        public function getRequestAcceptRejectDateTime() {
            return $this->requestAcceptRejectDateTime;
        }

        /**
         * @param mixed $requestAcceptRejectDateTime
         */
        public function setRequestAcceptRejectDateTime($requestAcceptRejectDateTime) {
            $this->requestAcceptRejectDateTime = $requestAcceptRejectDateTime;
        }

        /**
         * @return mixed
         */
        public function getIssueId() {
            return $this->issueId;
        }

        /**
         * @param mixed $issueId
         */
        public function setIssueId($issueId) {
            $this->issueId = $issueId;
        }

        /**
         * @return mixed
         */
        public function getIssueDate() {
            return $this->issueDate;
        }

        /**
         * @param mixed $issueDate
         */
        public function setIssueDate($issueDate) {
            $this->issueDate = $issueDate;
        }

        /**
         * @return mixed
         */
        public function getExpiryDate() {
            return $this->expiryDate;
        }

        /**
         * @param mixed $expiryDate
         */
        public function setExpiryDate($expiryDate) {
            $this->expiryDate = $expiryDate;
        }

        /**
         * @return mixed
         */
        public function getReturnDate() {
            return $this->returnDate;
        }

        /**
         * @param mixed $returnDate
         */
        public function setReturnDate($returnDate) {
            $this->returnDate = $returnDate;
        }

        /**
         * @return mixed
         */
        public function isLost() {
            return $this->isLost;
        }

        /**
         * @param mixed $isLost
         */
        public function setIsLost($isLost) {
            $this->isLost = $isLost;
        }

        /**
         * @return mixed
         */
        public function getPenalty() {
            return $this->penalty;
        }

        /**
         * @param mixed $penalty
         */
        public function setPenalty($penalty) {
            $this->penalty = $penalty;
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
        public function getBookCopyId() {
            return $this->bookCopyId;
        }

        /**
         * @param mixed $bookCopyId
         */
        public function setBookCopyId($bookCopyId) {
            $this->bookCopyId = $bookCopyId;
        }

        /**
         * @return mixed
         */
        public function isIssueDeleted() {
            return $this->isIssueDeleted;
        }

        /**
         * @param mixed $isIssueDeleted
         */
        public function setIsIssueDeleted($isIssueDeleted) {
            $this->isIssueDeleted = $isIssueDeleted;
        }
    }