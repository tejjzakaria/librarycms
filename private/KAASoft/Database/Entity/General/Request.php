<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Database\Entity\General;

    use JsonSerializable;
    use KAASoft\Database\Entity\DatabaseEntity;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class Request
     * @package KAASoft\Database\Entity\General
     */
    class Request extends DatabaseEntity implements JsonSerializable {
        const REQUEST_STATUS_ACCEPTED   = "ACCEPTED";
        const REQUEST_STATUS_REJECTED   = "REJECTED";
        const REQUEST_STATUS_PENDING    = "PENDING";
        const REQUEST_STATUS_COLLECTING = "COLLECTING";
        const REQUEST_STATUS_COLLECTED  = "COLLECTED";

        /**
         * @var User
         */
        private $user;
        /**
         * @var Book
         */
        private $book;
        /**
         * @var Issue
         */
        private $issue;

        private $userId;
        private $bookId;
        private $creationDate;
        private $notes;
        private $status;

        /**
         * Request constructor.
         * @param null $id
         */
        function __construct($id = null) {
            parent::__construct($id);
        }

        public static function getRequestStatuses() {
            return [ Request::REQUEST_STATUS_ACCEPTED   => _("Accepted"),
                     Request::REQUEST_STATUS_REJECTED   => _("Rejected"),
                     Request::REQUEST_STATUS_PENDING    => _("Pending"),
                     Request::REQUEST_STATUS_COLLECTING => _("Collecting"),
                     Request::REQUEST_STATUS_COLLECTED  => _("Collected") ];
        }

        /**
         * @return array
         */
        public function getDatabaseArray() {
            return array_merge(parent::getDatabaseArray(),
                               [ "userId"       => $this->userId,
                                 "bookId"       => $this->bookId,
                                 "creationDate" => $this->creationDate,
                                 "notes"        => $this->notes,
                                 "status"       => $this->status ]);
        }

        /**
         * @param array $databaseArray
         * @return Request to restore form databaseArray
         */
        public static function getObjectInstance(array $databaseArray) {
            $issue = new Request(ValidationHelper::getNullableInt($databaseArray["id"]));
            $issue->setUserId(ValidationHelper::getNullableInt($databaseArray["userId"]));
            $issue->setBookId(ValidationHelper::getNullableInt($databaseArray["bookId"]));
            $issue->setCreationDate(ValidationHelper::getString($databaseArray["creationDate"]));
            $issue->setNotes(ValidationHelper::getString($databaseArray["notes"]));
            $issue->setStatus(ValidationHelper::getString($databaseArray["status"]));

            return $issue;
        }


        /**
         * @return array
         */
        public static function getDatabaseFieldNames() {
            return array_merge(parent::getDatabaseFieldNames(),
                               [ KAASoftDatabase::$REQUESTS_TABLE_NAME . ".bookId",
                                 KAASoftDatabase::$REQUESTS_TABLE_NAME . ".userId",
                                 KAASoftDatabase::$REQUESTS_TABLE_NAME . ".creationDate",
                                 KAASoftDatabase::$REQUESTS_TABLE_NAME . ".notes",
                                 KAASoftDatabase::$REQUESTS_TABLE_NAME . ".status" ]);
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
        public function getNotes() {
            return $this->notes;
        }

        /**
         * @param mixed $notes
         */
        public function setNotes($notes) {
            $this->notes = $notes;
        }

        /**
         * @return mixed
         */
        public function getStatus() {
            return $this->status;
        }

        /**
         * @param mixed $status
         */
        public function setStatus($status) {
            $this->status = $status;
        }

        /**
         * @return Issue
         */
        public function getIssue() {
            return $this->issue;
        }

        /**
         * @param Issue $issue
         */
        public function setIssue($issue) {
            $this->issue = $issue;
        }
    }