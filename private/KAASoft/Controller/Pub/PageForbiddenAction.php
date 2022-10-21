<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Pub;

    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;

    /**
     * Class PageForbiddenAction
     * @package KAASoft\Controller\Pub
     */
    class PageForbiddenAction extends PublicActionBase {
        /**
         * PageForbiddenAction constructor.
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
            $this->smarty->assign("pageTitle",
                                  _("Library CMS - Page Is Forbidden"));
            Helper::setResponseCode(403);

            return new DisplaySwitch('403.tpl');
        }
    }