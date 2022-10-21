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
     * Class BookCopy
     * @package KAASoft\Util\Database
     */
    class BookCopy extends DatabaseEntity implements JsonSerializable {

        const BOOK_COPY_STATUS_NEW      = "NEW";
        const BOOK_COPY_STATUS_GOOD     = "GOOD";
        const BOOK_COPY_STATUS_REPAIRED = "REPAIRED";
        const BOOK_COPY_STATUS_DAMAGED  = "DAMAGED";
        /**
         * @var Book
         */
        private $book;

        private $issueStatus;
        private $bookId;
        private $bookSN;
        private $status;

        /**
         * BookCopy constructor.
         * @param null $id
         */
        public function __construct($id = null) {
            parent::__construct($id);
        }

        public static function getBookStatuses() {
            return [ BookCopy::BOOK_COPY_STATUS_NEW      => _("New"),
                     BookCopy::BOOK_COPY_STATUS_GOOD     => _("Good"),
                     BookCopy::BOOK_COPY_STATUS_REPAIRED => _("Repaired"),
                     BookCopy::BOOK_COPY_STATUS_DAMAGED  => _("Damaged") ];
        }

        /**
         * @return array
         */
        public function getDatabaseArray() {
            return [ "bookId"      => $this->bookId,
                     "bookSN"      => $this->bookSN,
                     "issueStatus" => $this->issueStatus,
                     "status"      => $this->status ];
        }

        /**
         * @param array $databaseArray
         * @return BookCopy to restore form databaseArray
         */
        public static function getObjectInstance(array $databaseArray) {
            $bookCopy = new BookCopy(ValidationHelper::getNullableInt($databaseArray["id"]));
            $bookCopy->setBookId(ValidationHelper::getInt($databaseArray["bookId"]));
            $bookCopy->setBookSN(ValidationHelper::getString($databaseArray["bookSN"]));
            $bookCopy->setStatus(ValidationHelper::getString($databaseArray["status"]));
            $bookCopy->setIssueStatus(ValidationHelper::getString($databaseArray["issueStatus"]));

            return $bookCopy;
        }

        /**
         * (PHP 5 &gt;= 5.4.0)<br/>
         * Specify data which should be serialized to JSON
         * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
         * @return mixed data which can be serialized by <b>json_encode</b>,
         * which is a value of any type other than a resource.
         */
        function jsonSerialize() {
            return array_merge($this->getDatabaseArray(),
                               [ "id" => $this->id ]);
        }

        public static function getDatabaseFieldNames() {
            return [ KAASoftDatabase::$BOOK_COPIES_TABLE_NAME . ".bookId",
                     KAASoftDatabase::$BOOK_COPIES_TABLE_NAME . ".bookSN",
                     KAASoftDatabase::$BOOK_COPIES_TABLE_NAME . ".issueStatus",
                     KAASoftDatabase::$BOOK_COPIES_TABLE_NAME . ".status" ];
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
         * @return mixed
         */
        public function getIssueStatus() {
            return $this->issueStatus;
        }

        /**
         * @param mixed $issueStatus
         */
        public function setIssueStatus($issueStatus) {
            $this->issueStatus = $issueStatus;
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
    }