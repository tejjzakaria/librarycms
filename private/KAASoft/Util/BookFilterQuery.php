<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Util;

    use KAASoft\Database\Entity\General\Book;

    /**
     * Class BookFilterQuery
     * @package KAASoft\Util
     */
    class BookFilterQuery {
        private $customFields;
        /**
         * @var array of int
         */
        private $authorIds;
        /**
         * @var array of int
         */
        private $genreIds;
        /**
         * @var array of int
         */
        private $seriesIds;
        /**
         * @var array of int
         */
        private $publisherIds;
        /**
         * @var int
         */
        private $startYear;
        /**
         * @var int
         */
        private $endYear;
        /**
         * @var array of int
         */
        private $bindings;
        /**
         * @var array of int
         */
        private $ownerIds;

        function __construct() {
        }

        /**
         * @param array $databaseArray
         * @return BookFilterQuery to restore form databaseArray
         */
        public static function getObjectInstance(array $databaseArray) {
            $bookFilterQuery = new BookFilterQuery();
            $bookFilterQuery->setAuthorIds(ValidationHelper::getArray($databaseArray["authorIds"]));
            $bookFilterQuery->setBindings(ValidationHelper::getArray($databaseArray["bindings"]));
            $bookFilterQuery->setGenreIds(ValidationHelper::getArray($databaseArray["genreIds"]));
            $bookFilterQuery->setSeriesIds(ValidationHelper::getArray($databaseArray["seriesIds"]));
            $bookFilterQuery->setPublisherIds(ValidationHelper::getArray($databaseArray["publisherIds"]));
            $bookFilterQuery->setOwnerIds(ValidationHelper::getArray($databaseArray["ownerIds"]));
            $bookFilterQuery->setStartYear(ValidationHelper::getNullableInt($databaseArray["startYear"]));
            $bookFilterQuery->setEndYear(ValidationHelper::getNullableInt($databaseArray["endYear"]));

            $bookCustomFields = Book::getCustomFields();
            $fieldNames = array_keys($databaseArray);
            foreach ($bookCustomFields as $bookCustomField) {
                if (in_array($bookCustomField->getName(),
                             $fieldNames)) {
                    $bookFilterQuery->addCustomField($bookCustomField->getName(),
                                                     $_POST[$bookCustomField->getName()]);
                }
            }

            return $bookFilterQuery;
        }

        /**
         * @return array
         */
        public function getBindings() {
            return $this->bindings;
        }

        /**
         * @param array $bindings
         */
        public function setBindings($bindings) {
            $this->bindings = $bindings;
        }

        /**
         * @return array
         */
        public function getAuthorIds() {
            return $this->authorIds;
        }

        /**
         * @param array $authorIds
         */
        public function setAuthorIds($authorIds) {
            $this->authorIds = $authorIds;
        }

        /**
         * @return array
         */
        public function getGenreIds() {
            return $this->genreIds;
        }

        /**
         * @param array $genreIds
         */
        public function setGenreIds($genreIds) {
            $this->genreIds = $genreIds;
        }

        /**
         * @return array
         */
        public function getSeriesIds() {
            return $this->seriesIds;
        }

        /**
         * @param array $seriesIds
         */
        public function setSeriesIds($seriesIds) {
            $this->seriesIds = $seriesIds;
        }

        /**
         * @return array
         */
        public function getPublisherIds() {
            return $this->publisherIds;
        }

        /**
         * @param array $publisherIds
         */
        public function setPublisherIds($publisherIds) {
            $this->publisherIds = $publisherIds;
        }

        /**
         * @return int
         */
        public function getStartYear() {
            return $this->startYear;
        }

        /**
         * @param int $startYear
         */
        public function setStartYear($startYear) {
            $this->startYear = $startYear;
        }

        /**
         * @return array
         */
        public function getOwnerIds() {
            return $this->ownerIds;
        }

        /**
         * @param array $ownerIds
         */
        public function setOwnerIds($ownerIds) {
            $this->ownerIds = $ownerIds;
        }

        /**
         * @return int
         */
        public function getEndYear() {
            return $this->endYear;
        }

        /**
         * @param int $endYear
         */
        public function setEndYear($endYear) {
            $this->endYear = $endYear;
        }

        /**
         * @return mixed
         */
        public function getCustomFields() {
            return $this->customFields;
        }

        /**
         * @param mixed $customFields
         */
        public function setCustomFields($customFields) {
            $this->customFields = $customFields;
        }

        /**
         * @param $name
         * @param $value
         */
        public function addCustomField($name, $value) {
            if ($this->customFields === null) {
                $this->customFields = [];
            }
            $this->customFields[$name] = $value;
        }
    }