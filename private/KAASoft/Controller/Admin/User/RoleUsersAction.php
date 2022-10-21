<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\User;

    use KAASoft\Controller\Admin\Util\UtilDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Environment\Routes\Admin\UserRoutes;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Paginator;

    /**
     * Class RoleUsersAction
     * @package KAASoft\Controller\Admin\User
     */
    class RoleUsersAction extends AdminActionBase {
        /**
         * RoleUsersAction constructor.
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
            $roleId = $args["roleId"];
            $page = isset( $args["page"] ) ? $args["page"] : 1;
            $userDatabaseHelper = new UserDatabaseHelper($this);
            $utilDatabaseHelper = new UtilDatabaseHelper($this);

            $perPage = $this->getPerPage(Session::USER_PER_PAGE_NUMBER,
                                         $this->siteViewOptions->getOptionValue(SiteViewOptions::USERS_PER_PAGE));
            $sortColumn = $this->getSortingColumn(Session::USER_SORTING_COLUMN);
            $sortOrder = $this->getSortingOrder(Session::USER_SORTING_ORDER);

            $paginator = new Paginator($page,
                                       $perPage,
                                       $userDatabaseHelper->getRoleUsersCount($roleId));

            $this->smarty->assign("pages",
                                  $paginator->preparePages($page,
                                                           $this->routeController->getRouteString(UserRoutes::ROLE_USER_LIST_VIEW_ROUTE,
                                                                                                  [ "roleId" => $roleId ])));

            $users = $userDatabaseHelper->getUsersByRoleId($roleId,
                                                           $paginator->getOffset(),
                                                           $perPage,
                                                           $sortColumn,
                                                           $sortOrder);

            $this->smarty->assign("users",
                                  $users);

            $this->smarty->assign("userRole",
                                  $utilDatabaseHelper->getRole($roleId));

            if (Helper::isAjaxRequest()) {
                return new DisplaySwitch('admin/users/user-list.tpl');
            }
            else {
                return new DisplaySwitch('admin/users/users.tpl');
            }
        }
    }