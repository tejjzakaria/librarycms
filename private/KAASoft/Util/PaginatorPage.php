<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Util;

    /**
     * Class PaginatorPage
     * @package KAASoft\Util
     */
    class PaginatorPage {

        private $link;
        private $title;
        private $isCurrent;
        private $isFirst;
        private $isLast;
        private $isNext;
        private $isPrevious;

        /**
         * @param      $link
         * @param      $title
         * @param bool $isCurrent
         */
        function __construct($link, $title, $isCurrent = false) {
            $this->link = $link;
            $this->title = $title;
            $this->isCurrent = $isCurrent;
        }

        /**
         * @return mixed
         */
        public function getLink() {
            return $this->link;
        }

        /**
         * @param mixed $link
         */
        public function setLink($link) {
            $this->link = $link;
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
         * @return boolean
         */
        public function isCurrent() {
            return $this->isCurrent;
        }

        /**
         * @param boolean $isCurrent
         */
        public function setIsCurrent($isCurrent) {
            $this->isCurrent = $isCurrent;
        }

        /**
         * @return mixed
         */
        public function isFirst() {
            return $this->isFirst;
        }

        /**
         * @param mixed $isFirst
         */
        public function setIsFirst($isFirst) {
            $this->isFirst = $isFirst;
        }

        /**
         * @return mixed
         */
        public function isLast() {
            return $this->isLast;
        }

        /**
         * @param mixed $isLast
         */
        public function setIsLast($isLast) {
            $this->isLast = $isLast;
        }

        /**
         * @return mixed
         */
        public function isNext() {
            return $this->isNext;
        }

        /**
         * @param mixed $isNext
         */
        public function setIsNext($isNext) {
            $this->isNext = $isNext;
        }

        /**
         * @return mixed
         */
        public function isPrevious() {
            return $this->isPrevious;
        }

        /**
         * @param mixed $isPrevious
         */
        public function setIsPrevious($isPrevious) {
            $this->isPrevious = $isPrevious;
        }

    }