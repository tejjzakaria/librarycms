<?php

    namespace KAASoft\Util\RSS;

    /**
     * Interface FeedInterface
     * @package KAASoft\Util\RSS
     */
    interface FeedInterface {
        /**
         * Add channel
         * @param ChannelInterface $channel
         * @return $this
         */
        public function addChannel(ChannelInterface $channel);

        /**
         * Generate XML as string
         * @return string
         */
        public function getXml();

        /**
         * Generate XML as string
         * @return string
         */
        public function __toString();
    }
