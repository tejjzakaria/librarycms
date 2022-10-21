<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Issue;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Environment\Routes\Admin\IssueRoutes;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Paginator;

    /**
     * Class IssueLogsAction
     * @package KAASoft\Controller\Admin\Issue
     */
    class IssueLogsAction extends AdminActionBase {
        /**
         * IssueLogsAction constructor.
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
            $issueDatabaseHelper = new IssueDatabaseHelper($this);

            $perPage = $this->getPerPage(Session::ISSUE_LOG_PER_PAGE_NUMBER,
                                         $this->siteViewOptions->getOptionValue(SiteViewOptions::ISSUES_PER_PAGE));
            $sortColumn = $this->getSortingColumn(Session::ISSUE_LOG_SORTING_COLUMN);
            $sortOrder = $this->getSortingOrder(Session::ISSUE_LOG_SORTING_ORDER);


            $paginator = new Paginator($page,
                                       $perPage,
                                       $issueDatabaseHelper->getIssueLogsCount());

            $this->smarty->assign("pages",
                                  $paginator->preparePages($page,
                                                           $this->routeController->getRouteString(IssueRoutes::ISSUE_LIST_VIEW_ROUTE)));


            $issueLogs = $issueDatabaseHelper->getIssueLogs($paginator->getOffset(),
                                                            $perPage,
                                                            $sortColumn,
                                                            $sortOrder);

            $this->smarty->assign("issueLogs",
                                  $issueLogs);

            if (Helper::isAjaxRequest()) {
                return new DisplaySwitch('...tpl');
            }
            else {
                return new DisplaySwitch('...tpl');
            }
        }
    }