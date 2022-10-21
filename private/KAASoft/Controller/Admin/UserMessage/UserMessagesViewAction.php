<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\UserMessage;

    use KAASoft\Environment\Routes\Admin\UserMessageRoutes;
    use KAASoft\Controller\Admin\Util\UtilDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Paginator;

    /**
     * Class UserMessagesViewAction
     * @package KAASoft\Controller\Admin\UserMessage
     */
    class UserMessagesViewAction extends AdminActionBase {

        /**
         * UserMessagesViewAction constructor.
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

            $perPage = $this->getPerPage(Session::USER_MESSAGES_PER_PAGE_NUMBER,
                                         $this->siteViewOptions->getOptionValue(SiteViewOptions::USER_MESSAGES_PER_PAGE));
            $sortColumn = $this->getSortingColumn(Session::USER_MESSAGES_SORTING_COLUMN,
                                                  KAASoftDatabase::$USER_MESSAGES_TABLE_NAME . ".creationDate");
            $sortOrder = $this->getSortingOrder(Session::USER_MESSAGES_SORTING_ORDER,
                                                "DESC");
            $helper = new UtilDatabaseHelper($this);


            $paginator = new Paginator($page,
                                       $perPage,
                                       $helper->getUserMessagesCount());
            $this->smarty->assign("pages",
                                  $paginator->preparePages($page,
                                                           $this->routeController->getRouteString(UserMessageRoutes::USER_MESSAGE_LIST_VIEW_ROUTE)));


            $this->smarty->assign("userMessages",
                                  $helper->getUserMessages($paginator->getOffset(),
                                                           $this->siteViewOptions->getOptionValue(SiteViewOptions::USER_MESSAGES_PER_PAGE),
                                                           $sortColumn,
                                                           $sortOrder));


            return new DisplaySwitch('admin/public/messages/messages.tpl');
        }
    }