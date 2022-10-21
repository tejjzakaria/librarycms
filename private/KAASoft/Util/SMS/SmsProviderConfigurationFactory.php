<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    /**
     * Created by KAA Soft.
     * Date: 2018-06-15
     */


    namespace KAASoft\Util\SMS;


    use KAASoft\Environment\SmsSettings;

    class SmsProviderConfigurationFactory {
        /**
         * @param $providerName
         * @return SmsProviderConfiguration|null
         */
        public static function getProviderConfigurationInstance($providerName) {
            $provider = null;
            switch ($providerName) {
                case SmsSettings::NEXMO_NAME:
                    return new NexmoConfiguration();
                case SmsSettings::TWILIO_NAME:
                    return new TwilioConfiguration();
                case SmsSettings::TEXT_LOCAL_NAME:
                    return new TextLocalConfiguration();
            }

            return $provider;
        }
    }