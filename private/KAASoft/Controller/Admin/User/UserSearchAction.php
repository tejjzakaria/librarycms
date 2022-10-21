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
     * Class UserSearchAction
     * @package KAASoft\Controller\Admin\User
     */
    class UserSearchAction extends AdminActionBase {
        /**
         * UserSearchAction constructor.
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
            if (Helper::isAjaxRequest()) {
                $searchText = isset( $_POST["searchText"] ) ? $_POST["searchText"] : "";
                /*if (strlen($searchText) < 3) {
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => "Search text is empty or less then 3 symbols." ]);

                    return new DisplaySwitch();
                }*/

                $userHelper = new UserDatabaseHelper($this);

                $users = $userHelper->searchUsersByName($searchText);
                $result = json_encode($users);
                Helper::printString($result !== false ? $result : json_encode("error"));
            }
            else {
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("AJAX request is required only.") ]);
            }

            return new DisplaySwitch();
        }
    }