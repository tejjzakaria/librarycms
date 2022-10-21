<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util;


    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Environment\SMTPSettings;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;

    /**
     * Class SMTPSettingsEditAction
     * @package KAASoft\Controller\Admin\Util
     */
    class SMTPSettingsEditAction extends AdminActionBase {
        /**
         * SMTPSettingEditAction constructor.
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
            $settings = new SMTPSettings();
            if (Helper::isPostRequest()) {
                $oldSettings = new SMTPSettings();
                $oldSettings->loadSettings();

                $settings->copySettings($_POST);
                if ($oldSettings !== null and empty( $settings->getPassword() )) {
                    $settings->setPassword($oldSettings->getPassword());
                }
                $settings->saveSettings();
            }
            else {
                $settings->loadSettings();
            }

            $this->smarty->assign("SMTPSettings",
                                  $settings);

            return new DisplaySwitch('admin/SMTPSettings.tpl');
        }
    }