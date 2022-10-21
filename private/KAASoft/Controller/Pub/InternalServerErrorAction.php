<?php
    /**
 * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
 */

    /**
     * Created by KAA Soft.
     * Date: 2017-12-13
     */

    namespace KAASoft\Controller\Pub;

    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;

    /**
     * Class InternalServerErrorAction
     * @package KAASoft\Controller\Pub
     */
    class InternalServerErrorAction extends PublicActionBase {
        /**
         * InternalServerErrorAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute) {
            parent::__construct($activeRoute);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         */
        protected function action($args) {
            $this->smarty->assign("pageTitle",
                                  _("Library CMS - Internal Server Error"));

            Helper::setResponseCode(500);

            return new DisplaySwitch('500.tpl');
        }
    }