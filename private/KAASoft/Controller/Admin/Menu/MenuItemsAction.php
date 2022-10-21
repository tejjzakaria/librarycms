<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Menu;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\Util\MenuItem;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use PDOException;

    /**
     * Class MenuItemsAction
     * @package KAASoft\Controller\Admin\Menu
     */
    class MenuItemsAction extends AdminActionBase {

        private $tmpOrder;

        /**
         * MenusAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         */
        protected function action($args) {
            $menuId = $args["menuId"];

            if (Helper::isAjaxRequest()) {
                if (Helper::isPostRequest()) { // POST request
                    try {
                        $menuDatabaseHelper = new MenuDatabaseHelper($this);
                        if (isset( $_POST["tree"] )) {
                            $tree = json_decode($_POST["tree"]);
                            $menuArray = [];
                            $this->tmpOrder = 1;
                            foreach ($tree as $treeItem) {
                                $menuArray = $this->createMenu($menuArray,
                                                               $menuId,
                                                               $treeItem);
                            }

                            if ($this->startDatabaseTransaction()) {
                                $menuDatabaseHelper->deleteMenuItems($menuId);

                                $queryResult = $menuDatabaseHelper->saveMenuItems($menuArray);
                                if ($queryResult === false) {
                                    $this->rollbackDatabaseChanges();
                                    $errorMessage = _("Menu items saving is failed for some reason.");
                                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                    return new DisplaySwitch();
                                }

                                $this->commitDatabaseChanges();
                                $this->putArrayToAjaxResponse([ "menuId" => $menuId ]);
                            }
                        }
                        else {
                            $errorMessage = _("There is no menu tree in request.");
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                            return new DisplaySwitch();
                        }
                    }
                    catch (PDOException $e) {
                        $this->rollbackDatabaseChanges();
                        ControllerBase::getLogger()->error($e->getMessage(),
                                                           $e);
                        $errorMessage = sprintf(_("Couldn't save Menu.%s%s"),
                                                Helper::HTML_NEW_LINE,
                                                $e->getMessage());
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                    }
                }
            }

            return new DisplaySwitch();
        }

        private function createMenu($menuArray, $menuId, $treeItem, $parentId = 0) {
            $menuItem = new MenuItem($treeItem->id);
            $menuItem->setLink($treeItem->link);
            $menuItem->setOrder($this->tmpOrder);
            $menuItem->setClass($treeItem->class);
            $menuItem->setPageId($treeItem->pageId);
            $menuItem->setPostId($treeItem->postId);
            $menuItem->setTitle($treeItem->title);
            $menuItem->setParentId($parentId);
            $menuItem->setMenuId($menuId);

            $this->tmpOrder++;
            $menuArray[] = $menuItem;

            if (isset( $treeItem->children ) and is_array($treeItem->children)) {
                foreach ($treeItem->children as $treeItemChild) {
                    $menuArray = $this->createMenu($menuArray,
                                                   $menuId,
                                                   $treeItemChild,
                                                   $menuItem->getId());
                }

            }

            return $menuArray;
        }
    }