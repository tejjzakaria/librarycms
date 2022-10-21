<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Pub\Genre;

    use KAASoft\Controller\Pub\PublicDatabaseHelper;
    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;

    /**
     * Class GenreSearchAction
     * @package KAASoft\Controller\Pub\Genre
     */
    class GenreSearchAction extends PublicActionBase {

        /**
         * GenreSearchAction constructor.
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
                    $publicHelper = new PublicDatabaseHelper($this);

                    $this->putArrayToAjaxResponse($publicHelper->searchGenres($searchText,
                                                                              $this->siteViewOptions->getOptionValue(SiteViewOptions::MAX_SEARCH_RESULT_NUMBER)));
                }
            }

            return new DisplaySwitch();
        }
    }