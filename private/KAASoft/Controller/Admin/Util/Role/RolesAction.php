<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util\Role;

    use KAASoft\Controller\Admin\Util\UtilDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Util\DisplaySwitch;

    /**
     * Class RolesAction
     * @package KAASoft\Controller\Admin\Util\Role
     */
    class RolesAction extends AdminActionBase {
        /**
         * RolesAction constructor.
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
            $utilDatabaseHelper = new UtilDatabaseHelper($this);
            $this->smarty->assign("roles",
                                  $utilDatabaseHelper->getRoles());

            return new DisplaySwitch('admin/roles/roles.tpl');

        }
    }