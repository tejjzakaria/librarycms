<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util;


    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Environment\EmailSettings;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;

    /**
     * Class EmailSettingsEditAction
     * @package KAASoft\Controller\Admin\Util
     */
    class EmailSettingsEditAction extends AdminActionBase {
        /**
         * EmailSettingsEditAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute) {
            parent::__construct($activeRoute);
        }

        protected function sanitizeUserInput($excludeKeys = []) {
            parent::sanitizeUserInput(array_merge($excludeKeys,
                                                  ["dynamicEmailTemplate",
                                                   "staticEmailTemplate"]));
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         * @throws \Exception
         */
        protected function action($args) {
            $settings = null;
            $settings = new EmailSettings();
            if (Helper::isPostRequest()) {
                $settings->copySettings($_POST);
                $settings->saveSettings();
            }
            else {
                // important
                $this->smarty->unregister_outputfilter([ $this,
                                                         "smartyOutputFilter" ]);
                $settings->loadSettings();
            }

            $this->smarty->assign("emailSettings",
                                  $settings);

            return new DisplaySwitch('admin/emailSettings.tpl');
        }
    }