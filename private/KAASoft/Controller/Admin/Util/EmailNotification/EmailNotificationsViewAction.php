<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util\EmailNotification;

    use KAASoft\Controller\Admin\Util\UtilDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Util\DisplaySwitch;

    /**
     * Class EmailNotificationsViewAction
     * @package KAASoft\Controller\Admin\Util\EmailNotification
     */
    class EmailNotificationsViewAction extends AdminActionBase {
        /**
         * EmailNotificationsAction constructor.
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
            $this->smarty->assign("emailNotifications",
                                  $utilDatabaseHelper->getEmailNotifications());

            return new DisplaySwitch('admin/email-notifications/email-notifications.tpl');

        }
    }