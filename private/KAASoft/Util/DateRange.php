<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Util;

    /**
     * Class DateRange
     * @package KAASoft\Util
     */
    class DateRange {

        private $startDate;
        private $endDate;

        /**
         * DateRange constructor.
         * @param null $startDate
         * @param null $endDate
         */
        function __construct($startDate = null, $endDate = null) {
            $this->startDate = $startDate;
            $this->endDate = $endDate;
        }

        /**
         * @return mixed
         */
        public function getStartDate() {
            return $this->startDate;
        }

        /**
         * @param mixed $startDate
         */
        public function setStartDate($startDate) {
            $this->startDate = $startDate;
        }

        /**
         * @return mixed
         */
        public function getEndDate() {
            return $this->endDate;
        }

        /**
         * @param mixed $endDate
         */
        public function setEndDate($endDate) {
            $this->endDate = $endDate;
        }

    }