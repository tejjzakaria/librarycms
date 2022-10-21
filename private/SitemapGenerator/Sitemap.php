<?php
    /**
     * Copyright (c) 2015 - 2016 by KAA Soft.  All rights reserved.
     */

    namespace SitemapGenerator;


    class Sitemap {

        private $fileName;
        private $sitemapContent;

        /**
         * SitemapUrl constructor.
         * @param null $fileName
         * @param null $sitemapContent
         */
        public function __construct($fileName = null, $sitemapContent = null) {
            $this->fileName = $fileName;
            $this->sitemapContent = $sitemapContent;
        }

        /**
         * @return null
         */
        public function getFileName() {
            return $this->fileName;
        }

        /**
         * @param null $fileName
         */
        public function setFileName($fileName) {
            $this->fileName = $fileName;
        }

        /**
         * @return null
         */
        public function getSitemapContent() {
            return $this->sitemapContent;
        }

        /**
         * @param null $sitemapContent
         */
        public function setSitemapContent($sitemapContent) {
            $this->sitemapContent = $sitemapContent;
        }
    }