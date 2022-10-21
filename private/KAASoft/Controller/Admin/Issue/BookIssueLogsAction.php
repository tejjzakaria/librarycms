<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Issue;

    use KAASoft\Controller\Admin\Book\BookDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Environment\Routes\Admin\IssueRoutes;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Paginator;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class BookIssueLogsAction
     * @package KAASoft\Controller\Admin\Issue
     */
    class BookIssueLogsAction extends AdminActionBase {
        /**
         * BookIssueLogsAction constructor.
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
            $bookId = ValidationHelper::getInt($args["bookId"]);
            $page = isset( $args["page"] ) ? $args["page"] : 1;
            $bookDatabaseHelper = new BookDatabaseHelper($this);

            $perPage = $this->getPerPage(Session::ISSUE_LOG_PER_PAGE_NUMBER,
                                         $this->siteViewOptions->getOptionValue(SiteViewOptions::ISSUE_LOGS_PER_PAGE));
            $sortColumn = $this->getSortingColumn(Session::ISSUE_LOG_SORTING_COLUMN);
            $sortOrder = $this->getSortingOrder(Session::ISSUE_LOG_SORTING_ORDER);


            $paginator = new Paginator($page,
                                       $perPage,
                                       $bookDatabaseHelper->getBookIssueLogsCount($bookId));

            $this->smarty->assign("pages",
                                  $paginator->preparePages($page,
                                                           $this->routeController->getRouteString(IssueRoutes::BOOK_ISSUE_LOG_LIST_VIEW_ROUTE,
                                                                                                  [ "bookId" => $bookId ])));


            $issueLogs = $bookDatabaseHelper->getBookLogs($bookId,
                                                          $paginator->getOffset(),
                                                          $perPage,
                                                          $sortColumn,
                                                          $sortOrder);

            $this->smarty->assign("issueLogs",
                                  $issueLogs);

            if (Helper::isAjaxRequest()) {
                return new DisplaySwitch('admin/books/bookIssueLogs.tpl');
            }
            else {
                return new DisplaySwitch('admin/books/bookIssueLogs.tpl');
            }
        }
    }