<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\User;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;

    /**
     * Class UserCheckEmailAction
     * @package KAASoft\Controller\Admin\User
     */
    class UserCheckEmailAction extends AdminActionBase {
        /**
         * UserCheckEmailAction constructor.
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
            $email = isset( $_POST["email"] ) ? $_POST["email"] : "";
            $userDatabaseHelper = new UserDatabaseHelper($this);
            if (Helper::isAjaxRequest()) {
                Helper::printAsJSON(!$userDatabaseHelper->isUserEmailExists($email));
            }
            else {
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("AJAX request is required only.") ]);
            }

            return new DisplaySwitch();
        }
    }