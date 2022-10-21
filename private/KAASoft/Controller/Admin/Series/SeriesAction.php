<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Series;

    use KAASoft\Environment\Routes\Admin\SeriesRoutes;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Paginator;

    /**
     * Class SeriesAction
     * @package KAASoft\Controller\Admin\Series
     */
    class SeriesAction extends AdminActionBase {
        /**
         * SeriesAction constructor.
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
            $seriesDatabaseHelper = new SeriesDatabaseHelper($this);

            $perPage = $this->getPerPage(Session::SERIES_PER_PAGE_NUMBER,
                                         $this->siteViewOptions->getOptionValue(SiteViewOptions::SERIES_PER_PAGE));
            $sortColumn = $this->getSortingColumn(Session::SERIES_SORTING_COLUMN);
            $sortOrder = $this->getSortingOrder(Session::SERIES_SORTING_ORDER);


            $paginator = new Paginator($page,
                                       $perPage,
                                       $seriesDatabaseHelper->getSeriesCount());

            $this->smarty->assign("pages",
                                  $paginator->preparePages($page,
                                                           $this->routeController->getRouteString(SeriesRoutes::SERIES_LIST_VIEW_ROUTE)));

            $series = $seriesDatabaseHelper->getSeriesList($paginator->getOffset(),
                                                           $perPage,
                                                           $sortColumn,
                                                           $sortOrder);

            $this->smarty->assign("seriesList",
                                  $series);

            if (Helper::isAjaxRequest()) {
                return new DisplaySwitch('admin/series/series-list.tpl');
            }
            else {
                return new DisplaySwitch('admin/series/series.tpl');
            }
        }
    }