<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment;

    use KAASoft\Util\FileHelper;
    use KAASoft\Util\SocialNetworkConfiguration;

    /**
     * Class DatabaseSettings
     * @package KAASoft\Environment
     */
    class SocialNetworkSettings extends AbstractSettings {
        const SocialNetworkSettingsFileNameJSON = '/KAASoft/Config/SocialNetworkSettings.json';
        const ROOT_SETTINGS_ELEMENT             = "providers";

        const FACEBOOK_PROVIDER_NAME = "facebook";
        const GOOGLE_PROVIDER_NAME   = "google";
        const TWITTER_PROVIDER_NAME  = "twitter";

        const SOCIAL_NETWORKS = [ SocialNetworkSettings::FACEBOOK_PROVIDER_NAME,
                                  SocialNetworkSettings::GOOGLE_PROVIDER_NAME,
                                  SocialNetworkSettings::TWITTER_PROVIDER_NAME ];
        /**
         * @var array
         */
        private $providers;

        function __construct() {
        }

        public static function getDefaultConfig() {
            return [ 'redirectUri' => Config::getSiteURL() . '/auth/${provider}/',
                     'provider'    => [ SocialNetworkSettings::FACEBOOK_PROVIDER_NAME => [ 'scope'   => [ 'email' ],
                                                                                           'options' => [ 'identity.fields' => [ 'email',
                                                                                                                                 'picture.width(99999)',
                                                                                                                                 'gender',
                                                                                                                                 'first_name',
                                                                                                                                 'last_name',
                                                                                                                                 'link',
                                                                                                                                 'locale',
                                                                                                                                 'name',
                                                                                                                                 'timezone',
                                                                                                                                 'updated_time',
                                                                                                                                 'verified' ] ] ],
                                        SocialNetworkSettings::TWITTER_PROVIDER_NAME  => [ 'enabled' => false ],
                                        SocialNetworkSettings::GOOGLE_PROVIDER_NAME   => [ 'scope'   => [ 'https://www.googleapis.com/auth/userinfo.email',
                                                                                                          'https://www.googleapis.com/auth/userinfo.profile' ],
                                                                                           'options' => [ 'auth.parameters' => [ 'hd' => 'domain.tld', ] ] ], ] ];
        }

        /**
         * @return array
         */
        public function getConfig() {
            $defaultConfig = SocialNetworkSettings::getDefaultConfig();

            foreach ($this->providers as $provider) {
                if ($provider instanceof SocialNetworkConfiguration) {
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
                if ($provider instanceof SocialNetworkConfiguration) {
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
            $providers = [];
            foreach ($settings[SocialNetworkSettings::ROOT_SETTINGS_ELEMENT] as $provider) {
                $providers [] = SocialNetworkConfiguration::getObjectInstance($provider);

            }
            $this->setProviders($providers);
        }

        /**
         * copy data from another Settings object
         * @param $settings SocialNetworkSettings
         */
        public function cloneSettings($settings) {
            $this->setProviders($settings->getProviders());
        }

        /**
         * Returns config file to load/store settings
         * @return string
         */
        public function getConfigFileName() {
            return realpath(FileHelper::getPrivateFolderLocation()) . SocialNetworkSettings::SocialNetworkSettingsFileNameJSON;
        }

        /**
         * Sets default settings
         */
        public function setDefaultSettings() {
            $this->providers = [];
            foreach (SocialNetworkSettings::SOCIAL_NETWORKS as $providerName) {
                $this->providers[] = new SocialNetworkConfiguration($providerName);
            }
        }

        /**
         * @return array
         */
        function jsonSerialize() {
            return [ SocialNetworkSettings::ROOT_SETTINGS_ELEMENT => $this->providers ];
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
    }