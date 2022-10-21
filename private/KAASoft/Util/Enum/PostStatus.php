<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Util\Enum;

    /**
     * Class PostStatus
     * @package KAASoft\Util\Enum
     */
    class PostStatus extends EnumBase {

        const PENDING = "Pending";
        const PUBLISH = "Publish";
        const FUTURE  = "Future";
        //const DRAFT   = "Draft";
        //const PRIVATE = "Private";
        //const TRASH = "Trash";


        /**
         * @param $statusString
         * @return int|string
         */
        public static function getPostStatus($statusString) {
            $constants = EnumBase::getConstants(PostStatus::class);

            foreach ($constants as $key => $value) {
                if (strcmp($statusString,
                           $value) === 0
                ) {
                    return $key;
                }
            }

            return EnumBase::UNKNOWN;
        }
    }

