<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Database\Entity\General;


    use JsonSerializable;
    use KAASoft\Database\Entity\DatabaseEntity;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\FileHelper;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class ElectronicBook
     * @package KAASoft\Database\Entity\General
     */
    class ElectronicBook extends DatabaseEntity implements JsonSerializable {
        /**
         * @var
         */
        private $webPath;

        private $title;
        private $path;
        private $uploadingDateTime;

        /**
         * ElectronicBook constructor.
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
                               [ 'title'             => $this->title,
                                 'path'              => $this->path,
                                 'uploadingDateTime' => $this->uploadingDateTime ]);
        }

        /**
         * @param array $databaseArray
         * @return ElectronicBook to restore form databaseArray
         */
        public static function getObjectInstance(array $databaseArray) {
            $electronicBook = new ElectronicBook(ValidationHelper::getNullableInt($databaseArray["id"]));
            $electronicBook->setPath(ValidationHelper::getString($databaseArray['path']));
            $electronicBook->setTitle(ValidationHelper::getString($databaseArray['title']));
            $electronicBook->setUploadingDateTime(ValidationHelper::getString($databaseArray['uploadingDateTime']));

            $electronicBookFullPath = FileHelper::getElectronicBookRootLocation() . $electronicBook->getPath();

            $electronicBook->setWebPath(FileHelper::getSitePublicResourceLocation($electronicBookFullPath));

            return $electronicBook;
        }

        /**
         * (PHP 5 &gt;= 5.4.0)<br/>
         * Specify data which should be serialized to JSON
         * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
         * @return mixed data which can be serialized by <b>json_encode</b>,
         * which is a value of any type other than a resource.
         */
        function jsonSerialize() {
            return [ "path"              => $this->path,
                     "webPath"           => $this->getWebPath(),
                     "title"             => $this->title,
                     "uploadingDateTime" => $this->uploadingDateTime ];
        }

        public static function getDatabaseFieldNames() {
            return [ KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME . ".path",
                     KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME . ".title",
                     KAASoftDatabase::$ELECTRONIC_BOOKS_TABLE_NAME . ".uploadingDateTime" ];
        }

        /**
         * @return mixed
         */
        public function getTitle() {
            return $this->title;
        }

        /**
         * @param mixed $title
         */
        public function setTitle($title) {
            $this->title = $title;
        }

        /**
         * @return mixed
         */
        public function getPath() {
            return $this->path;
        }

        /**
         * @param mixed $path
         */
        public function setPath($path) {
            $this->path = $path;
        }

        public function getAbsolutePath() {
            return realpath(FileHelper::getElectronicBookRootLocation() . $this->getPath());
        }

        /**
         * @return mixed
         */
        public function getWebPath() {
            $electronicBookFullPath = $this->getAbsolutePath();

            if (file_exists($electronicBookFullPath) and $this->getPath() !== null) {
                $originalWebPath = FileHelper::getSitePublicResourceLocation($electronicBookFullPath);

                return $originalWebPath;
            }
            else {
                return null;
            }
        }

        /**
         * @param mixed $webPath
         */
        public function setWebPath($webPath) {
            $this->webPath = $webPath;
        }

        /**
         * @return mixed
         */
        public function getUploadingDateTime() {
            return $this->uploadingDateTime;
        }

        /**
         * @param mixed $uploadingDateTime
         */
        public function setUploadingDateTime($uploadingDateTime) {
            $this->uploadingDateTime = $uploadingDateTime;
        }
    }