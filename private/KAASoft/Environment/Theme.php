<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    /**
     * Created by KAA Soft.
     * Date: 2017-12-16
     */

    namespace KAASoft\Environment;

    use JsonSerializable;
    use KAASoft\Util\ValidationHelper;

    class Theme implements JsonSerializable {
        const THEME_CONFIG_FILE_NAME = "config.json";

        private $location;

        private $cover;
        private $title;
        private $version;
        private $author;
        private $type;
        private $advertisementLocations;
        private $menuLocations;
        /**
         * @var array of ColorSchema
         */
        private $colorSchemas;
        private $activeColorSchema;


        public function getDatabaseArray() {
            return [ "cover"                  => $this->cover,
                     "title"                  => $this->title,
                     "version"                => $this->version,
                     "author"                 => $this->author,
                     "type"                   => $this->type,
                     "advertisementLocations" => $this->advertisementLocations,
                     "menuLocations"          => $this->menuLocations,
                     "colorSchemas"           => $this->colorSchemas,
                     "activeColorSchema"      => $this->activeColorSchema ];
        }

        /**
         * @param $settings array
         */
        public function copySettings($settings) {
            $this->setCover(ValidationHelper::getString($settings["cover"]));
            $this->setTitle(ValidationHelper::getString($settings["title"]));
            $this->setVersion(ValidationHelper::getString($settings["version"]));
            $this->setAuthor(ValidationHelper::getString($settings["author"]));
            $this->setType(ValidationHelper::getString($settings["type"]));
            $this->setAdvertisementLocations(ValidationHelper::getArray($settings["advertisementLocations"]));
            $this->setMenuLocations(ValidationHelper::getArray($settings["menuLocations"]));
            $this->setActiveColorSchema(ValidationHelper::getString($settings["activeColorSchema"]));

            $colorSchemaArrays = ValidationHelper::getArray($settings["colorSchemas"]);

            $colorSchemas = [];
            foreach ($colorSchemaArrays as $colorSchemaName => $colorSchemaArray) {
                $colorSchema = new ColorSchema();
                $colorSchema->copySettings($colorSchemaArray);
                $colorSchemas[$colorSchemaName] = $colorSchema;
            }
            if (count($colorSchemas) > 0) {
                $this->setColorSchemas($colorSchemas);
            }
        }

        /**
         * @return array
         */
        function jsonSerialize() {
            return $this->getDatabaseArray();
        }

        /**
         * Theme constructor.
         */
        public function __construct() {
        }

        /**
         * @return mixed
         */
        public function getCover() {
            return $this->cover;
        }

        /**
         * @param mixed $cover
         */
        public function setCover($cover) {
            $this->cover = $cover;
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
        public function getVersion() {
            return $this->version;
        }

        /**
         * @param mixed $version
         */
        public function setVersion($version) {
            $this->version = $version;
        }

        /**
         * @return mixed
         */
        public function getAuthor() {
            return $this->author;
        }

        /**
         * @param mixed $author
         */
        public function setAuthor($author) {
            $this->author = $author;
        }

        /**
         * @return mixed
         */
        public function getType() {
            return $this->type;
        }

        /**
         * @param mixed $type
         */
        public function setType($type) {
            $this->type = $type;
        }

        /**
         * @return mixed
         */
        public function getAdvertisementLocations() {
            return $this->advertisementLocations;
        }

        /**
         * @param mixed $advertisementLocations
         */
        public function setAdvertisementLocations($advertisementLocations) {
            $this->advertisementLocations = $advertisementLocations;
        }

        /**
         * @return mixed
         */
        public function getMenuLocations() {
            return $this->menuLocations;
        }

        /**
         * @param mixed $menuLocations
         */
        public function setMenuLocations($menuLocations) {
            $this->menuLocations = $menuLocations;
        }

        /**
         * @return mixed
         */
        public function getLocation() {
            return $this->location;
        }

        /**
         * @param mixed $location
         */
        public function setLocation($location) {
            $this->location = $location;
        }

        /**
         * @return mixed
         */
        public function getColorSchemas() {
            return $this->colorSchemas;
        }

        /**
         * @param mixed $colorSchemas
         */
        public function setColorSchemas($colorSchemas) {
            $this->colorSchemas = $colorSchemas;
        }

        /**
         * @return mixed
         */
        public function getActiveColorSchema() {
            return $this->activeColorSchema;
        }

        /**
         * @param mixed $activeColorSchema
         */
        public function setActiveColorSchema($activeColorSchema) {
            $this->activeColorSchema = $activeColorSchema;
        }
    }