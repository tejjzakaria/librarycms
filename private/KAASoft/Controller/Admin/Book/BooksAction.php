<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Book;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Pub\PublicDatabaseHelper;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Environment\Routes\Admin\BookRoutes;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Paginator;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class BooksAction
     * @package KAASoft\Controller\Admin\Book
     */
    class BooksAction extends AdminActionBase {
        /**
         * BooksAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         */
        protected function action($args) {
            $page = isset( $args["page"] ) ? $args["page"] : 1;
            $searchText = ValidationHelper::getString($_POST["searchText"]);
            $bookDatabaseHelper = new BookDatabaseHelper($this);
            $publicHelper = new PublicDatabaseHelper($this);

            $perPage = $this->getPerPage(Session::BOOK_PER_PAGE_NUMBER,
                                         $this->siteViewOptions->getOptionValue(SiteViewOptions::BOOKS_PER_PAGE_ADMIN));
            $sortColumn = $this->getSortingColumn(Session::BOOK_SORTING_COLUMN,
                                                  KAASoftDatabase::$BOOKS_TABLE_NAME . ".id");
            $sortOrder = $this->getSortingOrder(Session::BOOK_SORTING_ORDER,
                                                "DESC");

            if ($searchText !== null) {
                $totalBooks = $publicHelper->getBookSearchCount($searchText);
            }
            else {
                $totalBooks = $bookDatabaseHelper->getBooksCount();
            }

            $paginator = new Paginator($page,
                                       $perPage,
                                       $totalBooks);

            $this->smarty->assign("pages",
                                  $paginator->preparePages($page,
                                                           $this->routeController->getRouteString(BookRoutes::BOOK_LIST_VIEW_ROUTE)));

            $books = ( $searchText !== null ? $publicHelper->searchBooks($searchText,
                                                                         false,
                                                                         $paginator->getOffset(),
                                                                         $perPage,
                                                                         $sortColumn,
                                                                         $sortOrder) : $bookDatabaseHelper->getBooks($paginator->getOffset(),
                                                                                                                     $perPage,
                                                                                                                     $sortColumn,
                                                                                                                     $sortOrder) );

            $this->smarty->assign("books",
                                  $books);
            $this->smarty->assign("totalBooks",
                                  $totalBooks);
            if ($searchText !== null) {
                $this->smarty->assign("searchText",
                                      $searchText);
            }

            if (Helper::isAjaxRequest()) {
                return new DisplaySwitch('admin/books/books-list.tpl');
            }
            else {
                return new DisplaySwitch('admin/books/books.tpl');
            }
        }
    }