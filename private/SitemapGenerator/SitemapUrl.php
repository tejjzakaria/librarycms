<?php
    /**
     * Copyright (c) 2015 - 2016 by KAA Soft.  All rights reserved.
     */

    namespace SitemapGenerator;


    class SitemapUrl {

        private $location;
        private $lastModification;
        private $changeFrequency;
        private $priority;

        /**
         * SitemapUrl constructor.
         * @param null $location
         * @param null $lastModification
         * @param null $changeFrequency
         * @param null $priority
         */
        public function __construct($location = null, $lastModification = null, $changeFrequency = null, $priority = null) {
            $this->location = $location;
            $this->lastModification = $lastModification;
            $this->changeFrequency = $changeFrequency;
            $this->priority = $priority;
        }

        /**
         * @return null
         */
        public function getLocation() {
            return $this->location;
        }

        /**
         * @param null $location
         */
        public function setLocation($location) {
            $this->location = $location;
        }

        /**
         * @return null
         */
        public function getLastModification() {
            return $this->lastModification;
        }

        /**
         * @param null $lastModification
         */
        public function setLastModification($lastModification) {
            $this->lastModification = $lastModification;
        }

        /**
         * @return null
         */
        public function getChangeFrequency() {
            return $this->changeFrequency;
        }

        /**
         * @param null $changeFrequency
         */
        public function setChangeFrequency($changeFrequency) {
            $this->changeFrequency = $changeFrequency;
        }

        /**
         * @return null
         */
        public function getPriority() {
            return $this->priority;
        }

        /**
         * @param null $priority
         */
        public function setPriority($priority) {
            $this->priority = $priority;
        }
    }