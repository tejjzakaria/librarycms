<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Book;

    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Database\Entity\General\User;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Environment\Routes\Admin\IssueRoutes;
    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;
    use KAASoft\Util\Paginator;

    /**
     * Class UserIssuedBooksViewAction
     * @package KAASoft\Controller\Admin\Book
     */
    class UserIssuedBooksViewAction extends PublicActionBase {
        /**
         * UserIssuedBooksViewAction constructor.
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
                                           $bookDatabaseHelper->getUserIssuedBooksCount($userId));

                $this->smarty->assign("pages",
                                      $paginator->preparePages($page,
                                                               $this->routeController->getRouteString(IssueRoutes::USER_ISSUED_BOOKS_VIEW_ROUTE)));

                $books = $bookDatabaseHelper->getUserIssuedBooks($userId,
                                                                 $paginator->getOffset(),
                                                                 $perPage,
                                                                 $sortColumn,
                                                                 $sortOrder);

                $this->smarty->assign("books",
                                      $books);

                if (Helper::isAjaxRequest()) {
                    return new DisplaySwitch('user/userIssuedBooks-list.tpl');
                }

                else {
                    return new DisplaySwitch('user/userIssuedBooks.tpl');
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