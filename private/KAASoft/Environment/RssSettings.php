<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment;

    use KAASoft\Util\FileHelper;
    use KAASoft\Util\RSS\Channel;
    use KAASoft\Util\RSS\RssFeed;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class RssSettings
     * @package KAASoft\Environment
     */
    class RssSettings extends AbstractSettings {
        const RssSettingsFileNameJSON = '/KAASoft/Config/RssSettings.json';

        /**
         * @var Channel[]
         */
        private $channels;

        function __construct() {
            $this->channels = [];
        }

        /**
         * copy data from assoc array to object fields
         * @param $settings mixed
         */
        public function copySettings($settings) {
            $channels = [];

            if ($settings !== null and !ValidationHelper::isArrayEmpty($settings["channels"])) {
                foreach ($settings["channels"] as $channel) {
                    $channels [] = Channel::getInstance($channel);
                }
            }
            $this->setChannels($channels);
        }

        /**
         * copy data from another Settings object
         * @param $settings RssSettings
         */
        public function cloneSettings($settings) {
            $this->setChannels($settings->getChannels());
        }

        /**
         * Returns config file to load/store settings
         * @return string
         */
        public function getConfigFileName() {
            return realpath(FileHelper::getPrivateFolderLocation()) . RssSettings::RssSettingsFileNameJSON;
        }

        /**
         * Sets default settings
         */
        public function setDefaultSettings() {
            $this->channels = [];
            $channel = new Channel();
            $channel->setName(RssFeed::BOOK_RSS_CHANNEL_NAME);
            $channel->setDescription("Library CMS RSS");
            $channel->setTitle("Library CMS");
            $channel->setLink(Config::getSiteURL());
            $channel->setImage(Config::getSiteURL() . "/resources/images/logo.png");

            $this->channels[] = $channel;
        }

        /**
         * @param $name
         * @return Channel|null
         */
        public function getChannel($name) {
            foreach ($this->channels as $channel) {
                if ($channel !== null and $channel instanceof Channel) {
                    if (strcmp($channel->getName(),
                               $name) === 0
                    ) {
                        return $channel;
                    }
                }
            }

            return null;
        }

        /**
         * @return array
         */
        function jsonSerialize() {
            return [ "channels" => $this->channels ];
        }

        /**
         * @return \KAASoft\Util\RSS\Channel[]
         */
        public function getChannels() {
            return $this->channels;
        }

        /**
         * @param \KAASoft\Util\RSS\Channel[] $channels
         */
        public function setChannels($channels) {
            $this->channels = $channels;
        }
    }