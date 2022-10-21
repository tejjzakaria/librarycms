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
     * Class Review
     * @package KAASoft\Database\Entity\General
     */
    class Review extends DatabaseEntity implements JsonSerializable {
        /**
         * @var User
         */
        private $user;
        /**
         * @var Book
         */
        private $book;
        /**
         * @var float
         */
        private $bookRating;

        private $bookId;
        private $userId;
        private $text;
        private $email;
        private $name;
        private $isPublish;
        private $creationDateTime;

        /**
         * Review constructor.
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
                               [ "userId"           => $this->userId,
                                 "bookId"           => $this->bookId,
                                 "text"             => $this->text,
                                 "email"            => $this->email,
                                 "name"             => $this->name,
                                 "isPublish"        => $this->isPublish,
                                 "creationDateTime" => $this->creationDateTime ]);
        }

        /**
         * @param array $databaseArray
         * @return Review to restore form databaseArray
         */
        public static function getObjectInstance(array $databaseArray) {
            $review = new Review(ValidationHelper::getNullableInt($databaseArray["id"]));
            $review->setUserId(ValidationHelper::getNullableInt($databaseArray["userId"]));
            $review->setBookId(ValidationHelper::getNullableInt($databaseArray["bookId"]));
            $review->setText(ValidationHelper::getString($databaseArray["text"]));
            $review->setEmail(ValidationHelper::getString($databaseArray["email"]));
            $review->setName(ValidationHelper::getString($databaseArray["name"]));
            $review->setIsPublish(ValidationHelper::getBool($databaseArray["isPublish"]));
            $review->setCreationDateTime(ValidationHelper::getString($databaseArray["creationDateTime"]));

            return $review;
        }


        /**
         * @return array
         */
        public static function getDatabaseFieldNames() {
            return array_merge(parent::getDatabaseFieldNames(),
                               [ KAASoftDatabase::$REVIEWS_TABLE_NAME . ".userId",
                                 KAASoftDatabase::$REVIEWS_TABLE_NAME . ".bookId",
                                 KAASoftDatabase::$REVIEWS_TABLE_NAME . ".text",
                                 KAASoftDatabase::$REVIEWS_TABLE_NAME . ".email",
                                 KAASoftDatabase::$REVIEWS_TABLE_NAME . ".name",
                                 KAASoftDatabase::$REVIEWS_TABLE_NAME . ".isPublish",
                                 KAASoftDatabase::$REVIEWS_TABLE_NAME . ".creationDateTime" ]);
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
        public function getText() {
            return $this->text;
        }

        /**
         * @param mixed $text
         */
        public function setText($text) {
            $this->text = $text;
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
        public function isPublish() {
            return $this->isPublish;
        }

        /**
         * @param mixed $isPublish
         */
        public function setIsPublish($isPublish) {
            $this->isPublish = $isPublish;
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
         * @return float
         */
        public function getBookRating() {
            return $this->bookRating;
        }

        /**
         * @param float $bookRating
         */
        public function setBookRating($bookRating) {
            $this->bookRating = $bookRating;
        }
    }