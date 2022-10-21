<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Post;

    use KAASoft\Controller\DatabaseHelper;
    use KAASoft\Database\Entity\General\User;
    use KAASoft\Database\Entity\Post\Category;
    use KAASoft\Database\Entity\Post\Post;
    use KAASoft\Database\Entity\Util\Image;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\Helper;

    /**
     * Class PostDatabaseHelper
     * @package KAASoft\Controller\Admin\Post
     */
    class PostDatabaseHelper extends DatabaseHelper {

        /**
         * @param Post $post
         * @return bool|$userId
         */
        public function savePost(Post $post) {
            $data = $post->getDatabaseArray();
            if ($post->getId() === null) {
                return $this->kaaSoftDatabase->insert(KAASoftDatabase::$POSTS_TABLE_NAME,
                                                      $data);
            }

            else {
                unset( $data['creationDateTime'] );
                if (empty( $data['publishDateTime'] )) {
                    unset( $data['publishDateTime'] );
                }
                // fix for blog main page
                if ($post->getId() == 0) {
                    unset( $data["url"] );
                }

                return $this->kaaSoftDatabase->update(KAASoftDatabase::$POSTS_TABLE_NAME,
                                                      $data,
                                                      [ "id" => $post->getId() ]);
            }
        }

        public function hasPostUrl($url, $excludePostId = null) {
            if ($excludePostId === null) {
                return $this->kaaSoftDatabase->has(KAASoftDatabase::$POSTS_TABLE_NAME,
                                                   [ "url" => $url ]);
            }
            else {
                return $this->kaaSoftDatabase->has(KAASoftDatabase::$POSTS_TABLE_NAME,
                                                   [ "AND" => [ "url"   => $url,
                                                                "id[!]" => $excludePostId ] ]);
            }

        }

        /**
         * @return bool|int
         */
        public function getPublicPostsCount() {
            $queryParams = [ "AND" => [ "OR"                                          => [ "status" => "Publish",
                                                                                           "AND"    => [ "status"              => "Future",
                                                                                                         "publishDateTime[<=]" => Helper::getDateTimeString() ] ],
                                        KAASoftDatabase::$POSTS_TABLE_NAME . ".id[!]" => 0 ] ];

            return $this->kaaSoftDatabase->count(KAASoftDatabase::$POSTS_TABLE_NAME,
                                                 [ KAASoftDatabase::$POSTS_TABLE_NAME . ".id" ],
                                                 $queryParams);
        }

        /**
         * @param null $offset
         * @param null $perPage
         * @return array|null
         */
        public function getPublicPosts($offset = null, $perPage = null) {

            $queryParams = [ "AND" => [ "OR"                                          => [ "status" => "Publish",
                                                                                           "AND"    => [ "status"              => "Future",
                                                                                                         "publishDateTime[<=]" => Helper::getDateTimeString() ] ],
                                        KAASoftDatabase::$POSTS_TABLE_NAME . ".id[!]" => 0 ] ];
            if ($offset !== null && $perPage !== null) {
                $queryParams = array_merge($queryParams,
                                           [ "ORDER" => [ 'publishDateTime' => 'DESC' ],
                                             "LIMIT" => [ (int)$offset,
                                                          (int)$perPage ] ]);
            }
            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$POSTS_TABLE_NAME,
                                                          [ "[><]" . KAASoftDatabase::$USERS_TABLE_NAME => [ "userId" => "id" ],
                                                            "[>]" . KAASoftDatabase::$IMAGES_TABLE_NAME => [ "imageId" => "id" ] ],
                                                          array_merge([ KAASoftDatabase::$USERS_TABLE_NAME . ".firstName",
                                                                        KAASoftDatabase::$USERS_TABLE_NAME . ".lastName",
                                                                        KAASoftDatabase::$USERS_TABLE_NAME . ".middleName",
                                                                        KAASoftDatabase::$USERS_TABLE_NAME . ".gender",
                                                                        KAASoftDatabase::$IMAGES_TABLE_NAME . ".id(imageId)",
                                                                        KAASoftDatabase::$IMAGES_TABLE_NAME . ".title(imageTitle)",
                                                                        KAASoftDatabase::$IMAGES_TABLE_NAME . ".path",
                                                                        KAASoftDatabase::$IMAGES_TABLE_NAME . ".uploadingDateTime",
                                                                        KAASoftDatabase::$POSTS_TABLE_NAME . ".id" ],
                                                                      Post::getDatabaseFieldNames()),
                                                          $queryParams);

            if ($queryResult !== false) {
                $posts = [];

                foreach ($queryResult as $row) {
                    $post = Post::getObjectInstance($row);

                    $user = User::getObjectInstance($row);
                    $user->setId($post->getUserId());
                    $post->setUser($user);

                    $image = Image::getObjectInstance($row);
                    if (file_exists($image->getAbsolutePath())) {
                        $image->setId($row["imageId"]);
                        $image->setTitle($row["imageTitle"]);
                        $post->setImage($image);
                    }

                    $post->setCategories($this->getPostCategories($post->getId()));

                    $posts[] = $post;
                }

                return $posts;
            }

            return null;
        }

        /**
         * @param Category $category
         * @return bool|int
         */
        public function countCategoryPosts(Category $category) {
            if ($category !== null) {
                $queryParams = [ KAASoftDatabase::$POST_CATEGORIES_TABLE_NAME . ".categoryId" => $category->getId(),
                                 "postId[!]"                                                  => 0 ];


                $queryParams = [ "AND" => [ "OR"  => [ "status" => "Publish",
                                                       "AND"    => [ "status"              => "Future",
                                                                     "publishDateTime[<=]" => Helper::getDateTimeString() ] ],
                                            "AND" => $queryParams ] ];

                return $this->kaaSoftDatabase->count(KAASoftDatabase::$POSTS_TABLE_NAME,
                                                     [ "[><]" . KAASoftDatabase::$POST_CATEGORIES_TABLE_NAME => [ "id" => "postId" ] ],
                                                     [ KAASoftDatabase::$POST_CATEGORIES_TABLE_NAME . ".postId" ],
                                                     $queryParams);
            }

            return 0;
        }

        /**
         * @param null     $offset
         * @param null     $perPage
         * @param Category $category
         * @return array|null
         */
        public function getPublicCategoryPosts($offset = null, $perPage = null, $category = null) {

            if ($category !== null) {
                $queryParams = [ KAASoftDatabase::$POST_CATEGORIES_TABLE_NAME . ".categoryId" => $category->getId(),
                                 "postId[!]"                                                  => 0 ];
            }
            else {
                return null;
            }

            $queryParams = [ "AND" => [ "OR"  => [ "status" => "Publish",
                                                   "AND"    => [ "status"              => "Future",
                                                                 "publishDateTime[<=]" => Helper::getDateTimeString() ] ],
                                        "AND" => $queryParams ] ];


            if ($offset !== null && $perPage !== null) {
                $queryParams = array_merge($queryParams,
                                           [ "ORDER" => [ 'publishDateTime' => 'DESC' ],
                                             "LIMIT" => [ (int)$offset,
                                                          (int)$perPage ] ]);
            }
            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$POSTS_TABLE_NAME,
                                                          [ "[><]" . KAASoftDatabase::$USERS_TABLE_NAME           => [ "userId" => "id" ],
                                                            "[><]" . KAASoftDatabase::$POST_CATEGORIES_TABLE_NAME => [ "id" => "postId" ],
                                                            "[>]" . KAASoftDatabase::$IMAGES_TABLE_NAME           => [ "imageId" => "id" ] ],
                                                          array_merge([ KAASoftDatabase::$USERS_TABLE_NAME . ".firstName",
                                                                        KAASoftDatabase::$USERS_TABLE_NAME . ".lastName",
                                                                        KAASoftDatabase::$USERS_TABLE_NAME . ".middleName",
                                                                        KAASoftDatabase::$USERS_TABLE_NAME . ".gender",
                                                                        KAASoftDatabase::$IMAGES_TABLE_NAME . ".id(imageId)",
                                                                        KAASoftDatabase::$IMAGES_TABLE_NAME . ".title(imageTitle)",
                                                                        KAASoftDatabase::$IMAGES_TABLE_NAME . ".path",
                                                                        KAASoftDatabase::$IMAGES_TABLE_NAME . ".uploadingDateTime",
                                                                        KAASoftDatabase::$POSTS_TABLE_NAME . ".id" ],
                                                                      Post::getDatabaseFieldNames()),
                                                          $queryParams);

            if ($queryResult !== false) {
                $posts = [];

                foreach ($queryResult as $row) {
                    $post = Post::getObjectInstance($row);

                    $user = User::getObjectInstance($row);
                    $user->setId($post->getUserId());
                    $post->setUser($user);

                    $image = Image::getObjectInstance($row);
                    if (file_exists($image->getAbsolutePath())) {
                        $image->setId($row["imageId"]);
                        $image->setTitle($row["imageTitle"]);
                        $post->setImage($image);
                    }

                    $post->setCategories($this->getPostCategories($post->getId()));

                    $posts[] = $post;
                }

                return $posts;
            }

            return null;
        }

        /**
         * @param null   $offset
         * @param null   $perPage
         * @param string $sortBy
         * @param string $sortOrder
         * @return array|null
         */
        public function getPosts($offset = null, $perPage = null, $sortBy = "publishDateTime", $sortOrder = "DESC") {
            $queryParams = [];

            if ($sortBy != null && $sortOrder != null) {
                $queryParams = array_merge($queryParams,
                                           [ "ORDER" => [ $sortBy => $sortOrder ] ]);
            }
            if ($offset !== null && $perPage !== null) {
                $queryParams = array_merge($queryParams,
                                           [ "LIMIT" => [ (int)$offset,
                                                          (int)$perPage ] ]);
            }

            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$POSTS_TABLE_NAME,
                                                          [ "[><]" . KAASoftDatabase::$USERS_TABLE_NAME => [ "userId" => "id" ] ],
                                                          array_merge([ KAASoftDatabase::$USERS_TABLE_NAME . ".firstName",
                                                                        KAASoftDatabase::$USERS_TABLE_NAME . ".lastName",
                                                                        KAASoftDatabase::$USERS_TABLE_NAME . ".middleName",
                                                                        KAASoftDatabase::$USERS_TABLE_NAME . ".gender",
                                                                        KAASoftDatabase::$POSTS_TABLE_NAME . ".id" ],
                                                                      Post::getDatabaseFieldNames()),
                                                          $queryParams);


            if ($queryResult !== false) {
                $posts = [];

                foreach ($queryResult as $row) {
                    $post = Post::getObjectInstance($row);

                    $user = User::getObjectInstance($row);
                    $user->setId($post->getUserId());
                    $post->setUser($user);
                    $post->setPublishDateTime(Helper::reformatDateTimeString($post->getPublishDateTime(),
                                                                             $this->siteViewOptions->getOptionValue(SiteViewOptions::DATE_TIME_FORMAT)));


                    $post->setCategories($this->getPostCategories($post->getId()));

                    $posts[] = $post;
                }

                return $posts;
            }

            return null;
        }

        /**
         * @param $postUrl
         * @return Post|null
         */
        public function getPublicPostByURL($postUrl) {
            $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$POSTS_TABLE_NAME,
                                                       [ "[><]" . KAASoftDatabase::$USERS_TABLE_NAME => [ "userId" => "id" ],
                                                         "[>]" . KAASoftDatabase::$IMAGES_TABLE_NAME => [ "imageId" => "id" ] ],
                                                       array_merge([ KAASoftDatabase::$USERS_TABLE_NAME . ".firstName",
                                                                     KAASoftDatabase::$USERS_TABLE_NAME . ".lastName",
                                                                     KAASoftDatabase::$USERS_TABLE_NAME . ".middleName",
                                                                     KAASoftDatabase::$USERS_TABLE_NAME . ".gender",
                                                                     KAASoftDatabase::$IMAGES_TABLE_NAME . ".id(imageId)",
                                                                     KAASoftDatabase::$IMAGES_TABLE_NAME . ".title(imageTitle)",
                                                                     KAASoftDatabase::$IMAGES_TABLE_NAME . ".path",
                                                                     KAASoftDatabase::$IMAGES_TABLE_NAME . ".uploadingDateTime",
                                                                     KAASoftDatabase::$POSTS_TABLE_NAME . ".id" ],
                                                                   Post::getDatabaseFieldNames()),
                                                       [ "AND" => [ KAASoftDatabase::$POSTS_TABLE_NAME . ".id[!]" => 0,
                                                                    KAASoftDatabase::$POSTS_TABLE_NAME . ".url"   => $postUrl,
                                                                    "OR"                                          => [ "status" => "Publish",
                                                                                                                       "AND"    => [ "status"              => "Future",
                                                                                                                                     "publishDateTime[<=]" => Helper::getDateTimeString() ] ] ] ]);


            if ($queryResult !== false) {
                $post = Post::getObjectInstance($queryResult);

                $user = User::getObjectInstance($queryResult);
                $user->setId($post->getUserId());
                $post->setUser($user);

                $image = Image::getObjectInstance($queryResult);
                if (file_exists($image->getAbsolutePath())) {
                    $image->setId($queryResult["imageId"]);
                    $image->setTitle($queryResult["imageTitle"]);
                    $post->setImage($image);
                }

                $post->setCategories($this->getPostCategories($post->getId()));

                return $post;
            }

            return null;
        }

        /**
         * @param $categoryUrl
         * @return Category|null
         */
        public function getCategoryByUrl($categoryUrl) {
            $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$CATEGORIES_TABLE_NAME,
                                                       array_merge(Category::getDatabaseFieldNames(),
                                                                   [ KAASoftDatabase::$CATEGORIES_TABLE_NAME . ".id" ]),
                                                       [ "url" => $categoryUrl ]);

            if ($queryResult !== false) {
                $category = Category::getObjectInstance($queryResult);

                return $category;
            }

            return null;
        }

        /**
         * @param $postId
         * @return Post|null
         */
        public function getPost($postId) {
            $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$POSTS_TABLE_NAME,
                                                       [ "[><]" . KAASoftDatabase::$USERS_TABLE_NAME => [ "userId" => "id" ],
                                                         "[>]" . KAASoftDatabase::$IMAGES_TABLE_NAME => [ "imageId" => "id" ] ],
                                                       array_merge([ KAASoftDatabase::$IMAGES_TABLE_NAME . ".id(imageId)",
                                                                     KAASoftDatabase::$IMAGES_TABLE_NAME . ".title(imageTitle)",
                                                                     KAASoftDatabase::$IMAGES_TABLE_NAME . ".path",
                                                                     KAASoftDatabase::$IMAGES_TABLE_NAME . ".uploadingDateTime",
                                                                     KAASoftDatabase::$USERS_TABLE_NAME . ".firstName",
                                                                     KAASoftDatabase::$USERS_TABLE_NAME . ".lastName",
                                                                     KAASoftDatabase::$USERS_TABLE_NAME . ".middleName",
                                                                     KAASoftDatabase::$USERS_TABLE_NAME . ".gender",
                                                                     KAASoftDatabase::$POSTS_TABLE_NAME . ".id" ],
                                                                   Post::getDatabaseFieldNames()),
                                                       [ KAASoftDatabase::$POSTS_TABLE_NAME . ".id" => $postId ]);


            if ($queryResult !== false) {
                $post = Post::getObjectInstance($queryResult);

                $user = User::getObjectInstance($queryResult);
                $user->setId($post->getUserId());
                $post->setUser($user);

                if ($queryResult["imageId"] != 0) {
                    $image = Image::getObjectInstance($queryResult);
                    if (file_exists($image->getAbsolutePath())) {
                        $image->setId($queryResult["imageId"]);
                        $image->setTitle($queryResult["imageTitle"]);
                        $post->setImage($image);
                    }
                }

                $post->setCategories($this->getPostCategories($postId));

                return $post;
            }

            return null;
        }

        /**
         * @param $postId
         * @return array
         */
        public function getPostCategories($postId) {
            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$POST_CATEGORIES_TABLE_NAME,
                                                          [ "[><]" . KAASoftDatabase::$CATEGORIES_TABLE_NAME => [ "categoryId" => "id" ] ],
                                                          [ KAASoftDatabase::$CATEGORIES_TABLE_NAME . ".id",
                                                            KAASoftDatabase::$CATEGORIES_TABLE_NAME . ".name",
                                                            KAASoftDatabase::$CATEGORIES_TABLE_NAME . ".url",
                                                            KAASoftDatabase::$CATEGORIES_TABLE_NAME . ".metaTitle",
                                                            KAASoftDatabase::$CATEGORIES_TABLE_NAME . ".metaKeywords",
                                                            KAASoftDatabase::$CATEGORIES_TABLE_NAME . ".metaDescription" ],
                                                          [ "postId" => $postId ]);
            if ($queryResult !== false) {
                $categories = [];
                foreach ($queryResult as $row) {
                    $categories[] = Category::getObjectInstance($row);
                }

                return $categories;
            }

            return [];
        }

        /**
         * @param $postId
         * @return bool|int
         */
        public function deletePost($postId) {
            return $this->kaaSoftDatabase->delete(KAASoftDatabase::$POSTS_TABLE_NAME,
                                                  [ "id" => $postId ]);

        }

        /**
         * @return bool|int
         */
        public function getPostsCount() {
            return $this->kaaSoftDatabase->count(KAASoftDatabase::$POSTS_TABLE_NAME);
        }

        /**
         * @param Category $category
         * @return bool|$userId
         */
        public function saveCategory(Category $category) {
            $data = $category->getDatabaseArray();
            if ($category->getId() == null) {
                return $this->kaaSoftDatabase->insert(KAASoftDatabase::$CATEGORIES_TABLE_NAME,
                                                      $data);
            }

            else {
                return $this->kaaSoftDatabase->update(KAASoftDatabase::$CATEGORIES_TABLE_NAME,
                                                      $data,
                                                      [ "id" => $category->getId() ]);
            }
        }

        /**
         * @param $categoryId
         * @return bool|int
         */
        public function deleteCategory($categoryId) {
            return $this->kaaSoftDatabase->delete(KAASoftDatabase::$CATEGORIES_TABLE_NAME,
                                                  [ "id" => $categoryId ]);

        }

        /**
         * @param $categoryId
         * @return Category|null
         */
        public function getCategory($categoryId) {

            $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$CATEGORIES_TABLE_NAME,
                                                       array_merge(Category::getDatabaseFieldNames(),
                                                                   [ KAASoftDatabase::$CATEGORIES_TABLE_NAME . ".id" ]),
                                                       [ "id" => $categoryId ]);
            if ($queryResult !== false) {
                return Category::getObjectInstance($queryResult);
            }

            return null;
        }

        /**
         * @return array|null
         */
        public function getCategories() {
            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$CATEGORIES_TABLE_NAME,
                                                          array_merge(Category::getDatabaseFieldNames(),
                                                                      [ KAASoftDatabase::$CATEGORIES_TABLE_NAME . ".id" ]));
            if ($queryResult !== false) {
                $categories = [];
                foreach ($queryResult as $row) {
                    $category = Category::getObjectInstance($row);
                    $categories[] = $category;
                }

                return $categories;
            }

            return null;
        }

        /**
         * @param $postId
         * @param $categoryId
         * @return array|bool
         */
        public function savePostCategory($postId, $categoryId) {
            return $this->kaaSoftDatabase->insert(KAASoftDatabase::$POST_CATEGORIES_TABLE_NAME,
                                                  [ "postId"     => $postId,
                                                    "categoryId" => $categoryId ]);

        }

        /**
         * @param $postId
         * @return bool|int
         */
        public function deletePostCategories($postId) {
            return $this->kaaSoftDatabase->delete(KAASoftDatabase::$POST_CATEGORIES_TABLE_NAME,
                                                  [ "postId" => $postId ]);

        }

        /**
         * @param $postId
         * @param $categoryId
         * @return bool
         */
        public function isPostHasCategory($postId, $categoryId) {
            return $this->kaaSoftDatabase->has(KAASoftDatabase::$POST_CATEGORIES_TABLE_NAME,
                                               [ "AND" => [ "roleId"       => $postId,
                                                            "permissionId" => $categoryId ] ]);
        }

        /**
         * @return KAASoftDatabase
         */
        public function getKaaSoftDatabase() {
            return $this->kaaSoftDatabase;
        }

        /**
         * @param KAASoftDatabase $kaaSoftDatabase
         */
        public function setKaaSoftDatabase($kaaSoftDatabase) {
            $this->kaaSoftDatabase = $kaaSoftDatabase;
        }

        /**
         * @return SiteViewOptions
         */
        public function getSiteViewOptions() {
            return $this->siteViewOptions;
        }

        /**
         * @param SiteViewOptions $siteViewOptions
         */
        public function setSiteViewOptions($siteViewOptions) {
            $this->siteViewOptions = $siteViewOptions;
        }

    }