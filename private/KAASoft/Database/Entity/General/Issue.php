<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Database\Entity\General;


    use DateTime;
    use JsonSerializable;
    use KAASoft\Database\Entity\DatabaseEntity;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\Helper;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class Issue
     * @package KAASoft\Database\Entity\General
     */
    class Issue extends DatabaseEntity implements JsonSerializable {
        const ISSUE_STATUS_ISSUED    = "ISSUED";
        const ISSUE_STATUS_RETURNED  = "RETURNED";
        const ISSUE_STATUS_AVAILABLE = "AVAILABLE";
        const ISSUE_STATUS_LOST      = "LOST";

        /**
         * @var User
         */
        private $user;
        /**
         * @var BookCopy
         */
        private $bookCopy;
        /**
         * @var Book
         */
        private $book;
        /**
         * @var Request
         */
        private $request;
        /**
         * @var bool
         */
        private $isExpired = false;

        private $userId;
        private $bookId;
        private $bookCopyId;
        private $requestId;
        private $issueDate;
        private $expiryDate;
        private $returnDate;
        private $description;
        private $isLost;
        private $penalty;

        /**
         * Issue constructor.
         * @param null $id
         */
        function __construct($id = null) {
            parent::__construct($id);
        }

        public static function getIssueStatuses() {
            return [ Issue::ISSUE_STATUS_AVAILABLE => _("Available"),
                     Issue::ISSUE_STATUS_ISSUED    => _("Issued"),
                     Issue::ISSUE_STATUS_RETURNED  => _("Returned"),
                     Issue::ISSUE_STATUS_LOST      => _("Lost") ];
        }

        /**
         * @return array
         */
        public function getDatabaseArray() {
            return array_merge(parent::getDatabaseArray(),
                               [ "userId"     => $this->userId,
                                 "bookId"     => $this->bookId,
                                 "bookCopyId" => $this->bookCopyId,
                                 "requestId"  => $this->requestId,
                                 "issueDate"  => $this->issueDate,
                                 "expiryDate" => $this->expiryDate,
                                 "returnDate" => $this->returnDate,
                                 "isLost"     => $this->isLost,
                                 "penalty"    => $this->penalty ]);
        }

        /**
         * @param array $databaseArray
         * @return Issue to restore form databaseArray
         */
        public static function getObjectInstance(array $databaseArray) {
            $issue = new Issue(ValidationHelper::getNullableInt($databaseArray["id"]));
            $issue->setUserId(ValidationHelper::getNullableInt($databaseArray["userId"]));
            $issue->setBookId(ValidationHelper::getNullableInt($databaseArray["bookId"]));
            $issue->setBookCopyId(ValidationHelper::getNullableInt($databaseArray["bookCopyId"]));
            $issue->setRequestId(ValidationHelper::getNullableInt($databaseArray["requestId"]));
            $issue->setIssueDate(ValidationHelper::getString($databaseArray["issueDate"]));
            $issue->setExpiryDate(ValidationHelper::getString($databaseArray["expiryDate"]));
            $issue->setReturnDate(ValidationHelper::getString($databaseArray["returnDate"]));
            $issue->setIsLost(ValidationHelper::getBool($databaseArray["isLost"]));
            $issue->setPenalty(ValidationHelper::getFloat($databaseArray["penalty"]));

            return $issue;
        }


        /**
         * @return array
         */
        public static function getDatabaseFieldNames() {
            return array_merge(parent::getDatabaseFieldNames(),
                               [ KAASoftDatabase::$ISSUES_TABLE_NAME . ".bookId",
                                 KAASoftDatabase::$ISSUES_TABLE_NAME . ".bookCopyId",
                                 KAASoftDatabase::$ISSUES_TABLE_NAME . ".userId",
                                 KAASoftDatabase::$ISSUES_TABLE_NAME . ".requestId",
                                 KAASoftDatabase::$ISSUES_TABLE_NAME . ".issueDate",
                                 KAASoftDatabase::$ISSUES_TABLE_NAME . ".expiryDate",
                                 KAASoftDatabase::$ISSUES_TABLE_NAME . ".returnDate",
                                 KAASoftDatabase::$ISSUES_TABLE_NAME . ".isLost",
                                 KAASoftDatabase::$ISSUES_TABLE_NAME . ".penalty" ]);
        }

        /**
         * @param $user  User
         * @param $issue Issue
         * @return int|mixed
         */
        public static function calculatePenalty($user, $issue) {
            $penalty = 0;

            $userRole = $user->getRole();

            $finePerDay = $userRole->getFinePerDay();

            $expiryDateString = $issue->getExpiryDate();
            $returnDateString = $issue->getReturnDate();

            $expiryDate = Helper::getDateTimeFromString($expiryDateString,
                                                        date_default_timezone_get());

            $returnDate = Helper::getDateTimeFromString($returnDateString,
                                                        date_default_timezone_get());
            // if book is already returned
            if ($returnDate !== null and $returnDate > $expiryDate) {
                return $returnDate->diff($expiryDate)->days * $finePerDay;
            }

            $today = new DateTime();
            // if book is not returned yet but expired
            if ($today > $expiryDate and $returnDate === null) {
                return $today->diff($expiryDate)->days * $finePerDay;
            }


            return $penalty;
        }

        /**
         * @param $issue Issue
         * @return bool
         */
        public static function isIssueExpired($issue) {
            $expiryDateString = $issue->getExpiryDate();
            $returnDateString = $issue->getReturnDate();

            $expiryDate = Helper::getDateTimeFromString($expiryDateString,
                                                        date_default_timezone_get());

            $returnDate = Helper::getDateTimeFromString($returnDateString,
                                                        date_default_timezone_get());

            $today = new DateTime();
            // if book is not returned yet but expired
            if ($today > $expiryDate and $returnDate === null) {
                return true;
            }

            return false;
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
        public function getDescription() {
            return $this->description;
        }

        /**
         * @param mixed $description
         */
        public function setDescription($description) {
            $this->description = $description;
        }

        /**
         * @return User
         */
        public function getUser() {
            return $this->user;
        }

        /**
         * @param User $user
         */
        public function setUser($user) {
            $this->user = $user;
        }

        /**
         * @return Book
         */
        public function getBook() {
            return $this->book;
        }

        /**
         * @param Book $book
         */
        public function setBook($book) {
            $this->book = $book;
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
         * @return Request
         */
        public function getRequest() {
            return $this->request;
        }

        /**
         * @param Request $request
         */
        public function setRequest($request) {
            $this->request = $request;
        }

        /**
         * @return boolean
         */
        public function isExpired() {
            return $this->isExpired;
        }

        /**
         * @param boolean $isExpired
         */
        public function setIsExpired($isExpired) {
            $this->isExpired = $isExpired;
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
         * @return BookCopy
         */
        public function getBookCopy() {
            return $this->bookCopy;
        }

        /**
         * @param BookCopy $bookCopy
         */
        public function setBookCopy($bookCopy) {
            $this->bookCopy = $bookCopy;
        }
    }