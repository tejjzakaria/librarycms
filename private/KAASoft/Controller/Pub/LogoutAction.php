<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Pub;

    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Util\DisplaySwitch;

    /**
     * Class LogoutAction
     * @package KAASoft\Controller\Pub
     */
    class LogoutAction extends PublicActionBase {
        /**
         * LogoutAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute) {
            parent::__construct($activeRoute,
                                true);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         */
        protected function action($args) {
            $this->session->logout();

            return new DisplaySwitch(null,
                                     $this->routeController->getRouteString(GeneralPublicRoutes::PUBLIC_INDEX_ROUTE));
        }
    }