<?php
    /**
 * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
 */

    namespace KAASoft\Controller\Pub;

    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Util\DisplaySwitch;

    /**
     * Class SessionResetAction
     * @package KAASoft\Controller\Pub
     */
    class SessionResetAction extends PublicActionBase {
        /**
         * SessionResetAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute) {
            parent::__construct($activeRoute,
                                false);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         */
        protected function action($args) {
            $this->session->sessionReset();

            return new DisplaySwitch(null,
                                     $this->routeController->getRouteString(GeneralPublicRoutes::PUBLIC_INDEX_ROUTE));
        }
    }