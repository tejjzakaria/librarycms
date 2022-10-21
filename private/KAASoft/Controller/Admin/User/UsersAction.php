<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\User;

    use KAASoft\Environment\Routes\Admin\UserRoutes;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Paginator;

    /**
     * Class UsersAction
     * @package KAASoft\Controller\Admin\User
     */
    class UsersAction extends AdminActionBase {
        /**
         * UsersAction constructor.
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
            $userDatabaseHelper = new UserDatabaseHelper($this);

            $perPage = $this->getPerPage(Session::USER_PER_PAGE_NUMBER,
                                         $this->siteViewOptions->getOptionValue(SiteViewOptions::USERS_PER_PAGE));
            $sortColumn = $this->getSortingColumn(Session::USER_SORTING_COLUMN);
            $sortOrder = $this->getSortingOrder(Session::USER_SORTING_ORDER);

            $paginator = new Paginator($page,
                                       $perPage,
                                       $userDatabaseHelper->getUsersCount());

            $this->smarty->assign("pages",
                                  $paginator->preparePages($page,
                                                           $this->routeController->getRouteString(UserRoutes::USER_LIST_VIEW_ROUTE)));

            $users = $userDatabaseHelper->getUsers($paginator->getOffset(),
                                                   $perPage,
                                                   $sortColumn,
                                                   $sortOrder);

            $this->smarty->assign("users",
                                  $users);

            if (Helper::isAjaxRequest()) {
                return new DisplaySwitch('admin/users/user-list.tpl');
            }
            else {
                return new DisplaySwitch('admin/users/users.tpl');
            }
        }
    }