<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Pub;

    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;

    /**
     * Class PageNotFoundAction
     * @package KAASoft\Controller\Pub
     */
    class PageNotFoundAction extends PublicActionBase {
        /**
         * PageNotFoundAction constructor.
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
                                  _("Library CMS - Page Is Not Found"));

            Helper::setResponseCode(404);

            return new DisplaySwitch('404.tpl');
        }
    }