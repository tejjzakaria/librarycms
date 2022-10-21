<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Pub\Book;


    use KAASoft\Controller\Pub\PublicDatabaseHelper;
    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Environment\Routes\Pub\BookPublicRoutes;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Paginator;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class BookSearchPublicAction
     * @package KAASoft\Controller\Pub\Book
     */
    class BookSearchPublicAction extends PublicActionBase {

        /**
         * BookSearchPublicAction constructor.
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

            if (Helper::isPostRequest()) {
                $page = isset( $args["page"] ) ? $args["page"] : 1;
                $searchText = ValidationHelper::getString($_POST["searchText"]);
            }
            else {
                $page = isset( $_GET["page"] ) ? $_GET["page"] : 1;
                $searchText = ValidationHelper::getString($_GET["q"]);
            }

            $perPage = $this->getPerPage(Session::BOOK_PER_PAGE_NUMBER_PUBLIC,
                                         $this->siteViewOptions->getOptionValue(SiteViewOptions::BOOKS_PER_PAGE_PUBLIC));
            $sortColumn = $this->getSortingColumn(Session::BOOK_SORTING_COLUMN_PUBLIC,
                                                  KAASoftDatabase::$BOOKS_TABLE_NAME . ".id");
            $sortOrder = $this->getSortingOrder(Session::BOOK_SORTING_ORDER_PUBLIC,
                                                "DESC");

            $publicHelper = new PublicDatabaseHelper($this);
            $totalBooks = $publicHelper->getBookSearchCount($searchText);
            $paginator = new Paginator($page,
                                       $perPage,
                                       $totalBooks);

            $books = $publicHelper->searchBooks($searchText,
                                                false,
                                                $paginator->getOffset(),
                                                $perPage,
                                                $sortColumn,
                                                $sortOrder);

            $this->smarty->assign("pages",
                                  $paginator->preparePages($page,
                                                           $this->getRouteString(BookPublicRoutes::BOOK_SEARCH_PUBLIC_ROUTE)));


            $this->smarty->assign("books",
                                  $books);
            $this->smarty->assign("searchText",
                                  $searchText);
            $this->smarty->assign("totalBooks",
                                  $totalBooks);

            if (Helper::isAjaxRequest()) {
                return new DisplaySwitch('books/components/books-by-category-list.tpl');

            }
            else {
                return new DisplaySwitch('books/search-books.tpl');
            }
        }
    }