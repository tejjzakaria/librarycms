<?php
    /**
 * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
 */

    namespace KAASoft\Controller\Admin\Location;

    use KAASoft\Environment\Routes\Admin\LocationRoutes;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Paginator;

    /**
     * Class LocationsAction
     * @package KAASoft\Controller\Admin\Location
     */
    class LocationsAction extends AdminActionBase {
        /**
         * LocationsAction constructor.
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
            $locationDatabaseHelper = new LocationDatabaseHelper($this);

            $perPage = $this->getPerPage(Session::LOCATION_PER_PAGE_NUMBER,
                                         $this->siteViewOptions->getOptionValue(SiteViewOptions::LOCATIONS_PER_PAGE));
            $sortColumn = $this->getSortingColumn(Session::LOCATION_SORTING_COLUMN);
            $sortOrder = $this->getSortingOrder(Session::LOCATION_SORTING_ORDER);


            $paginator = new Paginator($page,
                                       $perPage,
                                       $locationDatabaseHelper->getLocationsCount());

            $this->smarty->assign("pages",
                                  $paginator->preparePages($page,
                                                           $this->routeController->getRouteString(LocationRoutes::LOCATION_LIST_VIEW_ROUTE)));

            $locations = $locationDatabaseHelper->getLocations($paginator->getOffset(),
                                                      $perPage,
                                                      $sortColumn,
                                                      $sortOrder);

            $this->smarty->assign("locations",
                                  $locations);

            if (Helper::isAjaxRequest()) {
                return new DisplaySwitch('admin/locations/location-list.tpl');
            }
            else {
                return new DisplaySwitch('admin/locations/locations.tpl');
            }
        }
    }