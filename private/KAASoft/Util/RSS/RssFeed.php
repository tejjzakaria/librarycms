<?php

    namespace KAASoft\Util\RSS;

    use DOMDocument;

    class RssFeed implements FeedInterface {

        const BOOK_RSS_CHANNEL_NAME = "bookRssChannel";
        const BLOG_RSS_CHANNEL_NAME = "blogRssChannel";

        /** @var ChannelInterface[] */
        protected $channels = [];

        /**
         * Add channel
         * @param ChannelInterface $channel
         * @return $this
         */
        public function addChannel(ChannelInterface $channel) {
            $this->channels[] = $channel;

            return $this;
        }

        /**
         * Render XML
         * @return string
         */
        public function getXml() {
            $xml = new XMLElement('<?xml version="1.0" encoding="UTF-8" ?><rss version="2.0"></rss>',
                                  LIBXML_NOERROR | LIBXML_ERR_NONE | LIBXML_ERR_FATAL);

            foreach ($this->channels as $channel) {
                $toDom = dom_import_simplexml($xml);
                $fromDom = dom_import_simplexml($channel->asXML());
                $toDom->appendChild($toDom->ownerDocument->importNode($fromDom,
                                                                      true));
            }

            $dom = new DOMDocument('1.0',
                                   'UTF-8');
            $dom->appendChild($dom->importNode(dom_import_simplexml($xml),
                                               true));
            $dom->formatOutput = true;

            return $dom->saveXML();
        }

        /**
         * Render XML
         * @return string
         */
        public function __toString() {
            return $this->getXml();
        }
    }
