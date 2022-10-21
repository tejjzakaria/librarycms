<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Book;

    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Database\Entity\General\User;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Environment\Routes\Admin\BookRoutes;
    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;
    use KAASoft\Util\Paginator;

    /**
     * Class UserBooksViewAction
     * @package KAASoft\Controller\Admin\Book
     */
    class UserBooksViewAction extends PublicActionBase {
        /**
         * UserBooksViewAction constructor.
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
            $user = $this->session->getUser();
            if ($user !== null and $user instanceof User) {
                $userId = $user->getId();
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
                                                               $this->routeController->getRouteString(BookRoutes::USER_BOOKS_VIEW_ROUTE)));

                $books = $bookDatabaseHelper->getUserBooks($userId,
                                                           $paginator->getOffset(),
                                                           $perPage,
                                                           $sortColumn,
                                                           $sortOrder);

                $this->smarty->assign("books",
                                      $books);

                if (Helper::isAjaxRequest()) {
                    return new DisplaySwitch('user/userBooks-list.tpl');
                }
                else {
                    return new DisplaySwitch('user/userBooks.tpl');
                }
            }
            else {
                $this->session->addSessionMessage(_("Please log in to view this page."),
                                                  Message::MESSAGE_STATUS_ERROR);

                return new DisplaySwitch(null,
                                         $this->routeController->getRouteString(GeneralPublicRoutes::PAGE_IS_FORBIDDEN_ROUTE));
            }
        }
    }