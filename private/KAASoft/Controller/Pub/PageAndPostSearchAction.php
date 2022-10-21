<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Pub;

    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;

    /**
     * Class PageAndPostSearchAction
     * @package KAASoft\Controller\Pub
     */
    class PageAndPostSearchAction extends PublicActionBase {

        /**
         * PageAndPostSearchAction constructor.
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

            $this->smarty->assign("postPages",
                                  $publicHelper->searchPages($searchText,
                                                             $this->siteViewOptions->getOptionValue(SiteViewOptions::MAX_SEARCH_RESULT_NUMBER)));
            $this->smarty->assign("posts",
                                  $publicHelper->searchPosts($searchText,
                                                             $this->siteViewOptions->getOptionValue(SiteViewOptions::MAX_SEARCH_RESULT_NUMBER)));

            return new DisplaySwitch('general/search.tpl');
        }
    }