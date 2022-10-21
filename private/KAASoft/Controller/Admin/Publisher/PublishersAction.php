<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Publisher;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Environment\Routes\Admin\PublisherRoutes;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Paginator;

    /**
     * Class PublishersAction
     * @package KAASoft\Controller\Admin\Publisher
     */
    class PublishersAction extends AdminActionBase {
        /**
         * PublishersAction constructor.
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
            $publisherDatabaseHelper = new PublisherDatabaseHelper($this);

            $perPage = $this->getPerPage(Session::PUBLISHER_PER_PAGE_NUMBER,
                                         $this->siteViewOptions->getOptionValue(SiteViewOptions::PUBLISHERS_PER_PAGE));
            $sortColumn = $this->getSortingColumn(Session::PUBLISHER_SORTING_COLUMN);
            $sortOrder = $this->getSortingOrder(Session::PUBLISHER_SORTING_ORDER);

            $paginator = new Paginator($page,
                                       $perPage,
                                       $publisherDatabaseHelper->getPublishersCount());

            $this->smarty->assign("pages",
                                  $paginator->preparePages($page,
                                                           $this->routeController->getRouteString(PublisherRoutes::PUBLISHER_LIST_VIEW_ROUTE)));

            $publishers = $publisherDatabaseHelper->getPublishers(null,
                                                                  $paginator->getOffset(),
                                                                  $perPage,
                                                                  $sortColumn,
                                                                  $sortOrder);

            $this->smarty->assign("publishers",
                                  $publishers);

            if (Helper::isAjaxRequest()) {
                return new DisplaySwitch('admin/publishers/publisher-list.tpl');
            }
            else {
                return new DisplaySwitch('admin/publishers/publishers.tpl');
            }
        }
    }