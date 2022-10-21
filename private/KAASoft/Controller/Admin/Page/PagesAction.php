<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Page;


    use KAASoft\Environment\Routes\Admin\PageRoutes;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Paginator;

    /**
     * Class PagesAction
     * @package KAASoft\Controller\Admin\Page
     */
    class PagesAction extends AdminActionBase {

        /**
         * PagesAction constructor.
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
            // change response type, JSON is default for AJAX
            if (Helper::isAjaxRequest()) {
                Helper::setHtmlContentType();
            }
            $page = isset( $args["page"] ) ? $args["page"] : 1;

            $perPage = $this->getPerPage(Session::PAGE_PER_PAGE_NUMBER,
                                         $this->siteViewOptions->getOptionValue(SiteViewOptions::PAGES_PER_PAGE));
            $sortColumn = $this->getSortingColumn(Session::PAGE_SORTING_COLUMN,
                                                  KAASoftDatabase::$PAGES_TABLE_NAME . ".creationDateTime");
            $sortOrder = $this->getSortingOrder(Session::PAGE_SORTING_ORDER,
                                                "DESC");

            $postHelper = new PageDatabaseHelper($this);


            $paginator = new Paginator($page,
                                       $perPage,
                                       $postHelper->getPagesCount());
            $this->smarty->assign("pages",
                                  $paginator->preparePages($page,
                                                           $this->routeController->getRouteString(PageRoutes::PAGE_LIST_VIEW_ROUTE)));


            $this->smarty->assign("postPages",
                                  $postHelper->getPages($paginator->getOffset(),
                                                        $perPage,
                                                        $sortColumn,
                                                        $sortOrder));

            if (Helper::isAjaxRequest()) {
                return new DisplaySwitch("admin/public/pages/page-list.tpl");
            }
            else {
                return new DisplaySwitch("admin/public/pages/pages.tpl");
            }
        }
    }