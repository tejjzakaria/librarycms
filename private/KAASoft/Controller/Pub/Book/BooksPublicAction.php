<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Pub\Book;

    use KAASoft\Controller\ActionBase;
    use KAASoft\Controller\Pub\PublicDatabaseHelper;
    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Database\Entity\General\Book;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Environment\Routes\Pub\BookPublicRoutes;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\BookFilterQuery;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Paginator;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class BooksPublicAction
     * @package KAASoft\Controller\Pub\Book
     */
    class BooksPublicAction extends PublicActionBase {
        /**
         * BooksPublicAction constructor.
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
            $publicHelper = new PublicDatabaseHelper($this);

            $query = BookFilterQuery::getObjectInstance($_POST);
            $page = isset( $args["page"] ) ? $args["page"] : 1;

            $perPage = $this->getPerPage(Session::BOOK_PER_PAGE_NUMBER_FILTER_PUBLIC,
                                         $this->siteViewOptions->getOptionValue(SiteViewOptions::BOOKS_PER_PAGE_PUBLIC_FILTER));
            $sortColumn = $this->getSortingColumn(Session::BOOK_SORTING_COLUMN_PUBLIC,
                                                  KAASoftDatabase::$BOOKS_TABLE_NAME . ".creationDateTime");
            $sortOrder = $this->getSortingOrder(Session::BOOK_SORTING_ORDER_PUBLIC,
                                                "DESC");

            $bookViewType = $this->getBookViewType($this->siteViewOptions->getOptionValue(SiteViewOptions::BOOK_VIEW_TYPE));

            $totalBooks = $publicHelper->filterBooks($query,
                                                     true);
            $paginator = new Paginator($page,
                                       $perPage,
                                       $totalBooks);

            $this->smarty->assign("pages",
                                  $paginator->preparePages($page,
                                                           $this->routeController->getRouteString(BookPublicRoutes::BOOK_LIST_VIEW_PUBLIC_ROUTE)));

            $books = $publicHelper->filterBooks($query,
                                                false,
                                                $paginator->getOffset(),
                                                $perPage,
                                                $sortColumn,
                                                $sortOrder);

            $this->smarty->assign("books",
                                  $books);
            $this->smarty->assign("totalBooks",
                                  $totalBooks);

            $this->smarty->assign("bindings",
                                  $this->kaaSoftDatabase->getBindings());
            $this->smarty->assign("bookTypes",
                                  $this->kaaSoftDatabase->getBookTypes());
            $this->smarty->assign("bookSizes",
                                  $this->kaaSoftDatabase->getBookSizes());
            $this->smarty->assign("physicalForms",
                                  $this->kaaSoftDatabase->getPhysicalForms());
            $this->smarty->assign("bookCustomFields",
                                  Book::getCustomFields());

            if (Helper::isAjaxRequest()) {
                $template = "books/components/books-view-type-grid.tpl";
                switch ($bookViewType) {
                    case SiteViewOptions::BOOK_VIEW_TYPE_MINI_LIST:
                        $template = "books/components/books-view-type-mini-list.tpl";
                        break;
                    case SiteViewOptions::BOOK_VIEW_TYPE_GRID:
                        $template = "books/components/books-view-type-grid.tpl";
                        break;
                    case SiteViewOptions::BOOK_VIEW_TYPE_LIST:
                        $template = "books/components/books-view-type-list.tpl";
                        break;
                    default:

                }
            }
            else {
                $template = "books/books-grid.tpl";
                switch ($bookViewType) {
                    case SiteViewOptions::BOOK_VIEW_TYPE_MINI_LIST:
                        $template = "books/books-mini-list.tpl";
                        break;
                    case SiteViewOptions::BOOK_VIEW_TYPE_GRID:
                        $template = "books/books-grid.tpl";
                        break;
                    case SiteViewOptions::BOOK_VIEW_TYPE_LIST:
                        $template = "books/books-list.tpl";
                        break;
                }
            }

            return new DisplaySwitch($template);
        }

        protected function getBook($sessionPerPageVarName, $defaultValue = 10) {
            $perPage = $defaultValue;
            if (isset( $_POST[ActionBase::PER_PAGE] )) {
                $perPage = ValidationHelper::getNullableInt($_POST[ActionBase::PER_PAGE]);
                $this->session->addSessionValue($sessionPerPageVarName,
                                                $perPage);
            }
            else {
                $perPage = $this->session->getSessionValue($sessionPerPageVarName,
                                                           $perPage);
            }

            return $perPage;
        }
    }