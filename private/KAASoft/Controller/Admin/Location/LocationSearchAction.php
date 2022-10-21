<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Location;

    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class LocationSearchAction
     * @package KAASoft\Controller\Pub\Location
     */
    class LocationSearchAction extends PublicActionBase {

        /**
         * LocationSearchAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute,
                                true);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         */
        protected function action($args) {
            $storeIds = ValidationHelper::getArray($_POST["stores"]);
            if (Helper::isAjaxRequest()) {
                if (Helper::isPostRequest()) {
                    $searchText = $_POST["searchText"];
                    $storeDatabaseHelper = new LocationDatabaseHelper($this);

                    $this->putArrayToAjaxResponse($storeDatabaseHelper->searchLocations($searchText,
                                                                                        $storeIds,
                                                                                        $this->siteViewOptions->getOptionValue(SiteViewOptions::MAX_SEARCH_RESULT_NUMBER)));
                }
            }

            return new DisplaySwitch();
        }
    }