<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Util\Enum;


    use ReflectionClass;

    /**
     * Class EnumBase
     * @package KAASoft\Util\Enum
     */
    abstract class EnumBase {

        const UNKNOWN = "Unknown";

        static function getConstants($className) {
            $reflectionClass = new ReflectionClass($className);

            return $reflectionClass->getConstants();
        }
    }