<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Database\Entity\Post;


    use KAASoft\Database\Entity\DatabaseEntity;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class Category
     * @package KAASoft\Database\Entity\Post
     */
    class Category extends DatabaseEntity {

        /**
         * @var string
         */
        private $name;
        /**
         * @var string
         */
        private $title;
        private $url;
        private $metaTitle;
        private $metaKeywords;
        private $metaDescription;

        public function __construct($id = null) {
            parent::__construct($id);
        }

        /**
         * @return array
         */
        public function getDatabaseArray() {
            return array_merge(parent::getDatabaseArray(),
                               [ 'name'            => $this->name,
                                 'title'           => $this->title,
                                 'url'             => $this->url,
                                 'metaTitle'       => $this->metaTitle,
                                 'metaKeywords'    => $this->metaKeywords,
                                 'metaDescription' => $this->metaDescription ]);
        }

        /**
         * @param array $databaseArray
         * @return Category to restore form databaseArray
         */
        public static function getObjectInstance(array $databaseArray) {
            $category = new Category(ValidationHelper::getNullableInt($databaseArray["id"]));
            $category->setName(ValidationHelper::getString($databaseArray['name']));
            $category->setTitle(ValidationHelper::getString($databaseArray['title']));
            $category->setUrl(ValidationHelper::getString($databaseArray['url']));
            $category->setMetaTitle(ValidationHelper::getString($databaseArray['metaTitle']));
            $category->setMetaKeywords(ValidationHelper::getString($databaseArray['metaKeywords']));
            $category->setMetaDescription(ValidationHelper::getString($databaseArray['metaDescription']));

            return $category;
        }

        /**
         * @return array
         */
        public static function getDatabaseFieldNames() {
            return array_merge(parent::getDatabaseFieldNames(),
                               [ KAASoftDatabase::$CATEGORIES_TABLE_NAME . ".name",
                                 KAASoftDatabase::$CATEGORIES_TABLE_NAME . ".title",
                                 KAASoftDatabase::$CATEGORIES_TABLE_NAME . ".url",
                                 KAASoftDatabase::$CATEGORIES_TABLE_NAME . ".metaTitle",
                                 KAASoftDatabase::$CATEGORIES_TABLE_NAME . ".metaKeywords",
                                 KAASoftDatabase::$CATEGORIES_TABLE_NAME . ".metaDescription" ]);
        }

        /**
         * @return string
         */
        public function getName() {
            return $this->name;
        }

        /**
         * @param string $name
         */
        public function setName($name) {
            $this->name = $name;
        }

        /**
         * @return mixed
         */
        public function getUrl() {
            return $this->url;
        }

        /**
         * @param mixed $url
         */
        public function setUrl($url) {
            $this->url = $url;
        }

        /**
         * @return mixed
         */
        public function getMetaTitle() {
            return $this->metaTitle;
        }

        /**
         * @param mixed $metaTitle
         */
        public function setMetaTitle($metaTitle) {
            $this->metaTitle = $metaTitle;
        }

        /**
         * @return mixed
         */
        public function getMetaKeywords() {
            return $this->metaKeywords;
        }

        /**
         * @param mixed $metaKeywords
         */
        public function setMetaKeywords($metaKeywords) {
            $this->metaKeywords = $metaKeywords;
        }

        /**
         * @return mixed
         */
        public function getMetaDescription() {
            return $this->metaDescription;
        }

        /**
         * @param mixed $metaDescription
         */
        public function setMetaDescription($metaDescription) {
            $this->metaDescription = $metaDescription;
        }

        /**
         * @return string
         */
        public function getTitle() {
            return $this->title;
        }

        /**
         * @param string $title
         */
        public function setTitle($title) {
            $this->title = $title;
        }
    }