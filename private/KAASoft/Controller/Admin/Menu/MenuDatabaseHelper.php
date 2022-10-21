<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Menu;

    use KAASoft\Controller\DatabaseHelper;
    use KAASoft\Database\Entity\Post\Page;
    use KAASoft\Database\Entity\Post\Post;
    use KAASoft\Database\Entity\Util\Menu;
    use KAASoft\Database\Entity\Util\MenuItem;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\Tree;
    use KAASoft\Util\TreeNode;

    /**
     * Class MenuDatabaseHelper
     * @package KAASoft\Controller\Admin\Menu
     */
    class MenuDatabaseHelper extends DatabaseHelper {
        /**
         * @param Menu $menu
         * @return bool|$userId
         */
        public function saveMenu(Menu $menu) {
            $data = $menu->getDatabaseArray();
            if ($menu->getId() == null) {
                return $this->kaaSoftDatabase->insert(KAASoftDatabase::$MENUS_TABLE_NAME,
                                                      $data);
            }

            else {
                return $this->kaaSoftDatabase->update(KAASoftDatabase::$MENUS_TABLE_NAME,
                                                      $data,
                                                      [ "id" => $menu->getId() ]);
            }
        }

        /**
         * @param $menuId
         * @return bool
         */
        public function hasMenu($menuId) {
            return $this->kaaSoftDatabase->has(KAASoftDatabase::$MENUS_TABLE_NAME,
                                               [ "id" => $menuId ]);


        }

        /**
         * @param null   $offset
         * @param null   $perPage
         * @param string $sortBy
         * @param string $sortOrder
         * @return array|null
         */
        public function getMenus($offset = null, $perPage = null, $sortBy = "id", $sortOrder = "ASC") {
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

            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$MENUS_TABLE_NAME,
                                                          array_merge(Menu::getDatabaseFieldNames(),
                                                                      [ KAASoftDatabase::$MENUS_TABLE_NAME . ".id" ]),
                                                          $queryParams);

            if ($queryResult !== false) {
                $menus = array();

                foreach ($queryResult as $row) {
                    $menu = Menu::getObjectInstance($row);

                    $menus[] = $menu;
                }

                return $menus;
            }

            return null;
        }

        /**
         * @param $menuId
         * @return Menu|null
         */
        public function getMenu($menuId) {
            $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$MENUS_TABLE_NAME,
                                                       array_merge(Menu::getDatabaseFieldNames(),
                                                                   [ KAASoftDatabase::$MENUS_TABLE_NAME . ".id" ]),
                                                       [ KAASoftDatabase::$MENUS_TABLE_NAME . ".id" => $menuId ]);

            if ($queryResult !== false) {
                $menu = Menu::getObjectInstance($queryResult);

                return $menu;
            }

            return null;
        }

        /**
         * @param $menuId
         * @return bool|int
         */
        public function deleteMenu($menuId) {
            return $this->kaaSoftDatabase->delete(KAASoftDatabase::$MENUS_TABLE_NAME,
                                                  [ "id" => $menuId ]);

        }

        /**
         * @return bool|int
         */
        public function getMenusCount() {
            return $this->kaaSoftDatabase->count(KAASoftDatabase::$MENUS_TABLE_NAME);
        }

        public function deleteMenuItems($menuId) {
            return $this->kaaSoftDatabase->delete(KAASoftDatabase::$MENU_ITEMS_TABLE_NAME,
                                                  [ "menuId" => $menuId ]);
        }

        /**
         * @param $menuArray
         * @return bool
         */
        public function saveMenuItems($menuArray) {
            $result = true;
            foreach ($menuArray as $menuItem) {
                if ($menuItem instanceof MenuItem) {
                    $data = $menuItem->getDatabaseArray();
                    $result = ( $result and ( $this->kaaSoftDatabase->insert(KAASoftDatabase::$MENU_ITEMS_TABLE_NAME,
                                                                             $data) !== false ) );
                    if ($result === false) {
                        return false;
                    }
                }
            }

            return $result;
        }

        /**
         * @param $menuId
         * @return Tree|null
         */
        public function getMenuItemsTree($menuId) {
            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$MENU_ITEMS_TABLE_NAME,
                                                          [ "[>]" . KAASoftDatabase::$PAGES_TABLE_NAME => [ "pageId" => "id" ],
                                                            "[>]" . KAASoftDatabase::$POSTS_TABLE_NAME => [ "postId" => "id" ] ],
                                                          array_merge([ KAASoftDatabase::$PAGES_TABLE_NAME . ".title(pageTitle)",
                                                                        KAASoftDatabase::$POSTS_TABLE_NAME . ".title(postTitle)",
                                                                        KAASoftDatabase::$MENU_ITEMS_TABLE_NAME . ".id" ],
                                                                      MenuItem::getDatabaseFieldNames()),
                                                          [ "menuId" => $menuId,
                                                            "ORDER"  => KAASoftDatabase::$MENU_ITEMS_TABLE_NAME . ".order" ]);


            if ($queryResult !== false) {

                $tree = new Tree();
                foreach ($queryResult as $row) {
                    $menuItem = MenuItem::getObjectInstance($row);

                    if ($row["pageId"] !== null) {
                        $page = new Page($row["pageId"]);
                        $page->setTitle($row["pageTitle"]);
                        $menuItem->setPage($page);
                    }

                    if ($row["postId"] !== null) {
                        $post = new Post($row["postId"]);
                        $post->setTitle($row["postTitle"]);
                        $menuItem->setPost($post);
                    }

                    if (!$this->hasParentMenuItem($menuItem)) {
                        $node = new TreeNode($menuItem);
                        $tree->addRootNode($node);
                        continue;
                    }
                    else {
                        $parentMenuItemNode = $this->findTreeNode($tree,
                                                                  $menuItem->getParentId());

                        if ($parentMenuItemNode !== null) {
                            $parentMenuItemNode->addChild(new TreeNode($menuItem));
                        }
                    }

                }

                return $tree;
            }

            return null;
        }

        /**
         * @param $menuItem MenuItem
         * @return bool
         */
        private function hasParentMenuItem($menuItem) {
            $parentId = $menuItem->getParentId();

            return $parentId !== null and $parentId !== 0 and strcmp($parentId,
                                                                     "0") !== 0;
        }

        /**
         * @param Tree $tree
         * @param      $menuItemId
         * @return TreeNode|null
         */
        private function findTreeNode($tree, $menuItemId) {
            $rootNodes = $tree->getRootNodes();

            return $this->visitTreeNodes($rootNodes,
                                         $menuItemId);
        }

        /**
         * @param $nodes
         * @param $menuItemId
         * @return TreeNode|null
         */
        private function visitTreeNodes($nodes, $menuItemId) {
            if ($nodes === null) {
                return null;
            }
            foreach ($nodes as $node) {
                if ($node instanceof TreeNode) {
                    if ($this->isNodeHasMenuItem($node,
                                                 $menuItemId)
                    ) {
                        return $node;
                    }
                    else {
                        $result = $this->visitTreeNodes($node->getChildren(),
                                                        $menuItemId);
                        if ($result != null) {
                            return $result;
                        }
                    }
                }
            }

            return null;
        }

        /**
         * @param TreeNode $node
         * @param          $menuItemId
         * @return bool
         */
        private function isNodeHasMenuItem($node, $menuItemId) {
            $value = $node->getValue();
            if ($value instanceof MenuItem) {
                if ($value->getId() === $menuItemId) {
                    return true;
                }
            }

            return false;
        }

        /**
         * @return array
         */
        public function getPageUrls() {
            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$PAGES_TABLE_NAME,
                                                          [ "id",
                                                            "url" ]);
            $result = [];
            if ($queryResult !== false) {

                foreach ($queryResult as $row) {
                    $result[$row["id"]] = $row["url"];
                }
            }

            return $result;
        }

        /**
         * @return array
         */
        public function getIndexMenuPages() {
            $queryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$PAGES_TABLE_NAME,
                                                          array_merge(Page::getDatabaseFieldNames(),
                                                                      [ KAASoftDatabase::$PAGES_TABLE_NAME . ".id" ]),
                                                          [ "isIndexMenu" => true,
                                                            "ORDER"       => [ "parentId" => "ASC",
                                                                               "title"    => "ASC" ] ]);
            $result = [];
            if ($queryResult !== false) {

                foreach ($queryResult as $row) {
                    $page = Page::getObjectInstance($row);
                    $result[$page->getId()] = $page;
                }

            }

            return $result;
        }
    }