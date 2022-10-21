<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Pub\Page;

    use KAASoft\Controller\Pub\PublicDatabaseHelper;
    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;

    /**
     * Class PageSearchAction
     * @package KAASoft\Controller\Pub\Page
     */
    class PageSearchAction extends PublicActionBase {

        /**
         * PageSearchAction constructor.
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
            $searchText = $_POST["searchText"];
            $publicHelper = new PublicDatabaseHelper($this);

            Helper::printAsJSON($publicHelper->searchPagesByTitle($searchText,
                                                                  $this->siteViewOptions->getOptionValue(SiteViewOptions::MAX_SEARCH_RESULT_NUMBER)));

            return new DisplaySwitch();
        }
    }