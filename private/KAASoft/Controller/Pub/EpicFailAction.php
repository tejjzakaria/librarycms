<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Pub;

    use Exception;
    use KAASoft\Controller\ConfiguredSmarty;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Environment\RouteController;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;


    /**
     * Class EpicFailAction
     * @package KAASoft\Controller\Pub
     */
    class EpicFailAction extends PublicActionBase {
        /**
         * EpicFailAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute) {
            parent::__construct($activeRoute);
            try {
                $this->siteViewOptions = SiteViewOptions::getInstance();
                $this->siteViewOptions->loadSiteViewOptions();
                $this->routeController = RouteController::getInstance();
                $this->smarty = ConfiguredSmarty::getInstance($activeRoute);
                $this->session = Session::getInstance($this->routeController,
                                                      $this->activeRoute);
            }
            catch (Exception $e) {
                ControllerBase::getLogger()->error($e->getMessage(),
                                                   $e);
                Helper::processFatalException($e,
                                              RouteController::getInstance(),
                                              true);
            }
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         */
        protected function action($args) {
            try {
                $this->smarty->assign("pageTitle",
                                      "Library CMS - Epic Fail");

                return new DisplaySwitch('epicFail.tpl');
            }
            catch (Exception $e) {
                ControllerBase::getLogger()->error($e->getMessage(),
                                                   $e);
                Helper::processFatalException($e,
                                              RouteController::getInstance(),
                                              true);
            }

            return null; // this line won't be accessed
        }
    }