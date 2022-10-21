<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Environment\Routes\Admin\UtilRoutes;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;

    /**
     * Class SiteViewOptionsEditAction
     * @package KAASoft\Controller\Admin\Util
     */
    class SiteViewOptionsEditAction extends AdminActionBase {
        /**
         * SiteViewOptionsEditAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute) {
            parent::__construct($activeRoute);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         * @throws \Exception
         */
        protected function action($args) {
            if (Helper::isPostRequest()) {
                $this->siteViewOptions->saveSiteViewOptions($_POST);
                $this->routeController->updateRoutes();

                return new DisplaySwitch(null,
                                         $this->routeController->getRouteString(UtilRoutes::OPTION_LIST_VIEW_ROUTE));
            }
            $this->smarty->assign("options",
                                  $this->siteViewOptions->splitOptionsByGroup());

            return new DisplaySwitch('admin/options.tpl');
        }
    }