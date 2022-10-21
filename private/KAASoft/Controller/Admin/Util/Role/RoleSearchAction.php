<?php
    /**
 * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
 */

    namespace KAASoft\Controller\Admin\Util\Role;

    use KAASoft\Controller\Admin\Util\UtilDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;

    /**
     * Class RoleSearchAction
     * @package KAASoft\Controller\Admin\Util\Role
     */
    class RoleSearchAction extends AdminActionBase {
        /**
         * RoleSearchAction constructor.
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

                $utilDatabaseHelper = new UtilDatabaseHelper($this);

                $roles = $utilDatabaseHelper->searchRoleByName($searchText);
                $result = json_encode($roles);
                Helper::printString($result !== false ? $result : json_encode("error"));
            }
            else {
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("AJAX request is required only.") ]);
            }

            return new DisplaySwitch();
        }
    }