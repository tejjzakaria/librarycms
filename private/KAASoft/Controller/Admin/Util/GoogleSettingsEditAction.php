<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util;


    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Environment\GoogleSettings;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;

    /**
     * Class GoogleSettingsEditAction
     * @package KAASoft\Controller\Admin\Util
     */
    class GoogleSettingsEditAction extends AdminActionBase {
        /**
         * GoogleSettingsEditAction constructor.
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
            $settings = new GoogleSettings();
            if (Helper::isPostRequest()) {
                $settings->copySettings($_POST);

                $settings->saveSettings();
            }
            else {
                $settings->loadSettings();
            }

            $this->smarty->assign("googleSettings",
                                  $settings);

            return new DisplaySwitch('admin/googleSettings.tpl');
        }
    }