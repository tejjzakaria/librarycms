<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Pub\Post;


    use KAASoft\Controller\Pub\PublicDatabaseHelper;
    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;

    /**
     * Class PostSearchAction
     * @package KAASoft\Controller\Pub\Page
     */
    class PostSearchAction extends PublicActionBase {

        /**
         * PostSearchAction constructor.
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

            Helper::printAsJSON($publicHelper->searchPostsByTitle($searchText,
                                                                  $this->siteViewOptions->getOptionValue(SiteViewOptions::MAX_SEARCH_RESULT_NUMBER)));

            return new DisplaySwitch();
        }
    }