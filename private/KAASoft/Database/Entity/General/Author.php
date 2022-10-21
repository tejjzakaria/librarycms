<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Database\Entity\General;

    use JsonSerializable;
    use KAASoft\Database\Entity\DatabaseEntity;
    use KAASoft\Database\Entity\Util\Image;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\Comparable;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class Author
     * @package KAASoft\Database\Entity\General
     */
    class Author extends DatabaseEntity implements JsonSerializable, Comparable {
        /**
         * @var Image
         */
        private $photo;
        /**
         * @var int
         */
        private $bookCount = 0;

        private $firstName;
        private $middleName;
        private $lastName;
        private $photoId;
        private $description;

        function __construct($id = null) {
            parent::__construct($id);
        }

        /**
         * @return array
         */
        public function getDatabaseArray() {
            return array_merge(parent::getDatabaseArray(),
                               [ "firstName"   => $this->firstName,
                                 "middleName"  => $this->middleName,
                                 "lastName"    => $this->lastName,
                                 "photoId"     => $this->photoId,
                                 "description" => $this->description, ]);
        }

        /**
         * @param array $databaseArray
         * @return Author to restore form databaseArray
         */
        public static function getObjectInstance(array $databaseArray) {
            $author = new Author(ValidationHelper::getNullableInt($databaseArray["id"]));
            $author->setFirstName(ValidationHelper::getString($databaseArray["firstName"]));
            $author->setMiddleName(ValidationHelper::getString($databaseArray["middleName"]));
            $author->setLastName(ValidationHelper::getString($databaseArray["lastName"]));
            $author->setPhotoId(ValidationHelper::getNullableInt($databaseArray["photoId"]));
            $author->setDescription(ValidationHelper::getString($databaseArray["description"]));

            return $author;
        }


        /**
         * @return array
         */
        public static function getDatabaseFieldNames() {
            return array_merge(parent::getDatabaseFieldNames(),
                               [ KAASoftDatabase::$AUTHORS_TABLE_NAME . ".firstName",
                                 KAASoftDatabase::$AUTHORS_TABLE_NAME . ".middleName",
                                 KAASoftDatabase::$AUTHORS_TABLE_NAME . ".lastName",
                                 KAASoftDatabase::$AUTHORS_TABLE_NAME . ".photoId",
                                 KAASoftDatabase::$AUTHORS_TABLE_NAME . ".description" ]);
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
         * @param $other
         * @return bool
         */
        public function compareTo($other) {
            return $other instanceof Author and strcasecmp($other->getFirstName(),
                                                           $this->firstName) === 0 and strcasecmp($other->getLastName(),
                                                                                                  $this->lastName) === 0 and strcasecmp($other->getMiddleName(),
                                                                                                                                        $this->middleName) === 0;
        }

        /**
         * @return mixed
         */
        public function getFirstName() {
            return $this->firstName;
        }

        /**
         * @param mixed $firstName
         */
        public function setFirstName($firstName) {
            $this->firstName = $firstName;
        }


        /**
         * @return mixed
         */
        public function getLastName() {
            return $this->lastName;
        }

        /**
         * @param mixed $lastName
         */
        public function setLastName($lastName) {
            $this->lastName = $lastName;
        }


        /**
         * @return mixed
         */
        public function getMiddleName() {
            return $this->middleName;
        }

        /**
         * @param mixed $middleName
         */
        public function setMiddleName($middleName) {
            $this->middleName = $middleName;
        }

        /**
         * @return mixed
         */
        public function getPhotoId() {
            return $this->photoId;
        }

        /**
         * @param mixed $photoId
         */
        public function setPhotoId($photoId) {
            $this->photoId = $photoId;
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
         * @return Image
         */
        public function getPhoto() {
            return $this->photo;
        }

        /**
         * @param Image $photo
         */
        public function setPhoto($photo) {
            $this->photo = $photo;
        }

        /**
         * @return int
         */
        public function getBookCount() {
            return $this->bookCount;
        }

        /**
         * @param int $bookCount
         */
        public function setBookCount($bookCount) {
            $this->bookCount = $bookCount;
        }
    }