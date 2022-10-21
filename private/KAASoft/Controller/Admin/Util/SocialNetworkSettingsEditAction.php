<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util;


    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Environment\SocialNetworkSettings;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\SocialNetworkConfiguration;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class SocialNetworkSettingsAction
     * @package KAASoft\Controller\Admin\Util
     */
    class SocialNetworkSettingsEditAction extends AdminActionBase {
        /**
         * SocialNetworkSettingsAction constructor.
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
            $settings = new SocialNetworkSettings();
            if (Helper::isPostRequest()) {
                $providers = [];
                foreach (SocialNetworkSettings::SOCIAL_NETWORKS as $provider) {
                    $socialNetwork = new SocialNetworkConfiguration($provider);
                    $socialNetwork->setApplicationId(ValidationHelper::getString($_POST[$provider]["applicationId"]));
                    $socialNetwork->setApplicationSecret(ValidationHelper::getString($_POST[$provider]["applicationSecret"]));
                    $socialNetwork->setIsActive(ValidationHelper::getBool($_POST[$provider]["isActive"]));

                    $providers[] = $socialNetwork;
                }
                $settings->setProviders($providers);
                $settings->saveSettings();
            }
            else {
                $settings->loadSettings();
            }

            $this->smarty->assign("socialNetworkSettings",
                                  $settings);

            return new DisplaySwitch("admin/socialNetworkSettings.tpl");
        }
    }