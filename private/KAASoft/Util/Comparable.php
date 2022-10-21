<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Util;

    /**
     * @param $other
     * @return bool - true if objects are equal, false - in other cases
     */
    interface Comparable {
        public function compareTo($other);
    }