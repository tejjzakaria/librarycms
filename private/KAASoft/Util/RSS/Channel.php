<?php

    namespace KAASoft\Util\RSS;

    use JsonSerializable;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class Channel
     * @package KAASoft\Util\RSS
     */
    class Channel implements ChannelInterface, JsonSerializable {
        /**
         * @var string
         */
        protected $name;

        /** @var string */
        protected $title;

        /** @var string */
        protected $link;

        /** @var string */
        protected $description;

        /** @var string */
        protected $language;

        /** @var string */
        protected $copyright;

        /** @var int */
        protected $pubDate;

        /** @var int */
        protected $lastBuildDate;

        /** @var int */
        protected $ttl;

        /** @var ItemInterface[] */
        protected $items = [];
        /**
         * @var string
         */
        protected $managingEditor;
        /**
         * @var string
         */
        protected $webMaster;
        /**
         * @var string
         */
        protected $rating;
        /**
         * @var string
         */
        protected $docs;
        /**
         * @var string
         */
        protected $generator;
        /**
         * @var string
         */
        protected $image;
        /**
         * @var string[]
         */
        protected $categories = [];
        /**
         * @var integer
         */
        protected $descriptionSymbolNumber = null;

        /**
         * Set channel title
         * @param string $title
         * @return $this
         */
        public function setTitle($title) {
            $this->title = $title;

            return $this;
        }

        /**
         * Set channel URL
         * @param string $link
         * @return $this
         */
        public function setLink($link) {
            $this->link = $link;

            return $this;
        }

        /**
         * Set channel description
         * @param string $description
         * @return $this
         */
        public function setDescription($description) {
            $this->description = $description;

            return $this;
        }

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
        public function setLanguage($language) {
            $this->language = $language;

            return $this;
        }

        /**
         * Set channel copyright
         * @param string $copyright
         * @return $this
         */
        public function setCopyright($copyright) {
            $this->copyright = $copyright;

            return $this;
        }

        /**
         * Set channel published date
         * @param int $pubDate Unix timestamp
         * @return $this
         */
        public function setPubDate($pubDate) {
            $this->pubDate = $pubDate;

            return $this;
        }

        /**
         * @return int
         */
        public function getPubDate() {
            return $this->pubDate;
        }

        /**
         * Set channel last build date
         * @param int $lastBuildDate Unix timestamp
         * @return $this
         */
        public function setLastBuildDate($lastBuildDate) {
            $this->lastBuildDate = $lastBuildDate;

            return $this;
        }

        /**
         * Set channel ttl (minutes)
         * @param int $ttl
         * @return $this
         */
        public function setTtl($ttl) {
            $this->ttl = $ttl;

            return $this;
        }

        /**
         * @param string $managingEditor
         * @return $this
         */
        public function setManagingEditor($managingEditor) {
            $this->managingEditor = $managingEditor;

            return $this;
        }

        /**
         * @param string $webMaster
         * @return $this
         */
        public function setWebmaster($webMaster) {
            $this->webMaster = $webMaster;

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
         * @param array $categories
         * @return $this
         */
        public function setCategories($categories) {
            $this->categories = $categories;

            return $this;
        }


        /**
         * @param string $generator
         * @return $this
         */
        public function setGenerator($generator) {
            $this->generator = $generator;

            return $this;
        }

        /**
         * @param string $docs
         * @return $this
         */
        public function setDocs($docs) {
            $this->docs = $docs;

            return $this;
        }

        /**
         * @param string $image
         * @return $this
         */
        public function setImage($image) {
            $this->image = $image;

            return $this;
        }

        /**
         * @param string $rating
         * @return $this
         */
        public function setRating($rating) {
            $this->rating = $rating;

            return $this;
        }

        /**
         * Add item object
         * @param ItemInterface $item
         * @return $this
         */
        public function addItem(ItemInterface $item) {
            $this->items[] = $item;

            return $this;
        }

        /**
         * Append to feed
         * @param FeedInterface $feed
         * @return $this
         */
        public function appendTo(FeedInterface $feed) {
            $feed->addChannel($this);

            return $this;
        }

        /**
         * Return XML object
         * @return XMLElement
         */
        public function asXML() {
            $xml = new XMLElement('<?xml version="1.0" encoding="UTF-8" ?><channel></channel>',
                                  LIBXML_NOERROR | LIBXML_ERR_NONE | LIBXML_ERR_FATAL);
            $xml->addChild('title',
                           $this->title);
            $xml->addChild('link',
                           $this->link);
            $xml->addChild('description',
                           ValidationHelper::isEmpty($this->descriptionSymbolNumber) ? $this->description : substr($this->description,
                                                                                                                   0,
                                                                                                                   $this->descriptionSymbolNumber));

            if ($this->language !== null) {
                $xml->addChild('language',
                               $this->language);
            }

            if ($this->copyright !== null) {
                $xml->addChild('copyright',
                               $this->copyright);
            }

            if ($this->managingEditor !== null) {
                $xml->addChild('managingEditor',
                               $this->managingEditor);
            }

            if ($this->webMaster !== null) {
                $xml->addChild('webMaster',
                               $this->webMaster);
            }

            if ($this->pubDate !== null) {
                $xml->addChild('pubDate',
                               date(DATE_RSS,
                                    $this->pubDate));
            }

            if ($this->lastBuildDate !== null) {
                $xml->addChild('lastBuildDate',
                               date(DATE_RSS,
                                    $this->lastBuildDate));
            }

            if ($this->generator !== null) {
                $xml->addChild('generator',
                               $this->generator);
            }

            if ($this->docs !== null) {
                $xml->addChild('docs',
                               $this->docs);
            }

            if ($this->ttl !== null) {
                $xml->addChild('ttl',
                               $this->ttl);
            }

            if ($this->image !== null) {
                $element = $xml->addChild('image');

                $element->addChild("url",
                                   $this->image);
                $element->addChild("title",
                                   $this->title);
                $element->addChild("link",
                                   $this->link);
                /*                $element->addChild("width",
                                                   $this->image->getWidth());
                                $element->addChild("height",
                                                   $this->image->getHeight());
                                $element->addChild("description",
                                                   $this->image->getTitle());*/
            }

            foreach ($this->categories as $category) {
                $element = $xml->addChild("category",
                                          $category[0]);

                if (isset( $category[1] )) {
                    $element->addAttribute("domain",
                                           $category[1]);
                }
            }

            if ($this->rating !== null) {
                $xml->addChild('rating',
                               $this->rating);
            }

            foreach ($this->items as $item) {
                $toDom = dom_import_simplexml($xml);
                $fromDom = dom_import_simplexml($item->asXML());
                $toDom->appendChild($toDom->ownerDocument->importNode($fromDom,
                                                                      true));
            }

            return $xml;
        }

        public function jsonSerialize() {
            return [ "name"                    => $this->name,
                     "title"                   => $this->title,
                     "link"                    => $this->link,
                     "description"             => $this->description,
                     "language"                => $this->language,
                     "copyright"               => $this->copyright,
                     "pubDate"                 => $this->pubDate,
                     "lastBuildDate"           => $this->lastBuildDate,
                     "ttl"                     => $this->ttl,
                     "managingEditor"          => $this->managingEditor,
                     "webMaster"               => $this->webMaster,
                     "rating"                  => $this->rating,
                     "docs"                    => $this->docs,
                     "generator"               => $this->generator,
                     "image"                   => $this->image,
                     "categories"              => $this->categories,
                     "descriptionSymbolNumber" => $this->descriptionSymbolNumber ];
        }

        /**
         * @param array $channelArray
         * @return Channel
         */
        public static function getInstance(array $channelArray) {
            $channel = new Channel();

            $channel->setName(ValidationHelper::getString($channelArray["name"]));
            $channel->setTitle(ValidationHelper::getString($channelArray["title"]));
            $channel->setLink(ValidationHelper::getString($channelArray["link"]));
            $channel->setDescription(ValidationHelper::getString($channelArray["description"]));
            $channel->setLanguage(ValidationHelper::getString($channelArray["language"]));
            $channel->setCopyright(ValidationHelper::getString($channelArray["copyright"]));
            $channel->setPubDate(ValidationHelper::getNullableInt($channelArray["pubDate"]));
            $channel->setLastBuildDate(ValidationHelper::getNullableInt($channelArray["lastBuildDate"]));
            $channel->setTtl(ValidationHelper::getNullableInt($channelArray["ttl"]));
            $channel->setManagingEditor(ValidationHelper::getString($channelArray["managingEditor"]));
            $channel->setWebmaster(ValidationHelper::getString($channelArray["webMaster"]));
            $channel->setRating(ValidationHelper::getString($channelArray["rating"]));
            $channel->setDocs(ValidationHelper::getString($channelArray["docs"]));
            $channel->setGenerator(ValidationHelper::getString($channelArray["generator"]));
            $channel->setGenerator(ValidationHelper::getString($channelArray["generator"]));
            $channel->setImage(ValidationHelper::getString($channelArray["image"]));
            $channel->setCategories(ValidationHelper::getArray($channelArray["categories"]));
            $channel->setDescriptionSymbolNumber(ValidationHelper::getNullableInt($channelArray["descriptionSymbolNumber"]));

            return $channel;
        }

        /**
         * @return string
         */
        public function getName() {
            return $this->name;
        }

        /**
         * @param string $name
         */
        public function setName($name) {
            $this->name = $name;
        }

        /**
         * @return string
         */
        public function getTitle() {
            return $this->title;
        }

        /**
         * @return string
         */
        public function getLink() {
            return $this->link;
        }

        /**
         * @return string
         */
        public function getDescription() {
            return $this->description;
        }

        /**
         * @return string
         */
        public function getLanguage() {
            return $this->language;
        }

        /**
         * @return string
         */
        public function getCopyright() {
            return $this->copyright;
        }

        /**
         * @return int
         */
        public function getLastBuildDate() {
            return $this->lastBuildDate;
        }

        /**
         * @return int
         */
        public function getTtl() {
            return $this->ttl;
        }

        /**
         * @return ItemInterface[]
         */
        public function getItems() {
            return $this->items;
        }

        /**
         * @return string
         */
        public function getManagingEditor() {
            return $this->managingEditor;
        }

        /**
         * @return string
         */
        public function getWebMaster() {
            return $this->webMaster;
        }

        /**
         * @return string
         */
        public function getRating() {
            return $this->rating;
        }

        /**
         * @return string
         */
        public function getDocs() {
            return $this->docs;
        }

        /**
         * @return string
         */
        public function getGenerator() {
            return $this->generator;
        }

        /**
         * @return string
         */
        public function getImage() {
            return $this->image;
        }

        /**
         * @return \string[]
         */
        public function getCategories() {
            return $this->categories;
        }

        /**
         * @return int
         */
        public function getDescriptionSymbolNumber() {
            return $this->descriptionSymbolNumber;
        }

        /**
         * @param int $descriptionSymbolNumber
         */
        public function setDescriptionSymbolNumber($descriptionSymbolNumber) {
            $this->descriptionSymbolNumber = $descriptionSymbolNumber;
        }
    }
