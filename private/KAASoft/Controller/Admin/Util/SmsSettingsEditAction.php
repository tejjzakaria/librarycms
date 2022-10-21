<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util;


    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Environment\SmsSettings;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\SMS\SmsProviderConfigurationFactory;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class SmsSettingsEditAction
     * @package KAASoft\Controller\Admin\Util
     */
    class SmsSettingsEditAction extends AdminActionBase {
        /**
         * SmsSettingsEditAction constructor.
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
            $smsSettings = new SmsSettings();
            if (Helper::isPostRequest()) {
                $providers = [];
                if (!ValidationHelper::isArrayEmpty($_POST["providers"])) {

                    foreach ($_POST["providers"] as $provider) {
                        $providerConfiguration = SmsProviderConfigurationFactory::getProviderConfigurationInstance(ValidationHelper::getString($provider["name"]));

                        if ($providerConfiguration === null) {
                            continue;
                        }
                        $providerConfiguration->populateInstance($provider);
                        $providers [] = $providerConfiguration;
                    }
                }
                $smsSettings->setProviders($providers);
                $smsSettings->setSender(ValidationHelper::getString($_POST["sender"]));
                $smsSettings->setDefaultMessage(ValidationHelper::getString($_POST["defaultMessage"]));
                $smsSettings->setActiveProvider(ValidationHelper::getString($_POST["activeProvider"]));
                $smsSettings->saveSettings();
            }
            else {
                $smsSettings->loadSettings();
            }

            $this->smarty->assign("smsSettings",
                                  $smsSettings);

            return new DisplaySwitch("admin/smsSettings.tpl");
        }
    }