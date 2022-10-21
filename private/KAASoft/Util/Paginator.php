<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Util;

    /**
     * Class Paginator
     * @package KAASoft\Util
     */
    class Paginator {

        public $currentPage;
        public $perPage;
        public $totalCount;

        public $visiblePages = 2;

        /**
         * @param int $page
         * @param int $perPage
         * @param int $totalCount
         */
        public function __construct($page = 1, $perPage = 20, $totalCount = 0) {
            $this->currentPage = (int)$page;
            $this->perPage = (int)$perPage;
            $this->totalCount = (int)$totalCount;

            if ($this->currentPage <= 1) {
                $this->currentPage = 1;
            }
            else {
                if ($this->currentPage > $this->getTotalPages()) {
                    $this->currentPage = $this->getTotalPages();
                }
                // fix for case when getTotalPages = 0
                if($this->currentPage < 1){
                    $this->currentPage = 1;
                }
            }
        }

        /**
         * @param $baseLink
         * @param $page
         * @return string
         */
        private static function buildPageLink($baseLink, $page) {
            return $baseLink . ( $page != 1 ? ( "/page/" . $page ) : "" );
        }

        /**
         * @param $page
         * @param $baseLink
         * @return array
         */
        public function preparePages($page, $baseLink) {
            $pages = [];

            $pageNumber = $this->getPageNumber();
            if ($pageNumber > 1) {
                // first page
                if ($page != 1) {
                    $first = new PaginatorPage(Paginator::buildPageLink($baseLink,
                                                                        1),
                                               _("First"));
                    $first->setIsFirst(true);
                    $pages[] = $first;
                }

                //previous page
                if ($this->hasPreviousPage()) {
                    $previous = new PaginatorPage(Paginator::buildPageLink($baseLink,
                                                                           $this->getPreviousPage()),
                                                  _("Previous"));
                    $previous->setIsPrevious(true);
                    $pages[] = $previous;
                }

                $startPage = max($this->getCurrentPage() - $this->visiblePages,
                                 1);
                $endPage = min($this->getCurrentPage() + $this->visiblePages,
                               $pageNumber);
                for ($i = $startPage; $i <= $endPage; $i++) {
                    $pages[] = new PaginatorPage(Paginator::buildPageLink($baseLink,
                                                                          $i),
                                                 $i,
                                                 $i == $this->getCurrentPage());
                }

                // next page
                if ($this->hasNextPage()) {
                    $next = new PaginatorPage(Paginator::buildPageLink($baseLink,
                                                                       $this->getNextPage()),
                                              _("Next"));
                    $next->setIsNext(true);
                    $pages[] = $next;
                }

                // last page
                if ($page != $this->getTotalPages()) {
                    $last = new PaginatorPage(Paginator::buildPageLink($baseLink,
                                                                       $this->getTotalPages()),
                                              _("Last"));
                    $last->setIsLast(true);
                    $pages[] = $last;
                }
            }


            return $pages;
        }

        public function getOffset() {
            // Assuming 20 items per page:
            // page 1 has an offset of 0    (1-1) * 20
            // page 2 has an offset of 20   (2-1) * 20
            //   in other words, page 2 starts with item 21
            return ( $this->currentPage - 1 ) * $this->perPage;
        }

        public function getCurrentPage() {
            return (int)$this->currentPage;
        }

        public function setCurrentPage($currentPage) {
            $this->currentPage = $currentPage;
        }

        public function getPageNumber() {
            return floor($this->totalCount / $this->perPage) + ( $this->totalCount % $this->perPage == 0 ? 0 : 1 );
        }

        public function getTotalPages() {
            return ceil($this->totalCount / $this->perPage);
        }

        public function getPreviousPage() {
            return $this->currentPage - 1;
        }

        public function getNextPage() {
            return $this->currentPage + 1;
        }

        public function hasPreviousPage() {
            return $this->getPreviousPage() >= 1 ? true : false;
        }

        public function hasNextPage() {
            return $this->getNextPage() <= $this->getTotalPages() ? true : false;
        }

        /**
         * @return int
         */
        public function getPerPage() {
            return $this->perPage;
        }

        /**
         * @param int $perPage
         */
        public function setPerPage($perPage) {
            $this->perPage = $perPage;
        }

        /**
         * @return int
         */
        public function getTotalCount() {
            return $this->totalCount;
        }

        /**
         * @param int $totalCount
         */
        public function setTotalCount($totalCount) {
            $this->totalCount = $totalCount;
        }

        /**
         * @return int
         */
        public function getVisiblePages() {
            return $this->visiblePages;
        }

        /**
         * @param int $visiblePages
         */
        public function setVisiblePages($visiblePages) {
            $this->visiblePages = $visiblePages;
        }
    }

    ?>