<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Store;

    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;

    /**
     * Class StoreSearchAction
     * @package KAASoft\Controller\Pub\Store
     */
    class StoreSearchAction extends PublicActionBase {

        /**
         * StoreSearchAction constructor.
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
            if (Helper::isAjaxRequest()) {
                if (Helper::isPostRequest()) {
                    $searchText = $_POST["searchText"];
                    $storeDatabaseHelper = new StoreDatabaseHelper($this);

                    $this->putArrayToAjaxResponse($storeDatabaseHelper->searchStores($searchText,
                                                                                     $this->siteViewOptions->getOptionValue(SiteViewOptions::MAX_SEARCH_RESULT_NUMBER)));
                }
            }

            return new DisplaySwitch();
        }
    }