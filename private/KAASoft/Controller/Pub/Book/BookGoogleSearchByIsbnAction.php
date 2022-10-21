<?php
    /**
 * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
 */

    namespace KAASoft\Controller\Pub\Book;

    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\ExternalAPI\Google\BookAPI;

    /**
     * Class BookGoogleSearchByIsbnAction
     * @package KAASoft\Controller\Pub\Book
     */
    class BookGoogleSearchByIsbnAction extends PublicActionBase {

        /**
         * BookGoogleSearchByIsbnAction constructor.
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

            $this->putArrayToAjaxResponse([ "books" => $bookApi->searchBookByIsbn($searchText,
                                                                            $this->siteViewOptions->getOptionValue(SiteViewOptions::MAX_SEARCH_RESULT_NUMBER)) ]);

            return new DisplaySwitch();
        }
    }