<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment;

    use KAASoft\Util\FileHelper;
    use KAASoft\Util\Layout\LayoutContainer;
    use KAASoft\Util\Layout\LayoutElement;

    /**
     * Class DatabaseSettings
     * @package KAASoft\Environment
     */
    class BookLayoutSettings extends AbstractSettings {
        const BookLayoutSettingsSettingsFileNameJSON = '/KAASoft/Config/BookLayoutSettings.json';

        const ROOT_SETTINGS_ELEMENT = "layoutContainers";

        const DEFAULT_CONTAINER_CONTENT = "content";
        const DEFAULT_CONTAINER_SIDEBAR = "sidebar";

        /**
         * @var array
         */
        private $layoutContainers;

        /**
         * copy data from assoc array to object fields
         * @param $settings BookLayoutSettings
         */
        public function copySettings($settings) {
            $containers = $settings[BookLayoutSettings::ROOT_SETTINGS_ELEMENT];

            foreach ($containers as $containerArray) {
                $container = LayoutContainer::getObjectInstance($containerArray);
                $this->addLayoutContainer($container);
            }
        }

        /**
         * @param $name
         * @return mixed|null
         */
        public function getContainer($name) {
            foreach ($this->layoutContainers as $container) {
                if (strcmp($container->getName(),
                           $name) === 0
                ) {
                    return $container;
                }
            }

            return null;
        }

        /**
         * copy data from another Settings object
         * @param $settings BookLayoutSettings
         */
        public function cloneSettings($settings) {
            $this->setLayoutContainers($settings->getLayoutContainers());
        }

        /**
         * Returns config file to load/store settings
         * @return string
         */
        public function getConfigFileName() {
            return realpath(FileHelper::getPrivateFolderLocation()) . BookLayoutSettings::BookLayoutSettingsSettingsFileNameJSON;
        }

        /**
         * Sets default settings
         */
        public function setDefaultSettings() {
            $this->layoutContainers = [];

            // content
            $contentContainer = new LayoutContainer(BookLayoutSettings::DEFAULT_CONTAINER_CONTENT);
            $contentContainer->addElement(new LayoutElement(0,
                                                            0,
                                                            6,
                                                            1,
                                                            "title",
                                                            "Title"));
            $contentContainer->addElement(new LayoutElement(6,
                                                            0,
                                                            6,
                                                            1,
                                                            "subtitle",
                                                            "SubTitle"));
            $contentContainer->addElement(new LayoutElement(0,
                                                            1,
                                                            12,
                                                            1,
                                                            "url",
                                                            "URL"));
            $contentContainer->addElement(new LayoutElement(0,
                                                            2,
                                                            6,
                                                            1,
                                                            "ISBN10",
                                                            "ISBN 10"));
            $contentContainer->addElement(new LayoutElement(6,
                                                            2,
                                                            6,
                                                            1,
                                                            "ISBN13",
                                                            "ISBN 13"));
            $contentContainer->addElement(new LayoutElement(0,
                                                            3,
                                                            6,
                                                            1,
                                                            "series",
                                                            "Series"));
            $contentContainer->addElement(new LayoutElement(6,
                                                            3,
                                                            6,
                                                            1,
                                                            "publisher",
                                                            "Publisher"));
            $contentContainer->addElement(new LayoutElement(0,
                                                            4,
                                                            12,
                                                            1,
                                                            "author",
                                                            "Authors"));
            $contentContainer->addElement(new LayoutElement(0,
                                                            5,
                                                            12,
                                                            1,
                                                            "genre",
                                                            "Genres"));
            $contentContainer->addElement(new LayoutElement(0,
                                                            6,
                                                            12,
                                                            1,
                                                            "tag",
                                                            "Tags"));
            $contentContainer->addElement(new LayoutElement(0,
                                                            7,
                                                            6,
                                                            1,
                                                            "edition",
                                                            "Edition"));
            $contentContainer->addElement(new LayoutElement(6,
                                                            7,
                                                            3,
                                                            1,
                                                            "publishingYear",
                                                            "Published Year"));
            $contentContainer->addElement(new LayoutElement(9,
                                                            7,
                                                            3,
                                                            1,
                                                            "pages",
                                                            "Pages"));
            $contentContainer->addElement(new LayoutElement(0,
                                                            8,
                                                            3,
                                                            1,
                                                            "type",
                                                            "Type"));
            $contentContainer->addElement(new LayoutElement(3,
                                                            8,
                                                            3,
                                                            1,
                                                            "physicalForm",
                                                            "Physical Form"));
            $contentContainer->addElement(new LayoutElement(6,
                                                            8,
                                                            3,
                                                            1,
                                                            "size",
                                                            "Size"));
            $contentContainer->addElement(new LayoutElement(9,
                                                            8,
                                                            3,
                                                            1,
                                                            "binding",
                                                            "Binding"));
            $contentContainer->addElement(new LayoutElement(0,
                                                            9,
                                                            12,
                                                            1,
                                                            "store",
                                                            "Store"));
            $contentContainer->addElement(new LayoutElement(0,
                                                            10,
                                                            12,
                                                            1,
                                                            "location",
                                                            "Location"));
            $contentContainer->addElement(new LayoutElement(0,
                                                            11,
                                                            6,
                                                            1,
                                                            "price",
                                                            "Price"));
            $contentContainer->addElement(new LayoutElement(6,
                                                            11,
                                                            6,
                                                            1,
                                                            "language",
                                                            "Language"));
            $contentContainer->addElement(new LayoutElement(0,
                                                            12,
                                                            12,
                                                            1,
                                                            "description",
                                                            "Description"));
            $contentContainer->addElement(new LayoutElement(0,
                                                            13,
                                                            12,
                                                            1,
                                                            "notes",
                                                            "Notes"));

            $this->layoutContainers[] = $contentContainer;

            // sidebar
            $sidebarContainer = new LayoutContainer(BookLayoutSettings::DEFAULT_CONTAINER_SIDEBAR);
            $sidebarContainer->addElement(new LayoutElement(0,
                                                            0,
                                                            4,
                                                            1,
                                                            "cover",
                                                            "Cover"));
            $sidebarContainer->addElement(new LayoutElement(0,
                                                            1,
                                                            4,
                                                            1,
                                                            "eBook",
                                                            "eBook"));

            $this->layoutContainers[] = $sidebarContainer;
        }

        /**
         * @return array
         */
        function jsonSerialize() {
            return [ BookLayoutSettings::ROOT_SETTINGS_ELEMENT => $this->layoutContainers ];
        }

        /**
         * @return array
         */
        public function getLayoutContainers() {
            return $this->layoutContainers;
        }

        /**
         * @param array $layoutContainers
         */
        public function setLayoutContainers($layoutContainers) {
            $this->layoutContainers = $layoutContainers;
        }

        /**
         * @param $container LayoutContainer
         */
        public function addLayoutContainer($container) {
            if ($this->layoutContainers === null) {
                $this->layoutContainers = [];
            }
            $this->layoutContainers[] = $container;
        }
    }