<?php

    namespace KAASoft\Util\RSS;

    interface ItemInterface {

        /**
         * Set item title
         * The title of the item.
         * @param string $title
         * @return $this
         */
        public function setTitle($title);

        /**
         * Set item link
         * The URL of the item.
         * @param string $link
         * @return $this
         */
        public function setLink($link);

        /**
         * Set author name for article
         * Email address of the author of the item.
         * @param $author
         * @return $this
         */
        public function setAuthor($author);

        /**
         * Set item description
         * The item synopsis.
         * @param string $description
         * @return $this
         */
        public function setDescription($description);

        /**
         * Set item category
         * Includes the item in one or more categories.
         * @param string $name   Category name
         * @param string $domain Category URL
         * @return $this
         */
        public function addCategory($name, $domain = null);

        /**
         * Set item comments
         * URL of a page for comments relating to the item.
         * @param string $url
         * @return $this
         */
        public function setComments($url);

        /**
         * Set GUID
         * A string that uniquely identifies the item.
         * @param string $guid
         * @param bool   $isPermalink
         * @return $this
         */
        public function setGuid($guid, $isPermalink = false);

        /**
         * Set published date
         * Indicates when the item was published.
         * @param int $pubDate Unix timestamp
         * @return $this
         */
        public function setPubDate($pubDate);

        /**
         * Set enclosure
         * Describes a media object that is attached to the item.
         * @param string $url    Url to media file
         * @param int    $length Length in bytes of the media file
         * @param string $type   Media type, default is audio/mpeg
         * @return $this
         */
        public function setEnclosure($url, $length = 0, $type = 'audio/mpeg');

        /**
         * Append item to the channel
         * @param ChannelInterface $channel
         * @return $this
         */
        public function appendTo(ChannelInterface $channel);

        /**
         * Return XML object
         * @return XMLElement
         */
        public function asXML();
    }
