<?php
    /**
 * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
 */

    namespace KAASoft\Controller\Admin\General;

    use KAASoft\Environment\Routes\Admin\SessionRoutes;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Util\DisplaySwitch;

    /**
     * Class SessionResetAction
     * @package KAASoft\Controller\Admin\General
     */
    class SessionResetAction extends AdminActionBase {

        /**
         * SessionResetAction constructor.
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
            $this->session->sessionReset();

            return new DisplaySwitch(null,
                                     $this->routeController->getRouteString(SessionRoutes::LOGIN_ROUTE));
        }
    }