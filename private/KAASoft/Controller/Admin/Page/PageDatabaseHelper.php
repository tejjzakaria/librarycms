<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Page;

    use KAASoft\Controller\DatabaseHelper;
    use KAASoft\Database\Entity\General\User;
    use KAASoft\Database\Entity\Post\Page;
    use KAASoft\Database\Entity\Util\Image;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Tree;
    use KAASoft\Util\TreeNode;

    /**
     * Class PageDatabaseHelper
     * @package KAASoft\Controller\Admin\Page
     */
    class PageDatabaseHelper extends DatabaseHelper {

        /**
         * @param Page $page
         * @return bool|$userId
         */
        public function savePage(Page $page) {
            $data = $page->getDatabaseArray();
            if ($page->getId() == null) {
                return $this->kaaSoftDatabase->insert(KAASoftDatabase::$PAGES_TABLE_NAME,
                                                      $data);
            }

            else {
                unset( $data['creationDateTime'] );
                if (empty( $data['publishDateTime'] )) {
                    unset( $data['publishDateTime'] );
                }

                return $this->kaaSoftDatabase->update(KAASoftDatabase::$PAGES_TABLE_NAME,
                                                      $data,
                                                      [ "id" => $page->getId() ]);
            }
        }

        public function hasPageUrl($url, $excludePostId = null) {
            if ($excludePostId === null) {
                return $this->kaaSoftDatabase->has(KAASoftDatabase::$PAGES_TABLE_NAME,
                                                   [ "url" => $url ]);
            }
            else {
                return $this->kaaSoftDatabase->has(KAASoftDatabase::$PAGES_TABLE_NAME,
                                                   [ "AND" => [ "url"   => $url,
                                                                "id[!]" => $excludePostId ] ]);
            }

        }

        /**
         * @param null   $offset
         * @param null   $perPage
         * @param string $sortBy
         * @param string $sortOrder
         * @return array|null
         */
        public function getPages($offset = null, $perPage = null, $sortBy = "id", $sortOrder = "ASC") {
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

            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$PAGES_TABLE_NAME,
                                                          [ "[><]" . KAASoftDatabase::$USERS_TABLE_NAME => [ "userId" => "id" ],
                                                            "[>]" . KAASoftDatabase::$IMAGES_TABLE_NAME => [ "imageId" => "id" ] ],
                                                          array_merge(Page::getDatabaseFieldNames(),
                                                                      [ KAASoftDatabase::$IMAGES_TABLE_NAME . ".id(imageId)",
                                                                        KAASoftDatabase::$IMAGES_TABLE_NAME . ".title(imageTitle)",
                                                                        KAASoftDatabase::$IMAGES_TABLE_NAME . ".path",
                                                                        KAASoftDatabase::$IMAGES_TABLE_NAME . ".uploadingDateTime",
                                                                        KAASoftDatabase::$USERS_TABLE_NAME . ".firstName",
                                                                        KAASoftDatabase::$USERS_TABLE_NAME . ".lastName",
                                                                        KAASoftDatabase::$USERS_TABLE_NAME . ".middleName",
                                                                        KAASoftDatabase::$PAGES_TABLE_NAME . ".id" ]),
                                                          $queryParams);


            if ($queryResult !== false) {
                $pages = [];

                foreach ($queryResult as $row) {
                    $page = Page::getObjectInstance($row);

                    $user = User::getObjectInstance($row);
                    $user->setId($page->getUserId());
                    $page->setUser($user);
                    $page->setBreadcrumbs($this->getParentPages($page));

                    if ($row["imageId"] != 0) {
                        $image = Image::getObjectInstance($row);
                        if (file_exists($image->getAbsolutePath())) {
                            $image->setId($row["imageId"]);
                            $image->setTitle($row["imageTitle"]);
                            $page->setImage($image);
                        }
                    }

                    $pages[] = $page;
                }

                return $pages;
            }

            return null;
        }

        /**
         * @param $pageId
         * @return Page|null
         */
        public function getPage($pageId) {
            $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$PAGES_TABLE_NAME,
                                                       [ "[><]" . KAASoftDatabase::$USERS_TABLE_NAME => [ "userId" => "id" ],
                                                         "[>]" . KAASoftDatabase::$IMAGES_TABLE_NAME => [ "imageId" => "id" ] ],
                                                       array_merge([ KAASoftDatabase::$IMAGES_TABLE_NAME . ".id(imageId)",
                                                                     KAASoftDatabase::$IMAGES_TABLE_NAME . ".title(imageTitle)",
                                                                     KAASoftDatabase::$IMAGES_TABLE_NAME . ".path",
                                                                     KAASoftDatabase::$IMAGES_TABLE_NAME . ".uploadingDateTime",
                                                                     KAASoftDatabase::$USERS_TABLE_NAME . ".firstName",
                                                                     KAASoftDatabase::$USERS_TABLE_NAME . ".lastName",
                                                                     KAASoftDatabase::$USERS_TABLE_NAME . ".middleName",
                                                                     KAASoftDatabase::$PAGES_TABLE_NAME . ".id" ],
                                                                   Page::getDatabaseFieldNames()),
                                                       [ KAASoftDatabase::$PAGES_TABLE_NAME . ".id" => $pageId ]);


            if ($queryResult !== false) {
                $page = Page::getObjectInstance($queryResult);

                $user = User::getObjectInstance($queryResult);
                $user->setId($page->getUserId());
                $page->setUser($user);

                if ($queryResult["imageId"] != 0) {
                    $image = Image::getObjectInstance($queryResult);
                    if (file_exists($image->getAbsolutePath())) {
                        $image->setId($queryResult["imageId"]);
                        $image->setTitle($queryResult["imageTitle"]);
                        $page->setImage($image);
                    }
                }


                return $page;
            }

            return null;
        }

        /**
         * @param $pageId
         * @return bool|int
         */
        public function deletePage($pageId) {
            return $this->kaaSoftDatabase->delete(KAASoftDatabase::$PAGES_TABLE_NAME,
                                                  [ "id" => $pageId ]);

        }


        /**
         * @return bool|int
         */
        public function getPagesCount() {
            return $this->kaaSoftDatabase->count(KAASoftDatabase::$PAGES_TABLE_NAME);
        }

        /**
         * @param $pageUrl
         * @return Page|null
         */
        public function getPageByURL($pageUrl) {
            $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$PAGES_TABLE_NAME,
                                                       [ "[>]" . KAASoftDatabase::$IMAGES_TABLE_NAME => [ "imageId" => "id" ] ],
                                                       array_merge(Page::getDatabaseFieldNames(),
                                                                   [ KAASoftDatabase::$PAGES_TABLE_NAME . ".id",
                                                                     KAASoftDatabase::$IMAGES_TABLE_NAME . ".id(imageId)",
                                                                     KAASoftDatabase::$IMAGES_TABLE_NAME . ".title(imageTitle)",
                                                                     KAASoftDatabase::$IMAGES_TABLE_NAME . ".path",
                                                                     KAASoftDatabase::$IMAGES_TABLE_NAME . ".uploadingDateTime" ]),
                                                       [ "AND" => [ "url" => $pageUrl,
                                                                    "OR"  => [ "status" => "Publish",
                                                                               "AND"    => [ "status"              => "Future",
                                                                                             "publishDateTime[<=]" => Helper::getDateTimeString() ] ] ] ]);


            if ($queryResult !== false) {
                $page = Page::getObjectInstance($queryResult);

                if ($queryResult["imageId"] != 0) {
                    $image = Image::getObjectInstance($queryResult);
                    if (file_exists($image->getAbsolutePath())) {
                        $image->setId($queryResult["imageId"]);
                        $image->setTitle($queryResult["imageTitle"]);
                        $page->setImage($image);
                    }
                }

                return $page;
            }

            return null;
        }

        /**
         * @param Page $page
         * @return bool
         */
        private function hasParentPage($page) {
            $parentId = $page->getParentId();

            return $parentId !== null and $parentId !== 0 and strcmp($parentId,
                                                                     "0") !== 0;
        }

        /**
         * @param Page  $page
         * @param array $pages
         */
        private function getParentPage($page, array &$pages) {
            $pages[] = $page;
            if ($this->hasParentPage($page)) {
                $parentPage = $this->getSimplePage($page->getParentId());
                if ($parentPage !== null) {
                    $this->getParentPage($parentPage,
                                         $pages);
                }
            }

            return;
        }

        /**
         * @param Page $page
         * @return array
         */
        public function getParentPages($page) {
            $pages = [];
            $this->getParentPage($page,
                                 $pages);

            return array_reverse($pages);
        }


        /**
         * @param $pageId
         * @return Page|null
         */
        public function getSimplePage($pageId) {
            $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$PAGES_TABLE_NAME,
                                                       array_merge(Page::getDatabaseFieldNames(),
                                                                   [ KAASoftDatabase::$PAGES_TABLE_NAME . ".id" ]),
                                                       [ KAASoftDatabase::$PAGES_TABLE_NAME . ".id" => $pageId ]);


            if ($queryResult !== false) {
                $page = Page::getObjectInstance($queryResult);

                return $page;
            }

            return null;
        }

        /**
         * @param Page $page
         * @return string
         */
        public function buildFullUrl($page) {
            $tree = $this->getParentPages($page);
            $url = "";
            for ($i = 0; $i < count($tree); $i++) {
                $node = $tree[$i];
                if ($node instanceof Page) {
                    $partialUrl = $node->getPartialUrl();
                    //if (strcmp($partialUrl,
                    //           "/") !== 0
                    //)
                    { // this condition is fix for main page to remove leading slash
                        $url .= $partialUrl;
                    }
                }
            }

            return $url;
        }

        /**
         * @return Tree|null
         */
        public function getPageTree() {

            $queryParams = [ "ORDER" => [ 'parentId' => "ASC",
                                          'title'    => "ASC" ] ];

            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$PAGES_TABLE_NAME,
                                                          array_merge(Page::getDatabaseFieldNames(),
                                                                      [ KAASoftDatabase::$PAGES_TABLE_NAME . ".id" ]),
                                                          $queryParams);


            if ($queryResult !== false) {
                $tree = new Tree();

                foreach ($queryResult as $row) {
                    $page = Page::getObjectInstance($row);

                    if (!$this->hasParentPage($page)) {
                        $node = new TreeNode($page);
                        $tree->addRootNode($node);
                        continue;
                    }
                    else {
                        $parentPageNode = $this->findTreeNode($tree,
                                                              $page->getParentId());

                        if ($parentPageNode !== null) {
                            $parentPageNode->addChild(new TreeNode($page));
                        }
                    }

                }

                return $tree;
            }

            return null;
        }

        /**
         * @param Tree $tree
         * @param      $pageId
         * @return TreeNode|null
         */
        private function findTreeNode($tree, $pageId) {
            $rootNodes = $tree->getRootNodes();

            return $this->visitTreeNodes($rootNodes,
                                         $pageId);
        }

        /**
         * @param $nodes
         * @param $pageId
         * @return null|TreeNode
         */
        private function visitTreeNodes($nodes, $pageId) {
            if ($nodes === null) {
                return null;
            }
            foreach ($nodes as $node) {
                if ($node instanceof TreeNode) {
                    if ($this->isNodeHasPage($node,
                                             $pageId)
                    ) {
                        return $node;
                    }
                    else {
                        $this->visitTreeNodes($node->getChildren(),
                                              $pageId);
                    }
                }
            }

            return null;
        }

        /**
         * @param TreeNode $node
         * @param          $pageId
         * @return bool
         */
        private function isNodeHasPage($node, $pageId) {
            $value = $node->getValue();
            if ($value instanceof Page) {
                if ($value->getId() === $pageId) {
                    return true;
                }
            }

            return false;
        }
    }