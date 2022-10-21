<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Util;

    /**
     * Interface BookSearcher
     * @package KAASoft\Util
     */
    interface BookSearcher {

        /**
         * @param $searchText string
         * @return array of Book
         */
        function searchBook($searchText);

    }