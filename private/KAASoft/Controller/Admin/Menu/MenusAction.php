<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Menu;

    use KAASoft\Environment\Routes\Admin\UtilRoutes;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Paginator;

    /**
     * Class MenusAction
     * @package KAASoft\Controller\Admin\Menu
     */
    class MenusAction extends AdminActionBase {

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
            if (Helper::isAjaxRequest()) {
                Helper::setHtmlContentType();
            }
            $page = isset( $args["page"] ) ? $args["page"] : 1;


            $perPage = $this->getPerPage(Session::MENU_PER_PAGE_NUMBER,
                                         $this->siteViewOptions->getOptionValue(SiteViewOptions::MENUS_PER_PAGE));
            $sortColumn = $this->getSortingColumn(Session::MENU_SORTING_COLUMN);
            $sortOrder = $this->getSortingOrder(Session::MENU_SORTING_ORDER);

            $menuDatabaseHelper = new MenuDatabaseHelper($this);


            $paginator = new Paginator($page,
                                       $perPage,
                                       $menuDatabaseHelper->getMenusCount());
            $this->smarty->assign("pages",
                                  $paginator->preparePages($page,
                                                           $this->routeController->getRouteString(UtilRoutes::MENU_LIST_VIEW_ROUTE)));


            $this->smarty->assign("menus",
                                  $menuDatabaseHelper->getMenus($paginator->getOffset(),
                                                                $perPage,
                                                                $sortColumn,
                                                                $sortOrder));

            if (Helper::isAjaxRequest()) {
                return new DisplaySwitch('admin/general/menu-list.tpl');
            }
            else {
                return new DisplaySwitch('admin/general/menus.tpl');
            }
        }
    }