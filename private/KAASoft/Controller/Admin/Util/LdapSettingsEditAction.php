<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util;


    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Environment\LdapSettings;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;

    /**
     * Class LdapSettingsEditAction
     * @package KAASoft\Controller\Admin\Util
     */
    class LdapSettingsEditAction extends AdminActionBase {
        /**
         * LdapSettingsEditAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute) {
            parent::__construct($activeRoute);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         * @throws \Exception
         */
        protected function action($args) {
            $settings = new LdapSettings();
            if (Helper::isPostRequest()) {
                $settings->copySettings($_POST);

                $settings->saveSettings();
            }
            else {
                $settings->loadSettings();
            }

            $this->smarty->assign("ldapSettings",
                                  $settings);

            return new DisplaySwitch('admin/ldapSettings.tpl');
        }
    }