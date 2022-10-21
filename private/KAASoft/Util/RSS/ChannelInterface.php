<?php

    namespace KAASoft\Util\RSS;

    interface ChannelInterface {

        /**
         * Set channel title
         * The name of the channel. It's how people refer to your service. If you have an HTML website that contains the same information as your RSS file, the title of your channel should be the same as the title of your website.
         * @param string $title
         * @return $this
         */
        public function setTitle($title);

        /**
         * Set channel URL
         * The URL to the HTML website corresponding to the channel.
         * @param string $link
         * @return $this
         */
        public function setLink($link);

        /**
         * Set channel description
         * Phrase or sentence describing the channel.
         * @param string $description
         * @return $this
         */
        public function setDescription($description);

        /**
         * Set ISO639 language code
         *
         * The language the channel is written in. This allows aggregators to group all
         * Italian language sites, for example, on a single page. A list of allowable
         * values for this element, as provided by Netscape, is here. You may also use
         * values defined by the W3C.
         *
         * @param string $language
         * @return $this
         */
        public function setLanguage($language);

        /**
         * Set channel copyright
         * Copyright notice for content in the channel.
         * @param string $copyright
         * @return $this
         */
        public function setCopyright($copyright);

        /**
         * Set channel managing editor
         * Email address for person responsible for editorial content.
         * @param string $managingEditor
         * @return $this
         */
        public function setManagingEditor($managingEditor);

        /**
         * Set channel web master
         * Email address for person responsible for technical issues relating to channel.
         * @param string $webMaster
         * @return $this
         */
        public function setWebmaster($webMaster);

        /**
         * Set channel published date
         * The publication date for the content in the channel. For example, the New York Times publishes on a daily basis, the publication date flips once every 24 hours. That's when the pubDate of the channel changes. All date-times in RSS conform to the Date and Time Specification of RFC 822, with the exception that the year may be expressed with two characters or four characters (four preferred).
         * @param int $pubDate Unix timestamp
         * @return $this
         */
        public function setPubDate($pubDate);

        /**
         * Set channel last build date
         * The last time the content of the channel changed.
         * @param int $lastBuildDate Unix timestamp
         * @return $this
         */
        public function setLastBuildDate($lastBuildDate);

        /**
         * Add channel category
         * Specify one or more categories that the channel belongs to. Follows the same rules as the <item>-level category element.
         * @param string $name   Category name
         * @param string $domain Category URL
         * @return $this
         */
        public function addCategory($name, $domain = null);

        /**
         * Set channel generator
         * A string indicating the program used to generate the channel.
         * @param string $generator
         * @return $this
         */
        public function setGenerator($generator);

        /**
         * Set channel docs
         * A URL that points to the documentation for the format used in the RSS file.
         * @param string $docs
         * @return $this
         */
        public function setDocs($docs);

        /**
         * Set channel ttl (minutes)
         * ttl stands for time to live. It's a number of minutes that indicates how long a channel can be cached before refreshing from the source.
         * @param int $ttl
         * @return $this
         */
        public function setTtl($ttl);

        /**
         * Set channel image
         *    Specifies a GIF, JPEG or PNG image that can be displayed with the channel.
         * @param string $image
         * @return $this
         */
        public function setImage($image);

        /**
         * Set channel rating
         * The PICS rating for the channel.
         * @param string $rating
         * @return $this
         */
        public function setRating($rating);

        /**
         * Add item object
         * @param ItemInterface $item
         * @return $this
         */
        public function addItem(ItemInterface $item);

        /**
         * Append to feed
         * @param FeedInterface $feed
         * @return $this
         */
        public function appendTo(FeedInterface $feed);

        /**
         * Return XML object
         * @return XMLElement
         */
        public function asXML();
    }
