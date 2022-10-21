<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Pub\Book;

    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\ExternalAPI\Google\BookAPI;

    /**
     * Class BookGoogleSearchAction
     * @package KAASoft\Controller\Pub\Book
     */
    class BookGoogleSearchAction extends PublicActionBase {

        /**
         * BookSearchAction constructor.
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

            $bookApi = new BookAPI();

            $this->putArrayToAjaxResponse([ "books" => $bookApi->searchBook($searchText,
                                                                            $this->siteViewOptions->getOptionValue(SiteViewOptions::MAX_SEARCH_RESULT_NUMBER)) ]);

            return new DisplaySwitch();
        }
    }