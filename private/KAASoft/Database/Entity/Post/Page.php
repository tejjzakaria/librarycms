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
     * Class Page
     * @package KAASoft\Database\Entity\Post
     */
    class Page extends DatabaseEntity implements JsonSerializable {
        /**
         * @var array
         */
        private $breadcrumbs;
        /**
         * @var User
         */
        private $user;
        /**
         * @var Image
         */
        private $image;

        private $parentId;
        private $userId;
        private $url;
        private $partialUrl;
        private $title;
        private $content;
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
        private $customTemplate;
        private $shortDescription;

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
                                 'partialUrl'       => $this->partialUrl,
                                 'parentId'         => $this->parentId,
                                 'url'              => $this->url,
                                 'title'            => $this->title,
                                 'content'          => $this->content,
                                 'metaTitle'        => $this->metaTitle,
                                 'metaKeywords'     => $this->metaKeywords,
                                 'metaDescription'  => $this->metaDescription,
                                 'imageId'          => $this->imageId,
                                 'status'           => $this->status,
                                 'publishDateTime'  => $this->publishDateTime,
                                 'creationDateTime' => $this->creationDateTime,
                                 'updateDateTime'   => $this->updateDateTime,
                                 'customTemplate'   => $this->customTemplate,
                                 'shortDescription' => $this->shortDescription ]);
        }

        /**
         * @param array $databaseArray
         * @return Page to restore form databaseArray
         */
        public static function getObjectInstance(array $databaseArray) {
            $page = new Page(ValidationHelper::getNullableInt($databaseArray["id"]));
            $page->setUrl(ValidationHelper::getString($databaseArray['url']));
            $page->setTitle(ValidationHelper::getString($databaseArray['title']));
            $page->setUserId(ValidationHelper::getNullableInt($databaseArray['userId']));
            $page->setStatus(ValidationHelper::getString($databaseArray['status']));
            $page->setContent(ValidationHelper::getString($databaseArray['content']));
            $page->setImageId(ValidationHelper::getNullableInt($databaseArray['imageId']));
            $page->setParentId(ValidationHelper::getNullableInt($databaseArray['parentId']));
            $page->setMetaTitle(ValidationHelper::getString($databaseArray['metaTitle']));
            $page->setPartialUrl(ValidationHelper::getString($databaseArray['partialUrl']));
            $page->setMetaKeywords(ValidationHelper::getString($databaseArray['metaKeywords']));
            $page->setMetaDescription(ValidationHelper::getString($databaseArray['metaDescription']));
            $page->setPublishDateTime(ValidationHelper::getString($databaseArray['publishDateTime']));
            $page->setCreationDateTime(ValidationHelper::getString($databaseArray['creationDateTime']));
            $page->setUpdateDateTime(ValidationHelper::getString($databaseArray['updateDateTime']));
            $page->setCustomTemplate(ValidationHelper::getString($databaseArray['customTemplate']));
            $page->setShortDescription(ValidationHelper::getString($databaseArray['shortDescription']));

            return $page;
        }

        /**
         * @return array
         */
        public static function getDatabaseFieldNames() {
            return array_merge(parent::getDatabaseFieldNames(),
                               [ KAASoftDatabase::$PAGES_TABLE_NAME . ".url",
                                 KAASoftDatabase::$PAGES_TABLE_NAME . ".title",
                                 KAASoftDatabase::$PAGES_TABLE_NAME . ".userId",
                                 KAASoftDatabase::$PAGES_TABLE_NAME . ".status",
                                 KAASoftDatabase::$PAGES_TABLE_NAME . ".content",
                                 KAASoftDatabase::$PAGES_TABLE_NAME . ".imageId",
                                 KAASoftDatabase::$PAGES_TABLE_NAME . ".parentId",
                                 KAASoftDatabase::$PAGES_TABLE_NAME . ".metaTitle",
                                 KAASoftDatabase::$PAGES_TABLE_NAME . ".partialUrl",
                                 KAASoftDatabase::$PAGES_TABLE_NAME . ".metaKeywords",
                                 KAASoftDatabase::$PAGES_TABLE_NAME . ".metaDescription",
                                 KAASoftDatabase::$PAGES_TABLE_NAME . ".publishDateTime",
                                 KAASoftDatabase::$PAGES_TABLE_NAME . ".creationDateTime",
                                 KAASoftDatabase::$PAGES_TABLE_NAME . ".updateDateTime",
                                 KAASoftDatabase::$PAGES_TABLE_NAME . ".shortDescription",
                                 KAASoftDatabase::$PAGES_TABLE_NAME . ".customTemplate" ]);
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
        public function getParentId() {
            return $this->parentId;
        }

        /**
         * @param mixed $parentId
         */
        public function setParentId($parentId) {
            $this->parentId = $parentId;
        }

        /**
         * @return mixed
         */
        public function getPartialUrl() {
            return $this->partialUrl;
        }

        /**
         * @param mixed $partialUrl
         */
        public function setPartialUrl($partialUrl) {
            $this->partialUrl = $partialUrl;
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
         * @return array
         */
        public function getBreadcrumbs() {
            return $this->breadcrumbs;
        }

        /**
         * @param array $breadcrumbs
         */
        public function setBreadcrumbs($breadcrumbs) {
            $this->breadcrumbs = $breadcrumbs;
        }

        /**
         * @return mixed
         */
        public function getCustomTemplate() {
            return $this->customTemplate;
        }

        /**
         * @param mixed $customTemplate
         */
        public function setCustomTemplate($customTemplate) {
            $this->customTemplate = $customTemplate;
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
    }