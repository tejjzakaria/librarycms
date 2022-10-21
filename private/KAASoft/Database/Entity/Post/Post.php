<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Database\Entity\Post;

    use JsonSerializable;
    use KAASoft\Database\Entity\DatabaseEntity;
    use KAASoft\Database\Entity\General\User;
    use KAASoft\Database\Entity\Util\Image;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\Enum\PostStatus;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class Post
     * @package KAASoft\Database\Entity\Post
     */
    class Post extends DatabaseEntity implements JsonSerializable {

        /**
         * @var array
         */
        private $categories;
        /**
         * @var User
         */
        private $user;
        /**
         * @var Image
         */
        private $image;

        private $userId;
        private $url;
        private $title;
        private $secondTitle;
        private $content;
        private $shortDescription;
        private $metaTitle;
        private $metaKeywords;
        private $metaDescription;
        private $imageId;
        /**
         * @var PostStatus
         */
        private $status;
        private $publishDateTime;
        private $creationDateTime;
        private $updateDateTime;

        public function __construct($id = null) {
            parent::__construct($id);
        }

        /**
         * @return array
         */
        function jsonSerialize() {
            return $this->getDatabaseArray();
        }

        /**
         * @return array
         */
        public function getDatabaseArray() {
            return array_merge(parent::getDatabaseArray(),
                               [ 'userId'           => $this->userId,
                                 'url'              => $this->url,
                                 'title'            => $this->title,
                                 'secondTitle'      => $this->secondTitle,
                                 'content'          => $this->content,
                                 'shortDescription' => $this->shortDescription,
                                 'metaTitle'        => $this->metaTitle,
                                 'metaKeywords'     => $this->metaKeywords,
                                 'metaDescription'  => $this->metaDescription,
                                 'imageId'          => $this->imageId,
                                 'status'           => $this->status,
                                 'publishDateTime'  => $this->publishDateTime,
                                 'creationDateTime' => $this->creationDateTime,
                                 'updateDateTime'   => $this->updateDateTime ]);
        }

        /**
         * @param array $databaseArray
         * @return Post to restore form databaseArray
         */
        public static function getObjectInstance(array $databaseArray) {
            $post = new Post(ValidationHelper::getNullableInt($databaseArray["id"]));
            $post->setUrl(ValidationHelper::getString($databaseArray['url']));
            $post->setTitle(ValidationHelper::getString($databaseArray['title']));
            $post->setSecondTitle(ValidationHelper::getString($databaseArray['secondTitle']));
            $post->setUserId(ValidationHelper::getNullableInt($databaseArray['userId']));
            $post->setStatus(ValidationHelper::getString($databaseArray['status']));
            $post->setContent(ValidationHelper::getString($databaseArray['content']));
            $post->setImageId(ValidationHelper::getNullableInt($databaseArray['imageId']));
            $post->setMetaTitle(ValidationHelper::getString($databaseArray['metaTitle']));
            $post->setMetaKeywords(ValidationHelper::getString($databaseArray['metaKeywords']));
            $post->setMetaDescription(ValidationHelper::getString($databaseArray['metaDescription']));
            $post->setPublishDateTime(ValidationHelper::getString($databaseArray['publishDateTime']));
            $post->setCreationDateTime(ValidationHelper::getString($databaseArray['creationDateTime']));
            $post->setUpdateDateTime(ValidationHelper::getString($databaseArray['updateDateTime']));
            $post->setShortDescription(ValidationHelper::getString($databaseArray['shortDescription']));

            return $post;
        }


        /**
         * @return array
         */
        public static function getDatabaseFieldNames() {
            return array_merge(parent::getDatabaseFieldNames(),
                               [ KAASoftDatabase::$POSTS_TABLE_NAME . ".url",
                                 KAASoftDatabase::$POSTS_TABLE_NAME . ".title",
                                 KAASoftDatabase::$POSTS_TABLE_NAME . ".userId",
                                 KAASoftDatabase::$POSTS_TABLE_NAME . ".secondTitle",
                                 KAASoftDatabase::$POSTS_TABLE_NAME . ".status",
                                 KAASoftDatabase::$POSTS_TABLE_NAME . ".content",
                                 KAASoftDatabase::$POSTS_TABLE_NAME . ".imageId",
                                 KAASoftDatabase::$POSTS_TABLE_NAME . ".metaTitle",
                                 KAASoftDatabase::$POSTS_TABLE_NAME . ".metaKeywords",
                                 KAASoftDatabase::$POSTS_TABLE_NAME . ".metaDescription",
                                 KAASoftDatabase::$POSTS_TABLE_NAME . ".publishDateTime",
                                 KAASoftDatabase::$POSTS_TABLE_NAME . ".creationDateTime",
                                 KAASoftDatabase::$POSTS_TABLE_NAME . ".updateDateTime",
                                 KAASoftDatabase::$POSTS_TABLE_NAME . ".shortDescription" ]);
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
         * @return mixed
         */
        public function getImageId() {
            return $this->imageId;
        }

        /**
         * @param mixed $imageId
         */
        public function setImageId($imageId) {
            $this->imageId = $imageId;
        }

        /**
         * @return PostStatus
         */
        public function getStatus() {
            return $this->status;
        }

        /**
         * @param $status
         */
        public function setStatus($status) {
            $this->status = $status;
        }

        /**
         * @return mixed
         */
        public function getPublishDateTime() {
            return $this->publishDateTime;
        }

        /**
         * @param mixed $publishDateTime
         */
        public function setPublishDateTime($publishDateTime) {
            $this->publishDateTime = $publishDateTime;
        }

        /**
         * @return array
         */
        public function getCategories() {
            return $this->categories;
        }

        /**
         * @param array $categories
         */
        public function setCategories($categories) {
            $this->categories = $categories;
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
        public function getShortDescription() {
            return $this->shortDescription;
        }

        /**
         * @param mixed $shortDescription
         */
        public function setShortDescription($shortDescription) {
            $this->shortDescription = $shortDescription;
        }

        /**
         * @return Image
         */
        public function getImage() {
            return $this->image;
        }

        /**
         * @param Image $image
         */
        public function setImage($image) {
            $this->image = $image;
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
        public function getSecondTitle() {
            return $this->secondTitle;
        }

        /**
         * @param mixed $secondTitle
         */
        public function setSecondTitle($secondTitle) {
            $this->secondTitle = $secondTitle;
        }
    }