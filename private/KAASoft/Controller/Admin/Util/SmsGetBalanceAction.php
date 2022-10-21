<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util;


    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Environment\SmsSettings;
    use KAASoft\Util\DisplaySwitch;
    use SocialConnect\Common\Http\Client\Curl;
    use SocialConnect\SMS\ProviderFactory;

    /**
     * Class SmsGetBalanceAction
     * @package KAASoft\Controller\Admin\Util
     */
    class SmsGetBalanceAction extends AdminActionBase {
        /**
         * SmsGetBalanceAction constructor.
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
            $this->setJsonContentType();
            $smsSettings = new SmsSettings();
            $smsSettings->loadSettings();

            $activeProviderName = $smsSettings->getActiveProvider();
            if ($activeProviderName !== null) {

                $providerConfig = $smsSettings->getConfig();
                $service = new ProviderFactory($providerConfig,
                                               new Curl());

                $provider = $service->factory($activeProviderName);

                $result = $provider->getBalance();

                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_SUCCESS => sprintf(_("Your '%s' balance is '%f'."),
                                                                                               SmsSettings::SMS_PROVIDERS[$activeProviderName],
                                                                                               $result),
                                                "balance"                           => $result ]);
            }
            else {
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Please select active SMS provider.") ]);

            }

            return new DisplaySwitch();
        }
    }