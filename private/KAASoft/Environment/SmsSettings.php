<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment;

    use KAASoft\Util\FileHelper;
    use KAASoft\Util\SMS\SmsProviderConfiguration;
    use KAASoft\Util\SMS\SmsProviderConfigurationFactory;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class SmsSettings
     * @package KAASoft\Environment
     */
    class SmsSettings extends AbstractSettings {
        const SmsSettingsFileNameJSON = '/KAASoft/Config/SmsSettings.json';

        const TEXT_LOCAL_NAME = "TextLocal";
        const NEXMO_NAME      = "Nexmo";
        const TWILIO_NAME     = "Twilio";

        const SMS_PROVIDERS = [ SmsSettings::TEXT_LOCAL_NAME => "Text Local",
                                SmsSettings::NEXMO_NAME      => "Nexmo",
                                SmsSettings::TWILIO_NAME     => "Twilio" ];

        /**
         * @var array
         */
        private $providers;
        private $activeProvider;
        private $sender;
        private $defaultMessage;

        function __construct() {
            $this->activeProvider = null;
        }

        private function getDefaultConfig() {
            return [ "provider" => [ SmsSettings::TEXT_LOCAL_NAME => [ "sender" => $this->sender ],
                                     SmsSettings::NEXMO_NAME      => [ "from" => $this->sender ],
                                     SmsSettings::TWILIO_NAME     => [ "From" => $this->sender ] ] ];
        }

        /**
         * @return array
         */
        public function getConfig() {
            $defaultConfig = $this->getDefaultConfig();

            foreach ($this->providers as $provider) {
                if ($provider instanceof SmsProviderConfiguration) {
                    $defaultConfig["provider"][$provider->getName()] = array_merge($defaultConfig["provider"][$provider->getName()],
                                                                                   $provider->getConfig());
                }
            }

            return $defaultConfig;

        }

        /**
         * @param $name
         * @return mixed|null
         */
        public function getProvider($name) {
            foreach ($this->providers as $provider) {
                if ($provider instanceof SmsProviderConfiguration) {
                    if (strcmp($name,
                               $provider->getName()) === 0
                    ) {
                        return $provider;
                    }
                }
            }

            return null;
        }

        /**
         * copy data from assoc array to object fields
         * @param $settings mixed
         */
        public function copySettings($settings) {
            $providerConfigurations = [];

            // create all sms settings
            foreach (SmsSettings::SMS_PROVIDERS as $provideName => $providerTitle) {
                $providerConfiguration = SmsProviderConfigurationFactory::getProviderConfigurationInstance($provideName);
                $providerConfigurations [] = $providerConfiguration;
            }
            // fill existing
            if ($settings !== null and !ValidationHelper::isArrayEmpty($settings["providers"])) {
                foreach ($settings["providers"] as $providerArray) {
                    if (!ValidationHelper::isEmpty($providerArray["name"])) {
                        foreach ($providerConfigurations as $providerConfiguration) {
                            if ($providerConfiguration instanceof SmsProviderConfiguration) {
                                if (strcmp($providerArray["name"],
                                           $providerConfiguration->getName()) == 0
                                ) {
                                    $providerConfiguration->populateInstance($providerArray);
                                    break;
                                }
                            }
                        }
                    }
                }
            }
            $this->setProviders($providerConfigurations);
            $this->setSender($settings["sender"]);
            $this->setDefaultMessage($settings["defaultMessage"]);
            $this->setActiveProvider($settings["activeProvider"]);
        }

        /**
         * copy data from another Settings object
         * @param $settings SmsSettings
         */
        public function cloneSettings($settings) {
            $this->setProviders($settings->getProviders());
        }

        /**
         * Returns config file to load/store settings
         * @return string
         */
        public function getConfigFileName() {
            return realpath(FileHelper::getPrivateFolderLocation()) . SmsSettings::SmsSettingsFileNameJSON;
        }

        /**
         * Sets default settings
         */
        public function setDefaultSettings() {
            $this->providers = [];
            foreach (SmsSettings::SMS_PROVIDERS as $providerName => $providerTitle) {
                $providerConfiguration = SmsProviderConfigurationFactory::getProviderConfigurationInstance($providerName);
                $this->providers [] = $providerConfiguration;
            }
            $this->setSender("Library CMS");
            $this->setDefaultMessage("Hi Man! This is a good time to return book.");
        }

        /**
         * @return array
         */
        function jsonSerialize() {
            return [ "providers"      => $this->providers,
                     "defaultMessage" => $this->defaultMessage,
                     "sender"         => $this->sender,
                     "activeProvider" => $this->activeProvider ];
        }

        /**
         * @return array
         */
        public function getProviders() {
            return $this->providers;
        }

        /**
         * @param array $providers
         */
        public function setProviders($providers) {
            $this->providers = $providers;
        }

        /**
         * @return null
         */
        public function getActiveProvider() {
            return $this->activeProvider;
        }

        /**
         * @param null $activeProvider
         */
        public function setActiveProvider($activeProvider) {
            $this->activeProvider = $activeProvider;
        }

        /**
         * @return mixed
         */
        public function getSender() {
            return $this->sender;
        }

        /**
         * @param mixed $sender
         */
        public function setSender($sender) {
            $this->sender = $sender;
        }

        /**
         * @return mixed
         */
        public function getDefaultMessage() {
            return $this->defaultMessage;
        }

        /**
         * @param mixed $defaultMessage
         */
        public function setDefaultMessage($defaultMessage) {
            $this->defaultMessage = $defaultMessage;
        }
    }