<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment;


    class OptionGroup {

        const OPTION_GROUP_BOOKS             = "optionGroupBooks";
        //const OPTION_GROUP_BOOK_FIELDS       = "optionGroupBookFields";
        const OPTION_GROUP_GENERAL           = "optionGroupGeneral";
        const OPTION_GROUP_IMAGES            = "optionGroupImages";
        const OPTION_GROUP_DATE_TIME_FORMATS = "optionGroupDateTimeFormats";
        const OPTION_GROUP_SITEMAP           = "optionGroupSitemap";

        const OPTION_GROUP_IDS = [ OptionGroup::OPTION_GROUP_BOOKS,
                                   OptionGroup::OPTION_GROUP_GENERAL,
                                   OptionGroup::OPTION_GROUP_IMAGES,
                                   OptionGroup::OPTION_GROUP_DATE_TIME_FORMATS,
                                   OptionGroup::OPTION_GROUP_SITEMAP/*,
                                   OptionGroup::OPTION_GROUP_BOOK_FIELDS*/ ];

        private static $OPTION_GROUPS  = [];
        private static $IS_INITIALIZED = false;

        private $id;
        private $title;
        private $priority;

        /**
         * OptionGroup constructor.
         * @param $id
         * @param $priority
         * @param $title
         */
        private function __construct($id, $priority, $title = null) {
            $this->id = $id;
            $this->title = $title;
            $this->priority = $priority;
        }

        /**
         * @param $optionGroupId
         * @return OptionGroup
         */
        public static function getOptionGroup($optionGroupId) {
            if (!OptionGroup::$IS_INITIALIZED) {
                OptionGroup::$OPTION_GROUPS[OptionGroup::OPTION_GROUP_GENERAL] = new OptionGroup(OptionGroup::OPTION_GROUP_GENERAL,
                                                                                                 1,
                                                                                                 _("General"));
                OptionGroup::$OPTION_GROUPS[OptionGroup::OPTION_GROUP_BOOKS] = new OptionGroup(OptionGroup::OPTION_GROUP_BOOKS,
                                                                                               2,
                                                                                               _("Books"));
                OptionGroup::$OPTION_GROUPS[OptionGroup::OPTION_GROUP_IMAGES] = new OptionGroup(OptionGroup::OPTION_GROUP_IMAGES,
                                                                                                3,
                                                                                                _("Images"));
                OptionGroup::$OPTION_GROUPS[OptionGroup::OPTION_GROUP_DATE_TIME_FORMATS] = new OptionGroup(OptionGroup::OPTION_GROUP_DATE_TIME_FORMATS,
                                                                                                           4,
                                                                                                           _("Date/Time Formats"));
                OptionGroup::$OPTION_GROUPS[OptionGroup::OPTION_GROUP_SITEMAP] = new OptionGroup(OptionGroup::OPTION_GROUP_SITEMAP,
                                                                                                 5,
                                                                                                 _("Sitemap"));
                /*OptionGroup::$OPTION_GROUPS[OptionGroup::OPTION_GROUP_BOOK_FIELDS] = new OptionGroup(OptionGroup::OPTION_GROUP_BOOK_FIELDS,
                                                                                                     6,
                                                                                                     _("Book Fields"));*/
                OptionGroup::$IS_INITIALIZED = true;
            }

            switch ($optionGroupId) {
                case OptionGroup::OPTION_GROUP_BOOKS:
                    return OptionGroup::$OPTION_GROUPS[OptionGroup::OPTION_GROUP_BOOKS];
                case OptionGroup::OPTION_GROUP_IMAGES:
                    return OptionGroup::$OPTION_GROUPS[OptionGroup::OPTION_GROUP_IMAGES];
                case OptionGroup::OPTION_GROUP_DATE_TIME_FORMATS:
                    return OptionGroup::$OPTION_GROUPS[OptionGroup::OPTION_GROUP_DATE_TIME_FORMATS];
                case OptionGroup::OPTION_GROUP_SITEMAP:
                    return OptionGroup::$OPTION_GROUPS[OptionGroup::OPTION_GROUP_SITEMAP];
                /*case OptionGroup::OPTION_GROUP_BOOK_FIELDS:
                    return OptionGroup::$OPTION_GROUPS[OptionGroup::OPTION_GROUP_BOOK_FIELDS];*/
                default:
                    return OptionGroup::$OPTION_GROUPS[OptionGroup::OPTION_GROUP_GENERAL];
            }

        }

        /**
         * @return mixed
         */
        public function getId() {
            return $this->id;
        }

        /**
         * @param mixed $id
         */
        public function setId($id) {
            $this->id = $id;
        }

        /**
         * @return mixed
         */
        public function getTitle() {
            return $this->title;
        }

        /**
         * @param mixed $title
         */
        public function setTitle($title) {
            $this->title = $title;
        }

        /**
         * @return mixed
         */
        public function getPriority() {
            return $this->priority;
        }

        /**
         * @param mixed $priority
         */
        public function setPriority($priority) {
            $this->priority = $priority;
        }
    }