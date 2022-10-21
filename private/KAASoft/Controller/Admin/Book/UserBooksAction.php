<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Book;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Environment\Routes\Admin\BookRoutes;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Paginator;

    /**
     * Class UserBooksAction
     * @package KAASoft\Controller\Admin\Book
     */
    class UserBooksAction extends AdminActionBase {
        /**
         * UserBooksAction constructor.
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
            $userId = $args["userId"];
            $page = isset( $args["page"] ) ? $args["page"] : 1;
            $bookDatabaseHelper = new BookDatabaseHelper($this);

            $perPage = $this->getPerPage(Session::BOOK_PER_PAGE_NUMBER,
                                         $this->siteViewOptions->getOptionValue(SiteViewOptions::BOOKS_PER_PAGE_ADMIN));
            $sortColumn = $this->getSortingColumn(Session::ISSUED_BOOK_SORTING_COLUMN,
                                                  KAASoftDatabase::$ISSUES_TABLE_NAME . ".issueDate");
            $sortOrder = $this->getSortingOrder(Session::ISSUED_BOOK_SORTING_ORDER,
                                                "DESC");

            $paginator = new Paginator($page,
                                       $perPage,
                                       $bookDatabaseHelper->getUserBooksCount($userId));

            $this->smarty->assign("pages",
                                  $paginator->preparePages($page,
                                                           $this->routeController->getRouteString(BookRoutes::USER_BOOKS_ADMIN_ROUTE,
                                                                                                  [ "userId" => $userId ])));

            $books = $bookDatabaseHelper->getUserBooks($userId,
                                                       $paginator->getOffset(),
                                                       $perPage,
                                                       $sortColumn,
                                                       $sortOrder);

            $this->smarty->assign("books",
                                  $books);

            if (Helper::isAjaxRequest()) {
                return new DisplaySwitch('admin/users/userBooks-list.tpl');
            }
            else {
                return new DisplaySwitch('admin/users/userBooks.tpl');
            }
        }
    }