<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Menu;

    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\Util\Menu;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;
    use PDOException;

    /**
     * Class MenuEditAction
     * @package KAASoft\Controller\Admin\Menu
     */
    class MenuEditAction extends AdminActionBase {

        /**
         * MenuEditAction constructor.
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
            $menuDatabaseHelper = new MenuDatabaseHelper($this);
            if (Helper::isAjaxRequest()) {
                if (Helper::isPostRequest()) { // POST request
                    try {
                        if ($this->startDatabaseTransaction()) {

                            $menu = Menu::getObjectInstance($_POST);
                            $menu->setId($menuId);
                            if (!$menuDatabaseHelper->hasMenu($menuId)) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = sprintf(_("There is no Menu with ID '<b>%d</b>'."),
                                                        $menu->getId());
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch();
                            }

                            $queryResult = $menuDatabaseHelper->saveMenu($menu);
                            if ($queryResult === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = _("Menu saving is failed for some reason.");
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch();
                            }

                            $this->commitDatabaseChanges();
                            $this->putArrayToAjaxResponse([ "menuId" => $menuId ]);
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

                return new DisplaySwitch();
            }
            else {
                $menu = $menuDatabaseHelper->getMenu($menuId);

                if ($menu === null) {
                    $errorMessage = sprintf(_("Menu with ID '%d' is not exist."),
                                            $menuId);
                    $this->session->addSessionMessage($errorMessage,
                                                      Message::MESSAGE_STATUS_ERROR);

                    return new DisplaySwitch(null,
                                             $this->routeController->getRouteString(GeneralPublicRoutes::PAGE_IS_NOT_FOUND_ROUTE));
                }
                $this->smarty->assign("action",
                                      "edit");
                $this->smarty->assign("menu",
                                      $menu);
                $this->smarty->assign("menuItemsTree",
                                      $menuDatabaseHelper->getMenuItemsTree($menuId));

                return new DisplaySwitch('admin/general/menu.tpl');
            }
        }
    }