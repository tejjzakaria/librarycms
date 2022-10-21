<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Store;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Environment\Routes\Admin\StoreRoutes;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Paginator;

    /**
     * Class StoresAction
     * @package KAASoft\Controller\Admin\Store
     */
    class StoresAction extends AdminActionBase {
        /**
         * StoresAction constructor.
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
            $page = isset( $args["page"] ) ? $args["page"] : 1;
            $storeDatabaseHelper = new StoreDatabaseHelper($this);

            $perPage = $this->getPerPage(Session::STORE_PER_PAGE_NUMBER,
                                         $this->siteViewOptions->getOptionValue(SiteViewOptions::STORES_PER_PAGE));
            $sortColumn = $this->getSortingColumn(Session::STORE_SORTING_COLUMN);
            $sortOrder = $this->getSortingOrder(Session::STORE_SORTING_ORDER);


            $paginator = new Paginator($page,
                                       $perPage,
                                       $storeDatabaseHelper->getStoresCount());

            $this->smarty->assign("pages",
                                  $paginator->preparePages($page,
                                                           $this->routeController->getRouteString(StoreRoutes::STORE_LIST_VIEW_ROUTE)));

            $stores = $storeDatabaseHelper->getStores($paginator->getOffset(),
                                                      $perPage,
                                                      $sortColumn,
                                                      $sortOrder);

            $this->smarty->assign("stores",
                                  $stores);

            if (Helper::isAjaxRequest()) {
                return new DisplaySwitch('admin/stores/store-list.tpl');
            }
            else {
                return new DisplaySwitch('admin/stores/stores.tpl');
            }
        }
    }