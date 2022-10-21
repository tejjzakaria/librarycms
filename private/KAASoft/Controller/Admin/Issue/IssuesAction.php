<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Issue;

    use KAASoft\Environment\Routes\Admin\IssueRoutes;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Paginator;

    /**
     * Class IssuesAction
     * @package KAASoft\Controller\Admin\Issue
     */
    class IssuesAction extends AdminActionBase {
        /**
         * IssuesAction constructor.
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

            $perPage = $this->getPerPage(Session::ISSUE_PER_PAGE_NUMBER,
                                         $this->siteViewOptions->getOptionValue(SiteViewOptions::ISSUES_PER_PAGE));
            $sortColumn = $this->getSortingColumn(Session::ISSUE_SORTING_COLUMN);
            $sortOrder = $this->getSortingOrder(Session::ISSUE_SORTING_ORDER);


            $paginator = new Paginator($page,
                                       $perPage,
                                       $issueDatabaseHelper->getIssuesCount());

            $this->smarty->assign("pages",
                                  $paginator->preparePages($page,
                                                           $this->routeController->getRouteString(IssueRoutes::ISSUE_LIST_VIEW_ROUTE)));


            $issues = $issueDatabaseHelper->getIssues($paginator->getOffset(),
                                                      $perPage,
                                                      $sortColumn,
                                                      $sortOrder);

            $this->smarty->assign("issues",
                                  $issues);

            if (Helper::isAjaxRequest()) {
                return new DisplaySwitch('admin/issues/issues-list.tpl');
            }
            else {
                return new DisplaySwitch('admin/issues/issues.tpl');
            }
        }
    }