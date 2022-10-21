<?php

    namespace KAASoft\Util\RSS;

    /**
     * Class Item
     * @package KAASoft\Util\RSS
     */
    class ChannelItem implements ItemInterface {

        /** @var string */
        protected $title;

        /** @var string */
        protected $link;

        /** @var string */
        protected $description;

        /** @var  string */
        protected $content;

        /** @var  string */
        protected $author;

        /** @var array */
        protected $categories = [];

        /** @var string */
        protected $guid;

        /** @var bool */
        protected $isPermalink;

        /** @var int */
        protected $pubDate;

        /** @var array */
        protected $enclosure;
        /**
         * @var string
         */
        protected $comments;

        /**
         * Set item title
         * @param string $title
         * @return $this
         */
        public function setTitle($title) {
            $this->title = $title;

            return $this;
        }

        /**
         * Set item URL
         * @param string $link
         * @return $this
         */
        public function setLink($link) {
            $this->link = $link;

            return $this;
        }

        /**
         * Set item description
         * @param string $description
         * @return $this
         */
        public function setDescription($description) {
            $this->description = $description;

            return $this;
        }

        /**
         * Set item category
         * @param string $name   Category name
         * @param string $domain Category URL
         * @return $this
         */
        public function addCategory($name, $domain = null) {
            $this->categories[] = [ $name,
                                    $domain ];

            return $this;
        }

        /**
         * Set GUID
         * @param string $guid
         * @param bool   $isPermalink
         * @return $this
         */
        public function setGuid($guid, $isPermalink = false) {
            $this->guid = $guid;
            $this->isPermalink = $isPermalink;

            return $this;
        }

        /**
         * Set published date
         * @param int $pubDate Unix timestamp
         * @return $this
         */
        public function setPubDate($pubDate) {
            $this->pubDate = $pubDate;

            return $this;
        }

        public function setComments($url) {
            $this->comments = $url;

            return $this;
        }

        /**
         * Set enclosure
         * @param string $url    Url to media file
         * @param int    $length Length in bytes of the media file
         * @param string $type   Media type, default is audio/mpeg
         * @return $this
         */
        public function setEnclosure($url, $length = 0, $type = "audio/mpeg") {
            $this->enclosure = [ "url"    => $url,
                                 "length" => $length,
                                 "type"   => $type ];

            return $this;
        }

        /**
         * Append item to the channel
         * @param ChannelInterface $channel
         * @return $this
         */
        public function appendTo(ChannelInterface $channel) {
            $channel->addItem($this);

            return $this;
        }

        /**
         * Set author name for article
         *
         * @param $author
         * @return $this
         */
        public function setAuthor($author) {
            $this->author = $author;

            return $this;
        }

        /**
         * Return XML object
         * @return XMLElement
         */
        public function asXML() {
            $xml = new XMLElement('<?xml version="1.0" encoding="UTF-8" ?><item></item>',
                                  LIBXML_NOERROR | LIBXML_ERR_NONE | LIBXML_ERR_FATAL);
            $xml->addChild("title",
                           $this->title);
            $xml->addChild("link",
                           $this->link);
            $xml->addChild("description",
                           $this->description);

            if ($this->pubDate !== null) {
                $xml->addChild("pubDate",
                               date(DATE_RSS,
                                    $this->pubDate));
            }

            $xml->addChild("comments",
                           $this->comments);

            if ($this->author) {
                $xml->addChildCData("author",
                                    $this->author);
            }
            if ($this->guid) {
                $guid = $xml->addChild("guid",
                                       $this->guid);

                if ($this->isPermalink) {
                    $guid->addAttribute("isPermaLink",
                                        "true");
                }
            }

            foreach ($this->categories as $category) {
                $element = $xml->addChild("category",
                                          $category[0]);

                if (isset( $category[1] )) {
                    $element->addAttribute("domain",
                                           $category[1]);
                }
            }

            if (is_array($this->enclosure) && ( count($this->enclosure) == 3 )) {
                $element = $xml->addChild("enclosure");
                $element->addAttribute("url",
                                       $this->enclosure["url"]);
                $element->addAttribute("type",
                                       $this->enclosure["type"]);

                if ($this->enclosure["length"]) {
                    $element->addAttribute("length",
                                           $this->enclosure["length"]);
                }
            }

            return $xml;
        }
    }
