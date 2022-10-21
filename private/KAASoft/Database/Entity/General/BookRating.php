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
     * Class BookRating
     * @package KAASoft\Database\Entity\General
     */
    class BookRating extends DatabaseEntity implements JsonSerializable {

        private $bookId;
        private $userId;
        private $rating;
        private $updateDateTime;
        private $creationDateTime;

        /**
         * BookRating constructor.
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
                               [ "bookId"           => $this->bookId,
                                 "userId"           => $this->userId,
                                 "rating"           => $this->rating,
                                 "updateDateTime"   => $this->updateDateTime,
                                 "creationDateTime" => $this->creationDateTime ]);
        }

        /**
         * @param array $databaseArray
         * @return BookRating to restore form databaseArray
         */
        public static function getObjectInstance(array $databaseArray) {
            $bookRating = new BookRating(ValidationHelper::getNullableInt($databaseArray["id"]));
            $bookRating->setBookId(ValidationHelper::getNullableInt($databaseArray["bookId"]));
            $bookRating->setUserId(ValidationHelper::getNullableInt($databaseArray["userId"]));
            $bookRating->setRating(ValidationHelper::getNullableInt($databaseArray["rating"]));
            $bookRating->setUpdateDateTime(ValidationHelper::getString($databaseArray["updateDateTime"]));
            $bookRating->setCreationDateTime(ValidationHelper::getString($databaseArray["creationDateTime"]));

            return $bookRating;
        }


        /**
         * @return array
         */
        public static function getDatabaseFieldNames() {
            return array_merge(parent::getDatabaseFieldNames(),
                               [ KAASoftDatabase::$BOOK_RATINGS_TABLE_NAME . ".bookId",
                                 KAASoftDatabase::$BOOK_RATINGS_TABLE_NAME . ".userId",
                                 KAASoftDatabase::$BOOK_RATINGS_TABLE_NAME . ".rating",
                                 KAASoftDatabase::$BOOK_RATINGS_TABLE_NAME . ".updateDateTime",
                                 KAASoftDatabase::$BOOK_RATINGS_TABLE_NAME . ".creationDateTime" ]);
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
        public function getRating() {
            return $this->rating;
        }

        /**
         * @param mixed $rating
         */
        public function setRating($rating) {
            $this->rating = $rating;
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
        public function getCreationDateTime() {
            return $this->creationDateTime;
        }

        /**
         * @param mixed $creationDateTime
         */
        public function setCreationDateTime($creationDateTime) {
            $this->creationDateTime = $creationDateTime;
        }
    }