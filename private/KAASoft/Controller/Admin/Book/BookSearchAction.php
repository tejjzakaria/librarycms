<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Book;

    use KAASoft\Controller\Pub\PublicDatabaseHelper;
    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Database\Entity\General\BookCopy;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Environment\Routes\Admin\BookRoutes;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Paginator;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class BookSearchAction
     * @package KAASoft\Controller\Admin\Book
     */
    class BookSearchAction extends PublicActionBase {

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
            $this->setJsonContentType();
            $page = isset( $args["page"] ) ? $args["page"] : 1;
            $searchText = ValidationHelper::getString($_POST["searchText"]);

            $perPage = $this->getPerPage(Session::BOOK_PER_PAGE_NUMBER,
                                         $this->siteViewOptions->getOptionValue(SiteViewOptions::BOOKS_PER_PAGE_ADMIN));
            $sortColumn = $this->getSortingColumn(Session::BOOK_SORTING_COLUMN,
                                                  KAASoftDatabase::$BOOKS_TABLE_NAME . ".id");
            $sortOrder = $this->getSortingOrder(Session::BOOK_SORTING_ORDER,
                                                "DESC");

            $publicHelper = new PublicDatabaseHelper($this);
            $paginator = new Paginator($page,
                                       $perPage,
                                       $publicHelper->getBookSearchCount($searchText));

            $pages = $paginator->preparePages($page,
                                              $this->getRouteString(BookRoutes::BOOK_SEARCH_ROUTE,
                                                                    [ "searchText" => $searchText ]));

            $books = $publicHelper->searchBooks($searchText,
                                                true,
                                                $paginator->getOffset(),
                                                $perPage,
                                                $sortColumn,
                                                $sortOrder);

            $this->putArrayToAjaxResponse([ "books"        => $books,
                                            "pages"        => $pages,
                                            "bookStatuses" => BookCopy::getBookStatuses() ]);

            return new DisplaySwitch();
        }
    }