<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Database\Entity\Util;


    use KAASoft\Database\Entity\DatabaseEntity;
    use KAASoft\Database\Entity\Post\Page;
    use KAASoft\Database\Entity\Post\Post;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class MenuItem
     * @package KAASoft\Database\Entity\Util
     */
    class MenuItem extends DatabaseEntity {
        /**
         * @var Page
         */
        private $page;

        /**
         * @var Post
         */
        private $post;

        private $title;
        private $menuId;
        private $parentId;
        private $pageId;
        private $postId;
        private $link;
        private $order;
        private $class;

        /**
         * MenuItem constructor.
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
                               [ 'title'    => $this->title,
                                 'menuId'   => $this->menuId,
                                 'parentId' => $this->parentId,
                                 'pageId'   => $this->pageId,
                                 'postId'   => $this->postId,
                                 'link'     => $this->link,
                                 'order'    => $this->order,
                                 'class'    => $this->class ]);
        }

        public static function getDatabaseFieldNames() {
            return [ KAASoftDatabase::$MENU_ITEMS_TABLE_NAME . ".title",
                     KAASoftDatabase::$MENU_ITEMS_TABLE_NAME . ".menuId",
                     KAASoftDatabase::$MENU_ITEMS_TABLE_NAME . ".parentId",
                     KAASoftDatabase::$MENU_ITEMS_TABLE_NAME . ".pageId",
                     KAASoftDatabase::$MENU_ITEMS_TABLE_NAME . ".postId",
                     KAASoftDatabase::$MENU_ITEMS_TABLE_NAME . ".link",
                     KAASoftDatabase::$MENU_ITEMS_TABLE_NAME . ".order",
                     KAASoftDatabase::$MENU_ITEMS_TABLE_NAME . ".class" ];
        }

        /**
         * @param array $databaseArray
         * @return MenuItem to restore form databaseArray
         */
        public static function getObjectInstance(array $databaseArray) {
            $menuItem = new MenuItem(ValidationHelper::getNullableInt($databaseArray["id"]));
            $menuItem->setTitle(ValidationHelper::getString($databaseArray['title']));
            $menuItem->setMenuId(ValidationHelper::getNullableInt($databaseArray['menuId']));
            $menuItem->setParentId(ValidationHelper::getNullableInt($databaseArray['parentId']));
            $menuItem->setPageId(ValidationHelper::getNullableInt($databaseArray['pageId']));
            $menuItem->setPostId(ValidationHelper::getNullableInt($databaseArray['postId']));
            $menuItem->setLink(ValidationHelper::getString($databaseArray['link']));
            $menuItem->setMenuId(ValidationHelper::getNullableInt($databaseArray['order']));
            $menuItem->setClass(ValidationHelper::getString($databaseArray['class']));

            return $menuItem;
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
        public function getMenuId() {
            return $this->menuId;
        }

        /**
         * @param mixed $menuId
         */
        public function setMenuId($menuId) {
            $this->menuId = $menuId;
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
        public function getPageId() {
            return $this->pageId;
        }

        /**
         * @param mixed $pageId
         */
        public function setPageId($pageId) {
            $this->pageId = $pageId;
        }

        /**
         * @return mixed
         */
        public function getPostId() {
            return $this->postId;
        }

        /**
         * @param mixed $postId
         */
        public function setPostId($postId) {
            $this->postId = $postId;
        }

        /**
         * @return mixed
         */
        public function getLink() {
            return $this->link;
        }

        /**
         * @param mixed $link
         */
        public function setLink($link) {
            $this->link = $link;
        }

        /**
         * @return mixed
         */
        public function getOrder() {
            return $this->order;
        }

        /**
         * @param mixed $order
         */
        public function setOrder($order) {
            $this->order = $order;
        }

        /**
         * @return mixed
         */
        public function getClass() {
            return $this->class;
        }

        /**
         * @param mixed $class
         */
        public function setClass($class) {
            $this->class = $class;
        }

        /**
         * @return Page
         */
        public function getPage() {
            return $this->page;
        }

        /**
         * @param Page $page
         */
        public function setPage($page) {
            $this->page = $page;
        }

        /**
         * @return Post
         */
        public function getPost() {
            return $this->post;
        }

        /**
         * @param Post $post
         */
        public function setPost($post) {
            $this->post = $post;
        }
    }